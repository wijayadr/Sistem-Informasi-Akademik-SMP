<?php

namespace App\Livewire\Forms;

use App\Models\Academic\ClassStudent;
use App\Models\Master\AcademicYear;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ClassStudentsForm extends Form
{
    #[Rule('required|array|min:1')]
    public array $student_ids = [];

    #[Rule('required|exists:classes,id')]
    public $class_id = '';

    #[Rule('nullable|date')]
    public $class_entry_date = '';

    public function rules()
    {
        return [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'class_entry_date' => 'nullable|date',
        ];
    }

    public function store(): void
    {
        $this->validate();

        $academicYear = AcademicYear::where('status', 'active')->first();

        if (!$academicYear) {
            throw new \Exception('Tidak ada tahun ajaran aktif');
        }

        foreach ($this->student_ids as $studentId) {
            // Check if student already assigned to any active class
            $existingAssignment = ClassStudent::where('student_id', $studentId)
                ->where('status', 'active')
                ->exists();

            if (!$existingAssignment) {
                ClassStudent::create([
                    'student_id' => $studentId,
                    'class_id' => $this->class_id,
                    'academic_year_id' => $academicYear->id,
                    'class_entry_date' => $this->class_entry_date ?: now(),
                    'status' => 'active'
                ]);
            }
        }

        $this->reset();
    }

    public function removeStudent($studentId, $classId): void
    {
        $classStudent = ClassStudent::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->where('status', 'active')
            ->first();

        if ($classStudent) {
            $classStudent->update([
                'status' => 'inactive',
                'class_exit_date' => now()
            ]);
        }
    }
}
