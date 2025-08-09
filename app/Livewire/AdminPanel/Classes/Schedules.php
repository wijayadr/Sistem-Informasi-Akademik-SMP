<?php

namespace App\Livewire\AdminPanel\Classes;

use App\Livewire\Forms\ScheduleForm;
use App\Models\Academic\Classes;
use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;

#[Title('Kelola Jadwal Kelas')]
class Schedules extends Component
{
    public Classes $class;
    public ScheduleForm $form;

    #[Url()]
    public string $selectedDay = '';

    public bool $showScheduleModal = false;
    public bool $editing = false;
    public array $listsForFields = [];
    public array $days = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu'
    ];

    public function mount(Classes $class): void
    {
        $this->class = $class->load(['academicYear', 'homeroomTeacher']);
        $this->selectedDay = 'monday'; // Default to Monday
        $this->initListsForFields();
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['teacher_subjects'] = $this->class->teacherSubjects()
            ->with(['teacher', 'subject'])
            ->where('status', 'active')
            ->get()
            ->mapWithKeys(function ($ts) {
                return [$ts->id => $ts->teacher->full_name . ' - ' . $ts->subject->subject_name];
            });

        $this->listsForFields['days'] = $this->days;

        $this->listsForFields['statuses'] = [
            'active' => 'Aktif',
            'cancelled' => 'Dibatalkan'
        ];
    }

    public function showScheduleModal(): void
    {
        $this->editing = false;
        $this->showScheduleModal = true;
        $this->form->reset();
        $this->form->day = $this->selectedDay;
    }

    public function editSchedule($scheduleId): void
    {
        $schedule = Schedule::with('teacherSubject')->findOrFail($scheduleId);
        $this->editing = true;
        $this->showScheduleModal = true;
        $this->form->setSchedule($schedule);
    }

    public function cancelEdit(): void
    {
        $this->showScheduleModal = false;
        $this->editing = false;
        $this->form->reset();
        $this->dispatch('closeModal');
    }

    public function saveSchedule(): void
    {
        // Check for time conflicts
        if ($this->hasTimeConflict()) {
            $this->showToastr('error', 'Jadwal bertabrakan dengan jadwal lain pada waktu yang sama');
            return;
        }

        if ($this->editing) {
            $this->form->update();
            $this->showToastr('success', 'Jadwal berhasil diperbarui');
        } else {
            $this->form->store();
            $this->showToastr('success', 'Jadwal berhasil ditambahkan');
        }

        $this->dispatch('closeModal');
    }

    protected function hasTimeConflict(): bool
    {
        $query = Schedule::where('day', $this->form->day)
            ->whereHas('teacherSubject', function ($q) {
                $q->where('class_id', $this->class->id);
            })
            ->where('status', 'active')
            ->where(function ($q) {
                // Check if new schedule overlaps with existing ones
                $q->whereBetween('start_time', [$this->form->start_time, $this->form->end_time])
                  ->orWhereBetween('end_time', [$this->form->start_time, $this->form->end_time])
                  ->orWhere(function ($q2) {
                      $q2->where('start_time', '<=', $this->form->start_time)
                         ->where('end_time', '>=', $this->form->end_time);
                  });
            });

        // Exclude current schedule when editing
        if ($this->editing && $this->form->schedule) {
            $query->where('id', '!=', $this->form->schedule->id);
        }

        return $query->exists();
    }

    #[On('delete-schedule')]
    public function deleteSchedule($id): void
    {
        Schedule::findOrFail($id)->delete();
        $this->showToastr('success', 'Jadwal berhasil dihapus');
    }

    public function deleteScheduleConfirm($scheduleId): void
    {
        $this->dispatch('swal:confirm',
            title: 'Hapus Jadwal?',
            text: 'Jadwal akan dihapus secara permanen!',
            icon: 'warning',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            method: 'delete-schedule',
            params: $scheduleId
        );
    }

    public function changeStatus($scheduleId, $status): void
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->update(['status' => $status]);

        $statusText = $status == 'active' ? 'diaktifkan' : 'dibatalkan';
        $this->showToastr('success', "Jadwal berhasil {$statusText}");
    }

    public function getSchedulesByDay()
    {
        return Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->class->id);
            })
            ->with(['teacherSubject.teacher', 'teacherSubject.subject'])
            ->where('day', $this->selectedDay)
            ->orderBy('start_time')
            ->get();
    }

    public function getAllSchedulesGroupedByDay()
    {
        $schedules = Schedule::whereHas('teacherSubject', function ($query) {
                $query->where('class_id', $this->class->id);
            })
            ->with(['teacherSubject.teacher', 'teacherSubject.subject'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        return $schedules;
    }

    public function render()
    {
        $schedules = $this->getSchedulesByDay();
        $allSchedules = $this->getAllSchedulesGroupedByDay();

        return view('livewire.admin-panel.classes.schedules', compact('schedules', 'allSchedules'));
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
