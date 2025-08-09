<?php

namespace App\Livewire\Forms;

use App\Models\Attendance\StudentAttendance;
use Livewire\Attributes\Rule;
use Livewire\Form;

class StudentAttendanceForm extends Form
{
    public ?StudentAttendance $attendance = null;

    #[Rule('required|exists:students,id')]
    public $student_id = '';

    #[Rule('required|exists:schedules,id')]
    public $schedule_id = '';

    #[Rule('required|date')]
    public string $attendance_date = '';

    #[Rule('required|in:present,absent,late,sick,permission')]
    public string $attendance_status = 'present';

    #[Rule('nullable|string|max:500')]
    public string $notes = '';

    #[Rule('nullable|date_format:H:i')]
    public string $check_in_time = '';

    #[Rule('nullable|date_format:H:i')]
    public string $check_out_time = '';

    #[Rule('nullable|exists:teachers,id')]
    public $input_teacher_id = '';

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,id',
            'schedule_id' => 'required|exists:schedules,id',
            'attendance_date' => 'required|date',
            'attendance_status' => 'required|in:present,absent,late,sick,permission',
            'notes' => 'nullable|string|max:500',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'input_teacher_id' => 'required|exists:teachers,id',
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'Pilih siswa',
            'student_id.exists' => 'Siswa tidak ditemukan',
            'schedule_id.required' => 'Pilih jadwal',
            'schedule_id.exists' => 'Jadwal tidak ditemukan',
            'attendance_date.required' => 'Tanggal absensi harus diisi',
            'attendance_date.date' => 'Format tanggal tidak valid',
            'attendance_status.required' => 'Status absensi harus dipilih',
            'attendance_status.in' => 'Status absensi tidak valid',
            'notes.string' => 'Catatan harus berupa teks',
            'notes.max' => 'Catatan maksimal 500 karakter',
            'check_in_time.date_format' => 'Format waktu masuk tidak valid (HH:MM)',
            'check_out_time.date_format' => 'Format waktu keluar tidak valid (HH:MM)',
            'check_out_time.after' => 'Waktu keluar harus setelah waktu masuk',
            'input_teacher_id.required' => 'Guru pengajar harus diisi',
            'input_teacher_id.exists' => 'Guru tidak ditemukan',
        ];
    }

    public function setAttendance(StudentAttendance $attendance): void
    {
        $this->attendance = $attendance;
        $this->student_id = $attendance->student_id;
        $this->schedule_id = $attendance->schedule_id;
        $this->attendance_date = $attendance->attendance_date->format('Y-m-d');
        $this->attendance_status = $attendance->attendance_status;
        $this->notes = $attendance->notes ?? '';
        $this->check_in_time = $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '';
        $this->check_out_time = $attendance->check_out_time ? $attendance->check_out_time->format('H:i') : '';
        $this->input_teacher_id = $attendance->input_teacher_id;
    }

    public function store(): void
    {
        $this->validate();

        // Check if attendance already exists
        $exists = StudentAttendance::where('student_id', $this->student_id)
            ->where('schedule_id', $this->schedule_id)
            ->whereDate('attendance_date', $this->attendance_date)
            ->exists();

        if ($exists) {
            throw new \Exception('Absensi untuk siswa ini pada jadwal dan tanggal yang sama sudah ada');
        }

        $data = $this->except('attendance');

        // Set check times to null if empty
        if (empty($data['check_in_time'])) {
            $data['check_in_time'] = null;
        }
        if (empty($data['check_out_time'])) {
            $data['check_out_time'] = null;
        }

        $data['input_teacher_id'] = auth()->user()->teacher->id;

        StudentAttendance::create($data);
        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $data = $this->except('attendance');

        // Set check times to null if empty
        if (empty($data['check_in_time'])) {
            $data['check_in_time'] = null;
        }
        if (empty($data['check_out_time'])) {
            $data['check_out_time'] = null;
        }

        $this->attendance->update($data);
        $this->reset();
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->attendance_status) {
            'present' => 'bg-success-subtle text-success',
            'absent' => 'bg-danger-subtle text-danger',
            'late' => 'bg-warning-subtle text-warning',
            'sick' => 'bg-info-subtle text-info',
            'permission' => 'bg-secondary-subtle text-secondary',
            default => 'bg-light text-dark'
        };
    }

    public function getStatusText(): string
    {
        return match($this->attendance_status) {
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            default => 'Tidak Diketahui'
        };
    }
}
