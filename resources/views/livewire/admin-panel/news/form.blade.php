<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editing ? 'edit' : 'save' }}" class="tablelist-form" autocomplete="off">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <x-input-label for="title" value="Judul Berita" required/>
                                <x-text-input wire:model="form.title" type="text" id="title" placeholder="Judul Berita" :error="$errors->get('form.title')" />
                                <x-input-error :messages="$errors->get('form.title')"/>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="category_id" value="Kategori" required/>
                                <x-select-list class="w-full" id="category_id" name="category_id" :options="$this->listsForFields['categories']" wire:model="form.category_id" data-placeholder="-- Pilih --"/>
                                <x-input-error :messages="$errors->get('form.category_id')"/>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="content" value="Konten" required/>
                                <x-textarea wire:model="form.content" id="content" placeholder="Konten" :error="$errors->get('form.content')" rows="3" />
                                <x-input-error :messages="$errors->get('form.content')"/>
                            </div>

                            <div class="col-lg-12">
                                <x-input-label for="thumbnail" value="Thumbnail" required/>
                                <input type="file" wire:model="form.thumbnail" class="form-control" placeholder="Thumbnail">
                                <x-input-error :messages="$errors->get('form.thumbnail')"/>
                            </div>

                            <div class="col-lg-12">
                                <div class="text-end">
                                    <x-primary-button type="submit">
                                        Simpan
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
