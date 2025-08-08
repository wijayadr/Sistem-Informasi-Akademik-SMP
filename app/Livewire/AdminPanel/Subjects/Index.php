<?php

namespace App\Livewire\AdminPanel\Subjects;

use App\Models\Master\Subject;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Title('Subjects')]
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public string $mode;
    public int $id;

    #[Rule('required|unique:subjects,subject_code')]
    public string $subject_code = '';

    #[Rule('required')]
    public string $subject_name = '';

    #[Rule('nullable')]
    public string $description = '';

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

        Subject::create([
            'subject_code' => $this->subject_code,
            'subject_name' => $this->subject_name,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        $this->showModal = false;
        $this->resetValidation();
        $this->showToastr('success', 'Data berhasil ditambahkan');
        $this->dispatch('closeModal');
    }

    public function edit($id): void
    {
        $subject = Subject::find($id);
        $this->id = $subject->id;
        $this->subject_code = $subject->subject_code;
        $this->subject_name = $subject->subject_name;
        $this->description = $subject->description;
        $this->status = $subject->status;
        $this->showModal = true;
        $this->mode = 'edit';
    }

    public function update(): void
    {
        $this->validate([
            'subject_code' => 'required|unique:subjects,subject_code,' . $this->id,
            'subject_name' => 'required',
            'description' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $subject = Subject::find($this->id);
        $subject->update([
            'subject_code' => $this->subject_code,
            'subject_name' => $this->subject_name,
            'description' => $this->description,
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
        $this->subject_code = '';
        $this->subject_name = '';
        $this->description = '';
        $this->status = 'active';
    }

    #[On('delete')]
    public function delete($id): void
    {
        Subject::find($id)->delete();
        $this->showToastr('success', 'Data berhasil dihapus');
    }

    public function render(): View
    {
        $subjects = Subject::when($this->search, fn ($query) =>
            $query->where('subject_name', 'like', '%' . $this->search . '%')
                  ->orWhere('subject_code', 'like', '%' . $this->search . '%')
        )->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin-panel.subjects.index', compact('subjects'));
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
