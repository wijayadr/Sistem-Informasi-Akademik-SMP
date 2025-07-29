<?php

namespace App\Livewire\AdminPanel\News;

use App\Livewire\Forms\NewsForm;
use App\Models\Master\Category;
use App\Models\Master\News;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class Form extends Component
{
    use WithFileUploads;
    public News $news;
    public bool $editing = false;
    public NewsForm $form;

    public array $listsForFields = [];

    public function mount(News $news): void
    {
        $this->initListsForFields();

        if($news->exists) {
            $this->editing = true;
            $this->form->setNews($news);
        }
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['categories'] = Category::pluck('name', 'id');
    }

    public function save(): RedirectResponse|Redirector
    {
        $this->form->store();

        session()->flash('success', 'Data Berita berhasil disimpan');

        return redirect()->route('admin.news.index');
    }

    public function edit(): RedirectResponse|Redirector
    {
        $this->form->update();

        session()->flash('success', 'Data Berita berhasil diubah');

        return redirect()->route('admin.news.index');
    }

    public function render(): View
    {
        $title = $this->editing ? 'Edit Data Berita' : 'Tambah Data Berita';

        return view('livewire.admin-panel.news.form')->title($title);
    }
}
