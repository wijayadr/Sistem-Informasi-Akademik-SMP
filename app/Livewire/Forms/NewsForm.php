<?php

namespace App\Livewire\Forms;

use App\Models\Master\News;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Str;

class NewsForm extends Form
{
    public ?News $news = null;

    #[Rule('required')]
    public $category_id = '';

    #[Rule('required')]
    public string $title = '';

    public string $slug = '';

    #[Rule('required')]
    public string $content = '';

    #[Rule('required')]
    public $thumbnail = '';

    public int $created_by = 0;

    public function setNews(News $news): void
    {
        $this->news = $news;
        $this->category_id = $news->category_id;
        $this->title = $news->title;
        $this->slug = $news->slug;
        $this->content = $news->content;
        $this->thumbnail = $news->thumbnail;
        $this->created_by = $news->created_by;
    }

    public function store(): void
    {
        $this->validate();

        $this->thumbnail = $this->thumbnail ? $this->handleUploadedImage($this->thumbnail, $this->title) : 'default.jpg';
        $this->slug = Str::slug($this->title);
        $this->created_by = auth()->user()->id;

        News::create($this->except('news'));

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->thumbnail = $this->thumbnail !== $this->news->thumbnail ? $this->handleUploadedImage($this->thumbnail, $this->title) : $this->news->thumbnail;
        $this->slug = Str::slug($this->title);

        $this->news->update($this->except('news'));

        $this->reset();
    }

    public function handleUploadedImage($image, $name): string
    {
        if ($image) {
            $image = $image;
            $imageName = time() . '-' . Str::slug($name) . '.' . $image->getClientOriginalExtension();

            $image->storeAs('images/news', $imageName, 'public');
            return $imageName;
        }
    }
}
