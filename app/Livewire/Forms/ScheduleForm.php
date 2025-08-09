<?php

namespace App\Livewire\Forms;

use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use App\Models\Master\AcademicYear;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ScheduleForm extends Form
{
    public ?Schedule $schedule = null;

    #[Rule('required|exists:teacher_subjects,id')]
    public $teacher_subject_id = '';

    #[Rule('required|in:monday,tuesday,wednesday,thursday,friday,saturday')]
    public string $day = '';

    #[Rule('required|date_format:H:i')]
    public string $start_time = '';

    #[Rule('required|date_format:H:i|after:start_time')]
    public string $end_time = '';

    #[Rule('nullable|string|max:50')]
    public string $classroom = '';

    #[Rule('nullable|string')]
    public string $notes = '';

    #[Rule('required|in:active,cancelled')]
    public string $status = 'active';

    public function rules()
    {
        return [
            'teacher_subject_id' => [
                'required',
                'exists:teacher_subjects,id',
                function ($attribute, $value, $fail) {
                    // Validate that teacher_subject belongs to selected academic year
                    if ($this->teacher_subject_id) {
                        $teacherSubject = TeacherSubject::find($this->teacher_subject_id);
                        if (!$teacherSubject || $teacherSubject->status !== 'active') {
                            $fail('Guru dan mata pelajaran tidak aktif atau tidak ditemukan.');
                        }
                    }
                }
            ],
            'day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                function ($attribute, $value, $fail) {
                    // Check for time conflicts
                    if ($this->start_time && $this->end_time && $this->teacher_subject_id && $this->day) {
                        $query = Schedule::where('day', $this->day)
                            ->where('status', 'active')
                            ->where(function ($q) {
                                $q->where(function ($subQ) {
                                    $subQ->whereTime('start_time', '<=', $this->start_time)
                                         ->whereTime('end_time', '>', $this->start_time);
                                })->orWhere(function ($subQ) {
                                    $subQ->whereTime('start_time', '<', $this->end_time)
                                         ->whereTime('end_time', '>=', $this->end_time);
                                })->orWhere(function ($subQ) {
                                    $subQ->whereTime('start_time', '>=', $this->start_time)
                                         ->whereTime('end_time', '<=', $this->end_time);
                                });
                            })
                            ->whereHas('teacherSubject', function ($q) {
                                $teacherSubject = TeacherSubject::find($this->teacher_subject_id);
                                if ($teacherSubject) {
                                    $q->where('teacher_id', $teacherSubject->teacher_id);
                                }
                            });

                        if ($this->schedule) {
                            $query->where('id', '!=', $this->schedule->id);
                        }

                        if ($query->exists()) {
                            $fail('Terdapat konflik jadwal dengan jadwal lain pada waktu yang sama.');
                        }
                    }
                }
            ],
            'classroom' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:active,cancelled',
        ];
    }

    public function messages()
    {
        return [
            'teacher_subject_id.required' => 'Pilih guru dan mata pelajaran',
            'teacher_subject_id.exists' => 'Guru dan mata pelajaran tidak ditemukan',
            'day.required' => 'Pilih hari',
            'day.in' => 'Hari tidak valid',
            'start_time.required' => 'Waktu mulai harus diisi',
            'start_time.date_format' => 'Format waktu mulai tidak valid (HH:MM)',
            'end_time.required' => 'Waktu selesai harus diisi',
            'end_time.date_format' => 'Format waktu selesai tidak valid (HH:MM)',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
            'classroom.string' => 'Ruang kelas harus berupa teks',
            'classroom.max' => 'Ruang kelas maksimal 50 karakter',
            'notes.string' => 'Catatan harus berupa teks',
            'notes.max' => 'Catatan maksimal 500 karakter',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ];
    }

    public function setSchedule(Schedule $schedule): void
    {
        $this->schedule = $schedule;
        $this->teacher_subject_id = $schedule->teacher_subject_id;
        $this->day = $schedule->day;
        $this->start_time = $schedule->start_time->format('H:i');
        $this->end_time = $schedule->end_time->format('H:i');
        $this->classroom = $schedule->classroom ?? '';
        $this->notes = $schedule->notes ?? '';
        $this->status = $schedule->status;
    }

    public function store(): void
    {
        $this->validate();

        $data = $this->except('schedule');

        Schedule::create($data);
        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $data = $this->except('schedule');

        $this->schedule->update($data);
        $this->reset();
    }

    public function getDurationInMinutes(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = \Carbon\Carbon::createFromFormat('H:i', $this->start_time);
        $end = \Carbon\Carbon::createFromFormat('H:i', $this->end_time);

        return $end->diffInMinutes($start);
    }

    public function getFormattedDuration(): string
    {
        $minutes = $this->getDurationInMinutes();

        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes == 0) {
            return $hours . ' jam';
        }

        return $hours . ' jam ' . $remainingMinutes . ' menit';
    }

    public function getAvailableTeacherSubjects(?int $teacherId = null, ?int $academicYearId = null)
    {
        $query = TeacherSubject::with(['subject', 'class', 'teacher'])
            ->where('status', 'active');

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        } else {
            // Default to current academic year
            $query->whereHas('academicYear', function ($q) {
                $q->where('status', 'active');
            });
        }

        return $query->orderBy('teacher_id')
            ->orderBy('class_id')
            ->orderBy('subject_id')
            ->get();
    }
}
