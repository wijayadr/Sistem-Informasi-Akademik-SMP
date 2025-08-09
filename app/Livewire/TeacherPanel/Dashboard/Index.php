<?php

namespace App\Livewire\TeacherPanel\Dashboard;

use App\Models\Academic\ClassStudent;
use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use App\Models\Assessment\StudentGrade;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\User\Teacher;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Guru')]
class Index extends Component
{
    public Teacher $teacher;
    public $teacherSubjects = [];
    public $selectedSubjectId = '';

    public function mount(): void
    {
        // Get current authenticated teacher
        $this->teacher = Teacher::where('user_id', auth()->id())->firstOrFail();

        // Get all subjects taught by this teacher in current academic year
        $currentAcademicYear = $this->getCurrentAcademicYear();

        if ($currentAcademicYear) {
            $this->teacherSubjects = TeacherSubject::where('teacher_id', $this->teacher->id)
                ->where('academic_year_id', $currentAcademicYear->id)
                ->where('status', 'active')
                ->with(['subject', 'class'])
                ->get();

            // Set first subject as default
            if ($this->teacherSubjects->count() > 0) {
                $this->selectedSubjectId = $this->teacherSubjects->first()->id;
            }
        }
    }

    public function getSelectedSubject()
    {
        if (!$this->selectedSubjectId) {
            return null;
        }

        return TeacherSubject::find($this->selectedSubjectId);
    }

    public function getCurrentAcademicYear()
    {
        return AcademicYear::active()->first();
    }

    public function getTodaySchedules()
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return collect();
        }

        $today = strtolower(now()->format('l'));

        return Schedule::whereHas('teacherSubject', function ($query) use ($currentAcademicYear) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $currentAcademicYear->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
            ->where('day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getTodayAttendanceSummary()
    {
        $todaySchedules = $this->getTodaySchedules();
        $summary = [];

        foreach ($todaySchedules as $schedule) {
            $totalStudents = ClassStudent::where('class_id', $schedule->teacherSubject->class_id)
                ->where('academic_year_id', $schedule->teacherSubject->academic_year_id)
                ->where('status', 'active')
                ->count();

            $attendedStudents = StudentAttendance::where('schedule_id', $schedule->id)
                ->whereDate('attendance_date', today())
                ->count();

            $summary[] = [
                'schedule' => $schedule,
                'total_students' => $totalStudents,
                'attended_students' => $attendedStudents,
                'attendance_percentage' => $totalStudents > 0 ? round(($attendedStudents / $totalStudents) * 100, 1) : 0
            ];
        }

        return collect($summary);
    }

    public function getMonthlyTeachingStats()
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return [
                'total_classes' => 0,
                'total_subjects' => 0,
                'total_students' => 0,
                'completed_schedules' => 0
            ];
        }

        $teacherSubjects = TeacherSubject::where('teacher_id', $this->teacher->id)
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('status', 'active')
            ->with(['class', 'subject'])
            ->get();

        $totalClasses = $teacherSubjects->pluck('class_id')->unique()->count();
        $totalSubjects = $teacherSubjects->pluck('subject_id')->unique()->count();

        $totalStudents = ClassStudent::whereIn('class_id', $teacherSubjects->pluck('class_id'))
            ->where('academic_year_id', $currentAcademicYear->id)
            ->where('status', 'active')
            ->count();

        $completedSchedules = Schedule::whereHas('teacherSubject', function ($query) use ($currentAcademicYear) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $currentAcademicYear->id);
            })
            ->whereHas('attendances', function ($query) {
                $query->whereMonth('attendance_date', now()->month)
                      ->whereYear('attendance_date', now()->year);
            })
            ->count();

        return [
            'total_classes' => $totalClasses,
            'total_subjects' => $totalSubjects,
            'total_students' => $totalStudents,
            'completed_schedules' => $completedSchedules
        ];
    }

    public function getRecentGrades()
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return collect();
        }

        return StudentGrade::with(['student', 'teacherSubject.subject', 'gradeComponent'])
            ->whereHas('teacherSubject', function ($query) use ($currentAcademicYear) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $currentAcademicYear->id);
            })
            ->orderBy('input_date', 'desc')
            ->limit(5)
            ->get();
    }

    public function getUpcomingSchedules()
    {
        $currentAcademicYear = $this->getCurrentAcademicYear();

        if (!$currentAcademicYear) {
            return collect();
        }

        $tomorrow = now()->addDay();
        $tomorrowDay = strtolower($tomorrow->format('l'));

        return Schedule::whereHas('teacherSubject', function ($query) use ($currentAcademicYear) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $currentAcademicYear->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
            ->where('day', $tomorrowDay)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function updatedSelectedSubjectId(): void
    {
        // Refresh data when subject selection changes
        $this->dispatch('subject-changed');
    }

    protected function getAttendanceStatusIcon($status): string
    {
        return match($status) {
            'present' => 'ri-check-line',
            'absent' => 'ri-close-line',
            'late' => 'ri-time-line',
            'sick' => 'ri-heart-pulse-line',
            'permission' => 'ri-file-text-line',
            default => 'ri-question-line'
        };
    }

    protected function getAttendanceStatusClass($status): string
    {
        return match($status) {
            'present' => 'bg-success',
            'absent' => 'bg-danger',
            'late' => 'bg-warning',
            'sick' => 'bg-info',
            'permission' => 'bg-secondary',
            default => 'bg-light'
        };
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
        $selectedSubject = $this->getSelectedSubject();
        $currentAcademicYear = $this->getCurrentAcademicYear();
        $todaySchedules = $this->getTodaySchedules();
        $todayAttendanceSummary = $this->getTodayAttendanceSummary();
        $monthlyTeachingStats = $this->getMonthlyTeachingStats();
        $recentGrades = $this->getRecentGrades();
        $upcomingSchedules = $this->getUpcomingSchedules();

        return view('livewire.teacher-panel.dashboard.index', compact(
            'selectedSubject',
            'currentAcademicYear',
            'todaySchedules',
            'todayAttendanceSummary',
            'monthlyTeachingStats',
            'recentGrades',
            'upcomingSchedules'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }
}
