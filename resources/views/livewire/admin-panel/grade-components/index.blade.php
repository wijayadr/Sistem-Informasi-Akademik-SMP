<div class="row">
    <div class="col-lg-12">
        <div class="card" id="gradeComponentList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Data Komponen Nilai</h5>
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
                    <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border-0 py-3" placeholder="Pencarian komponen nilai ...">
                    <i class="ri-search-line search-icon"></i>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="gradeComponentTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Nama Komponen</th>
                                    <th class="text-uppercase">Bobot (%)</th>
                                    <th class="text-uppercase">Deskripsi</th>
                                    <th class="text-uppercase text-center" style="width: 100px;">Status</th>
                                    <th class="text-uppercase text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="grade-component-list-data">
                                @forelse($gradeComponents as $key => $gradeComponent)
                                    <tr wire:key="{{ $gradeComponent->id }}">
                                        <td class="text-center">
                                            {{ $gradeComponents->firstItem() + $loop->index }}
                                        </td>
                                        <td>{{ $gradeComponent->component_name }}</td>
                                        <td>{{ $gradeComponent->weight_percentage }}%</td>
                                        <td>{{ $gradeComponent->description ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($gradeComponent->status == 'active')
                                                <span class="badge badge-soft-success">Aktif</span>
                                            @else
                                                <span class="badge badge-soft-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a href="javascript:void(0)" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#showModal" wire:click="edit('{{ $gradeComponent->id }}')">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                    <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteConfirm('delete', '{{ $gradeComponent->id }}')">
                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-data :colspan="6" />
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-pagination :items="$gradeComponents" />
                </div>
            </div>
        </div>

        {{-- Modal Tambah/Edit --}}
        <x-modal name="showModal" :title="$mode == 'add' ? 'Tambah Data Komponen Nilai' : 'Edit Data Komponen Nilai'">
            <form wire:submit.prevent="{{ $mode == 'add' ? 'save' : 'update'}}" class="tablelist-form" autocomplete="off">
                <div class="modal-body">
                    <div class="mb-3">
                        <x-input-label for="component_name" value="Nama Komponen" required />
                        <x-text-input wire:model="component_name" type="text" id="component_name" placeholder="Contoh: UTS, UAS, Tugas, Quiz" :error="$errors->get('component_name')" />
                        <x-input-error :messages="$errors->get('component_name')"/>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="weight_percentage" value="Bobot Persentase (%)" required />
                        <x-text-input wire:model="weight_percentage" type="number" id="weight_percentage" placeholder="Contoh: 30" min="0" max="100" step="0.01" :error="$errors->get('weight_percentage')" />
                        <x-input-error :messages="$errors->get('weight_percentage')"/>
                        <small class="text-muted">Nilai antara 0-100</small>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="3" placeholder="Deskripsi komponen nilai (opsional)"></textarea>
                        <x-input-error :messages="$errors->get('description')"/>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="status" value="Status" required />
                        <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="">Pilih Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')"/>
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
