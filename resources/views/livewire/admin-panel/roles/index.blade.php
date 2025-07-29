<div class="row">
    <div class="col-lg-12">
        <div class="card" id="roleList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Data Role</h5>
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
                    <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border-0 py-3" placeholder="Pencarian role ...">
                    <i class="ri-search-line search-icon"></i>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="roleTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Role</th>
                                    <th class="text-uppercase" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="role-list-data">
                                @forelse($roles as $key => $role)
                                    <tr wire:key="{{ $role->id }}">
                                        <td class="text-center">
                                            {{ $roles->firstItem() + $loop->index }}
                                        </td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a href="javascript:void(0)" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#showModal" wire:click="edit('{{ $role->id }}')">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                    <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteConfirm('delete', '{{ $role->id }}')">
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

                    <x-pagination :items="$roles" />
                </div>
            </div>
        </div>

        {{-- Modal Tambah --}}
        <x-modal name="showModal" :title="$mode == 'add' ? 'Tambah Data Role' : 'Edit Data Role'">
            <form wire:submit.prevent="{{ $mode == 'add' ? 'save' : 'update'}}" class="tablelist-form" autocomplete="off">
                <div class="modal-body">
                    <div class="mb-3">
                        <x-input-label for="name" value="Role" required />
                        <x-text-input wire:model="name" type="text" id="name" placeholder="Nama Role" :error="$errors->get('name')" />
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
