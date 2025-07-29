<?php

namespace App\Livewire\AdminPanel\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pengguna')]
class Index extends Component
{
    use WithPagination;

    #[Url()]
    public string $search = '';

    #[On('delete')]
    public function delete($id): void
    {
        User::findOrFail($id)->delete();

        $this->showToastr('success', 'Data berhasil dihapus');
    }

    public function render(): View
    {
        $users = User::when($this->search, fn ($user) => $user->where('name', 'like', '%' . $this->search . '%'))->paginate(10);

        return view('livewire.admin-panel.users.index', compact('users'));
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
