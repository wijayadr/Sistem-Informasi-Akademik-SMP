<?php

namespace App\Livewire\AdminPanel\Auth;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Rule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportRedirects\Redirector;

#[Title('Home')]
#[Layout('layouts.login')]
class Login extends Component
{
    #[Rule('required')]
    public string $username = '';

    #[Rule('required')]
    public string $password = '';

    public bool $remember = false;

    public function login(): Redirector
    {
        $validated = $this->validate();

        if ($validated) {
            $user = User::where('username', $this->username)->first();

            if (!$user || !Hash::check($this->password, $user->password)) {
                throw ValidationException::withMessages([
                    'username' => __('auth.failed'),
                ]);
            } else {
                $this->remember ? Auth::attempt($this->only('username', 'password'), true) : Auth::attempt($this->only('username', 'password'));

                return redirect()->route('admin.dashboard');
            }
        }
    }

    public function render(): View
    {
        return view('livewire.admin-panel.auth.login');
    }
}
