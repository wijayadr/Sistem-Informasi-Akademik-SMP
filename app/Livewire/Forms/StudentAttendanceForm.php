<?php

namespace App\Livewire\Forms;

use App\Models\Academic\Schedule;
use App\Models\Attendance\StudentAttendance;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Form;

class StudentAttendanceForm extends Form
{
    public ?StudentAttendance $attendance;

    #[Validate('required')]
    public string $student_id = '';

    #[Validate('required')]
    public string $schedule_id = '';

    #[Validate('required|date')]
    public string $attendance_date = '';

    #[Validate('required|in:present,absent,late,sick,permission')]
    public string $attendance_status = 'present';

    public string $notes = '';

    #[Validate('nullable|date_format:H:i')]
    public string $check_in_time = '';

    #[Validate('nullable|date_format:H:i')]
    public string $check_out_time = '';

    public string $input_teacher_id = '';
    public string $academic_year_id = '';

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

        // Get academic year from schedule's teacher subject
        $this->academic_year_id = $attendance->schedule->teacherSubject->academic_year_id;
    }

    public function store(): void
    {
        $this->validate();

        // Validate that the schedule belongs to the selected academic year
        $schedule = Schedule::whereHas('teacherSubject', function ($query) {
            $query->where('academic_year_id', $this->academic_year_id);
        })->find($this->schedule_id);

        if (!$schedule) {
            throw new \Exception('Jadwal tidak valid untuk tahun akademik yang dipilih');
        }

        // Check if attendance already exists
        $exists = StudentAttendance::where('student_id', $this->student_id)
            ->where('schedule_id', $this->schedule_id)
            ->whereDate('attendance_date', $this->attendance_date)
            ->exists();

        if ($exists) {
            throw new \Exception('Absensi untuk siswa ini pada jadwal dan tanggal yang sama sudah ada');
        }

        // Validate that student is in the class for this academic year
        $studentInClass = \App\Models\Academic\ClassStudent::where('student_id', $this->student_id)
            ->where('class_id', $schedule->teacherSubject->class_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->exists();

        if (!$studentInClass) {
            throw new \Exception('Siswa tidak terdaftar di kelas ini untuk tahun akademik yang dipilih');
        }

        StudentAttendance::create([
            'student_id' => $this->student_id,
            'schedule_id' => $this->schedule_id,
            'attendance_date' => $this->attendance_date,
            'attendance_status' => $this->attendance_status,
            'notes' => $this->notes,
            'check_in_time' => $this->check_in_time ? Carbon::createFromFormat('H:i', $this->check_in_time) : null,
            'check_out_time' => $this->check_out_time ? Carbon::createFromFormat('H:i', $this->check_out_time) : null,
            'input_teacher_id' => auth()->user()->teacher->id,
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        // Validate that the schedule belongs to the selected academic year
        $schedule = \App\Models\Academic\Schedule::whereHas('teacherSubject', function ($query) {
            $query->where('academic_year_id', $this->academic_year_id);
        })->find($this->schedule_id);

        if (!$schedule) {
            throw new \Exception('Jadwal tidak valid untuk tahun akademik yang dipilih');
        }

        // Check if attendance already exists for different record
        $exists = StudentAttendance::where('student_id', $this->student_id)
            ->where('schedule_id', $this->schedule_id)
            ->whereDate('attendance_date', $this->attendance_date)
            ->where('id', '!=', $this->attendance->id)
            ->exists();

        if ($exists) {
            throw new \Exception('Absensi untuk siswa ini pada jadwal dan tanggal yang sama sudah ada');
        }

        // Validate that student is in the class for this academic year
        $studentInClass = \App\Models\Academic\ClassStudent::where('student_id', $this->student_id)
            ->where('class_id', $schedule->teacherSubject->class_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->exists();

        if (!$studentInClass) {
            throw new \Exception('Siswa tidak terdaftar di kelas ini untuk tahun akademik yang dipilih');
        }

        $this->attendance->update([
            'student_id' => $this->student_id,
            'schedule_id' => $this->schedule_id,
            'attendance_date' => $this->attendance_date,
            'attendance_status' => $this->attendance_status,
            'notes' => $this->notes,
            'check_in_time' => $this->check_in_time ? Carbon::createFromFormat('H:i', $this->check_in_time) : null,
            'check_out_time' => $this->check_out_time ? Carbon::createFromFormat('H:i', $this->check_out_time) : null,
            'input_teacher_id' => $this->input_teacher_id,
        ]);

        $this->reset();
    }

    protected function validationAttributes(): array
    {
        return [
            'student_id' => 'siswa',
            'schedule_id' => 'jadwal',
            'attendance_date' => 'tanggal absensi',
            'attendance_status' => 'status absensi',
            'check_in_time' => 'waktu masuk',
            'check_out_time' => 'waktu keluar',
        ];
    }

    protected function messages(): array
    {
        return [
            'student_id.required' => 'Siswa harus dipilih',
            'schedule_id.required' => 'Jadwal harus dipilih',
            'attendance_date.required' => 'Tanggal absensi harus diisi',
            'attendance_date.date' => 'Format tanggal tidak valid',
            'attendance_status.required' => 'Status absensi harus dipilih',
            'attendance_status.in' => 'Status absensi tidak valid',
            'check_in_time.date_format' => 'Format waktu masuk tidak valid (HH:MM)',
            'check_out_time.date_format' => 'Format waktu keluar tidak valid (HH:MM)',
        ];
    }
}
