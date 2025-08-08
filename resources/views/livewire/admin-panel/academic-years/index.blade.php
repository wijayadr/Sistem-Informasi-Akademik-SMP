<div class="row">
    <div class="col-lg-12">
        <div class="card" id="academicYearList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Data Tahun Akademik</h5>
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
                    <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border-0 py-3" placeholder="Pencarian tahun akademik ...">
                    <i class="ri-search-line search-icon"></i>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="academicYearTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Tahun Akademik</th>
                                    <th class="text-uppercase">Tanggal Mulai</th>
                                    <th class="text-uppercase">Tanggal Berakhir</th>
                                    <th class="text-uppercase text-center" style="width: 100px;">Status</th>
                                    <th class="text-uppercase text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="academic-year-list-data">
                                @forelse($academicYears as $key => $academicYear)
                                    <tr wire:key="{{ $academicYear->id }}">
                                        <td class="text-center">
                                            {{ $academicYears->firstItem() + $loop->index }}
                                        </td>
                                        <td>{{ $academicYear->academic_year }}</td>
                                        <td>{{ $academicYear->start_date->format('d/m/Y') }}</td>
                                        <td>{{ $academicYear->end_date->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            @if($academicYear->status == 'active')
                                                <span class="badge badge-soft-success">Aktif</span>
                                            @else
                                                <span class="badge badge-soft-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a href="javascript:void(0)" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#showModal" wire:click="edit('{{ $academicYear->id }}')">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                    <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteConfirm('delete', '{{ $academicYear->id }}')">
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

                    <x-pagination :items="$academicYears" />
                </div>
            </div>
        </div>

        {{-- Modal Tambah/Edit --}}
        <x-modal name="showModal" :title="$mode == 'add' ? 'Tambah Data Tahun Akademik' : 'Edit Data Tahun Akademik'">
            <form wire:submit.prevent="{{ $mode == 'add' ? 'save' : 'update'}}" class="tablelist-form" autocomplete="off">
                <div class="modal-body">
                    <div class="mb-3">
                        <x-input-label for="academic_year" value="Tahun Akademik" required />
                        <x-text-input wire:model="academic_year" type="text" id="academic_year" placeholder="Contoh: 2024/2025" :error="$errors->get('academic_year')" />
                        <x-input-error :messages="$errors->get('academic_year')"/>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label for="start_date" value="Tanggal Mulai" required />
                                <x-text-input wire:model="start_date" type="date" id="start_date" :error="$errors->get('start_date')" />
                                <x-input-error :messages="$errors->get('start_date')"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label for="end_date" value="Tanggal Berakhir" required />
                                <x-text-input wire:model="end_date" type="date" id="end_date" :error="$errors->get('end_date')" />
                                <x-input-error :messages="$errors->get('end_date')"/>
                            </div>
                        </div>
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
