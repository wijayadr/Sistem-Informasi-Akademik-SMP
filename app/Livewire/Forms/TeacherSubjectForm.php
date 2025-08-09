<?php

namespace App\Livewire\Forms;

use App\Models\Academic\TeacherSubject;
use App\Models\Master\AcademicYear;
use Livewire\Attributes\Rule;
use Livewire\Form;

class TeacherSubjectForm extends Form
{
    public ?TeacherSubject $teacherSubject = null;

    #[Rule('required|exists:teachers,id')]
    public $teacher_id = '';

    #[Rule('required|exists:subjects,id')]
    public $subject_id = '';

    #[Rule('required|exists:classes,id')]
    public $class_id = '';

    #[Rule('required|exists:academic_years,id')]
    public $academic_year_id = '';

    #[Rule('nullable|numeric|min:1|max:40')]
    public $weekly_teaching_hours = '';

    #[Rule('required|in:active,inactive')]
    public string $status = 'active';

    public function rules()
    {
        $rules = [
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'weekly_teaching_hours' => 'nullable|numeric|min:1|max:40',
            'status' => 'required|in:active,inactive',
        ];

        // Add unique validation for teacher-subject-class combination
        if ($this->teacherSubject) {
            $rules['teacher_id'] = 'required|exists:teachers,id|unique:teacher_subjects,teacher_id,' . $this->teacherSubject->id . ',id,subject_id,' . $this->subject_id . ',class_id,' . $this->class_id . ',status,active';
        } else {
            $rules['teacher_id'] = 'required|exists:teachers,id|unique:teacher_subjects,teacher_id,NULL,id,subject_id,' . $this->subject_id . ',class_id,' . $this->class_id . ',status,active';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'teacher_id.required' => 'Pilih guru yang akan ditugaskan',
            'teacher_id.exists' => 'Guru tidak ditemukan',
            'teacher_id.unique' => 'Guru sudah mengajar mata pelajaran ini di kelas ini',
            'subject_id.required' => 'Pilih mata pelajaran',
            'subject_id.exists' => 'Mata pelajaran tidak ditemukan',
            'class_id.required' => 'Kelas harus dipilih',
            'class_id.exists' => 'Kelas tidak ditemukan',
            'academic_year_id.required' => 'Tahun ajaran harus dipilih',
            'academic_year_id.exists' => 'Tahun ajaran tidak ditemukan',
            'weekly_teaching_hours.numeric' => 'Jam mengajar harus berupa angka',
            'weekly_teaching_hours.min' => 'Jam mengajar minimal 1 jam',
            'weekly_teaching_hours.max' => 'Jam mengajar maksimal 40 jam',
        ];
    }

    public function setTeacherSubject(TeacherSubject $teacherSubject): void
    {
        $this->teacherSubject = $teacherSubject;
        $this->teacher_id = $teacherSubject->teacher_id;
        $this->subject_id = $teacherSubject->subject_id;
        $this->class_id = $teacherSubject->class_id;
        $this->academic_year_id = $teacherSubject->academic_year_id;
        $this->weekly_teaching_hours = $teacherSubject->weekly_teaching_hours;
        $this->status = $teacherSubject->status;
    }

    public function store(): void
    {
        // Set academic year to active academic year if not set
        if (!$this->academic_year_id) {
            $activeAcademicYear = AcademicYear::where('status', 'active')->first();
            if ($activeAcademicYear) {
                $this->academic_year_id = $activeAcademicYear->id;
            }
        }

        $this->validate();

        $data = $this->except('teacherSubject');

        TeacherSubject::create($data);
        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $data = $this->except('teacherSubject');

        $this->teacherSubject->update($data);
        $this->reset();
    }
}
