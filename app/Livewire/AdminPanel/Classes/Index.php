<?php

namespace App\Livewire\AdminPanel\Classes;

use App\Models\Academic\Classes;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Data Kelas')]
class Index extends Component
{
    use WithPagination;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $status_filter = '';

    #[Url()]
    public string $grade_filter = '';

    #[On('delete')]
    public function delete($id): void
    {
        Classes::findOrFail($id)->delete();
        $this->showToastr('success', 'Data berhasil dihapus');
    }

    public function render()
    {
        $classes = Classes::with(['academicYear', 'homeroomTeacher'])
            ->when($this->search, fn ($query) => $query->where('class_name', 'like', '%' . $this->search . '%')
                ->orWhereHas('homeroomTeacher', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->orWhereHas('academicYear', fn($q) => $q->where('academic_year', 'like', '%' . $this->search . '%')))
            ->when($this->status_filter, fn ($query) => $query->where('status', $this->status_filter))
            ->when($this->grade_filter, fn ($query) => $query->where('grade_level', $this->grade_filter))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin-panel.classes.index', compact('classes'));
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

    public function resetFilters(): void
    {
        $this->reset(['search', 'status_filter', 'grade_filter']);
    }
}
