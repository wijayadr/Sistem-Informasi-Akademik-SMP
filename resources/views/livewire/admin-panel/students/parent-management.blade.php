<div>
    <div class="row">
        <div class="col-lg-12">
            <!-- Student Info Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">{{ $student->full_name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="ri-user-line me-1"></i>NIS: {{ $student->nis }}
                                @if($student->national_student_id)
                                    | NISN: {{ $student->national_student_id }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                <i class="ri-arrow-left-line me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parents Management Card -->
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            <i class="ri-parent-line me-2"></i>Kelola Orang Tua
                        </h5>
                        <div class="flex-shrink-0">
                            <button wire:click="openModal" class="btn btn-info waves-effect waves-light">
                                <i class="ri-add-line align-bottom me-1"></i>
                                Tambah Orang Tua
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($parents) > 0)
                        <div class="row g-3">
                            @foreach($parents as $parent)
                                <div class="col-lg-6" wire:key="parent-{{ $parent['id'] }}">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="card-title mb-1">{{ $parent['full_name'] }}</h6>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        {{ ucfirst($parent['relationship']) }}
                                                    </span>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-soft-secondary btn-sm dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                        <i class="ri-more-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0)"
                                                               wire:click="openModal({{ $parent['id'] }})">
                                                                <i class="ri-edit-line me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        @if(!$parent['user_id'])
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0)"
                                                                   wire:click="createUserConfirm('create-parent-user', {{ $parent['id'] }})">
                                                                    <i class="ri-user-add-line me-2"></i>Buat Akun User
                                                                </a>
                                                            </li>
                                                        @else
                                                            @if($parent['user']['status'] === 'active')
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                                       wire:click="toggleStatusConfirm('toggle-parent-user-status', {{ $parent['id'] }})">
                                                                        <i class="ri-pause-line me-2"></i>Nonaktifkan Akun
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                                       wire:click="toggleStatusConfirm('toggle-parent-user-status', {{ $parent['id'] }})">
                                                                        <i class="ri-play-line me-2"></i>Aktifkan Akun
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                               wire:click="deleteConfirm('delete-parent', {{ $parent['id'] }})">
                                                                <i class="ri-delete-bin-line me-2"></i>Hapus
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <small class="text-muted d-block">
                                                    <i class="ri-phone-line me-1"></i>{{ $parent['phone_number'] }}
                                                </small>
                                                @if($parent['email'])
                                                    <small class="text-muted d-block">
                                                        <i class="ri-mail-line me-1"></i>{{ $parent['email'] }}
                                                    </small>
                                                @endif
                                                @if($parent['occupation'])
                                                    <small class="text-muted d-block">
                                                        <i class="ri-briefcase-line me-1"></i>{{ $parent['occupation'] }}
                                                    </small>
                                                @endif
                                                @if($parent['address'])
                                                    <small class="text-muted d-block">
                                                        <i class="ri-map-pin-line me-1"></i>{{ Str::limit($parent['address'], 50) }}
                                                    </small>
                                                @endif
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if($parent['user_id'])
                                                        @if($parent['user']['status'] === 'active')
                                                            <span class="badge bg-success">
                                                                <i class="ri-user-line me-1"></i>Akun Aktif
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="ri-pause-line me-1"></i>Akun Tidak Aktif
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="ri-user-unfollow-line me-1"></i>Belum Ada Akun
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ri-parent-line fs-1 text-muted mb-2"></i>
                                <h5 class="text-muted">Belum ada data orang tua</h5>
                                <p class="text-muted mb-3">Silakan tambah data orang tua untuk siswa ini</p>
                                <button wire:click="openModal" class="btn btn-info">
                                    <i class="ri-add-line me-1"></i>Tambah Orang Tua
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal show d-block" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingParent ? 'Edit Data Orang Tua' : 'Tambah Data Orang Tua' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label for="full_name" class="form-label">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" wire:model="full_name"
                                           class="form-control @error('full_name') is-invalid @enderror"
                                           id="full_name" placeholder="Nama lengkap orang tua">
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="relationship" class="form-label">
                                        Hubungan <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="relationship"
                                            class="form-select @error('relationship') is-invalid @enderror"
                                            id="relationship">
                                        <option value="">-- Pilih Hubungan --</option>
                                        <option value="father">Ayah</option>
                                        <option value="mother">Ibu</option>
                                        <option value="guardian">Wali</option>
                                    </select>
                                    @error('relationship')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="phone_number" class="form-label">
                                        Nomor Telepon <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" wire:model="phone_number"
                                           class="form-control @error('phone_number') is-invalid @enderror"
                                           id="phone_number" placeholder="Nomor telepon">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" wire:model="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email" placeholder="Email (opsional)">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-12">
                                    <label for="occupation" class="form-label">Pekerjaan</label>
                                    <input type="text" wire:model="occupation"
                                           class="form-control @error('occupation') is-invalid @enderror"
                                           id="occupation" placeholder="Pekerjaan (opsional)">
                                    @error('occupation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-12">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea wire:model="address"
                                              class="form-control @error('address') is-invalid @enderror"
                                              id="address" rows="3" placeholder="Alamat lengkap (opsional)"></textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(!$editingParent)
                                    <div class="col-lg-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="create_user_account" wire:model="create_user_account">
                                            <label class="form-check-label" for="create_user_account">
                                                <strong>Buat akun user untuk orang tua ini</strong>
                                            </label>
                                            <div class="form-text">
                                                Username akan menggunakan nomor telepon dan password default "password"
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri-save-line me-1"></i>
                                    {{ $editingParent ? 'Perbarui' : 'Simpan' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <div class="spinner-border spinner-border-sm me-1" role="status"></div>
                                    {{ $editingParent ? 'Memperbarui...' : 'Menyimpan...' }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
