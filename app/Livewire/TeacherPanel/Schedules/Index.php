<?php

namespace App\Livewire\TeacherPanel\Schedules;

use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use App\Models\Master\AcademicYear;
use App\Models\User\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Jadwal Mengajar')]
class Index extends Component
{
    public Teacher $teacher;

    #[Url(keep: true)]
    public ?int $selectedAcademicYear = null;

    #[Url(keep: true)]
    public string $selectedDay = '';

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
        // Get current authenticated teacher
        $this->teacher = Teacher::where('user_id', Auth::id())->firstOrFail();

        // Set default academic year to current active year
        if (!$this->selectedAcademicYear) {
            $currentAcademicYear = AcademicYear::where('status', 'active')->first();
            $this->selectedAcademicYear = $currentAcademicYear?->id;
        }

        // Default to Monday
        if (!$this->selectedDay) {
            $this->selectedDay = 'monday';
        }
    }

    public function updatedSelectedAcademicYear(): void
    {
        // Reset day selection when academic year changes
        $this->selectedDay = 'monday';

        // Refresh the component
        $this->dispatch('academic-year-changed');
    }

    public function updatedSelectedDay(): void
    {
        // Refresh schedules when day changes
        $this->dispatch('day-changed');
    }

    public function getAcademicYearsProperty()
    {
        return AcademicYear::orderBy('start_date', 'desc')->get();
    }

    public function getSelectedAcademicYearModelProperty()
    {
        return AcademicYear::find($this->selectedAcademicYear);
    }

    public function getSchedulesByDay()
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }

        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $this->selectedAcademicYear)
                      ->where('status', 'active');
            })
            ->with([
                'teacherSubject.subject',
                'teacherSubject.class',
                'teacherSubject.academicYear'
            ])
            ->where('day', $this->selectedDay)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getAllSchedulesGroupedByDay()
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }

        $schedules = Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $this->selectedAcademicYear)
                      ->where('status', 'active');
            })
            ->with([
                'teacherSubject.subject',
                'teacherSubject.class',
                'teacherSubject.academicYear'
            ])
            ->where('status', 'active')
            ->orderBy('day')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        return $schedules;
    }

    public function getTodaySchedules()
    {
        if (!$this->selectedAcademicYear) {
            return collect();
        }

        $today = strtolower(now()->format('l'));

        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $this->selectedAcademicYear)
                      ->where('status', 'active');
            })
            ->with([
                'teacherSubject.subject',
                'teacherSubject.class',
                'teacherSubject.academicYear'
            ])
            ->where('day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getWeeklyStats()
    {
        if (!$this->selectedAcademicYear) {
            return [
                'total_schedules' => 0,
                'total_classes' => 0,
                'total_subjects' => 0,
                'today_schedules' => 0
            ];
        }

        $allSchedules = $this->getAllSchedulesGroupedByDay();

        return [
            'total_schedules' => $allSchedules->flatten()->count(),
            'total_classes' => $allSchedules->flatten()
                ->pluck('teacherSubject.class.id')
                ->unique()
                ->count(),
            'total_subjects' => $allSchedules->flatten()
                ->pluck('teacherSubject.subject.id')
                ->unique()
                ->count(),
            'today_schedules' => $this->getTodaySchedules()->count()
        ];
    }

    public function getCurrentSchedule()
    {
        if (!$this->selectedAcademicYear) {
            return null;
        }

        // Only show current schedule if viewing current academic year
        $currentAcademicYear = AcademicYear::where('status', 'active')->first();
        if (!$currentAcademicYear || $this->selectedAcademicYear !== $currentAcademicYear->id) {
            return null;
        }

        $today = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        return Schedule::whereHas('teacherSubject', function ($query) use ($currentAcademicYear) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $currentAcademicYear->id)
                      ->where('status', 'active');
            })
            ->with([
                'teacherSubject.subject',
                'teacherSubject.class',
                'teacherSubject.academicYear'
            ])
            ->where('day', $today)
            ->where('status', 'active')
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>=', $currentTime)
            ->first();
    }

    public function getNextSchedule()
    {
        if (!$this->selectedAcademicYear) {
            return null;
        }

        // Only show next schedule if viewing current academic year
        $currentAcademicYear = AcademicYear::where('status', 'active')->first();
        if (!$currentAcademicYear || $this->selectedAcademicYear !== $currentAcademicYear->id) {
            return null;
        }

        $today = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        return Schedule::whereHas('teacherSubject', function ($query) use ($currentAcademicYear) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('academic_year_id', $currentAcademicYear->id)
                      ->where('status', 'active');
            })
            ->with([
                'teacherSubject.subject',
                'teacherSubject.class',
                'teacherSubject.academicYear'
            ])
            ->where('day', $today)
            ->where('status', 'active')
            ->whereTime('start_time', '>', $currentTime)
            ->orderBy('start_time')
            ->first();
    }

    public function getTotalTeachingHours()
    {
        if (!$this->selectedAcademicYear) {
            return 0;
        }

        $schedules = $this->getAllSchedulesGroupedByDay()->flatten();
        $totalMinutes = 0;

        foreach ($schedules as $schedule) {
            $start = \Carbon\Carbon::createFromFormat('H:i', $schedule->start_time->format('H:i'));
            $end = \Carbon\Carbon::createFromFormat('H:i', $schedule->end_time->format('H:i'));
            $totalMinutes += $end->diffInMinutes($start);
        }

        return round($totalMinutes / 60, 1); // Convert to hours with 1 decimal
    }

    public function render()
    {
        $schedules = $this->getSchedulesByDay();
        $allSchedules = $this->getAllSchedulesGroupedByDay();
        $todaySchedules = $this->getTodaySchedules();
        $weeklyStats = $this->getWeeklyStats();
        $currentSchedule = $this->getCurrentSchedule();
        $nextSchedule = $this->getNextSchedule();
        $academicYears = $this->academicYears;
        $selectedAcademicYearModel = $this->selectedAcademicYearModel;
        $totalTeachingHours = $this->getTotalTeachingHours();

        return view('livewire.teacher-panel.schedules.index', compact(
            'schedules',
            'allSchedules',
            'todaySchedules',
            'weeklyStats',
            'currentSchedule',
            'nextSchedule',
            'academicYears',
            'selectedAcademicYearModel',
            'totalTeachingHours'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }
}
