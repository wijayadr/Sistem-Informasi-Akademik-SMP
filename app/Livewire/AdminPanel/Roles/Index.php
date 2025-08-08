<?php

namespace App\Livewire\AdminPanel\Roles;

use App\Models\User\Role;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[Title('Roles')]
class Index extends Component
{
    use WithPagination;
    public bool $showModal = false;
    public string $mode;
    public int $id;

    #[Rule('required')]
    public string $name = '';

    #[Url()]
    public string $search = '';

    public function openModal(): void
    {
        $this->showModal = true;
        $this->mode = 'add';
    }

    public function save(): void
    {
        $this->validate();

        Role::create([
            'name' => $this->name,
            'slug' => str()->slug($this->name)
        ]);

        $this->showModal = false;
        $this->resetValidation();
        $this->showToastr('success', 'Data berhasil ditambahkan');
        $this->dispatch('closeModal');
    }

    public function edit($id): void
    {
        $role = Role::find($id);
        $this->id = $role->id;
        $this->name = $role->name;
        $this->showModal = true;
        $this->mode = 'edit';
    }

    public function update(): void
    {
        $this->validate();

        $role = Role::find($this->id);

        $role->update([
            'name' => $this->name,
            'slug' => str()->slug($this->name)
        ]);

        $this->showModal = false;
        $this->resetValidation();
        $this->showToastr('success', 'Data berhasil diubah');
        $this->dispatch('closeModal');
    }

    public function cancelEdit(): void
    {
        $this->resetValidation();
    }

    #[On('delete')]
    public function delete($id): void
    {
        Role::find($id)->delete();
        $this->showToastr('success', 'Data berhasil dihapus');
    }

    public function render(): View
    {
        $roles = Role::when($this->search, fn ($role) => $role->where('name', 'like', '%' . $this->search . '%'))->paginate(10);

        return view('livewire.admin-panel.roles.index', compact('roles'));
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
