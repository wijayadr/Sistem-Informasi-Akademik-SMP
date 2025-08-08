<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $editing ? 'Edit Data Siswa' : 'Tambah Data Siswa' }}</h5>
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
                                    <x-input-label for="nis" value="NIS" required/>
                                    <x-text-input wire:model="form.nis" type="text" id="nis"
                                        placeholder="Masukkan NIS (contoh: 2024001)"
                                        :error="$errors->get('form.nis')" />
                                    <x-input-error :messages="$errors->get('form.nis')"/>
                                </div>

                                <div class="col-lg-6">
                                    <x-input-label for="national_student_id" value="NISN" />
                                    <x-text-input wire:model="form.national_student_id" type="text" id="national_student_id"
                                        placeholder="Nomor Induk Siswa Nasional (opsional)"
                                        :error="$errors->get('form.national_student_id')" />
                                    <x-input-error :messages="$errors->get('form.national_student_id')"/>
                                </div>

                                <div class="col-lg-12">
                                    <x-input-label for="full_name" value="Nama Lengkap" required/>
                                    <x-text-input wire:model="form.full_name" type="text" id="full_name"
                                        placeholder="Nama lengkap siswa"
                                        :error="$errors->get('form.full_name')" />
                                    <x-input-error :messages="$errors->get('form.full_name')"/>
                                </div>

                                <div class="col-lg-6">
                                    <x-input-label for="birth_place" value="Tempat Lahir" required/>
                                    <x-text-input wire:model="form.birth_place" type="text" id="birth_place"
                                        placeholder="Tempat lahir"
                                        :error="$errors->get('form.birth_place')" />
                                    <x-input-error :messages="$errors->get('form.birth_place')"/>
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
                                        placeholder="Nomor telepon siswa"
                                        :error="$errors->get('form.phone_number')" />
                                    <x-input-error :messages="$errors->get('form.phone_number')"/>
                                </div>

                                <div class="col-lg-12">
                                    <x-input-label for="address" value="Alamat" required/>
                                    <x-textarea wire:model="form.address" id="address"
                                        placeholder="Alamat lengkap siswa"
                                        :error="$errors->get('form.address')" rows="3" />
                                    <x-input-error :messages="$errors->get('form.address')"/>
                                </div>
                            </div>
                        </div>

                        <!-- Data Orang Tua -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0">
                                    <i class="ri-parent-line me-2"></i>Data Orang Tua
                                </h6>
                                <button type="button" wire:click="addParent" class="btn btn-outline-primary btn-sm">
                                    <i class="ri-add-line me-1"></i>Tambah Orang Tua
                                </button>
                            </div>

                            @foreach($form->parents as $index => $parent)
                                <div class="border rounded p-3 mb-3" wire:key="parent-{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h6 class="text-secondary mb-0">
                                            <i class="ri-user-heart-line me-1"></i>Orang Tua {{ $index + 1 }}
                                        </h6>
                                        @if(count($form->parents) > 1)
                                            <button type="button" wire:click="removeParent({{ $index }})" class="btn btn-outline-danger btn-sm">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <x-input-label for="parent_full_name_{{ $index }}" value="Nama Lengkap" required/>
                                            <x-text-input wire:model="form.parents.{{ $index }}.full_name" type="text"
                                                id="parent_full_name_{{ $index }}"
                                                placeholder="Nama lengkap orang tua"
                                                :error="$errors->get('form.parents.' . $index . '.full_name')" />
                                            <x-input-error :messages="$errors->get('form.parents.' . $index . '.full_name')"/>
                                        </div>

                                        <div class="col-lg-6">
                                            <x-input-label for="parent_relationship_{{ $index }}" value="Hubungan" required/>
                                            <select wire:model="form.parents.{{ $index }}.relationship"
                                                id="parent_relationship_{{ $index }}"
                                                class="form-select {{ $errors->get('form.parents.' . $index . '.relationship') ? 'is-invalid' : '' }}">
                                                <option value="">-- Pilih Hubungan --</option>
                                                <option value="father">Ayah</option>
                                                <option value="mother">Ibu</option>
                                                <option value="guardian">Wali</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('form.parents.' . $index . '.relationship')"/>
                                        </div>

                                        <div class="col-lg-6">
                                            <x-input-label for="parent_phone_{{ $index }}" value="Nomor Telepon" required/>
                                            <x-text-input wire:model="form.parents.{{ $index }}.phone_number" type="text"
                                                id="parent_phone_{{ $index }}"
                                                placeholder="Nomor telepon orang tua"
                                                :error="$errors->get('form.parents.' . $index . '.phone_number')" />
                                            <x-input-error :messages="$errors->get('form.parents.' . $index . '.phone_number')"/>
                                        </div>

                                        <div class="col-lg-6">
                                            <x-input-label for="parent_email_{{ $index }}" value="Email" required/>
                                            <x-text-input wire:model="form.parents.{{ $index }}.email" type="email"
                                                id="parent_email_{{ $index }}"
                                                placeholder="Email orang tua"
                                                :error="$errors->get('form.parents.' . $index . '.email')" />
                                            <small class="text-muted">Email ini akan digunakan untuk akun user orang tua jika dibuat</small><br/>
                                            <x-input-error :messages="$errors->get('form.parents.' . $index . '.email')"/>
                                        </div>

                                        <div class="col-lg-6">
                                            <x-input-label for="parent_occupation_{{ $index }}" value="Pekerjaan"/>
                                            <x-text-input wire:model="form.parents.{{ $index }}.occupation" type="text"
                                                id="parent_occupation_{{ $index }}"
                                                placeholder="Pekerjaan orang tua (opsional)"
                                                :error="$errors->get('form.parents.' . $index . '.occupation')" />
                                            <x-input-error :messages="$errors->get('form.parents.' . $index . '.occupation')"/>
                                        </div>

                                        <div class="col-lg-6">
                                            <x-input-label for="parent_address_{{ $index }}" value="Alamat"/>
                                            <x-textarea wire:model="form.parents.{{ $index }}.address"
                                                id="parent_address_{{ $index }}"
                                                placeholder="Alamat orang tua (opsional)"
                                                :error="$errors->get('form.parents.' . $index . '.address')"
                                                rows="2" />
                                            <x-input-error :messages="$errors->get('form.parents.' . $index . '.address')"/>
                                        </div>

                                        @if(!$editing || !($parent['has_user_account'] ?? false))
                                            <div class="col-lg-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="create_parent_account_{{ $index }}"
                                                        wire:model="form.parents.{{ $index }}.create_user_account">
                                                    <label class="form-check-label" for="create_parent_account_{{ $index }}">
                                                        <strong>Buat akun user untuk orang tua ini</strong>
                                                    </label>
                                                    <div class="form-text">
                                                        Jika diaktifkan, sistem akan otomatis membuat akun user untuk orang tua
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-lg-12">
                                                <div class="alert alert-info">
                                                    <i class="ri-information-line me-2"></i>
                                                    <strong>Akun User Sudah Tersedia</strong> - Orang tua ini sudah memiliki akun user yang terdaftar.
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <!-- Legacy fields for backward compatibility -->
                            <div class="mt-4">
                                <h6 class="text-secondary mb-3">
                                    <i class="ri-information-line me-2"></i>Data Tambahan (Opsional)
                                </h6>
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <x-input-label for="father_name" value="Nama Ayah (Legacy)" />
                                        <x-text-input wire:model="form.father_name" type="text" id="father_name"
                                            placeholder="Nama lengkap ayah (akan dipindahkan ke data orang tua)"
                                            :error="$errors->get('form.father_name')" />
                                        <x-input-error :messages="$errors->get('form.father_name')"/>
                                    </div>

                                    <div class="col-lg-6">
                                        <x-input-label for="father_occupation" value="Pekerjaan Ayah (Legacy)" />
                                        <x-text-input wire:model="form.father_occupation" type="text" id="father_occupation"
                                            placeholder="Pekerjaan ayah (akan dipindahkan ke data orang tua)"
                                            :error="$errors->get('form.father_occupation')" />
                                        <x-input-error :messages="$errors->get('form.father_occupation')"/>
                                    </div>

                                    <div class="col-lg-6">
                                        <x-input-label for="mother_name" value="Nama Ibu (Legacy)" />
                                        <x-text-input wire:model="form.mother_name" type="text" id="mother_name"
                                            placeholder="Nama lengkap ibu (akan dipindahkan ke data orang tua)"
                                            :error="$errors->get('form.mother_name')" />
                                        <x-input-error :messages="$errors->get('form.mother_name')"/>
                                    </div>

                                    <div class="col-lg-6">
                                        <x-input-label for="mother_occupation" value="Pekerjaan Ibu (Legacy)" />
                                        <x-text-input wire:model="form.mother_occupation" type="text" id="mother_occupation"
                                            placeholder="Pekerjaan ibu (akan dipindahkan ke data orang tua)"
                                            :error="$errors->get('form.mother_occupation')" />
                                        <x-input-error :messages="$errors->get('form.mother_occupation')"/>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Pendaftaran -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="ri-file-list-line me-2"></i>Data Pendaftaran
                            </h6>
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <x-input-label for="enrollment_date" value="Tanggal Masuk" required/>
                                    <x-text-input wire:model="form.enrollment_date" type="date" id="enrollment_date"
                                        :error="$errors->get('form.enrollment_date')" />
                                    <x-input-error :messages="$errors->get('form.enrollment_date')"/>
                                </div>
                            </div>
                        </div>

                        <!-- Data Akun User Siswa -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="ri-user-settings-line me-2"></i>Pengaturan Akun User Siswa
                            </h6>
                            <div class="row g-3">
                                @if(!$editing || !$student->user_id)
                                    <div class="col-lg-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="create_user_account"
                                                wire:model.live="form.create_user_account">
                                            <label class="form-check-label" for="create_user_account">
                                                <strong>Buat akun user untuk siswa ini</strong>
                                            </label>
                                            <div class="form-text">
                                                Jika diaktifkan, sistem akan otomatis membuat akun user untuk siswa dengan username berupa NIS
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
                                                    <span class="text-primary">{{ $form->nis ?: '(akan menggunakan NIS)' }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Email:</strong>
                                                    <span class="text-primary">
                                                        {{ $form->full_name ? strtolower(str_replace(' ', '.', $form->full_name)) . '@student.com' : '(akan dibuat otomatis)' }}
                                                    </span>
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
                                            <small class="text-muted">Password default yang akan digunakan siswa untuk login pertama kali</small>
                                            <x-input-error :messages="$errors->get('form.password')"/>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-lg-12">
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading">
                                                <i class="ri-check-line me-2"></i>Akun User Sudah Tersedia
                                            </h6>
                                            <p class="mb-0">Siswa ini sudah memiliki akun user yang terdaftar dalam sistem.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary me-2">
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
