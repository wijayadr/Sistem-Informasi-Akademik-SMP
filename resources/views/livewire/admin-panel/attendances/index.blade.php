<div>
    <!-- Academic Year Selection -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-2">Data Absensi Siswa</h4>
                            <div class="text-muted">
                                <span class="me-3">Administrator Panel</span>
                                @if($selectedAcademicYear)
                                    <span class="me-3">Tahun Akademik: {{ $selectedAcademicYear->academic_year }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2 justify-content-end">
                                <select wire:model.live="academic_year_id" class="form-select" style="width: auto;">
                                    <option value="">-- Pilih Tahun Akademik --</option>
                                    @foreach($this->listsForFields['academic_years'] as $id => $year)
                                        <option value="{{ $id }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                <input type="date" wire:model.live="attendance_date" class="form-control" style="width: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($academic_year_id)
        <!-- Attendance Statistics -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistik Absensi - {{ \Carbon\Carbon::parse($attendance_date)->format('d F Y') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-primary text-white rounded-circle">{{ $attendanceStats['total'] }}</span>
                                </div>
                                <h6 class="mb-0">Total</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-success text-white rounded-circle">{{ $attendanceStats['present'] }}</span>
                                </div>
                                <h6 class="mb-0">Hadir</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-danger text-white rounded-circle">{{ $attendanceStats['absent'] }}</span>
                                </div>
                                <h6 class="mb-0">Tidak Hadir</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-warning text-white rounded-circle">{{ $attendanceStats['late'] }}</span>
                                </div>
                                <h6 class="mb-0">Terlambat</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-info text-white rounded-circle">{{ $attendanceStats['sick'] }}</span>
                                </div>
                                <h6 class="mb-0">Sakit</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-secondary text-white rounded-circle">{{ $attendanceStats['permission'] }}</span>
                                </div>
                                <h6 class="mb-0">Izin</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tingkat Kehadiran</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $presentPercentage = $attendanceStats['total'] > 0 ?
                                round(($attendanceStats['present'] / $attendanceStats['total']) * 100, 1) : 0;
                        @endphp
                        <div class="text-center">
                            <div class="avatar-lg mx-auto mb-3">
                                <span class="avatar-title bg-success-subtle text-success rounded-circle fs-24">
                                    {{ $presentPercentage }}%
                                </span>
                            </div>
                            <h6 class="mb-0">Tingkat Kehadiran Hari Ini</h6>
                            <small class="text-muted">{{ $attendanceStats['present'] }} dari {{ $attendanceStats['total'] }} siswa</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance by Class -->
        @if($attendancesByClass->count() > 0)
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="ri-group-line me-2"></i>Absensi Per Kelas
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($attendancesByClass as $classData)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="card-title mb-0">{{ $classData['class_name'] }}</h6>
                                                    <span class="badge bg-primary-subtle text-primary">{{ $classData['total'] }} siswa</span>
                                                </div>
                                                <div class="row text-center g-2">
                                                    <div class="col-4">
                                                        <div class="text-success">
                                                            <div class="fs-18 fw-semibold">{{ $classData['present'] }}</div>
                                                            <div class="fs-12 text-muted">Hadir</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="text-danger">
                                                            <div class="fs-18 fw-semibold">{{ $classData['absent'] }}</div>
                                                            <div class="fs-12 text-muted">Tidak Hadir</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="text-warning">
                                                            <div class="fs-18 fw-semibold">{{ $classData['late'] }}</div>
                                                            <div class="fs-12 text-muted">Terlambat</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $classPresent = $classData['total'] > 0 ?
                                                        round(($classData['present'] / $classData['total']) * 100, 1) : 0;
                                                @endphp
                                                <div class="mt-3">
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                             style="width: {{ $classPresent }}%"
                                                             aria-valuenow="{{ $classPresent }}"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="text-center mt-1">
                                                        <small class="text-muted">{{ $classPresent }}% Kehadiran</small>
                                                    </div>
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

        <!-- Attendance List -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Daftar Absensi Siswa</h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ $attendances->total() }} data</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0 border-bottom border-bottom-dashed">
                        <div class="row g-3 p-3">
                            <div class="col-md-2">
                                <div class="search-box">
                                    <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Cari siswa...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select wire:model.live="attendance_status_filter" class="form-select">
                                    <option value="">Semua Status</option>
                                    @foreach($this->listsForFields['attendance_statuses'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select wire:model.live="class_filter" class="form-select">
                                    <option value="">Semua Kelas</option>
                                    @foreach($this->listsForFields['classes'] as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select wire:model.live="subject_filter" class="form-select">
                                    <option value="">Semua Mata Pelajaran</option>
                                    @foreach($this->listsForFields['subjects'] as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select wire:model.live="teacher_filter" class="form-select">
                                    <option value="">Semua Guru</option>
                                    @foreach($this->listsForFields['teachers'] as $id => $name)
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
                                            <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                            <th class="text-uppercase">Siswa</th>
                                            <th class="text-uppercase">Kelas</th>
                                            <th class="text-uppercase">Mata Pelajaran</th>
                                            <th class="text-uppercase">Guru</th>
                                            <th class="text-uppercase">Waktu</th>
                                            <th class="text-uppercase">Status</th>
                                            <th class="text-uppercase">Waktu Masuk</th>
                                            <th class="text-uppercase">Waktu Keluar</th>
                                            <th class="text-uppercase">Input Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @foreach($attendances as $key => $attendance)
                                            <tr wire:key="{{ $attendance->id }}">
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
                                                    <div>
                                                        <span class="fw-medium">{{ $attendance->schedule->teacherSubject->teacher->full_name }}</span>
                                                        <small class="text-muted d-block">{{ $attendance->schedule->teacherSubject->teacher->employee_id ?? '-' }}</small>
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
                                                    <div class="text-muted small">
                                                        <div>{{ $attendance->inputTeacher->full_name }}</div>
                                                        <div>{{ $attendance->created_at->format('d/m/Y H:i') }}</div>
                                                    </div>
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
                                <p class="text-muted">Belum ada data absensi untuk tanggal yang dipilih.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Academic Year Selected -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                <i class="ri-calendar-line"></i>
                            </div>
                        </div>
                        <h5>Pilih Tahun Akademik</h5>
                        <p class="text-muted">Silakan pilih tahun akademik terlebih dahulu untuk melihat data absensi siswa.</p>
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

.class-attendance-card {
    transition: all 0.2s ease;
}

.class-attendance-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.075);
}

.academic-year-selector {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem;
}

.progress {
    background-color: #e9ecef;
}

.fs-18 {
    font-size: 1.125rem;
}

.fs-12 {
    font-size: 0.75rem;
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
