<div class="row">
    <div class="col-lg-12">
        <div class="card" id="studentList">
            <div class="card-header border-bottom-dashed">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">Kelola Siswa</h5>
                    <div class="flex-shrink-0">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.students.create') }}" class="btn btn-info w-100 waves-effect waves-light">
                                <i class="ri-add-line align-bottom me-1"></i>
                                Tambah Siswa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 border-bottom border-bottom-dashed">
                <div class="row g-3 p-3">
                    <div class="col-lg-6">
                        <div class="search-box">
                            <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Cari nama, NIS, atau NISN...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <select wire:model.live="gender_filter" class="form-select">
                            <option value="">Semua Jenis Kelamin</option>
                            <option value="M">Laki-laki</option>
                            <option value="F">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <select wire:model.live="status_filter" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                            <option value="graduated">Lulus</option>
                            <option value="dropped_out">Keluar</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap" id="studentTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                    <th class="text-uppercase">Siswa</th>
                                    <th class="text-uppercase">NIS/NISN</th>
                                    <th class="text-uppercase">Kontak</th>
                                    <th class="text-uppercase">Orang Tua</th>
                                    <th class="text-uppercase">Tanggal Masuk</th>
                                    <th class="text-uppercase">Status</th>
                                    <th class="text-uppercase">User</th>
                                    <th class="text-uppercase" style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="list" id="student-list-data">
                                @forelse($students as $key => $row)
                                    <tr wire:key="{{ $row->id }}">
                                        <td class="text-center">
                                            {{ $students->firstItem() + $loop->index }}
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $row->full_name }}</span>
                                                <small class="text-muted">{{ $row->birth_place }}, {{ $row->birth_date?->format('d/m/Y') }}</small>
                                                <small class="text-muted">
                                                    @if($row->gender === 'M')
                                                        <i class="ri-user-3-line me-1"></i>Laki-laki
                                                    @elseif($row->gender === 'F')
                                                        <i class="ri-user-4-line me-1"></i>Perempuan
                                                    @endif
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-medium">{{ $row->nis }}</span>
                                                @if($row->national_student_id)
                                                    <small class="text-muted">NISN: {{ $row->national_student_id }}</small>
                                                @endif
                                            </div>
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
                                            <div class="d-flex flex-column">
                                                @if($row->parents->count() > 0)
                                                    @foreach($row->parents as $parent)
                                                        <div class="mb-1 p-1 border rounded" style="font-size: 0.8rem;">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <small><strong>{{ ucfirst($parent->relationship) }}:</strong> {{ $parent->full_name }}</small>
                                                                    <br>
                                                                    <small class="text-muted">{{ $parent->phone_number }}</small>
                                                                    @if($parent->user_id)
                                                                        <br>
                                                                        <span class="badge bg-success-subtle text-success" style="font-size: 0.7rem;">
                                                                            <i class="ri-user-line me-1"></i>Terdaftar
                                                                        </span>
                                                                    @else
                                                                        <br>
                                                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">
                                                                            <i class="ri-user-unfollow-line me-1"></i>Belum Terdaftar
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="font-size: 0.7rem;">
                                                                        <i class="ri-more-fill"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                                        @if(!$parent->user_id)
                                                                            <li>
                                                                                <a class="dropdown-item" href="javascript:void(0)" wire:click="registerParentConfirm('register-parent', [{{ $parent->id }}])">
                                                                                    <i class="ri-user-add-line align-bottom me-2 text-muted"></i>Buat Akun User
                                                                                </a>
                                                                            </li>
                                                                        @else
                                                                            @if($parent->user->status === 'active')
                                                                                <li>
                                                                                    <a class="dropdown-item" href="javascript:void(0)" wire:click="toggleParentStatusConfirm('toggle-parent-status', '{{ $parent->id }}')">
                                                                                        <i class="ri-pause-line align-bottom me-2 text-muted"></i>Nonaktifkan
                                                                                    </a>
                                                                                </li>
                                                                            @else
                                                                                <li>
                                                                                    <a class="dropdown-item" href="javascript:void(0)" wire:click="toggleParentStatusConfirm('toggle-parent-status', '{{ $parent->id }}')">
                                                                                        <i class="ri-play-line align-bottom me-2 text-muted"></i>Aktifkan
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        @endif
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Tidak ada data orang tua</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $row->enrollment_date?->format('d/m/Y') ?: '-' }}</td>
                                        <td>
                                            @if($row->user && $row->user->status === 'active')
                                                <span class="badge bg-success">
                                                    <i class="ri-check-line me-1"></i>Aktif
                                                </span>
                                            @elseif($row->user && $row->user->status === 'inactive')
                                                <span class="badge bg-warning">
                                                    <i class="ri-pause-line me-1"></i>Tidak Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="ri-user-unfollow-line me-1"></i>Belum Ada Akun
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->user_id)
                                                <span class="badge bg-primary">
                                                    <i class="ri-user-line me-1"></i>
                                                    Terdaftar
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="ri-user-unfollow-line me-1"></i>
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
                                                        <a class="dropdown-item" href="{{ route('admin.students.edit', $row) }}">
                                                            <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.students.parents', $row) }}">
                                                            <i class="ri-eye-fill align-bottom me-2 text-muted"></i>Detail Orang Tua
                                                        </a>
                                                    </li>
                                                    @if(!$row->user_id)
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)" wire:click="registerUserConfirm('register-user', '{{ $row->id }}')">
                                                                <i class="ri-user-add-line align-bottom me-2 text-muted"></i>Buat Akun User
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($row->user)
                                                        @if($row->user->status === 'active')
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" wire:click="toggleStatusConfirm('toggle-status', '{{ $row->id }}')">
                                                                    <i class="ri-pause-line align-bottom me-2 text-muted"></i>Nonaktifkan
                                                                </a>
                                                            </li>
                                                        @else
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)" wire:click="toggleStatusConfirm('toggle-status', '{{ $row->id }}')">
                                                                    <i class="ri-play-line align-bottom me-2 text-muted"></i>Aktifkan
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
                                        <td colspan="9" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ri-folder-open-line fs-1 text-muted mb-2"></i>
                                                <h5 class="text-muted">Tidak ada data siswa</h5>
                                                <p class="text-muted mb-0">Silakan tambah data siswa baru</p>
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
                                Menampilkan {{ $students->firstItem() ?? 0 }} sampai {{ $students->lastItem() ?? 0 }}
                                dari {{ $students->total() }} siswa
                            </p>
                        </div>
                        <div>
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
