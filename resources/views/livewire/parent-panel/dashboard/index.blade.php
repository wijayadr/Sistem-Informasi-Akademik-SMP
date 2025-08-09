<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title text-white mb-2">Dashboard Orang Tua</h4>
                            <div class="text-white-50">
                                <span class="me-3"><i class="ri-user-heart-line me-1"></i>{{ $parent->full_name }}</span>
                                @if($selectedStudent)
                                    <span class="me-3"><i class="ri-user-3-line me-1"></i>Memantau: {{ $selectedStudent->full_name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($students->count() > 1)
                                <select wire:model.live="selectedStudentId" class="form-select">
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                                    @endforeach
                                </select>
                            @endif
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
                        <h5>Tidak ada data anak</h5>
                        <p class="text-muted">Belum ada data anak yang terdaftar untuk akun Anda.</p>
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
                                <i class="ri-school-line"></i>
                            </div>
                        </div>
                        <h5>Belum Terdaftar di Kelas</h5>
                        <p class="text-muted">{{ $selectedStudent->full_name }} belum terdaftar di kelas untuk tahun akademik aktif.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-success text-white rounded-circle fs-24">
                                <i class="ri-calendar-check-line"></i>
                            </span>
                        </div>
                        <h3 class="text-success mb-1">{{ $monthlyAttendanceStats['percentage'] }}%</h3>
                        <p class="text-muted mb-0">Kehadiran Bulan Ini</p>
                        <small class="text-muted">{{ $monthlyAttendanceStats['present'] }}/{{ $monthlyAttendanceStats['total'] }}</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-24">
                                <i class="ri-award-line"></i>
                            </span>
                        </div>
                        <h3 class="text-primary mb-1">{{ $monthlyGradeAverage }}</h3>
                        <p class="text-muted mb-0">Rata-rata Nilai Bulan Ini</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-info text-white rounded-circle fs-24">
                                <i class="ri-building-2-line"></i>
                            </span>
                        </div>
                        <h3 class="text-info mb-1">{{ $studentClass->class->class_name }}</h3>
                        <p class="text-muted mb-0">Kelas</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-warning text-white rounded-circle fs-24">
                                <i class="ri-calendar-2-line"></i>
                            </span>
                        </div>
                        <h3 class="text-warning mb-1">{{ $currentAcademicYear->academic_year }}</h3>
                        <p class="text-muted mb-0">Tahun Akademik</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedules and Attendance -->
        <div class="row mb-4">
            <!-- Today's Schedule -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-calendar-line me-2"></i>Jadwal Hari Ini
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ $todaySchedules->count() }} mata pelajaran</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($todaySchedules->count() > 0)
                            @foreach($todaySchedules as $schedule)
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-light text-primary rounded-circle">
                                            <i class="ri-book-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $schedule->teacherSubject->subject->subject_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-user-line me-1"></i>{{ $schedule->teacherSubject->teacher->full_name }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-primary fw-medium">{{ $schedule->start_time->format('H:i') }}</div>
                                        <small class="text-muted">{{ $schedule->end_time->format('H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="ri-calendar-line fs-24 mb-2 d-block"></i>
                                    Tidak ada jadwal hari ini
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Today's Attendance -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-user-check-line me-2"></i>Absensi Hari Ini
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-success">{{ $todayAttendance ? $todayAttendance->count() : 0 }} data</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($todayAttendance && $todayAttendance->count() > 0)
                            @foreach($todayAttendance as $attendance)
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                    <div class="avatar-sm me-3">
                                        @php
                                            $statusIcon = match($attendance->attendance_status) {
                                                'present' => 'ri-check-line',
                                                'absent' => 'ri-close-line',
                                                'late' => 'ri-time-line',
                                                'sick' => 'ri-heart-pulse-line',
                                                'permission' => 'ri-file-text-line',
                                                default => 'ri-question-line'
                                            };
                                            $statusClass = match($attendance->attendance_status) {
                                                'present' => 'bg-success',
                                                'absent' => 'bg-danger',
                                                'late' => 'bg-warning',
                                                'sick' => 'bg-info',
                                                'permission' => 'bg-secondary',
                                                default => 'bg-light'
                                            };
                                        @endphp
                                        <span class="avatar-title {{ $statusClass }} text-white rounded-circle">
                                            <i class="{{ $statusIcon }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $attendance->schedule->teacherSubject->subject->subject_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            @php
                                                $statusText = match($attendance->attendance_status) {
                                                    'present' => 'Hadir',
                                                    'absent' => 'Tidak Hadir',
                                                    'late' => 'Terlambat',
                                                    'sick' => 'Sakit',
                                                    'permission' => 'Izin',
                                                    default => 'Tidak Diketahui'
                                                };
                                            @endphp
                                            {{ $statusText }}
                                            @if($attendance->check_in_time)
                                                - {{ $attendance->check_in_time->format('H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="ri-user-check-line fs-24 mb-2 d-block"></i>
                                    Belum ada data absensi hari ini
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Grades -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-award-line me-2"></i>Nilai Terbaru
                            </h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('parent.grades.index') }}" class="btn btn-sm btn-outline-primary">
                                    Lihat Semua <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($recentGrades->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Mata Pelajaran</th>
                                            <th scope="col">Komponen</th>
                                            <th scope="col" class="text-center">Nilai</th>
                                            <th scope="col" class="text-center">Grade</th>
                                            <th scope="col">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentGrades as $grade)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-3">
                                                            <span class="avatar-title bg-light text-dark rounded-circle">
                                                                <i class="ri-book-line"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $grade->teacherSubject->subject->subject_name }}</h6>
                                                            <small class="text-muted">{{ $grade->teacherSubject->teacher->full_name }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-medium">{{ $grade->gradeComponent->component_name }}</span>
                                                    <small class="text-muted d-block">Bobot: {{ $grade->gradeComponent->weight_percentage }}%</small>
                                                </td>
                                                <td class="text-center">
                                                    <h5 class="mb-0 text-primary">{{ number_format($grade->grade_value, 1) }}</h5>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $gradeLetter = $this->getGradeLetter($grade->grade_value);
                                                        $gradeBadgeClass = $this->getGradeBadgeClass($grade->grade_value);
                                                    @endphp
                                                    <span class="badge {{ $gradeBadgeClass }} fs-6">{{ $gradeLetter }}</span>
                                                </td>
                                                <td>
                                                    <div class="text-muted">{{ $grade->input_date->format('d/m/Y') }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="ri-award-line fs-24 mb-2 d-block"></i>
                                    Belum ada nilai terbaru
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="row">
            {{-- <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-24">
                                <i class="ri-file-list-3-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Rapor Lengkap</h5>
                        <p class="text-muted mb-3">Lihat rapor lengkap dengan nilai dan ranking</p>
                        <a href="{{ route('parent.reports.index') }}" class="btn btn-primary">
                            <i class="ri-eye-line me-1"></i>Lihat Rapor
                        </a>
                    </div>
                </div>
            </div> --}}

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-success text-white rounded-circle fs-24">
                                <i class="ri-award-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Detail Nilai</h5>
                        <p class="text-muted mb-3">Pantau perkembangan nilai per mata pelajaran</p>
                        <a href="{{ route('parent.grades.index') }}" class="btn btn-success">
                            <i class="ri-bar-chart-line me-1"></i>Lihat Nilai
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-info text-white rounded-circle fs-24">
                                <i class="ri-calendar-check-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Riwayat Absensi</h5>
                        <p class="text-muted mb-3">Monitor kehadiran dan kedisiplinan anak</p>
                        <a href="{{ route('parent.attendances.index') }}" class="btn btn-info">
                            <i class="ri-calendar-event-line me-1"></i>Lihat Absensi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Information Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Anak</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="ps-0 fw-medium" style="width: 150px;">Nama Lengkap:</td>
                                            <td>{{ $selectedStudent->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">NIS:</td>
                                            <td>{{ $selectedStudent->nis }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">NISN:</td>
                                            <td>{{ $selectedStudent->national_student_id ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Kelas:</td>
                                            <td>{{ $studentClass->class->class_name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Tahun Akademik:</td>
                                            <td>{{ $studentClass->academicYear->academic_year }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td class="ps-0 fw-medium" style="width: 150px;">Tempat Lahir:</td>
                                            <td>{{ $selectedStudent->birth_place ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Tanggal Lahir:</td>
                                            <td>{{ $selectedStudent->birth_date ? $selectedStudent->birth_date->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Jenis Kelamin:</td>
                                            <td>{{ $selectedStudent->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Status:</td>
                                            <td>
                                                <span class="badge bg-success-subtle text-success">
                                                    {{ ucfirst($selectedStudent->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Tanggal Masuk:</td>
                                            <td>{{ $selectedStudent->enrollment_date ? $selectedStudent->enrollment_date->format('d/m/Y') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('style')
<style>
.dashboard-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.avatar-title {
    font-weight: 600;
}

.quick-nav-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.quick-nav-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12);
}

.schedule-item {
    transition: all 0.2s ease;
    border-radius: 0.375rem;
    padding: 0.75rem;
}

.schedule-item:hover {
    background-color: #f8f9fa;
}

.attendance-item {
    transition: all 0.2s ease;
    border-radius: 0.375rem;
    padding: 0.75rem;
}

.attendance-item:hover {
    background-color: #f8f9fa;
}

.student-info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.table-borderless td {
    padding: 0.5rem 0;
}
</style>
@endpush

@push('script')
<script>
document.addEventListener('livewire:initialized', () => {
    // Auto-refresh dashboard every 2 minutes
    setInterval(() => {
        @this.call('$refresh');
    }, 120000);

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add smooth transitions for cards
    document.querySelectorAll('.dashboard-card, .quick-nav-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
