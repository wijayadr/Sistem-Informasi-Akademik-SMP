<?php

namespace App\Livewire\AdminPanel\Students;

use App\Models\User\Student;
use App\Models\User\ParentModel;
use App\Models\User;
use App\Models\User\Role;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Hash;

#[Title('Kelola Orang Tua')]
class ParentManagement extends Component
{
    public Student $student;
    public $parents = [];
    public $showModal = false;
    public $editingParent = null;

    // Form fields
    public $full_name = '';
    public $relationship = '';
    public $phone_number = '';
    public $email = '';
    public $address = '';
    public $occupation = '';
    public $create_user_account = false;

    protected $rules = [
        'full_name' => 'required|string|max:255',
        'relationship' => 'required|in:father,mother,guardian',
        'phone_number' => 'required|string|max:20',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'occupation' => 'nullable|string|max:255',
    ];

    public function mount(Student $student): void
    {
        $this->student = $student;
        $this->loadParents();
    }

    public function loadParents(): void
    {
        $this->parents = $this->student->parents()->with('user')->get()->toArray();
    }

    public function openModal($parentId = null): void
    {
        $this->resetForm();

        if ($parentId) {
            $parent = ParentModel::find($parentId);
            if ($parent) {
                $this->editingParent = $parent->id;
                $this->full_name = $parent->full_name;
                $this->relationship = $parent->relationship;
                $this->phone_number = $parent->phone_number;
                $this->email = $parent->email ?? '';
                $this->address = $parent->address ?? '';
                $this->occupation = $parent->occupation ?? '';
            }
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingParent = null;
        $this->full_name = '';
        $this->relationship = '';
        $this->phone_number = '';
        $this->email = '';
        $this->address = '';
        $this->occupation = '';
        $this->create_user_account = false;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->editingParent) {
                // Update existing parent
                $parent = ParentModel::find($this->editingParent);
                $parent->update([
                    'full_name' => $this->full_name,
                    'relationship' => $this->relationship,
                    'phone_number' => $this->phone_number,
                    'email' => $this->email ?: null,
                    'address' => $this->address ?: null,
                    'occupation' => $this->occupation ?: null,
                ]);

                $this->showToastr('success', 'Data orang tua berhasil diperbarui');
            } else {
                // Create new parent
                $parent = ParentModel::create([
                    'student_id' => $this->student->id,
                    'full_name' => $this->full_name,
                    'relationship' => $this->relationship,
                    'phone_number' => $this->phone_number,
                    'email' => $this->email ?: null,
                    'address' => $this->address ?: null,
                    'occupation' => $this->occupation ?: null,
                ]);

                // Create user account if requested
                if ($this->create_user_account) {
                    $this->createParentUserAccount($parent);
                }

                $this->showToastr('success', 'Data orang tua berhasil ditambahkan');
            }

            $this->loadParents();
            $this->closeModal();
        } catch (\Exception $e) {
            $this->showToastr('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    #[On('delete-parent')]
    public function deleteParent($parentId): void
    {
        try {
            $parent = ParentModel::findOrFail($parentId);

            // Delete user account if exists
            if ($parent->user_id) {
                User::find($parent->user_id)?->delete();
            }

            $parent->delete();

            $this->loadParents();
            $this->showToastr('success', 'Data orang tua berhasil dihapus');
        } catch (\Exception $e) {
            $this->showToastr('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    #[On('create-parent-user')]
    public function createParentUser($parentId): void
    {
        try {
            $parent = ParentModel::findOrFail($parentId);

            if ($parent->user_id) {
                $this->showToastr('error', 'Orang tua sudah memiliki akun user');
                return;
            }

            $this->createParentUserAccount($parent);

            $this->loadParents();
            $this->showToastr('success', 'Akun user berhasil dibuat untuk orang tua');
        } catch (\Exception $e) {
            $this->showToastr('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    #[On('toggle-parent-user-status')]
    public function toggleParentUserStatus($parentId): void
    {
        try {
            $parent = ParentModel::findOrFail($parentId);

            if (!$parent->user_id) {
                $this->showToastr('error', 'Orang tua belum memiliki akun user');
                return;
            }

            $newStatus = $parent->user->status === 'active' ? 'inactive' : 'active';
            $parent->user->update(['status' => $newStatus]);

            $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
            $this->loadParents();
            $this->showToastr('success', "Akun orang tua berhasil {$statusText}");
        } catch (\Exception $e) {
            $this->showToastr('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function createParentUserAccount(ParentModel $parent): void
    {
        $parentRole = Role::where('name', 'Parent')->orWhere('slug', 'parent')->first();
        if (!$parentRole) {
            throw new \Exception('Role Parent tidak ditemukan');
        }

        // Generate unique email
        $baseEmail = strtolower(str_replace(' ', '.', $parent->full_name));
        $email = $baseEmail . '@parent.com';

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@parent.com';
            $counter++;
        }

        $user = User::create([
            'role_id' => $parentRole->id,
            'username' => $parent->phone_number,
            'email' => $email,
            'password' => Hash::make('password'),
            'avatar' => 'default.png',
            'status' => 'active',
        ]);

        $parent->update(['user_id' => $user->id]);
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function deleteConfirm($method, $params = null): void
    {
        $this->dispatch('swal:confirm',
            title: 'Apakah anda yakin?',
            text: 'Data orang tua dan akun user terkait akan dihapus!',
            icon: 'warning',
            confirmButtonText: 'Hapus!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }

    public function createUserConfirm($method, $params = null): void
    {
        $this->dispatch('swal:confirm',
            title: 'Buat Akun User?',
            text: 'Akun user akan dibuat untuk orang tua dengan password default "password".',
            icon: 'question',
            confirmButtonText: 'Ya, Buat!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }

    public function toggleStatusConfirm($method, $params = null): void
    {
        $parent = ParentModel::find($params);
        $action = $parent->user && $parent->user->status === 'active' ? 'nonaktifkan' : 'aktifkan';

        $this->dispatch('swal:confirm',
            title: "Apakah anda yakin ingin {$action} akun ini?",
            text: 'Status akun orang tua akan berubah.',
            icon: 'question',
            confirmButtonText: 'Ya!',
            cancelButtonText: 'Batal',
            method: $method,
            params: $params,
            callback: ''
        );
    }

    public function render()
    {
        return view('livewire.admin-panel.students.parent-management');
    }
}
