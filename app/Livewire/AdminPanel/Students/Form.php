<?php

namespace App\Livewire\AdminPanel\Students;

use App\Livewire\Forms\StudentForm;
use App\Models\User\Student;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class Form extends Component
{
    public Student $student;
    public bool $editing = false;
    public StudentForm $form;

    public function mount(Student $student): void
    {
        $this->student = $student;

        if($student->exists) {
            $this->editing = true;
            $this->form->setStudent($student);
        } else {
            // Add default parent entries for new students
            $this->form->addParent(); // Add first parent slot
        }
    }

    public function addParent(): void
    {
        $this->form->addParent();
    }

    public function removeParent(int $index): void
    {
        $this->form->removeParent($index);
    }

    public function save(): RedirectResponse|Redirector
    {
        $this->form->store();
        session()->flash('success', 'Data Siswa berhasil disimpan');
        return redirect()->route('admin.students.index');
    }

    public function edit(): RedirectResponse|Redirector
    {
        $this->form->update();
        session()->flash('success', 'Data Siswa berhasil diubah');
        return redirect()->route('admin.students.index');
    }

    public function render(): View
    {
        $title = $this->editing ? 'Edit Data Siswa' : 'Tambah Data Siswa';
        return view('livewire.admin-panel.students.form')->title($title);
    }
}
