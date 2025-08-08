<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $editing ? 'Edit Data Guru' : 'Tambah Data Guru' }}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="{{ $editing ? 'edit' : 'save' }}" class="tablelist-form" autocomplete="off">

                        <!-- Data Pribadi -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="ri-user-line me-2"></i>Data Pribadi
                            </h6>
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <x-input-label for="employee_id" value="ID Pegawai" required/>
                                    <x-text-input wire:model="form.employee_id" type="text" id="employee_id"
                                        placeholder="Masukkan ID pegawai (contoh: GR001)"
                                        :error="$errors->get('form.employee_id')" />
                                    <small class="text-muted">ID Pegawai akan digunakan sebagai username untuk login</small>
                                    <x-input-error :messages="$errors->get('form.employee_id')"/>
                                </div>

                                <div class="col-lg-6">
                                    <x-input-label for="full_name" value="Nama Lengkap" required/>
                                    <x-text-input wire:model="form.full_name" type="text" id="full_name"
                                        placeholder="Nama lengkap guru"
                                        :error="$errors->get('form.full_name')" />
                                    <x-input-error :messages="$errors->get('form.full_name')"/>
                                </div>

                                <div class="col-lg-6">
                                    <x-input-label for="birth_date" value="Tanggal Lahir" required/>
                                    <x-text-input wire:model="form.birth_date" type="date" id="birth_date"
                                        :error="$errors->get('form.birth_date')" />
                                    <x-input-error :messages="$errors->get('form.birth_date')"/>
                                </div>

                                <div class="col-lg-6">
                                    <x-input-label for="gender" value="Jenis Kelamin" required/>
                                    <select wire:model="form.gender" id="gender"
                                        class="form-select {{ $errors->get('form.gender') ? 'is-invalid' : '' }}">
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="M">Laki-laki</option>
                                        <option value="F">Perempuan</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('form.gender')"/>
                                </div>

                                <div class="col-lg-6">
                                    <x-input-label for="phone_number" value="Nomor Telepon" required/>
                                    <x-text-input wire:model="form.phone_number" type="text" id="phone_number"
                                        placeholder="Nomor telepon guru"
                                        :error="$errors->get('form.phone_number')" />
                                    <x-input-error :messages="$errors->get('form.phone_number')"/>
                                </div>

                                <div class="col-lg-12">
                                    <x-input-label for="address" value="Alamat" required/>
                                    <x-textarea wire:model="form.address" id="address"
                                        placeholder="Alamat lengkap guru"
                                        :error="$errors->get('form.address')" rows="3" />
                                    <x-input-error :messages="$errors->get('form.address')"/>
                                </div>
                            </div>
                        </div>

                        <!-- Data Profesional -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="ri-briefcase-line me-2"></i>Data Profesional
                            </h6>
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <x-input-label for="last_education" value="Pendidikan Terakhir" required/>
                                    <x-text-input wire:model="form.last_education" type="text" id="last_education"
                                        placeholder="Contoh: S1 Pendidikan Matematika"
                                        :error="$errors->get('form.last_education')" />
                                    <x-input-error :messages="$errors->get('form.last_education')"/>
                                </div>

                                <div class="col-lg-6">
                                    <x-input-label for="employment_status" value="Status Kepegawaian" required/>
                                    <select wire:model="form.employment_status" id="employment_status"
                                        class="form-select {{ $errors->get('form.employment_status') ? 'is-invalid' : '' }}">
                                        <option value="">-- Pilih Status Kepegawaian --</option>
                                        <option value="civil_servant">Pegawai Tetap</option>
                                        <option value="contract">Pegawai Kontrak</option>
                                        <option value="honorary">Pegawai Honorer</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('form.employment_status')"/>
                                </div>
                            </div>
                        </div>

                        <!-- Data Akun User -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="ri-user-settings-line me-2"></i>Pengaturan Akun User
                            </h6>
                            <div class="row g-3">
                                @if(!$editing || !$teacher->user_id)
                                    <div class="col-lg-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="create_user_account"
                                                wire:model.live="form.create_user_account">
                                            <label class="form-check-label" for="create_user_account">
                                                <strong>Buat akun user untuk guru ini</strong>
                                            </label>
                                            <div class="form-text">
                                                Jika diaktifkan, sistem akan otomatis membuat akun user untuk guru dengan username berupa ID Pegawai
                                            </div>
                                        </div>
                                    </div>

                                    @if($form->create_user_account)
                                        <div class="col-lg-12">
                                            <div class="alert alert-info">
                                                <h6 class="alert-heading">
                                                    <i class="ri-information-line me-2"></i>Informasi Akun User
                                                </h6>
                                                <div class="mb-2">
                                                    <strong>Username:</strong>
                                                    <span class="text-primary">{{ $form->employee_id ?: '(akan menggunakan ID Pegawai)' }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Email:</strong>
                                                    <span class="text-primary">
                                                        {{ $form->full_name ? strtolower(str_replace(' ', '.', $form->full_name)) . '@teacher.com' : '(akan dibuat otomatis)' }}
                                                    </span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Role:</strong>
                                                    <span class="text-primary">Teacher</span>
                                                </div>
                                                <div class="mb-0">
                                                    <strong>Password:</strong>
                                                    <span class="text-primary">{{ $form->password }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <x-input-label for="password" value="Password Default" />
                                            <x-text-input wire:model="form.password" type="text" id="password"
                                                placeholder="Password default untuk akun user"
                                                :error="$errors->get('form.password')" />
                                            <small class="text-muted">Password default yang akan digunakan guru untuk login pertama kali</small>
                                            <x-input-error :messages="$errors->get('form.password')"/>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-lg-12">
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading">
                                                <i class="ri-check-line me-2"></i>Akun User Sudah Tersedia
                                            </h6>
                                            <div class="mb-2">
                                                <strong>Username:</strong> <span class="text-success">{{ $teacher->user->username ?? '-' }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>Email:</strong> <span class="text-success">{{ $teacher->user->email ?? '-' }}</span>
                                            </div>
                                            <div class="mb-0">
                                                <strong>Status:</strong>
                                                @if($teacher->user->status === 'active')
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary me-2">
                                        <i class="ri-arrow-left-line me-1"></i>Kembali
                                    </a>
                                    <x-primary-button type="submit" wire:loading.attr="disabled">
                                        <div wire:loading wire:target="{{ $editing ? 'edit' : 'save' }}" class="spinner-border spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <i class="ri-save-line me-1" wire:loading.remove wire:target="{{ $editing ? 'edit' : 'save' }}"></i>
                                        <span wire:loading.remove wire:target="{{ $editing ? 'edit' : 'save' }}">
                                            {{ $editing ? 'Perbarui' : 'Simpan' }}
                                        </span>
                                        <span wire:loading wire:target="{{ $editing ? 'edit' : 'save' }}">
                                            {{ $editing ? 'Memperbarui...' : 'Menyimpan...' }}
                                        </span>
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
