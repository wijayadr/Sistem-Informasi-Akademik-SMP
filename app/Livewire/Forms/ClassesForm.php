<?php

namespace App\Livewire\Forms;

use App\Models\Academic\Classes;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ClassesForm extends Form
{
    public ?Classes $classes = null;

    #[Rule('required')]
    public string $class_name = '';

    #[Rule('required|in:1,2,3,4,5,6,7,8,9,10,11,12')]
    public string $grade_level = '';

    #[Rule('required|numeric|min:1')]
    public $capacity = '';

    #[Rule('required')]
    public $academic_year_id = '';

    #[Rule('nullable')]
    public $homeroom_teacher_id = '';

    #[Rule('required|in:active,inactive')]
    public string $status = 'active';

    public function rules()
    {
        $rules = [
            'class_name' => 'required|string|max:255',
            'grade_level' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
            'capacity' => 'required|numeric|min:1',
            'academic_year_id' => 'required|exists:academic_years,id',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
            'status' => 'required|in:active,inactive',
        ];

        if ($this->classes) {
            $rules['class_name'] = 'required|string|max:255|unique:classes,class_name,' . $this->classes->id;
        } else {
            $rules['class_name'] = 'required|string|max:255|unique:classes,class_name';
        }

        return $rules;
    }

    public function setClasses(Classes $classes): void
    {
        $this->classes = $classes;
        $this->class_name = $classes->class_name;
        $this->grade_level = $classes->grade_level;
        $this->capacity = $classes->capacity;
        $this->academic_year_id = $classes->academic_year_id;
        $this->homeroom_teacher_id = $classes->homeroom_teacher_id;
        $this->status = $classes->status;
    }

    public function store(): void
    {
        $this->validate();

        $data = $this->except('classes');

        Classes::create($data);
        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $data = $this->except('classes');

        $this->classes->update($data);
        $this->reset();
    }
}
