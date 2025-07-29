<?php

namespace App\Livewire\AdminPanel\Dashboard;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Index extends Component
{
    public function render(): View
    {
        return view('livewire.admin-panel.dashboard.index');
    }
}
