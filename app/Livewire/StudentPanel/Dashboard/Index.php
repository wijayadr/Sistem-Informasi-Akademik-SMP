<?php

namespace App\Livewire\StudentPanel\Dashboard;

use App\Models\Academic\ClassStudent;
use App\Models\Academic\Schedule;
use App\Models\Assessment\StudentGrade;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Siswa')]
class Index extends Component
{
    public Student $student;
    public $currentClass;
    public $currentAcademicYear;

    public function mount(): void
    {
        // Get current authenticated student
        $this->student = Student::where('user_id', auth()->id())->firstOrFail();
        $this->currentAcademicYear = $this->getCurrentAcademicYear();
        $this->currentClass = $this->getStudentClass();
    }

    public function getCurrentAcademicYear()
    {
        return AcademicYear::active()->first();
    }

    public function getStudentClass()
    {
        if (!$this->currentAcademicYear) {
            return null;
        }

        return ClassStudent::where('student_id', $this->student->id)
            ->where('academic_year_id', $this->currentAcademicYear->id)
            ->where('status', 'active')
            ->with(['class', 'academicYear'])
            ->first();
    }

    public function getTodayAttendanceStatus()
    {
        if (!$this->currentClass) {
            return collect();
        }

        return StudentAttendance::where('student_id', $this->student->id)
            ->whereDate('attendance_date', today())
            ->whereHas('schedule.teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id);
            })
            ->with(['schedule.teacherSubject.subject', 'schedule.teacherSubject.teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRecentGrades()
    {
        if (!$this->currentClass) {
            return collect();
        }

        return StudentGrade::with(['teacherSubject.subject', 'teacherSubject.teacher', 'gradeComponent'])
            ->where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id)
                      ->where('status', 'active');
            })
            ->orderBy('input_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getMonthlyAttendanceStats()
    {
        if (!$this->currentClass) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'sick' => 0,
                'permission' => 0,
                'percentage' => 0
            ];
        }

        $attendances = StudentAttendance::where('student_id', $this->student->id)
            ->whereHas('schedule.teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id);
            })
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->get();

        $total = $attendances->count();
        $present = $attendances->where('attendance_status', 'present')->count();
        $late = $attendances->where('attendance_status', 'late')->count();
        $absent = $attendances->where('attendance_status', 'absent')->count();
        $sick = $attendances->where('attendance_status', 'sick')->count();
        $permission = $attendances->where('attendance_status', 'permission')->count();

        $presentTotal = $present + $late; // Late still counts as present
        $percentage = $total > 0 ? round(($presentTotal / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'sick' => $sick,
            'permission' => $permission,
            'percentage' => $percentage
        ];
    }

    public function getMonthlyGradeAverage()
    {
        if (!$this->currentClass) {
            return 0;
        }

        $grades = StudentGrade::where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id);
            })
            ->whereMonth('input_date', now()->month)
            ->whereYear('input_date', now()->year)
            ->get();

        return $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0;
    }

    public function getSemesterGradeAverage()
    {
        if (!$this->currentClass) {
            return 0;
        }

        $grades = StudentGrade::where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id);
            })
            ->get();

        return $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0;
    }

    public function getTodaySchedules()
    {
        if (!$this->currentClass) {
            return collect();
        }

        $today = strtolower(now()->format('l'));
        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher'])
            ->where('day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getUpcomingSchedules()
    {
        if (!$this->currentClass) {
            return collect();
        }

        $currentTime = now()->format('H:i:s');
        $today = strtolower(now()->format('l'));

        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher'])
            ->where('day', $today)
            ->where('start_time', '>', $currentTime)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->limit(3)
            ->get();
    }

    public function getWeeklyAttendanceStats()
    {
        if (!$this->currentClass) {
            return [];
        }

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $attendances = StudentAttendance::where('student_id', $this->student->id)
            ->whereHas('schedule.teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id);
            })
            ->whereBetween('attendance_date', [$startOfWeek, $endOfWeek])
            ->with(['schedule.teacherSubject.subject'])
            ->get()
            ->groupBy(function($attendance) {
                return $attendance->attendance_date->format('Y-m-d');
            });

        return $attendances;
    }

    public function getSubjectGradeSummary()
    {
        if (!$this->currentClass) {
            return collect();
        }

        return StudentGrade::with(['teacherSubject.subject'])
            ->where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->currentClass->class_id)
                      ->where('academic_year_id', $this->currentClass->academic_year_id);
            })
            ->get()
            ->groupBy('teacherSubject.subject.subject_name')
            ->map(function($grades, $subjectName) {
                $average = round($grades->avg('grade_value'), 2);
                $count = $grades->count();
                $latest = $grades->sortByDesc('input_date')->first();

                return [
                    'subject_name' => $subjectName,
                    'average' => $average,
                    'count' => $count,
                    'latest_grade' => $latest->grade_value ?? 0,
                    'latest_date' => $latest->input_date ?? null,
                    'grade_letter' => $this->getGradeLetter($average)
                ];
            })
            ->sortByDesc('average');
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

    protected function getAttendanceStatusBadge($status): array
    {
        return match($status) {
            'present' => ['class' => 'bg-success-subtle text-success', 'text' => 'Hadir', 'icon' => 'ri-check-line'],
            'absent' => ['class' => 'bg-danger-subtle text-danger', 'text' => 'Tidak Hadir', 'icon' => 'ri-close-line'],
            'late' => ['class' => 'bg-warning-subtle text-warning', 'text' => 'Terlambat', 'icon' => 'ri-time-line'],
            'sick' => ['class' => 'bg-info-subtle text-info', 'text' => 'Sakit', 'icon' => 'ri-heart-pulse-line'],
            'permission' => ['class' => 'bg-secondary-subtle text-secondary', 'text' => 'Izin', 'icon' => 'ri-file-text-line'],
            default => ['class' => 'bg-light text-muted', 'text' => 'Tidak Diketahui', 'icon' => 'ri-question-line']
        };
    }

    public function refreshDashboard(): void
    {
        $this->currentAcademicYear = $this->getCurrentAcademicYear();
        $this->currentClass = $this->getStudentClass();
        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        $todayAttendance = $this->getTodayAttendanceStatus();
        $recentGrades = $this->getRecentGrades();
        $monthlyAttendanceStats = $this->getMonthlyAttendanceStats();
        $monthlyGradeAverage = $this->getMonthlyGradeAverage();
        $semesterGradeAverage = $this->getSemesterGradeAverage();
        $todaySchedules = $this->getTodaySchedules();
        $upcomingSchedules = $this->getUpcomingSchedules();
        $weeklyAttendanceStats = $this->getWeeklyAttendanceStats();
        $subjectGradeSummary = $this->getSubjectGradeSummary();

        return view('livewire.student-panel.dashboard.index', compact(
            'todayAttendance',
            'recentGrades',
            'monthlyAttendanceStats',
            'monthlyGradeAverage',
            'semesterGradeAverage',
            'todaySchedules',
            'upcomingSchedules',
            'weeklyAttendanceStats',
            'subjectGradeSummary'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }
}
