<?php

namespace App\Livewire\StudentPanel\Schedules;

use App\Models\Academic\Schedule;
use App\Models\Academic\ClassStudent;
use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Jadwal Pelajaran')]
class Index extends Component
{
    public Student $student;

    #[Url()]
    public string $selectedDay = '';

    #[Url()]
    public $academic_year_id = '';

    public array $days = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu'
    ];

    public function mount(): void
    {
        // Get current authenticated student
        $this->student = Student::where('user_id', Auth::id())->firstOrFail();
        $this->selectedDay = 'monday'; // Default to Monday

        // Set current academic year as default
        $currentAcademicYear = AcademicYear::active()->first();
        if ($currentAcademicYear) {
            $this->academic_year_id = $currentAcademicYear->id;
        }
    }

    public function getAcademicYears()
    {
        return AcademicYear::orderBy('start_date', 'desc')->get();
    }

    public function getStudentClass()
    {
        if (!$this->academic_year_id) {
            return null;
        }

        return ClassStudent::where('student_id', $this->student->id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->with(['class'])
            ->first();
    }

    public function getSchedulesByDay()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return collect();
        }

        return Schedule::whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'teacherSubject.class'])
            ->where('day', $this->selectedDay)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getAllSchedulesGroupedByDay()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return collect();
        }

        $schedules = Schedule::whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'teacherSubject.class'])
            ->where('status', 'active')
            ->orderBy('day')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        return $schedules;
    }

    public function getTodaySchedules()
    {
        $today = strtolower(now()->format('l'));
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return collect();
        }

        return Schedule::whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'teacherSubject.class'])
            ->where('day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getWeeklyStats()
    {
        $allSchedules = $this->getAllSchedulesGroupedByDay();

        return [
            'total_schedules' => $allSchedules->flatten()->count(),
            'total_subjects' => $allSchedules->flatten()->pluck('teacherSubject.subject.id')->unique()->count(),
            'total_teachers' => $allSchedules->flatten()->pluck('teacherSubject.teacher.id')->unique()->count(),
            'today_schedules' => $this->getTodaySchedules()->count()
        ];
    }

    public function getCurrentSchedule()
    {
        $today = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return null;
        }

        return Schedule::whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'teacherSubject.class'])
            ->where('day', $today)
            ->where('status', 'active')
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>=', $currentTime)
            ->first();
    }

    public function getNextSchedule()
    {
        $today = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return null;
        }

        return Schedule::whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'teacherSubject.class'])
            ->where('day', $today)
            ->where('status', 'active')
            ->whereTime('start_time', '>', $currentTime)
            ->orderBy('start_time')
            ->first();
    }

    public function updatedAcademicYearId(): void
    {
        // Reset to Monday when academic year changes
        $this->selectedDay = 'monday';
    }

    public function render()
    {
        $schedules = $this->getSchedulesByDay();
        $allSchedules = $this->getAllSchedulesGroupedByDay();
        $todaySchedules = $this->getTodaySchedules();
        $weeklyStats = $this->getWeeklyStats();
        $currentSchedule = $this->getCurrentSchedule();
        $nextSchedule = $this->getNextSchedule();
        $academicYears = $this->getAcademicYears();
        $studentClass = $this->getStudentClass();

        return view('livewire.student-panel.schedules.index', compact(
            'schedules',
            'allSchedules',
            'todaySchedules',
            'weeklyStats',
            'currentSchedule',
            'nextSchedule',
            'academicYears',
            'studentClass'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function updatedSelectedDay(): void
    {
        // Refresh schedules when day changes
        $this->render();
    }
}
