<?php

namespace App\Livewire\Forms;

use App\Models\User\Teacher;
use App\Models\User;
use App\Models\User\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Form;

class TeacherForm extends Form
{
    public ?Teacher $teacher = null;

    #[Rule('required|string|max:255')]
    public string $employee_id = '';

    #[Rule('required|string|max:255')]
    public string $full_name = '';

    #[Rule('required|date')]
    public $birth_date = '';

    #[Rule('required|in:M,F')]
    public string $gender = '';

    #[Rule('required|string|max:20')]
    public string $phone_number = '';

    #[Rule('required|string')]
    public string $address = '';

    #[Rule('required|string|max:255')]
    public string $last_education = '';

    #[Rule('required|in:civil_servant,contract,honorary')]
    public string $employment_status = 'civil_servant';

    #[Rule('nullable|boolean')]
    public bool $create_user_account = true;

    #[Rule('nullable|string|min:8')]
    public string $password = 'password';

    public function setTeacher(Teacher $teacher): void
    {
        $this->teacher = $teacher;
        $this->employee_id = $teacher->employee_id ?? '';
        $this->full_name = $teacher->full_name ?? '';
        $this->birth_date = $teacher->birth_date?->format('Y-m-d') ?? '';
        $this->gender = $teacher->gender ?? '';
        $this->phone_number = $teacher->phone_number ?? '';
        $this->address = $teacher->address ?? '';
        $this->last_education = $teacher->last_education ?? '';
        $this->employment_status = $teacher->employment_status ?? 'civil_servant';
        $this->create_user_account = !$teacher->user_id;
        $this->password = 'password';
    }

    public function rules()
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:M,F',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'last_education' => 'required|string|max:255',
            'employment_status' => 'required|in:civil_servant,contract,honorary',
        ];

        // Dynamic unique rule for employee_id
        if ($this->teacher) {
            $rules['employee_id'] = 'required|string|max:255|unique:teachers,employee_id,' . $this->teacher->id;
        } else {
            $rules['employee_id'] = 'required|string|max:255|unique:teachers,employee_id';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'ID Pegawai wajib diisi',
            'employee_id.unique' => 'ID Pegawai sudah digunakan',
            'full_name.required' => 'Nama lengkap wajib diisi',
            'birth_date.required' => 'Tanggal lahir wajib diisi',
            'gender.required' => 'Jenis kelamin wajib dipilih',
            'phone_number.required' => 'Nomor telepon wajib diisi',
            'address.required' => 'Alamat wajib diisi',
            'last_education.required' => 'Pendidikan terakhir wajib diisi',
            'employment_status.required' => 'Status kepegawaian wajib dipilih',
        ];
    }

    public function store(): void
    {
        $this->validate();

        DB::transaction(function () {
            // Create teacher
            $teacher = Teacher::create($this->except(['teacher', 'create_user_account', 'password']));

            // Create user account if requested
            if ($this->create_user_account) {
                $this->createUserAccount($teacher);
            }
        });

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        DB::transaction(function () {
            $this->teacher->update($this->except(['teacher', 'create_user_account', 'password']));

            // Create user account if requested and doesn't exist
            if ($this->create_user_account && !$this->teacher->user_id) {
                $this->createUserAccount($this->teacher);
            }
        });

        $this->reset();
    }

    private function createUserAccount(Teacher $teacher): void
    {
        // Get teacher role
        $teacherRole = Role::where('name', 'Teacher')->orWhere('slug', 'teacher')->first();

        if (!$teacherRole) {
            throw new \Exception('Role Teacher tidak ditemukan. Pastikan role sudah ada di database.');
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
            'password' => Hash::make($this->password),
            'avatar' => 'default.png',
            'status' => 'active',
        ]);

        // Update teacher with user_id
        $teacher->update(['user_id' => $user->id]);
    }
}
