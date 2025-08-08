<div class="row">
    <div class="col-lg-12">
        <div class="card" id="subjectList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Data Mata Pelajaran</h5>
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
                    <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border-0 py-3" placeholder="Pencarian mata pelajaran ...">
                    <i class="ri-search-line search-icon"></i>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="subjectTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Kode</th>
                                    <th class="text-uppercase">Nama Mata Pelajaran</th>
                                    <th class="text-uppercase text-center" style="width: 100px;">Status</th>
                                    <th class="text-uppercase text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="subject-list-data">
                                @forelse($subjects as $key => $subject)
                                    <tr wire:key="{{ $subject->id }}">
                                        <td class="text-center">
                                            {{ $subjects->firstItem() + $loop->index }}
                                        </td>
                                        <td>{{ $subject->subject_code }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $subject->subject_name }}</span>
                                                @if($subject->description)
                                                    <small class="text-muted">{{ Str::limit($subject->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($subject->status == 'active')
                                                <span class="badge badge-soft-success">Aktif</span>
                                            @else
                                                <span class="badge badge-soft-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a href="javascript:void(0)" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#showModal" wire:click="edit('{{ $subject->id }}')">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                    <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteConfirm('delete', '{{ $subject->id }}')">
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

                    <x-pagination :items="$subjects" />
                </div>
            </div>
        </div>

        {{-- Modal Tambah/Edit --}}
        <x-modal name="showModal" :title="$mode == 'add' ? 'Tambah Data Mata Pelajaran' : 'Edit Data Mata Pelajaran'">
            <form wire:submit.prevent="{{ $mode == 'add' ? 'save' : 'update'}}" class="tablelist-form" autocomplete="off">
                <div class="modal-body">
                    <div class="mb-3">
                        <x-input-label for="subject_code" value="Kode Mata Pelajaran" required />
                        <x-text-input wire:model="subject_code" type="text" id="subject_code" placeholder="Contoh: MTK001" :error="$errors->get('subject_code')" />
                        <x-input-error :messages="$errors->get('subject_code')"/>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="subject_name" value="Nama Mata Pelajaran" required />
                        <x-text-input wire:model="subject_name" type="text" id="subject_name" placeholder="Nama Mata Pelajaran" :error="$errors->get('subject_name')" />
                        <x-input-error :messages="$errors->get('subject_name')"/>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea wire:model="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsi Mata Pelajaran"></textarea>
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
                            {{ $mode == 'add' ? 'Simpan' : 'Update' }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </x-modal>
    </div>
</div>
