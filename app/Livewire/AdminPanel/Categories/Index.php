<?php

namespace App\Livewire\AdminPanel\Categories;

use App\Models\Master\Category;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kategori Berita')]
class Index extends Component
{
    use WithPagination;
    public bool $showModal = false;
    public string $mode;
    public int $id;

    #[Rule('required')]
    public string $name = '';

    #[Url()]
    public string $search = '';

    public function openModal(): void
    {
        $this->showModal = true;
        $this->mode = 'add';
    }

    public function save(): void
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'slug' => str()->slug($this->name)
        ]);

        $this->showModal = false;
        $this->resetValidation();
        $this->showToastr('success', 'Data berhasil ditambahkan');
        $this->dispatch('closeModal');
    }

    public function edit($id): void
    {
        $category = Category::find($id);
        $this->id = $category->id;
        $this->name = $category->name;
        $this->showModal = true;
        $this->mode = 'edit';
    }

    public function update(): void
    {
        $this->validate();

        Category::find($this->id)->update([
            'name' => $this->name,
            'slug' => str()->slug($this->name)
        ]);

        $this->showModal = false;
        $this->resetValidation();
        $this->showToastr('success', 'Data berhasil diubah');
        $this->dispatch('closeModal');
    }

    public function cancelEdit(): void
    {
        $this->resetValidation();
    }

    #[On('delete')]
    public function delete($id): void
    {
        Category::find($id)->delete();
        $this->showToastr('success', 'Data berhasil dihapus');
    }

    public function render(): View
    {
        $categories = Category::when($this->search, fn ($category) => $category->where('name', 'like', '%' . $this->search . '%'))->paginate(10);

        return view('livewire.admin-panel.categories.index', compact('categories'));
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
}
