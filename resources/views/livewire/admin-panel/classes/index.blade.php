<div class="row">
    <div class="col-lg-12">
        <div class="card" id="classesList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Data Kelas</h5>
                    <div class="flex-shrink-0">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.classes.create') }}" class="btn btn-info w-100 waves-effect waves-light">
                                <i class="ri-add-line align-bottom me-1"></i>
                                Tambah
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 border-bottom border-bottom-dashed">
                <div class="row g-3 p-3">
                    <div class="col-md-5">
                        <div class="search-box">
                            <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Pencarian kelas ...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="status_filter" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select wire:model.live="grade_filter" class="form-select">
                            <option value="">Semua Tingkat</option>
                            <option value="7">Kelas 7</option>
                            <option value="8">Kelas 8</option>
                            <option value="9">Kelas 9</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="ri-refresh-line align-bottom me-1"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="classesTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Nama Kelas</th>
                                    <th class="text-uppercase">Tingkat</th>
                                    <th class="text-uppercase">Tahun Ajaran</th>
                                    <th class="text-uppercase">Wali Kelas</th>
                                    <th class="text-uppercase">Kapasitas</th>
                                    <th class="text-uppercase">Jumlah Siswa</th>
                                    <th class="text-uppercase">Status</th>
                                    <th class="text-uppercase" style="width: 250px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="classes-list-data">
                                @forelse($classes as $key => $row)
                                    <tr wire:key="{{ $row->id }}">
                                        <td class="text-center">
                                            {{ $classes->firstItem() + $loop->index }}
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $row->class_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">Kelas {{ $row->grade_level }}</span>
                                        </td>
                                        <td>{{ $row->academicYear->academic_year ?? '-' }}</td>
                                        <td>{{ $row->homeroomTeacher->full_name ?? '-' }}</td>
                                        <td>{{ $row->capacity }} siswa</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $row->getActiveStudentsCount() }}/{{ $row->capacity }}</span>
                                                <small class="text-muted">Sisa: {{ $row->getAvailableCapacity() }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($row->status == 'active')
                                                <span class="badge bg-success-subtle text-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Kelola Siswa">
                                                    <a href="{{ route('admin.classes.students', $row) }}" class="text-success d-inline-block">
                                                        <i class="ri-user-add-line fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Kelola Guru">
                                                    <a href="{{ route('admin.classes.teachers', $row) }}" class="text-info d-inline-block">
                                                        <i class="ri-user-2-line fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Kelola Jadwal">
                                                    <a href="{{ route('admin.classes.schedules', $row) }}" class="text-warning d-inline-block">
                                                        <i class="ri-calendar-line fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                    <a href="{{ route('admin.classes.edit', $row) }}" class="text-primary d-inline-block">
                                                        <i class="ri-pencil-fill fs-16"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                    <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteConfirm('delete', '{{ $row->id }}')">
                                                        <i class="ri-delete-bin-5-fill fs-16"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-data :colspan="9" />
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <x-pagination :items="$classes" />
                </div>
            </div>
        </div>
    </div>
</div>
