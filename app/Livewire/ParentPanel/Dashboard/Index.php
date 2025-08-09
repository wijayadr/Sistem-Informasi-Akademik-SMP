<?php

namespace App\Livewire\ParentPanel\Dashboard;

use App\Models\Academic\ClassStudent;
use App\Models\Academic\Schedule;
use App\Models\Assessment\StudentGrade;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\User\ParentModel;
use App\Models\User\Student;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Orang Tua')]
class Index extends Component
{
    public ParentModel $parent;
    public $students = [];
    public $selectedStudentId = '';

    public function mount(): void
    {
        // Get current authenticated parent
        $this->parent = ParentModel::where('user_id', auth()->id())->firstOrFail();

        // Get all children of this parent
        $this->students = Student::where('id', $this->parent->student_id)
            ->orWhereHas('parents', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        // Set first student as default
        if ($this->students->count() > 0) {
            $this->selectedStudentId = $this->students->first()->id;
        }
    }

    public function getSelectedStudent()
    {
        if (!$this->selectedStudentId) {
            return null;
        }

        return Student::find($this->selectedStudentId);
    }

    public function getCurrentAcademicYear()
    {
        return AcademicYear::active()->first();
    }

    public function getStudentClass($studentId = null)
    {
        $studentId = $studentId ?: $this->selectedStudentId;
        $academicYear = $this->getCurrentAcademicYear();

        if (!$studentId || !$academicYear) {
            return null;
        }

        return ClassStudent::where('student_id', $studentId)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->with(['class', 'academicYear'])
            ->first();
    }

    public function getTodayAttendanceStatus($studentId = null)
    {
        $studentId = $studentId ?: $this->selectedStudentId;
        $studentClass = $this->getStudentClass($studentId);

        if (!$studentClass) {
            return null;
        }

        return StudentAttendance::where('student_id', $studentId)
            ->whereDate('attendance_date', today())
            ->whereHas('schedule.teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $studentClass->academic_year_id);
            })
            ->with(['schedule.teacherSubject.subject'])
            ->get();
    }

    public function getRecentGrades($studentId = null)
    {
        $studentId = $studentId ?: $this->selectedStudentId;
        $studentClass = $this->getStudentClass($studentId);

        if (!$studentClass) {
            return collect();
        }

        return StudentGrade::with(['teacherSubject.subject', 'gradeComponent'])
            ->where('student_id', $studentId)
            ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $studentClass->academic_year_id)
                      ->where('status', 'active');
            })
            ->orderBy('input_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getMonthlyAttendanceStats($studentId = null)
    {
        $studentId = $studentId ?: $this->selectedStudentId;
        $studentClass = $this->getStudentClass($studentId);

        if (!$studentClass) {
            return [
                'total' => 0,
                'present' => 0,
                'percentage' => 0
            ];
        }

        $attendances = StudentAttendance::where('student_id', $studentId)
            ->whereHas('schedule.teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $studentClass->academic_year_id);
            })
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->get();

        $total = $attendances->count();
        $present = $attendances->whereIn('attendance_status', ['present', 'late'])->count();
        $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'percentage' => $percentage
        ];
    }

    public function getMonthlyGradeAverage($studentId = null)
    {
        $studentId = $studentId ?: $this->selectedStudentId;
        $studentClass = $this->getStudentClass($studentId);

        if (!$studentClass) {
            return 0;
        }

        $grades = StudentGrade::where('student_id', $studentId)
            ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $studentClass->academic_year_id);
            })
            ->whereMonth('input_date', now()->month)
            ->whereYear('input_date', now()->year)
            ->get();

        return $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0;
    }

    public function getTodaySchedules($studentId = null)
    {
        $studentId = $studentId ?: $this->selectedStudentId;
        $studentClass = $this->getStudentClass($studentId);

        if (!$studentClass) {
            return collect();
        }

        $today = strtolower(now()->format('l'));
        return Schedule::whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $studentClass->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'teacherSubject.class'])
            ->where('day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function updatedSelectedStudentId(): void
    {
        // Refresh data when student selection changes
        $this->dispatch('student-changed');
    }

    protected function getGradeLetter($value): string
    {
        if ($value >= 90) return 'A';
        if ($value >= 80) return 'B';
        if ($value >= 70) return 'C';
        if ($value >= 60) return 'D';
        return 'E';
    }

    protected function getGradeBadgeClass($value): string
    {
        if ($value >= 80) return 'bg-success-subtle text-success';
        if ($value >= 70) return 'bg-info-subtle text-info';
        if ($value >= 60) return 'bg-warning-subtle text-warning';
        return 'bg-danger-subtle text-danger';
    }

    public function render()
    {
        $selectedStudent = $this->getSelectedStudent();
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $studentClass = $this->getStudentClass();
        $todayAttendance = $this->getTodayAttendanceStatus();
        $recentGrades = $this->getRecentGrades();
        $monthlyAttendanceStats = $this->getMonthlyAttendanceStats();
        $monthlyGradeAverage = $this->getMonthlyGradeAverage();
        $todaySchedules = $this->getTodaySchedules();

        return view('livewire.parent-panel.dashboard.index', compact(
            'selectedStudent',
            'currentAcademicYear',
            'studentClass',
            'todayAttendance',
            'recentGrades',
            'monthlyAttendanceStats',
            'monthlyGradeAverage',
            'todaySchedules'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }
}
