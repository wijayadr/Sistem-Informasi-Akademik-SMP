<?php

namespace App\Livewire\Forms;

use App\Models\User\Student;
use App\Models\User\ParentModel;
use App\Models\User;
use App\Models\User\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Form;

class StudentForm extends Form
{
    public ?Student $student = null;

    // Student fields
    #[Validate('required|string|unique:students,nis')]
    public string $nis = '';

    #[Validate('nullable|string')]
    public string $national_student_id = '';

    #[Validate('required|string|max:255')]
    public string $full_name = '';

    #[Validate('required|date')]
    public string $birth_date = '';

    #[Validate('required|in:M,F')]
    public string $gender = '';

    #[Validate('required|string|max:255')]
    public string $birth_place = '';

    #[Validate('required|string')]
    public string $address = '';

    #[Validate('required|string|max:20')]
    public string $phone_number = '';

    #[Validate('nullable|string|max:255')]
    public string $father_name = '';

    #[Validate('nullable|string|max:255')]
    public string $mother_name = '';

    #[Validate('nullable|string|max:255')]
    public string $father_occupation = '';

    #[Validate('nullable|string|max:255')]
    public string $mother_occupation = '';

    #[Validate('required|date')]
    public string $enrollment_date = '';

    // User account fields
    public bool $create_user_account = false;

    #[Validate('nullable|string|min:6')]
    public string $password = 'password';

    // Parent fields
    public array $parents = [];

    public function rules()
    {
        $rules = [
            'nis' => 'required|string|unique:students,nis',
            'national_student_id' => 'nullable|string',
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:M,F',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'enrollment_date' => 'required|date',
            'password' => 'nullable|string|min:6',
            'parents' => 'array',
            'parents.*.full_name' => 'required|string|max:255',
            'parents.*.relationship' => 'required|in:father,mother,guardian',
            'parents.*.phone_number' => 'required|string|max:20',
            'parents.*.email' => 'required|email|max:255',
            'parents.*.address' => 'nullable|string',
            'parents.*.occupation' => 'nullable|string|max:255',
            'parents.*.create_user_account' => 'boolean',
        ];

        if ($this->student?->exists) {
            $rules['nis'] = 'required|string|unique:students,nis,' . $this->student->id;
        }

        return $rules;
    }

    public function setStudent(Student $student): void
    {
        $this->student = $student;

        $this->nis = $student->nis;
        $this->national_student_id = $student->national_student_id ?? '';
        $this->full_name = $student->full_name;
        $this->birth_date = $student->birth_date?->format('Y-m-d') ?? '';
        $this->gender = $student->gender;
        $this->birth_place = $student->birth_place;
        $this->address = $student->address;
        $this->phone_number = $student->phone_number;
        $this->father_name = $student->father_name ?? '';
        $this->mother_name = $student->mother_name ?? '';
        $this->father_occupation = $student->father_occupation ?? '';
        $this->mother_occupation = $student->mother_occupation ?? '';
        $this->enrollment_date = $student->enrollment_date?->format('Y-m-d') ?? '';

        // Load existing parents
        $this->parents = $student->parents->map(function ($parent) {
            return [
                'id' => $parent->id,
                'full_name' => $parent->full_name,
                'relationship' => $parent->relationship,
                'phone_number' => $parent->phone_number,
                'email' => $parent->email ?? '',
                'address' => $parent->address ?? '',
                'occupation' => $parent->occupation ?? '',
                'create_user_account' => false,
                'has_user_account' => $parent->user_id ? true : false,
            ];
        })->toArray();
    }

    public function addParent(): void
    {
        $this->parents[] = [
            'full_name' => '',
            'relationship' => '',
            'phone_number' => '',
            'email' => '',
            'address' => '',
            'occupation' => '',
            'create_user_account' => false,
            'has_user_account' => false,
        ];
    }

    public function removeParent(int $index): void
    {
        unset($this->parents[$index]);
        $this->parents = array_values($this->parents);
    }

    public function store(): Student
    {
        $this->validate();

        $student = Student::create([
            'nis' => $this->nis,
            'national_student_id' => $this->national_student_id ?: null,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'birth_place' => $this->birth_place,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'father_name' => $this->father_name ?: null,
            'mother_name' => $this->mother_name ?: null,
            'father_occupation' => $this->father_occupation ?: null,
            'mother_occupation' => $this->mother_occupation ?: null,
            'enrollment_date' => $this->enrollment_date,
            'status' => 'active',
        ]);

        // Create user account if requested
        if ($this->create_user_account) {
            $this->createUserAccount($student);
        }

        // Create parent accounts
        $this->createParents($student);

        return $student;
    }

    public function update(): Student
    {
        $this->validate();

        $this->student->update([
            'nis' => $this->nis,
            'national_student_id' => $this->national_student_id ?: null,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'birth_place' => $this->birth_place,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'father_name' => $this->father_name ?: null,
            'mother_name' => $this->mother_name ?: null,
            'father_occupation' => $this->father_occupation ?: null,
            'mother_occupation' => $this->mother_occupation ?: null,
            'enrollment_date' => $this->enrollment_date,
        ]);

        // Update parents
        $this->updateParents($this->student);

        return $this->student;
    }

    private function createUserAccount(Student $student): void
    {
        $studentRole = Role::where('name', 'Student')->orWhere('slug', 'student')->first();
        if (!$studentRole) {
            return;
        }

        // Generate unique email
        $baseEmail = strtolower(str_replace(' ', '.', $this->full_name));
        $email = $baseEmail . '@student.com';

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . $counter . '@student.com';
            $counter++;
        }

        $user = User::create([
            'role_id' => $studentRole->id,
            'username' => $this->nis,
            'email' => $email,
            'password' => Hash::make($this->password),
            'avatar' => 'default.png',
            'status' => 'active',
        ]);

        $student->update(['user_id' => $user->id]);
    }

    private function createParents(Student $student): void
    {
        foreach ($this->parents as $parentData) {
            if (empty($parentData['full_name']) || empty($parentData['relationship'])) {
                continue;
            }

            $parent = ParentModel::create([
                'student_id' => $student->id,
                'full_name' => $parentData['full_name'],
                'relationship' => $parentData['relationship'],
                'phone_number' => $parentData['phone_number'],
                'email' => $parentData['email'] ?: null,
                'address' => $parentData['address'] ?: null,
                'occupation' => $parentData['occupation'] ?: null,
            ]);

            // Create user account if requested
            if ($parentData['create_user_account'] ?? false) {
                $this->createParentUserAccount($parent);
            }
        }
    }

    private function updateParents(Student $student): void
    {
        $existingParentIds = [];

        foreach ($this->parents as $parentData) {
            if (empty($parentData['full_name']) || empty($parentData['relationship'])) {
                continue;
            }

            if (isset($parentData['id'])) {
                // Update existing parent
                $parent = ParentModel::find($parentData['id']);
                if ($parent) {
                    $parent->update([
                        'full_name' => $parentData['full_name'],
                        'relationship' => $parentData['relationship'],
                        'phone_number' => $parentData['phone_number'],
                        'email' => $parentData['email'] ?: null,
                        'address' => $parentData['address'] ?: null,
                        'occupation' => $parentData['occupation'] ?: null,
                    ]);

                    // Create user account if requested and doesn't exist
                    if (($parentData['create_user_account'] ?? false) && !$parent->user_id) {
                        $this->createParentUserAccount($parent);
                    }

                    $existingParentIds[] = $parent->id;
                }
            } else {
                // Create new parent
                $parent = ParentModel::create([
                    'student_id' => $student->id,
                    'full_name' => $parentData['full_name'],
                    'relationship' => $parentData['relationship'],
                    'phone_number' => $parentData['phone_number'],
                    'email' => $parentData['email'] ?: null,
                    'address' => $parentData['address'] ?: null,
                    'occupation' => $parentData['occupation'] ?: null,
                ]);

                // Create user account if requested
                if ($parentData['create_user_account'] ?? false) {
                    $this->createParentUserAccount($parent);
                }

                $existingParentIds[] = $parent->id;
            }
        }

        // Delete parents that are no longer in the form
        $student->parents()->whereNotIn('id', $existingParentIds)->each(function ($parent) {
            if ($parent->user_id) {
                User::find($parent->user_id)?->delete();
            }
            $parent->delete();
        });
    }

    private function createParentUserAccount(ParentModel $parent): void
    {
        $parentRole = Role::where('name', 'Parent')->orWhere('slug', 'parent')->first();
        if (!$parentRole) {
            return;
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
            'username' => $parent->email,
            'email' => $email,
            'password' => Hash::make('password'),
            'avatar' => 'default.png',
            'status' => 'active',
        ]);

        $parent->update(['user_id' => $user->id]);
    }
}
