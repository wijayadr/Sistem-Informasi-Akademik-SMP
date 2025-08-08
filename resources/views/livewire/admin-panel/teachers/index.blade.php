<div class="row">
    <div class="col-lg-12">
        <div class="card" id="teacherList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Kelola Guru</h5>
                    <div class="flex-shrink-0">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.teachers.create') }}" class="btn btn-info w-100 waves-effect waves-light">
                                <i class="ri-add-line align-bottom me-1"></i>
                                Tambah Guru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 border-bottom border-bottom-dashed">
                <div class="row g-3 p-3">
                    <div class="col-lg-5">
                        <div class="search-box">
                            <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Cari nama atau ID pegawai...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <select wire:model.live="gender_filter" class="form-select">
                            <option value="">Semua Jenis Kelamin</option>
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <select wire:model.live="employment_status_filter" class="form-select">
                            <option value="">Semua Status Kepegawaian</option>
                            <option value="civil_servant">Tetap</option>
                            <option value="contract">Kontrak</option>
                            <option value="honorary">Honorer</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="teacherTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Guru</th>
                                    <th class="text-uppercase">ID Pegawai</th>
                                    <th class="text-uppercase">Kontak</th>
                                    <th class="text-uppercase">Pendidikan</th>
                                    <th class="text-uppercase">Status Kepegawaian</th>
                                    <th class="text-uppercase">User</th>
                                    <th class="text-uppercase" style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="teacher-list-data">
                                @forelse($teachers as $key => $row)
                                    <tr wire:key="{{ $row->id }}">
                                        <td class="text-center">
                                            {{ $teachers->firstItem() + $loop->index }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $row->full_name }}</span>
                                                <small class="text-muted">{{ $row->birth_date?->format('d/m/Y') }}</small>
                                                <small class="text-muted">
                                                    @if($row->gender === 'male')
                                                        <i class="ri-user-3-line me-1"></i>Laki-laki
                                                    @elseif($row->gender === 'female')
                                                        <i class="ri-user-4-line me-1"></i>Perempuan
                                                    @endif
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-primary">{{ $row->employee_id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span><i class="ri-phone-line me-1"></i>{{ $row->phone_number }}</span>
                                                <small class="text-muted text-truncate" style="max-width: 150px;" title="{{ $row->address }}">
                                                    <i class="ri-map-pin-line me-1"></i>{{ $row->address }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                <i class="ri-graduation-cap-line me-1"></i>{{ $row->last_education }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($row->employment_status === 'civil_servant')
                                                <span class="badge bg-success">
                                                    <i class="ri-shield-check-line me-1"></i>Tetap
                                                </span>
                                            @elseif($row->employment_status === 'contract')
                                                <span class="badge bg-warning">
                                                    <i class="ri-file-text-line me-1"></i>Kontrak
                                                </span>
                                            @elseif($row->employment_status === 'honorary')
                                                <span class="badge bg-info">
                                                    <i class="ri-time-line me-1"></i>Honorer
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->user_id)
                                                @if($row->user->status === 'active')
                                                    <span class="badge bg-primary">
                                                        <i class="ri-user-line me-1"></i>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="ri-user-unfollow-line me-1"></i>
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="ri-user-add-line me-1"></i>
                                                    Belum Terdaftar
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-soft-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-fill align-middle"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.teachers.edit', $row) }}">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>Edit
                                                        </a>
                                                    </li>
                                                    @if(!$row->user_id)
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" wire:click="registerUserConfirm('register-user', '{{ $row->id }}')">
                                                                <i class="ri-user-add-line align-bottom me-2 text-muted"></i>Buat Akun User
                                                            </a>
                                                        </li>
                                                    @else
                                                        @if($row->user->status === 'active')
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" wire:click="toggleStatusConfirm('toggle-status', '{{ $row->id }}')">
                                                                    <i class="ri-pause-line align-bottom me-2 text-muted"></i>Nonaktifkan Akun
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" wire:click="toggleStatusConfirm('toggle-status', '{{ $row->id }}')">
                                                                    <i class="ri-play-line align-bottom me-2 text-muted"></i>Aktifkan Akun
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0)" wire:click="deleteConfirm('delete', '{{ $row->id }}')">
                                                            <i class="ri-delete-bin-fill align-bottom me-2"></i>Hapus
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ri-folder-open-line fs-1 text-muted mb-2"></i>
                                                <h5 class="text-muted">Tidak ada data guru</h5>
                                                <p class="text-muted mb-0">Silakan tambah data guru baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">
                                Menampilkan {{ $teachers->firstItem() ?? 0 }} sampai {{ $teachers->lastItem() ?? 0 }}
                                dari {{ $teachers->total() }} guru
                            </p>
                        </div>
                        <div>
                            {{ $teachers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
