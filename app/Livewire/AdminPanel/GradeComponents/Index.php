<?php

namespace App\Livewire\AdminPanel\GradeComponents;

use App\Models\Assessment\GradeComponent;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Title('Grade Components')]
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public string $mode;
    public int $id;

    #[Rule('required')]
    public string $component_name = '';

    #[Rule('required|numeric|min:0|max:100')]
    public string $weight_percentage = '';

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

        GradeComponent::create([
            'component_name' => $this->component_name,
            'weight_percentage' => $this->weight_percentage,
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
        $gradeComponent = GradeComponent::find($id);
        $this->id = $gradeComponent->id;
        $this->component_name = $gradeComponent->component_name;
        $this->weight_percentage = $gradeComponent->weight_percentage;
        $this->description = $gradeComponent->description;
        $this->status = $gradeComponent->status;
        $this->showModal = true;
        $this->mode = 'edit';
    }

    public function update(): void
    {
        $this->validate();

        $gradeComponent = GradeComponent::find($this->id);
        $gradeComponent->update([
            'component_name' => $this->component_name,
            'weight_percentage' => $this->weight_percentage,
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
        $this->component_name = '';
        $this->weight_percentage = '';
        $this->description = '';
        $this->status = 'active';
    }

    #[On('delete')]
    public function delete($id): void
    {
        GradeComponent::find($id)->delete();
        $this->showToastr('success', 'Data berhasil dihapus');
    }

    public function render(): View
    {
        $gradeComponents = GradeComponent::when($this->search, fn ($query) =>
            $query->where('component_name', 'like', '%' . $this->search . '%')
        )->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin-panel.grade-components.index', compact('gradeComponents'));
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
