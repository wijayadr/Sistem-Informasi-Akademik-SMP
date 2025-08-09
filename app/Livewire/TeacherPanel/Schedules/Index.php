<?php

namespace App\Livewire\TeacherPanel\Schedules;

use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use App\Models\User\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Jadwal Mengajar')]
class Index extends Component
{
    public Teacher $teacher;

    #[Url()]
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
        $this->selectedDay = 'monday'; // Default to Monday
    }

    public function getSchedulesByDay()
    {
        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
            ->where('day', $this->selectedDay)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getAllSchedulesGroupedByDay()
    {
        $schedules = Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
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

        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
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
            'total_classes' => $allSchedules->flatten()->pluck('teacherSubject.class.id')->unique()->count(),
            'total_subjects' => $allSchedules->flatten()->pluck('teacherSubject.subject.id')->unique()->count(),
            'today_schedules' => $this->getTodaySchedules()->count()
        ];
    }

    public function getCurrentSchedule()
    {
        $today = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
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

        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('teacher_id', $this->teacher->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
            ->where('day', $today)
            ->where('status', 'active')
            ->whereTime('start_time', '>', $currentTime)
            ->orderBy('start_time')
            ->first();
    }

    public function render()
    {
        $schedules = $this->getSchedulesByDay();
        $allSchedules = $this->getAllSchedulesGroupedByDay();
        $todaySchedules = $this->getTodaySchedules();
        $weeklyStats = $this->getWeeklyStats();
        $currentSchedule = $this->getCurrentSchedule();
        $nextSchedule = $this->getNextSchedule();

        return view('livewire.teacher-panel.schedules.index', compact(
            'schedules',
            'allSchedules',
            'todaySchedules',
            'weeklyStats',
            'currentSchedule',
            'nextSchedule'
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
