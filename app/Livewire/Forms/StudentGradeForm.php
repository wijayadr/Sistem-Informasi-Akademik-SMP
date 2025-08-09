<?php

namespace App\Livewire\Forms;

use App\Models\Assessment\StudentGrade;
use Livewire\Attributes\Rule;
use Livewire\Form;

class StudentGradeForm extends Form
{
    public ?StudentGrade $grade = null;

    #[Rule('required|exists:students,id')]
    public $student_id = '';

    #[Rule('required|exists:teacher_subjects,id')]
    public $teacher_subject_id = '';

    #[Rule('required|exists:grade_components,id')]
    public $grade_component_id = '';

    #[Rule('required|numeric|min:0|max:100')]
    public $grade_value = '';

    #[Rule('required|date')]
    public string $input_date = '';

    #[Rule('nullable|string|max:500')]
    public string $notes = '';

    #[Rule('nullable|exists:teachers,id')]
    public $input_teacher_id = '';

    public function rules()
    {
        $rules = [
            'student_id' => 'required|exists:students,id',
            'teacher_subject_id' => 'required|exists:teacher_subjects,id',
            'grade_component_id' => 'required|exists:grade_components,id',
            'grade_value' => 'required|numeric|min:0|max:100',
            'input_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'input_teacher_id' => 'required|exists:teachers,id',
        ];

        // Add unique validation when creating new grade
        if (!$this->grade) {
            $rules['student_id'] .= '|unique:student_grades,student_id,NULL,id,teacher_subject_id,' . $this->teacher_subject_id . ',grade_component_id,' . $this->grade_component_id . ',input_date,' . $this->input_date;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'student_id.required' => 'Pilih siswa',
            'student_id.exists' => 'Siswa tidak ditemukan',
            'student_id.unique' => 'Nilai untuk siswa ini pada komponen dan tanggal yang sama sudah ada',
            'teacher_subject_id.required' => 'Pilih mata pelajaran',
            'teacher_subject_id.exists' => 'Mata pelajaran tidak ditemukan',
            'grade_component_id.required' => 'Pilih komponen nilai',
            'grade_component_id.exists' => 'Komponen nilai tidak ditemukan',
            'grade_value.required' => 'Nilai harus diisi',
            'grade_value.numeric' => 'Nilai harus berupa angka',
            'grade_value.min' => 'Nilai minimal 0',
            'grade_value.max' => 'Nilai maksimal 100',
            'input_date.required' => 'Tanggal input harus diisi',
            'input_date.date' => 'Format tanggal tidak valid',
            'notes.string' => 'Catatan harus berupa teks',
            'notes.max' => 'Catatan maksimal 500 karakter',
            'input_teacher_id.required' => 'Guru pengajar harus diisi',
            'input_teacher_id.exists' => 'Guru tidak ditemukan',
        ];
    }

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
    }

    public function store(): void
    {
        $this->validate();

        $this->input_teacher_id = auth()->user()->teacher->id;

        $data = $this->except('grade');

        StudentGrade::create($data);
        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $data = $this->except('grade');

        $this->grade->update($data);
        $this->reset();
    }

    public function getGradeLetter(): string
    {
        if ($this->grade_value >= 90) return 'A';
        if ($this->grade_value >= 80) return 'B';
        if ($this->grade_value >= 70) return 'C';
        if ($this->grade_value >= 60) return 'D';
        return 'E';
    }

    public function getGradeBadgeClass(): string
    {
        if ($this->grade_value >= 80) return 'bg-success-subtle text-success';
        if ($this->grade_value >= 70) return 'bg-info-subtle text-info';
        if ($this->grade_value >= 60) return 'bg-warning-subtle text-warning';
        return 'bg-danger-subtle text-danger';
    }

    public function getGradeCategory(): string
    {
        if ($this->grade_value >= 90) return 'Sangat Baik';
        if ($this->grade_value >= 80) return 'Baik';
        if ($this->grade_value >= 70) return 'Cukup';
        if ($this->grade_value >= 60) return 'Kurang';
        return 'Sangat Kurang';
    }

    public static function getGradeLetterStatic($value): string
    {
        if ($value >= 90) return 'A';
        if ($value >= 80) return 'B';
        if ($value >= 70) return 'C';
        if ($value >= 60) return 'D';
        return 'E';
    }

    public static function getGradeBadgeClassStatic($value): string
    {
        if ($value >= 80) return 'bg-success-subtle text-success';
        if ($value >= 70) return 'bg-info-subtle text-info';
        if ($value >= 60) return 'bg-warning-subtle text-warning';
        return 'bg-danger-subtle text-danger';
    }
}
