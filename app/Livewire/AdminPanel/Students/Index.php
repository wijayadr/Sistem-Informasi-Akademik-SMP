<?php

namespace App\Livewire\AdminPanel\Students;

use App\Models\User\Student;
use App\Models\User\ParentModel;
use App\Models\User;
use App\Models\User\Role;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Title('Kelola Siswa')]
class Index extends Component
{
    use WithPagination;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $gender_filter = '';

    #[Url()]
    public string $status_filter = '';

    #[On('delete')]
    public function delete($id): void
    {
        $student = Student::findOrFail($id);

        // Delete parent accounts if exists
        $parents = ParentModel::where('student_id', $student->id)->get();
        foreach ($parents as $parent) {
            if ($parent->user_id) {
                User::find($parent->user_id)?->delete();
            }
            $parent->delete();
        }

        // Delete user account if exists
        if($student->user_id) {
            User::find($student->user_id)?->delete();
        }

        $student->delete();
        $this->showToastr('success', 'Data siswa dan orang tua berhasil dihapus');
    }

    #[On('register-user')]
    public function registerUser($id): void
    {
        $student = Student::findOrFail($id);

        if($student->user_id) {
            $this->showToastr('error', 'Siswa sudah memiliki akun user');
            return;
        }

        // Get student role
        $studentRole = Role::where('name', 'Student')->orWhere('slug', 'student')->first();
        if(!$studentRole) {
            $this->showToastr('error', 'Role Student tidak ditemukan');
            return;
        }

        // Generate unique email
        $baseEmail = strtolower(str_replace(' ', '.', $student->full_name));
        $email = $baseEmail . '@student.com';

        // Check if email already exists and make it unique
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@student.com';
            $counter++;
        }

        // Create user account
        $user = User::create([
            'role_id' => $studentRole->id,
            'username' => $student->nis,
            'email' => $email,
            'password' => Hash::make('password'),
            'avatar' => 'default.png',
            'status' => 'active',
        ]);

        // Update student with user_id
        $student->update(['user_id' => $user->id]);

        $this->showToastr('success', 'Akun user berhasil dibuat untuk siswa');
    }

    #[On('register-parent')]
    public function registerParent($id): void
    {
        $parent = ParentModel::findOrFail($id)->first();

        if($parent->user_id) {
            $this->showToastr('error', 'Orang tua sudah memiliki akun user');
            return;
        }

        // Get parent role
        $parentRole = Role::where('name', 'Parent')->orWhere('slug', 'parent')->first();
        if(!$parentRole) {
            $this->showToastr('error', 'Role Parent tidak ditemukan');
            return;
        }

        // Generate unique email
        $baseEmail = strtolower(str_replace(' ', '.', $parent->full_name));
        $email = $baseEmail . '@parent.com';

        // Check if email already exists and make it unique
        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@parent.com';
            $counter++;
        }

        // Create user account
        $user = User::create([
            'role_id' => $parentRole->id,
            'username' => $parent->email,
            'email' => $parent->email,
            'password' => Hash::make('password'),
            'avatar' => 'default.png',
            'status' => 'active',
        ]);

        // Update parent with user_id
        $parent->update(['user_id' => $user->id]);

        $this->showToastr('success', 'Akun user berhasil dibuat untuk orang tua');
    }

    #[On('toggle-status')]
    public function toggleStatus($id): void
    {
        $student = Student::findOrFail($id);
        $newStatus = $student->user->status === 'active' ? 'inactive' : 'active';

        $student->user->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToastr('success', "Siswa berhasil {$statusText}");
    }

    #[On('toggle-parent-status')]
    public function toggleParentStatus($parentId): void
    {
        $parent = ParentModel::findOrFail($parentId);

        if (!$parent->user_id) {
            $this->showToastr('error', 'Orang tua belum memiliki akun user');
            return;
        }

        $newStatus = $parent->user->status === 'active' ? 'inactive' : 'active';
        $parent->user->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToastr('success', "Orang tua berhasil {$statusText}");
    }

    public function render()
    {
        $students = Student::with(['user', 'parents.user'])
            ->when($this->search, fn ($students) => $students->where('full_name', 'like', '%' . $this->search . '%')
                ->orWhere('nis', 'like', '%' . $this->search . '%')
                ->orWhere('national_student_id', 'like', '%' . $this->search . '%'))
            ->when($this->gender_filter, fn ($students) => $students->where('gender', $this->gender_filter))
            ->when($this->status_filter, fn ($students) => $students->where('status', $this->status_filter))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin-panel.students.index', compact('students'));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function deleteConfirm($method, $params = null): void
    {
        $this->dispatch('swal:confirm',
            title: 'Apakah anda yakin?',
            text: 'Data siswa dan semua data orang tua yang terkait akan dihapus!',
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
            text: 'Akun user akan dibuat untuk siswa ini dengan password default "password".',
            icon: 'question',
            confirmButtonText: 'Ya, Buat Akun!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }

    public function registerParentConfirm($method, $params = null): void
    {
        $this->dispatch('swal:confirm',
            title: 'Registrasi Akun User Orang Tua?',
            text: 'Akun user akan dibuat untuk orang tua dengan password default "password".',
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
        $student = Student::find($params);
        $action = $student->user->status === 'active' ? 'nonaktifkan' : 'aktifkan';

        $this->dispatch('swal:confirm',
            title: "Apakah anda yakin ingin {$action} siswa ini?",
            text: 'Status siswa akan berubah.',
            icon: 'question',
            confirmButtonText: 'Ya!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }

    public function toggleParentStatusConfirm($method, $params = null): void
    {
        $parent = ParentModel::find($params);
        $action = $parent->user && $parent->user->status === 'active' ? 'nonaktifkan' : 'aktifkan';

        $this->dispatch('swal:confirm',
            title: "Apakah anda yakin ingin {$action} orang tua ini?",
            text: 'Status orang tua akan berubah.',
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

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}
