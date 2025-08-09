<div>
    <!-- Class Info Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">{{ $class->class_name }} - Penugasan Guru</h4>
                            <div class="text-muted">
                                <span class="badge bg-info-subtle text-info me-2">Kelas {{ $class->grade_level }}</span>
                                <span class="me-3">Tahun Ajaran: {{ $class->academicYear->academic_year ?? '-' }}</span>
                                <span class="me-3">Wali Kelas: {{ $class->homeroomTeacher->full_name ?? '-' }}</span>
                                <span>Kapasitas: {{ $class->getActiveStudentsCount() }}/{{ $class->capacity }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary me-2">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                            <button type="button" wire:click="showModalTeacher" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="ri-user-add-line me-1"></i> Tugaskan Guru
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Subjects List -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Daftar Guru & Mata Pelajaran</h5>
                    </div>
                </div>

                <div class="card-body p-0 border-bottom border-bottom-dashed">
                    <div class="row g-3 p-3">
                        <div class="col-md-5">
                            <div class="search-box">
                                <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Cari guru atau mata pelajaran...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="subject_filter" class="form-select">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach($availableSubjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                @endforeach
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
                    @if($teacherSubjects->count() > 0)
                        <div class="table-responsive table-card">
                            <table class="table align-middle table-nowrap" id="teacherSubjectsTable">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                        <th class="text-uppercase">Guru</th>
                                        <th class="text-uppercase">Mata Pelajaran</th>
                                        <th class="text-uppercase">Kode Mapel</th>
                                        <th class="text-uppercase">Jam/Minggu</th>
                                        <th class="text-uppercase">Status</th>
                                        <th class="text-uppercase" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @foreach($teacherSubjects as $key => $teacherSubject)
                                        <tr wire:key="{{ $teacherSubject->id }}">
                                            <td class="text-center">
                                                {{ $teacherSubjects->firstItem() + $loop->index }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-3">
                                                        <span class="avatar-title rounded-circle bg-primary text-white font-size-12">
                                                            {{ substr($teacherSubject->teacher->full_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $teacherSubject->teacher->full_name }}</h6>
                                                        <small class="text-muted">{{ $teacherSubject->teacher->employee_id ?? 'NIP: -' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $teacherSubject->subject->subject_name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $teacherSubject->subject->subject_code }}</span>
                                            </td>
                                            <td>
                                                @if($teacherSubject->weekly_teaching_hours)
                                                    {{ $teacherSubject->weekly_teaching_hours }} jam
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($teacherSubject->status == 'active')
                                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <ul class="list-inline hstack gap-2 mb-0">
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus Penugasan">
                                                        <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="removeTeacherSubjectConfirm({{ $teacherSubject->id }})">
                                                            <i class="ri-delete-bin-5-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($teacherSubjects->hasPages())
                            <div class="mt-3">
                                {{ $teacherSubjects->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                    <i class="ri-user-2-line"></i>
                                </div>
                            </div>
                            <h5>Belum ada guru yang ditugaskan</h5>
                            <p class="text-muted">Klik tombol "Tugaskan Guru" untuk menambahkan guru ke mata pelajaran di kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Teacher Modal -->
    <x-modal name="showModal" :title="'Tugaskan Guru ke Mata Pelajaran'">
        <form wire:submit.prevent="assignTeacher">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <x-input-label for="teacher_id" value="Pilih Guru" required/>
                        <select wire:model="form.teacher_id" id="teacher_id" class="form-select @error('form.teacher_id') is-invalid @enderror">
                            <option value="">-- Pilih Guru --</option>
                            @foreach($this->listsForFields['teachers'] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.teacher_id')"/>
                    </div>

                    <div class="col-12">
                        <x-input-label for="subject_id" value="Pilih Mata Pelajaran" required/>
                        <select wire:model="form.subject_id" id="subject_id" class="form-select @error('form.subject_id') is-invalid @enderror">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($this->listsForFields['subjects'] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.subject_id')"/>
                    </div>

                    <div class="col-12">
                        <x-input-label for="weekly_teaching_hours" value="Jam Mengajar per Minggu"/>
                        <input type="number" wire:model="form.weekly_teaching_hours" id="weekly_teaching_hours"
                                class="form-control @error('form.weekly_teaching_hours') is-invalid @enderror"
                                placeholder="Contoh: 4" min="1" max="40">
                        <x-input-error :messages="$errors->get('form.weekly_teaching_hours')"/>
                        <small class="text-muted">Opsional - Kosongkan jika tidak diperlukan</small>
                    </div>

                    <div class="col-12">
                        <x-input-label for="status" value="Status" required/>
                        <select wire:model="form.status" class="form-select @error('form.status') is-invalid @enderror">
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                        </select>
                        <x-input-error :messages="$errors->get('form.status')"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> Tugaskan
                </button>
            </div>
        </form>
    </x-modal>
</div>
