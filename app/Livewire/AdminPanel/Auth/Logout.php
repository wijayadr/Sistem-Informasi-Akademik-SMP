<?php

namespace App\Livewire\AdminPanel\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class Logout extends Component
{
    public function render(): View
    {
        return view('livewire.admin-panel.auth.logout');
    }

    public function logout(): Redirector
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
