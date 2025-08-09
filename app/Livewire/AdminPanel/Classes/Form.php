<?php

namespace App\Livewire\AdminPanel\Classes;

use App\Livewire\Forms\ClassesForm;
use App\Models\Academic\Classes;
use App\Models\Master\AcademicYear;
use App\Models\User\Teacher;
use Livewire\Component;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class Form extends Component
{
    public Classes $classes;
    public bool $editing = false;
    public ClassesForm $form;
    public array $listsForFields = [];

    public function mount(Classes $classes): void
    {
        $this->initListsForFields();

        if($classes->exists) {
            $this->editing = true;
            $this->form->setClasses($classes);
        }
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['academic_years'] = AcademicYear::where('status', 'active')->pluck('academic_year', 'id');
        $this->listsForFields['teachers'] = Teacher::pluck('full_name', 'id');
        $this->listsForFields['grade_levels'] = [
            '7' => 'Kelas 7',
            '8' => 'Kelas 8',
            '9' => 'Kelas 9',
        ];
        $this->listsForFields['statuses'] = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
        ];
    }

    public function save(): RedirectResponse|Redirector
    {
        $this->form->store();
        session()->flash('success', 'Data Kelas berhasil disimpan');
        return redirect()->route('admin.classes.index');
    }

    public function edit(): RedirectResponse|Redirector
    {
        $this->form->update();
        session()->flash('success', 'Data Kelas berhasil diubah');
        return redirect()->route('admin.classes.index');
    }

    public function render(): View
    {
        $title = $this->editing ? 'Edit Data Kelas' : 'Tambah Data Kelas';
        return view('livewire.admin-panel.classes.form')->title($title);
    }
}
