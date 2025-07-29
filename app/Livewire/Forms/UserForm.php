<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Attributes\Rule;
use Livewire\Form;
use Illuminate\Support\Str;

class UserForm extends Form
{
    public ?User $user = null;

    #[Rule(['required', 'string', 'max:255'])]
    public string $username = '';

    #[Rule(['required', 'string', 'max:255'])]
    public string $name = '';

    #[Rule(['required', 'string', 'email', 'max:255'])]
    public string $email = '';

    #[Rule(['nullable'])]
    public $avatar = '';

    #[Rule(['nullable'])]
    public $phone_number = '';

    #[Rule(['nullable', 'string', 'max:255'])]
    public string $password = '';

    #[Rule(['required'])]
    public $role_id = '';

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->role_id = $user->role_id;
        $this->username = $user->username;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->avatar = $user->avatar;
        $this->phone_number = $user->phone_number;
    }

    public function store(): void
    {
        $this->validate();

        $this->avatar = $this->avatar ? $this->handleUploadedImage($this->avatar, $this->name) : 'default.jpg';

        User::create($this->except('user'));

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->avatar = $this->avatar !== $this->user->avatar ? $this->handleUploadedImage($this->avatar, $this->name) : $this->user->avatar;
        $this->password = $this->password ? bcrypt($this->password) : $this->user->password;

        if ($this->avatar !== $this->user->avatar || $this->avatar != 'default.jpg') {
            $destinationPath = public_path('/storage/images/users/');
            if (file_exists($destinationPath . $this->user->avatar)) {
                unlink($destinationPath . $this->user->avatar);
            }
        }

        $this->user->update($this->except('user'));

        $this->reset();
    }

    public function handleUploadedImage($image, $name): string
    {
        if ($image) {
            $image = $image;
            $imageName = time() . '-' . Str::slug($name) . '.' . $image->getClientOriginalExtension();

            $image->storeAs('images/users', $imageName, 'public');
            return $imageName;
        }
    }
}
