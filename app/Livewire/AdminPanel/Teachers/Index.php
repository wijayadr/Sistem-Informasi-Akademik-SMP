<?php

namespace App\Livewire\AdminPanel\Teachers;

use App\Models\User\Teacher;
use App\Models\User;
use App\Models\User\Role;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Title('Kelola Guru')]
class Index extends Component
{
    use WithPagination;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $gender_filter = '';

    #[Url()]
    public string $employment_status_filter = '';

    #[On('delete')]
    public function delete($id): void
    {
        $teacher = Teacher::findOrFail($id);

        // Delete user account if exists
        if($teacher->user_id) {
            User::find($teacher->user_id)?->delete();
        }

        $teacher->delete();
        $this->showToastr('success', 'Data guru berhasil dihapus');
    }

    #[On('register-user')]
    public function registerUser($id): void
    {
        $teacher = Teacher::findOrFail($id);

        if($teacher->user_id) {
            $this->showToastr('error', 'Guru sudah memiliki akun user');
            return;
        }

        // Get teacher role
        $teacherRole = Role::where('name', 'Teacher')->orWhere('slug', 'teacher')->first();
        if(!$teacherRole) {
            $this->showToastr('error', 'Role Teacher tidak ditemukan');
            return;
        }

        // Generate unique email
        $baseEmail = strtolower(str_replace(' ', '.', $teacher->full_name));
        $email = $baseEmail . '@teacher.com';

        // Check if email already exists and make it unique
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@teacher.com';
            $counter++;
        }

        // Create user account
        $user = User::create([
            'role_id' => $teacherRole->id,
            'username' => $teacher->employee_id,
            'email' => $email,
            'password' => Hash::make('password'),
            'avatar' => 'default.png',
            'status' => 'active',
        ]);

        // Update teacher with user_id
        $teacher->update(['user_id' => $user->id]);

        $this->showToastr('success', 'Akun user berhasil dibuat untuk guru');
    }

    #[On('toggle-status')]
    public function toggleStatus($id): void
    {
        $teacher = Teacher::findOrFail($id);

        if ($teacher->user_id) {
            $user = User::find($teacher->user_id);
            $newStatus = $user->status === 'active' ? 'inactive' : 'active';
            $user->update(['status' => $newStatus]);

            $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
            $this->showToastr('success', "Akun guru berhasil {$statusText}");
        } else {
            $this->showToastr('error', 'Guru belum memiliki akun user');
        }
    }

    public function render()
    {
        $teachers = Teacher::with(['user'])
            ->when($this->search, fn ($teachers) => $teachers->where('full_name', 'like', '%' . $this->search . '%')
                ->orWhere('employee_id', 'like', '%' . $this->search . '%'))
            ->when($this->gender_filter, fn ($teachers) => $teachers->where('gender', $this->gender_filter))
            ->when($this->employment_status_filter, fn ($teachers) => $teachers->where('employment_status', $this->employment_status_filter))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin-panel.teachers.index', compact('teachers'));
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

    public function registerUserConfirm($method, $params = null): void
    {
        $this->dispatch('swal:confirm',
            title: 'Registrasi Akun User?',
            text: 'Akun user akan dibuat untuk guru ini dengan password default "password".',
            icon: 'question',
            confirmButtonText: 'Ya, Buat Akun!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }

    public function toggleStatusConfirm($method, $params = null): void
    {
        $teacher = Teacher::with('user')->find($params);
        if (!$teacher->user_id) {
            $this->showToastr('error', 'Guru belum memiliki akun user');
            return;
        }

        $action = $teacher->user->status === 'active' ? 'nonaktifkan' : 'aktifkan';

        $this->dispatch('swal:confirm',
            title: "Apakah anda yakin ingin {$action} akun guru ini?",
            text: 'Status akun guru akan berubah.',
            icon: 'question',
            confirmButtonText: 'Ya!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }

    public function updatedGenderFilter()
    {
        $this->resetPage();
    }

    public function updatedEmploymentStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
