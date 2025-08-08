<?php

namespace App\Livewire\AdminPanel\Teachers;

use App\Livewire\Forms\TeacherForm;
use App\Models\User\Teacher;
use Livewire\Component;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class Form extends Component
{
    public Teacher $teacher;
    public bool $editing = false;
    public TeacherForm $form;

    public function mount(Teacher $teacher): void
    {
        if($teacher->exists) {
            $this->editing = true;
            $this->form->setTeacher($teacher);
        }
    }

    public function save(): RedirectResponse|Redirector
    {
        $this->form->store();
        session()->flash('success', 'Data Guru berhasil disimpan');
        return redirect()->route('admin.teachers.index');
    }

    public function edit(): RedirectResponse|Redirector
    {
        $this->form->update();
        session()->flash('success', 'Data Guru berhasil diubah');
        return redirect()->route('admin.teachers.index');
    }

    public function render(): View
    {
        $title = $this->editing ? 'Edit Data Guru' : 'Tambah Data Guru';
        return view('livewire.admin-panel.teachers.form')->title($title);
    }
}
