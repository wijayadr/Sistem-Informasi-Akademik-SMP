<?php

namespace App\Livewire\Forms;

use App\Models\Assessment\StudentGrade;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Form;

class StudentGradeForm extends Form
{
    public ?StudentGrade $grade;

    #[Validate('required')]
    public string $student_id = '';

    #[Validate('required')]
    public string $teacher_subject_id = '';

    #[Validate('required')]
    public string $grade_component_id = '';

    #[Validate('required|numeric|min:0|max:100')]
    public string $grade_value = '';

    #[Validate('required|date')]
    public string $input_date = '';

    public string $notes = '';
    public string $input_teacher_id = '';
    public string $academic_year_id = '';

    public function setGrade(StudentGrade $grade): void
    {
        $this->grade = $grade;
        $this->student_id = $grade->student_id;
        $this->teacher_subject_id = $grade->teacher_subject_id;
        $this->grade_component_id = $grade->grade_component_id;
        $this->grade_value = $grade->grade_value;
        $this->input_date = $grade->input_date->format('Y-m-d');
        $this->notes = $grade->notes ?? '';
        $this->input_teacher_id = $grade->input_teacher_id;

        // Get academic year from teacher subject
        $this->academic_year_id = $grade->teacherSubject->academic_year_id;
    }

    public function store(): void
    {
        $this->validate();

        // Validate that the teacher subject belongs to the selected academic year
        $teacherSubject = \App\Models\Academic\TeacherSubject::where('id', $this->teacher_subject_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->first();

        if (!$teacherSubject) {
            throw new \Exception('Mata pelajaran tidak valid untuk tahun akademik yang dipilih');
        }

        // Check if grade already exists
        $exists = StudentGrade::where('student_id', $this->student_id)
            ->where('teacher_subject_id', $this->teacher_subject_id)
            ->where('grade_component_id', $this->grade_component_id)
            ->whereDate('input_date', $this->input_date)
            ->exists();

        if ($exists) {
            throw new \Exception('Nilai untuk siswa ini pada mata pelajaran, komponen, dan tanggal yang sama sudah ada');
        }

        // Validate that student is in the class for this academic year
        $studentInClass = \App\Models\Academic\ClassStudent::where('student_id', $this->student_id)
            ->where('class_id', $teacherSubject->class_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->exists();

        if (!$studentInClass) {
            throw new \Exception('Siswa tidak terdaftar di kelas ini untuk tahun akademik yang dipilih');
        }

        StudentGrade::create([
            'student_id' => $this->student_id,
            'teacher_subject_id' => $this->teacher_subject_id,
            'grade_component_id' => $this->grade_component_id,
            'grade_value' => $this->grade_value,
            'input_date' => $this->input_date,
            'notes' => $this->notes,
            'input_teacher_id' => $this->input_teacher_id,
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        // Validate that the teacher subject belongs to the selected academic year
        $teacherSubject = \App\Models\Academic\TeacherSubject::where('id', $this->teacher_subject_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->first();

        if (!$teacherSubject) {
            throw new \Exception('Mata pelajaran tidak valid untuk tahun akademik yang dipilih');
        }

        // Check if grade already exists for different record
        $exists = StudentGrade::where('student_id', $this->student_id)
            ->where('teacher_subject_id', $this->teacher_subject_id)
            ->where('grade_component_id', $this->grade_component_id)
            ->whereDate('input_date', $this->input_date)
            ->where('id', '!=', $this->grade->id)
            ->exists();

        if ($exists) {
            throw new \Exception('Nilai untuk siswa ini pada mata pelajaran, komponen, dan tanggal yang sama sudah ada');
        }

        // Validate that student is in the class for this academic year
        $studentInClass = \App\Models\Academic\ClassStudent::where('student_id', $this->student_id)
            ->where('class_id', $teacherSubject->class_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->exists();

        if (!$studentInClass) {
            throw new \Exception('Siswa tidak terdaftar di kelas ini untuk tahun akademik yang dipilih');
        }

        $this->grade->update([
            'student_id' => $this->student_id,
            'teacher_subject_id' => $this->teacher_subject_id,
            'grade_component_id' => $this->grade_component_id,
            'grade_value' => $this->grade_value,
            'input_date' => $this->input_date,
            'notes' => $this->notes,
            'input_teacher_id' => $this->input_teacher_id,
        ]);

        $this->reset();
    }

    public function getGradeLetter(): string
    {
        if (empty($this->grade_value)) {
            return '-';
        }

        $value = (float) $this->grade_value;

        if ($value >= 90) return 'A';
        if ($value >= 80) return 'B';
        if ($value >= 70) return 'C';
        if ($value >= 60) return 'D';
        return 'E';
    }

    public function getGradeCategory(): string
    {
        if (empty($this->grade_value)) {
            return '-';
        }

        $value = (float) $this->grade_value;

        if ($value >= 90) return 'Sangat Baik';
        if ($value >= 80) return 'Baik';
        if ($value >= 70) return 'Cukup';
        if ($value >= 60) return 'Kurang';
        return 'Sangat Kurang';
    }

    public function getGradeBadgeClass(): string
    {
        if (empty($this->grade_value)) {
            return 'bg-secondary';
        }

        $value = (float) $this->grade_value;

        if ($value >= 90) return 'bg-success';
        if ($value >= 80) return 'bg-primary';
        if ($value >= 70) return 'bg-info';
        if ($value >= 60) return 'bg-warning';
        return 'bg-danger';
    }

    public static function getGradeLetterStatic($value): string
    {
        if (empty($value) || $value == 0) {
            return '-';
        }

        $value = (float) $value;

        if ($value >= 90) return 'A';
        if ($value >= 80) return 'B';
        if ($value >= 70) return 'C';
        if ($value >= 60) return 'D';
        return 'E';
    }

    public static function getGradeBadgeClassStatic($value): string
    {
        if (empty($value) || $value == 0) {
            return 'bg-secondary';
        }

        $value = (float) $value;

        if ($value >= 90) return 'bg-success';
        if ($value >= 80) return 'bg-primary';
        if ($value >= 70) return 'bg-info';
        if ($value >= 60) return 'bg-warning';
        return 'bg-danger';
    }

    protected function validationAttributes(): array
    {
        return [
            'student_id' => 'siswa',
            'teacher_subject_id' => 'mata pelajaran',
            'grade_component_id' => 'komponen nilai',
            'grade_value' => 'nilai',
            'input_date' => 'tanggal input',
        ];
    }

    protected function messages(): array
    {
        return [
            'student_id.required' => 'Siswa harus dipilih',
            'teacher_subject_id.required' => 'Mata pelajaran harus dipilih',
            'grade_component_id.required' => 'Komponen nilai harus dipilih',
            'grade_value.required' => 'Nilai harus diisi',
            'grade_value.numeric' => 'Nilai harus berupa angka',
            'grade_value.min' => 'Nilai minimal 0',
            'grade_value.max' => 'Nilai maksimal 100',
            'input_date.required' => 'Tanggal input harus diisi',
            'input_date.date' => 'Format tanggal tidak valid',
        ];
    }
}
