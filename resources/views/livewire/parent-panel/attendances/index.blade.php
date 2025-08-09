<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">Riwayat Absensi Anak</h4>
                            <div class="text-muted">
                                @if($selectedStudent)
                                    <span class="me-3"><i class="ri-user-3-line me-1"></i>{{ $selectedStudent->full_name }}</span>
                                    <span class="me-3"><i class="ri-id-card-line me-1"></i>NIS: {{ $selectedStudent->nis }}</span>
                                    @if($studentClass)
                                        <span class="me-3"><i class="ri-building-2-line me-1"></i>{{ $studentClass->class->class_name }}</span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-end">
                                <div class="text-success fw-bold fs-18">{{ $attendanceStats['attendance_percentage'] }}%</div>
                                <small class="text-muted">Persentase Kehadiran</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student and Academic Year Selection -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @if($students->count() > 1)
                            <div class="col-md-6">
                                <label class="form-label">Pilih Anak</label>
                                <select wire:model.live="selected_student_id" class="form-select">
                                    <option value="">-- Pilih Anak --</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->nis }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Tahun Akademik</label>
                            <select wire:model.live="academic_year_id" class="form-select">
                                <option value="">-- Pilih Tahun Akademik --</option>
                                @foreach($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}">{{ $academicYear->academic_year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$selectedStudent)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                <i class="ri-user-unfollow-line"></i>
                            </div>
                        </div>
                        <h5>Pilih Anak</h5>
                        <p class="text-muted">Silakan pilih anak untuk melihat data absensi.</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif(!$studentClass)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                <i class="ri-user-unfollow-line"></i>
                            </div>
                        </div>
                        <h5>Belum Terdaftar di Kelas</h5>
                        <p class="text-muted">{{ $selectedStudent->full_name }} belum terdaftar di kelas manapun untuk tahun akademik yang dipilih.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Today's Schedules -->
        @if($todaySchedules->count() > 0)
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="ri-calendar-check-line me-2"></i>Jadwal Hari Ini - {{ $selectedStudent->full_name }}
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
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
                                                    <div><i class="ri-user-line me-1"></i>{{ $schedule->teacherSubject->teacher->full_name }}</div>
                                                    <div><i class="ri-time-line me-1"></i>{{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}</div>
                                                    @if($schedule->classroom)
                                                        <div><i class="ri-building-line me-1"></i>{{ $schedule->classroom }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Attendance Statistics -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistik Absensi - {{ $selectedStudent->full_name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
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
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-secondary text-white rounded-circle">{{ $attendanceStats['permission'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Izin</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-warning text-white rounded-circle">{{ $attendanceStats['late'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Terlambat</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Percentage Bar -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $attendanceStats['attendance_percentage'] }}%"
                                         aria-valuenow="{{ $attendanceStats['attendance_percentage'] }}"
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="text-center mt-1">
                                    <small class="text-muted">Persentase Kehadiran: {{ $attendanceStats['attendance_percentage'] }}%</small>
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
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ $attendances->total() }} data</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0 border-bottom border-bottom-dashed">
                        <div class="row g-3 p-3">
                            <div class="col-md-3">
                                <div class="search-box">
                                    <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Cari mata pelajaran/guru...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select wire:model.live="attendance_status_filter" class="form-select">
                                    <option value="">Semua Status</option>
                                    @foreach($listsForFields['attendance_statuses'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select wire:model.live="subject_filter" class="form-select">
                                    <option value="">Semua Mata Pelajaran</option>
                                    @foreach($listsForFields['subjects'] as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="month" wire:model.live="month_filter" class="form-control" title="Filter Bulan">
                            </div>
                            <div class="col-md-2">
                                <input type="date" wire:model.live="attendance_date" class="form-control" title="Filter Tanggal">
                            </div>
                            <div class="col-md-1">
                                <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                                    <i class="ri-refresh-line align-bottom"></i>
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
                                            <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                            <th class="text-uppercase">Tanggal</th>
                                            <th class="text-uppercase">Mata Pelajaran</th>
                                            <th class="text-uppercase">Guru</th>
                                            <th class="text-uppercase">Waktu</th>
                                            <th class="text-uppercase">Status</th>
                                            <th class="text-uppercase">Waktu Masuk</th>
                                            <th class="text-uppercase">Waktu Keluar</th>
                                            <th class="text-uppercase">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @foreach($attendances as $key => $attendance)
                                            <tr wire:key="{{ $attendance->id }}">
                                                <td class="text-center">
                                                    {{ $attendances->firstItem() + $loop->index }}
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ $attendance->attendance_date->format('d/m/Y') }}</div>
                                                    <small class="text-muted">{{ $attendance->attendance_date->format('l') }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-medium">{{ $attendance->schedule->teacherSubject->subject->subject_name }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-3">
                                                            <span class="avatar-title rounded-circle bg-primary text-white font-size-12">
                                                                {{ substr($attendance->schedule->teacherSubject->teacher->full_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $attendance->schedule->teacherSubject->teacher->full_name }}</h6>
                                                        </div>
                                                    </div>
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
                                                            'late' => 'Terlambat',
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
                                                    @if($attendance->notes)
                                                        <span class="text-muted">{{ $attendance->notes }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{ $attendances->links() }}
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-md mx-auto mb-4">
                                    <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                        <i class="ri-user-received-2-line"></i>
                                    </div>
                                </div>
                                <h5>Belum ada data absensi</h5>
                                <p class="text-muted">Belum ada data absensi untuk filter yang dipilih.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
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

.progress {
    background-color: #f1f3f4;
}

.monthly-chart-card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.monthly-chart-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.075);
}

.student-selector {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    padding: 1rem;
}

.parent-badge {
    background-color: #e7f3ff;
    color: #0066cc;
    font-weight: 500;
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
</script>
@endpush
