<?php

namespace App\Livewire\AdminPanel\Users;

use App\Livewire\Forms\UserForm;
use App\Models\Master\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;
    public User $user;
    public bool $editing = false;
    public UserForm $form;

    public array $listsForFields = [];

    public function mount(User $user): void
    {
        $this->initListsForFields();

        if($user->exists) {
            $this->editing = true;
            $this->form->setUser($user);
            $this->dispatch('recentAvatar', avatar: $user->avatar);
        }
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['roles'] = Role::pluck('name', 'id');
    }

    public function save()
    {
        // Check if user already exists
        $user = User::where('username', $this->form->username)->first();

        if ($user) {
            $this->showToastr('error', 'Pengguna sudah terdaftar');
            return;
        }

        $this->form->store();

        session()->flash('success', 'Data berhasil disimpan');

        return redirect()->route('admin.users.index');
    }

    public function edit(): RedirectResponse|Redirector
    {
        $this->form->update();

        session()->flash('success', 'Data berhasil diupdate');

        return redirect()->route('admin.users.index');
    }

    public function render(): View
    {
        $title = $this->editing ? 'Edit Data Pengguna' : 'Tambah Data Pengguna';

        return view('livewire.admin-panel.users.form')->title($title);
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }
}
