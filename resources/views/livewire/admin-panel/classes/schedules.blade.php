<div>
    <!-- Class Info Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">{{ $class->class_name }} - Jadwal Pelajaran</h4>
                            <div class="text-muted">
                                <span class="badge bg-info-subtle text-info me-2">Kelas {{ $class->grade_level }}</span>
                                <span class="me-3">Tahun Ajaran: {{ $class->academicYear->academic_year ?? '-' }}</span>
                                <span class="me-3">Wali Kelas: {{ $class->homeroomTeacher->full_name ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary me-2">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                            <button type="button" wire:click="showScheduleModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="ri-add-line me-1"></i> Tambah Jadwal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Day Selector Sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pilih Hari</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($days as $dayKey => $dayName)
                            <a href="javascript:void(0)"
                               wire:click="$set('selectedDay', '{{ $dayKey }}')"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedDay == $dayKey ? 'active' : '' }}">
                                {{ $dayName }}
                                @if(isset($allSchedules[$dayKey]))
                                    <span class="badge bg-primary rounded-pill">{{ $allSchedules[$dayKey]->count() }}</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">0</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Weekly Summary -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Ringkasan Mingguan</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-info text-white rounded-circle fs-20">
                                {{ $allSchedules->flatten()->count() }}
                            </span>
                        </div>
                        <h6>Total Jadwal</h6>
                        <p class="text-muted mb-0">dalam seminggu</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule List -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            Jadwal {{ $days[$selectedDay] }}
                        </h5>
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary">{{ $schedules->count() }} jadwal</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($schedules->count() > 0)
                        <div class="timeline-container">
                            @foreach($schedules as $schedule)
                                <div class="schedule-item border rounded p-3 mb-3 {{ $schedule->status == 'cancelled' ? 'bg-light' : '' }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="time-block text-center">
                                                <div class="fw-bold text-primary">{{ $schedule->start_time->format('H:i') }}</div>
                                                <small class="text-muted">{{ $schedule->end_time->format('H:i') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="schedule-info">
                                                <h6 class="mb-1 {{ $schedule->status == 'cancelled' ? 'text-decoration-line-through' : '' }}">
                                                    {{ $schedule->teacherSubject->subject->subject_name }}
                                                </h6>
                                                <div class="text-muted">
                                                    <i class="ri-user-3-line me-1"></i>
                                                    {{ $schedule->teacherSubject->teacher->full_name }}
                                                </div>
                                                @if($schedule->classroom)
                                                    <div class="text-muted">
                                                        <i class="ri-building-line me-1"></i>
                                                        {{ $schedule->classroom }}
                                                    </div>
                                                @endif
                                                @if($schedule->notes)
                                                    <div class="text-muted mt-1">
                                                        <small><i class="ri-information-line me-1"></i>{{ $schedule->notes }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                @if($schedule->status == 'active')
                                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Dibatalkan</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="d-flex gap-2 justify-content-end">
                                                @if($schedule->status == 'active')
                                                    <button type="button"
                                                            wire:click="changeStatus({{ $schedule->id }}, 'cancelled')"
                                                            class="btn btn-sm btn-outline-warning"
                                                            data-bs-toggle="tooltip" title="Batalkan">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            wire:click="changeStatus({{ $schedule->id }}, 'active')"
                                                            class="btn btn-sm btn-outline-success"
                                                            data-bs-toggle="tooltip" title="Aktifkan">
                                                        <i class="ri-check-line"></i>
                                                    </button>
                                                @endif

                                                <button type="button"
                                                        wire:click="editSchedule({{ $schedule->id }})"
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-bs-target="#showModal"
                                                        data-bs-toggle="modal"
                                                        data-bs-toggle="tooltip" title="Edit">
                                                    <i class="ri-pencil-line"></i>
                                                </button>

                                                <button type="button"
                                                        wire:click="deleteScheduleConfirm({{ $schedule->id }})"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip" title="Hapus">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                    <i class="ri-calendar-line"></i>
                                </div>
                            </div>
                            <h5>Belum ada jadwal</h5>
                            <p class="text-muted">Belum ada jadwal untuk hari {{ $days[$selectedDay] }}. Klik tombol "Tambah Jadwal" untuk menambahkan jadwal baru.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Modal -->
    <x-modal name="showModal" :title="$editing ? 'Edit Jadwal' : 'Tambah Jadwal Baru'" :maxWidth="'xl'">
        <form wire:submit.prevent="saveSchedule">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="teacher_subject_id" value="Guru & Mata Pelajaran" required/>
                        <select wire:model="form.teacher_subject_id" id="teacher_subject_id" class="form-select @error('form.teacher_subject_id') is-invalid @enderror">
                            <option value="">-- Pilih Guru & Mata Pelajaran --</option>
                            @foreach($this->listsForFields['teacher_subjects'] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.teacher_subject_id')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="day" value="Hari" required/>
                        <select wire:model="form.day" id="day" class="form-select @error('form.day') is-invalid @enderror">
                            <option value="">-- Pilih Hari --</option>
                            @foreach($this->listsForFields['days'] as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.day')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="start_time" value="Waktu Mulai" required/>
                        <input type="time" wire:model="form.start_time" id="start_time"
                                class="form-control @error('form.start_time') is-invalid @enderror">
                        <x-input-error :messages="$errors->get('form.start_time')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="end_time" value="Waktu Selesai" required/>
                        <input type="time" wire:model="form.end_time" id="end_time"
                                class="form-control @error('form.end_time') is-invalid @enderror">
                        <x-input-error :messages="$errors->get('form.end_time')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="classroom" value="Ruang Kelas"/>
                        <input type="text" wire:model="form.classroom" id="classroom"
                                class="form-control @error('form.classroom') is-invalid @enderror"
                                placeholder="Contoh: 7A, Lab Komputer">
                        <x-input-error :messages="$errors->get('form.classroom')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="status" value="Status" required/>
                        <select wire:model="form.status" class="form-select @error('form.status') is-invalid @enderror">
                            @foreach($this->listsForFields['statuses'] as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.status')"/>
                    </div>

                    <div class="col-12">
                        <x-input-label for="notes" value="Catatan"/>
                        <textarea wire:model="form.notes" id="notes" rows="3"
                                    class="form-control @error('form.notes') is-invalid @enderror"
                                    placeholder="Catatan tambahan (opsional)"></textarea>
                        <x-input-error :messages="$errors->get('form.notes')"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i> {{ $editing ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>
        </form>
    </x-modal>
</div>

@push('style')
<style>
.schedule-item {
    transition: all 0.3s ease;
}

.schedule-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.time-block {
    border-right: 2px solid #e9ecef;
}

.list-group-item.active {
    background-color: var(--vz-topnav-item-color-active);
    border-color: var(--vz-topnav-item-color-active);
}
</style>
@endpush
