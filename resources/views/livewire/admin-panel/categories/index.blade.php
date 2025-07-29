<div class="row">
    <div class="col-lg-12">
        <div class="card" id="categoryList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Data Kategori Berita</h5>
                    <div class="flex-shrink-0">
                        <div class="d-flex gap-2 flex-wrap">
                            <x-button buttonType="info" wire:click.prevent="openModal" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal">
                                <i class="ri-add-line align-bottom me-1"></i>
                                Tambah
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 border-bottom border-bottom-dashed">
                <div class="search-box">
                    <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border-0 py-3" placeholder="Pencarian kategori berita ...">
                    <i class="ri-search-line search-icon"></i>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="categoryTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Kategori</th>
                                    <th class="text-uppercase" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="category-list-data">
                                @forelse($categories as $key => $category)
                                    <tr wire:key="{{ $category->id }}">
                                        <td class="text-center">
                                            {{ $categories->firstItem() + $loop->index }}
                                        </td>
                                        <td>{{ $category->name }}</td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a href="javascript:void(0)" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#showModal" wire:click="edit('{{ $category->id }}')">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                    <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteConfirm('delete', '{{ $category->id }}')">
                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-data :colspan="3" />
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-pagination :items="$categories" />
                </div>
            </div>
        </div>

        {{-- Modal Tambah --}}
        <x-modal name="showModal" :title="$mode == 'add' ? 'Tambah Data Kategori' : 'Edit Data Kategori'">
            <form wire:submit.prevent="{{ $mode == 'add' ? 'save' : 'update'}}" class="tablelist-form" autocomplete="off">
                <div class="modal-body">
                    <div class="mb-3">
                        <x-input-label for="name" value="Kategori" required />
                        <x-text-input wire:model="name" type="text" id="name" placeholder="Nama Kategori" :error="$errors->get('name')" />
                        <x-input-error :messages="$errors->get('name')"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <x-secondary-button data-bs-dismiss="modal" wire:click="cancelEdit">
                            Close
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            Simpan
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </x-modal>
    </div>
</div>
