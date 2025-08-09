<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">Absensi Siswa</h4>
                            <div class="text-muted">
                                <span class="me-3">Tanggal: {{ \Carbon\Carbon::parse($attendance_date)->format('d F Y') }}</span>
                                <span class="me-3">Guru: {{ auth()->user()->teacher->full_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <input type="date" wire:model.live="attendance_date" class="form-control d-inline-block w-auto me-2">
                            <button type="button" wire:click="showAttendanceModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="ri-add-line me-1"></i> Tambah Absensi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedules -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            <i class="ri-calendar-check-line me-2"></i>Jadwal Hari Ini
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($todaySchedules->count() > 0)
                        <div class="row">
                            @foreach($todaySchedules as $schedule)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">{{ $schedule->teacherSubject->subject->subject_name }}</h6>
                                                <span class="badge bg-primary-subtle text-primary">{{ $schedule->teacherSubject->class->class_name }}</span>
                                            </div>
                                            <div class="text-muted small mb-2">
                                                <div><i class="ri-time-line me-1"></i>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                @if($schedule->classroom)
                                                    <div><i class="ri-building-line me-1"></i>{{ $schedule->classroom }}</div>
                                                @endif
                                            </div>
                                            <button type="button"
                                                    wire:click="createAttendanceForSchedule({{ $schedule->id }})"
                                                    class="btn btn-sm btn-outline-primary w-100">
                                                <i class="ri-user-add-line me-1"></i>Buat Absensi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <div class="text-muted">
                                <i class="ri-calendar-line fs-24 mb-2"></i>
                                <p class="mb-0">Tidak ada jadwal mengajar hari ini</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Statistics -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistik Absensi - {{ \Carbon\Carbon::parse($attendance_date)->format('d F Y') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-primary text-white rounded-circle">{{ $attendanceStats['total'] }}</span>
                                </div>
                                <h6 class="mb-0">Total</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-success text-white rounded-circle">{{ $attendanceStats['present'] }}</span>
                                </div>
                                <h6 class="mb-0">Hadir</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-danger text-white rounded-circle">{{ $attendanceStats['absent'] }}</span>
                                </div>
                                <h6 class="mb-0">Tidak Hadir</h6>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-info text-white rounded-circle">{{ $attendanceStats['sick'] }}</span>
                                </div>
                                <h6 class="mb-0">Sakit</h6>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-secondary text-white rounded-circle">{{ $attendanceStats['permission'] }}</span>
                                </div>
                                <h6 class="mb-0">Izin</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Daftar Absensi</h5>
                        @if(count($selectedStudents) > 0)
                            <div class="flex-shrink-0 me-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <select wire:model="bulkAttendanceStatus" class="form-select form-select-sm" style="width: auto;">
                                        <option value="">Pilih Status</option>
                                        @foreach($this->listsForFields['attendance_statuses'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" wire:click="bulkUpdateStatus" class="btn btn-sm btn-warning">
                                        <i class="ri-edit-line me-1"></i>Update Status
                                    </button>
                                </div>
                            </div>
                        @endif
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary">{{ $attendances->total() }} data</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 border-bottom border-bottom-dashed">
                    <div class="row g-3 p-3">
                        <div class="col-md-4">
                            <div class="search-box">
                                <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Cari siswa...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="attendance_status_filter" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach($this->listsForFields['attendance_statuses'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="subject_filter" class="form-select">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach($this->listsForFields['subjects'] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
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
                    @if($attendances->count() > 0)
                        <div class="table-responsive table-card">
                            <table class="table align-middle table-nowrap" id="attendanceTable">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" wire:model.live="selectAll" id="selectAllAttendance">
                                                <label class="form-check-label" for="selectAllAttendance"></label>
                                            </div>
                                        </th>
                                        <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                        <th class="text-uppercase">Siswa</th>
                                        <th class="text-uppercase">Kelas</th>
                                        <th class="text-uppercase">Mata Pelajaran</th>
                                        <th class="text-uppercase">Waktu</th>
                                        <th class="text-uppercase">Status</th>
                                        <th class="text-uppercase">Waktu Masuk</th>
                                        <th class="text-uppercase">Waktu Keluar</th>
                                        <th class="text-uppercase" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @foreach($attendances as $key => $attendance)
                                        <tr wire:key="{{ $attendance->id }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" wire:model.live="selectedStudents" value="{{ $attendance->id }}" id="attendance_{{ $attendance->id }}">
                                                    <label class="form-check-label" for="attendance_{{ $attendance->id }}"></label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{ $attendances->firstItem() + $loop->index }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-3">
                                                        <span class="avatar-title rounded-circle bg-primary text-white font-size-12">
                                                            {{ substr($attendance->student->full_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $attendance->student->full_name }}</h6>
                                                        <small class="text-muted">NIS: {{ $attendance->student->nis }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $attendance->schedule->teacherSubject->class->class_name }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $attendance->schedule->teacherSubject->subject->subject_name }}</span>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    <div>{{ $attendance->schedule->start_time->format('H:i') }} - {{ $attendance->schedule->end_time->format('H:i') }}</div>
                                                    @if($attendance->schedule->classroom)
                                                        <div>{{ $attendance->schedule->classroom }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = match($attendance->attendance_status) {
                                                        'present' => 'bg-success-subtle text-success',
                                                        'absent' => 'bg-danger-subtle text-danger',
                                                        'late' => 'bg-warning-subtle text-warning',
                                                        'sick' => 'bg-info-subtle text-info',
                                                        'permission' => 'bg-secondary-subtle text-secondary',
                                                        default => 'bg-light text-dark'
                                                    };
                                                    $statusText = match($attendance->attendance_status) {
                                                        'present' => 'Hadir',
                                                        'absent' => 'Tidak Hadir',
                                                        'sick' => 'Sakit',
                                                        'permission' => 'Izin',
                                                        default => 'Tidak Diketahui'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                @if($attendance->check_in_time)
                                                    <span class="text-success">{{ $attendance->check_in_time->format('H:i') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attendance->check_out_time)
                                                    <span class="text-success">{{ $attendance->check_out_time->format('H:i') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <ul class="list-inline hstack gap-2 mb-0">
                                                    <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                        <a href="javascript:void(0)" wire:click="editAttendance({{ $attendance->id }})" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#showModal">
                                                            <i class="ri-pencil-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                        <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteAttendanceConfirm({{ $attendance->id }})">
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

                        <x-pagination :items="$attendances" />
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                    <i class="ri-user-received-2-line"></i>
                                </div>
                            </div>
                            <h5>Belum ada data absensi</h5>
                            <p class="text-muted">Belum ada data absensi untuk tanggal yang dipilih. Klik tombol "Tambah Absensi" atau gunakan fitur "Buat Absensi" pada jadwal hari ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Modal -->
    <x-modal name="showModal" :title="$editing ? 'Edit Absensi' : 'Tambah Absensi Baru'" :maxWidth="'xl'">
        <form wire:submit.prevent="saveAttendance">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="schedule_id" value="Jadwal Pelajaran" required/>
                        <select wire:model.live="form.schedule_id" id="schedule_id" class="form-select @error('form.schedule_id') is-invalid @enderror">
                            <option value="">-- Pilih Jadwal --</option>
                            @foreach($this->listsForFields['schedules'] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.schedule_id')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="student_id" value="Siswa" required/>
                        <select wire:model="form.student_id" id="student_id" class="form-select @error('form.student_id') is-invalid @enderror">
                            <option value="">-- Pilih Siswa --</option>
                            @if($form->schedule_id)
                                @php
                                    $schedule = \App\Models\Academic\Schedule::with('teacherSubject.class.classStudents.student')->find($form->schedule_id);
                                    $students = $schedule ? $schedule->teacherSubject->class->classStudents->where('status', 'active') : collect();
                                @endphp
                                @foreach($students as $classStudent)
                                    <option value="{{ $classStudent->student->id }}">{{ $classStudent->student->full_name }} ({{ $classStudent->student->nis }})</option>
                                @endforeach
                            @endif
                        </select>
                        <x-input-error :messages="$errors->get('form.student_id')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="attendance_date" value="Tanggal" required/>
                        <input type="date" wire:model="form.attendance_date" id="attendance_date"
                                class="form-control @error('form.attendance_date') is-invalid @enderror">
                        <x-input-error :messages="$errors->get('form.attendance_date')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="attendance_status" value="Status Absensi" required/>
                        <select wire:model="form.attendance_status" id="attendance_status" class="form-select @error('form.attendance_status') is-invalid @enderror">
                            @foreach($this->listsForFields['attendance_statuses'] as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.attendance_status')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="check_in_time" value="Waktu Masuk"/>
                        <input type="time" wire:model="form.check_in_time" id="check_in_time"
                                class="form-control @error('form.check_in_time') is-invalid @enderror">
                        <x-input-error :messages="$errors->get('form.check_in_time')"/>
                        <small class="text-muted">Kosongkan jika tidak diperlukan</small>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="check_out_time" value="Waktu Keluar"/>
                        <input type="time" wire:model="form.check_out_time" id="check_out_time"
                                class="form-control @error('form.check_out_time') is-invalid @enderror">
                        <x-input-error :messages="$errors->get('form.check_out_time')"/>
                        <small class="text-muted">Kosongkan jika tidak diperlukan</small>
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
.attendance-card {
    transition: all 0.3s ease;
}

.attendance-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.schedule-time {
    font-weight: 600;
    color: #495057;
}

.search-box {
    position: relative;
}

.search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #74788d;
}

.table-card .table td {
    vertical-align: middle;
}

.attendance-stats .avatar-title {
    font-weight: 600;
    font-size: 1.1rem;
}
</style>
@endpush

@push('script')
<script>
document.addEventListener('livewire:initialized', () => {
    // Auto-refresh attendance stats every 30 seconds
    setInterval(() => {
        @this.call('$refresh');
    }, 30000);

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Handle modal events
window.addEventListener('closeModal', event => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('showModal'));
    if (modal) {
        modal.hide();
    }
});
</script>
@endpush
