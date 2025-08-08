<?php

namespace App\Livewire\AdminPanel\AcademicYears;

use App\Models\Master\AcademicYear;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Title('Academic Years')]
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public string $mode;
    public int $id;

    #[Rule('required')]
    public string $academic_year = '';

    #[Rule('required|date')]
    public string $start_date = '';

    #[Rule('required|date|after:start_date')]
    public string $end_date = '';

    #[Rule('required|in:active,inactive')]
    public string $status = 'active';

    #[Url()]
    public string $search = '';

    public function openModal(): void
    {
        $this->showModal = true;
        $this->mode = 'add';
        $this->resetForm();
    }

    public function save(): void
    {
        $this->validate();

        AcademicYear::create([
            'academic_year' => $this->academic_year,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ]);

        $this->showModal = false;
        $this->resetValidation();
        $this->showToastr('success', 'Data berhasil ditambahkan');
        $this->dispatch('closeModal');
    }

    public function edit($id): void
    {
        $academicYear = AcademicYear::find($id);
        $this->id = $academicYear->id;
        $this->academic_year = $academicYear->academic_year;
        $this->start_date = $academicYear->start_date->format('Y-m-d');
        $this->end_date = $academicYear->end_date->format('Y-m-d');
        $this->status = $academicYear->status;
        $this->showModal = true;
        $this->mode = 'edit';
    }

    public function update(): void
    {
        $this->validate();

        $academicYear = AcademicYear::find($this->id);
        $academicYear->update([
            'academic_year' => $this->academic_year,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ]);

        $this->showModal = false;
        $this->resetValidation();
        $this->showToastr('success', 'Data berhasil diubah');
        $this->dispatch('closeModal');
    }

    public function cancelEdit(): void
    {
        $this->resetValidation();
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->academic_year = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->status = 'active';
    }

    #[On('delete')]
    public function delete($id): void
    {
        AcademicYear::find($id)->delete();
        $this->showToastr('success', 'Data berhasil dihapus');
    }

    public function render(): View
    {
        $academicYears = AcademicYear::when($this->search, fn ($query) =>
            $query->where('academic_year', 'like', '%' . $this->search . '%')
        )->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin-panel.academic-years.index', compact('academicYears'));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function deleteConfirm($method, $params = null): void
    {
        $this->dispatch('swal:confirm',
            title: 'Apakah anda yakin?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            confirmButtonText: 'Hapus!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }
}
