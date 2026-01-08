<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title text-white mb-2">Dashboard Admin</h4>
                            <div class="text-white-50">
                                <span class="me-3"><i class="ri-shield-user-line me-1"></i>Administrator Panel</span>
                                @if($selectedAcademicYear)
                                    <span class="me-3"><i class="ri-calendar-line me-1"></i>{{ $selectedAcademicYear->academic_year }}</span>
                                @endif
                                <span><i class="ri-time-line me-1"></i>{{ now()->format('l, d F Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($academicYears->count() > 0)
                                <select wire:model.live="selectedAcademicYearId" class="form-select bg-white">
                                    <option value="">Pilih Tahun Akademik</option>
                                    @foreach($academicYears as $academicYear)
                                        <option value="{{ $academicYear->id }}">
                                            {{ $academicYear->academic_year }}
                                            @if($academicYear->status === 'active') (Aktif) @endif
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$selectedAcademicYear)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                <i class="ri-calendar-line"></i>
                            </div>
                        </div>
                        <h5>Pilih Tahun Akademik</h5>
                        <p class="text-muted">Silakan pilih tahun akademik untuk melihat data dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Overall Statistics -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-secondary text-white rounded-circle fs-24">
                                <i class="ri-book-line"></i>
                            </span>
                        </div>
                        <h3 class="text-secondary mb-1">{{ number_format($overallStats['total_subjects']) }}</h3>
                        <p class="text-muted mb-0 small">Mata Pelajaran</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-danger text-white rounded-circle fs-24">
                                <i class="ri-calendar-event-line"></i>
                            </span>
                        </div>
                        <h3 class="text-danger mb-1">{{ number_format($overallStats['active_schedules']) }}</h3>
                        <p class="text-muted mb-0 small">Jadwal Aktif</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Overview -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom-dashed">
                        <h6 class="card-title mb-0">
                            <i class="ri-calendar-check-line me-2"></i>Absensi Hari Ini
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <h2 class="text-success mb-1">{{ $todayAttendanceOverview['attendance_percentage'] }}%</h2>
                            <p class="text-muted mb-0">{{ $todayAttendanceOverview['total_attended'] }}/{{ $todayAttendanceOverview['total_students'] }} siswa hadir</p>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="text-success fw-medium">{{ $todayAttendanceOverview['by_status']['present'] ?? 0 }}</div>
                                    <small class="text-muted">Hadir</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="text-warning fw-medium">{{ $todayAttendanceOverview['by_status']['late'] ?? 0 }}</div>
                                    <small class="text-muted">Terlambat</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="text-danger fw-medium">{{ $todayAttendanceOverview['by_status']['absent'] ?? 0 }}</div>
                                    <small class="text-muted">Absen</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="text-info fw-medium">{{ $todayAttendanceOverview['by_status']['sick'] ?? 0 }}</div>
                                    <small class="text-muted">Sakit</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom-dashed">
                        <h6 class="card-title mb-0">
                            <i class="ri-award-line me-2"></i>Nilai Bulan Ini
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <h2 class="text-primary mb-1">{{ $monthlyGradeOverview['average_grade'] }}</h2>
                            <p class="text-muted mb-0">Rata-rata dari {{ number_format($monthlyGradeOverview['total_grades']) }} nilai</p>
                        </div>
                        <div class="row g-1">
                            @foreach($monthlyGradeOverview['grade_distribution'] as $grade => $count)
                                <div class="col">
                                    <div class="border rounded p-2">
                                        <div class="fw-medium
                                            @if($grade === 'A') text-success
                                            @elseif($grade === 'B') text-primary
                                            @elseif($grade === 'C') text-info
                                            @elseif($grade === 'D') text-warning
                                            @else text-danger @endif">{{ $count }}</div>
                                        <small class="text-muted">{{ $grade }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom-dashed">
                        <h6 class="card-title mb-0">
                            <i class="ri-calendar-2-line me-2"></i>Absensi Bulan Ini
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <h2 class="text-success mb-1">{{ $monthlyAttendanceOverview['percentage'] }}%</h2>
                            <p class="text-muted mb-0">dari {{ number_format($monthlyAttendanceOverview['total_records']) }} record</p>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="text-success fw-medium">{{ $monthlyAttendanceOverview['present'] }}</div>
                                    <small class="text-muted">Hadir</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <div class="text-warning fw-medium">{{ $monthlyAttendanceOverview['late'] }}</div>
                                    <small class="text-muted">Terlambat</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Per Class and Top Performers -->
        <div class="row mb-4">

            <!-- Top Performing Students -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-trophy-line me-2"></i>Siswa Berprestasi
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-warning">Top 5</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($topPerformingStudents->count() > 0)
                            @foreach($topPerformingStudents as $index => $studentData)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-sm me-3">
                                        @php
                                            $rankClass = match($index) {
                                                0 => 'bg-warning text-white', // Gold
                                                1 => 'bg-secondary text-white', // Silver
                                                2 => 'bg-info text-white', // Bronze
                                                default => 'bg-light text-dark'
                                            };
                                        @endphp
                                        <span class="avatar-title {{ $rankClass }} rounded-circle">
                                            @if($index < 3)
                                                <i class="ri-trophy-line"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 small">{{ $studentData['student']->full_name }}</h6>
                                        <small class="text-muted">{{ $studentData['class_name'] }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-medium">{{ $studentData['average_grade'] }}</div>
                                        @php
                                            $gradeBadgeClass = $this->getGradeBadgeClass($studentData['average_grade']);
                                        @endphp
                                        <span class="badge {{ $gradeBadgeClass }} small">{{ $studentData['grade_letter'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="ri-trophy-line fs-24 mb-2 d-block"></i>
                                    <small>Belum ada data prestasi</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Per Academic Year -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-graduation-cap-line me-2"></i>Data Siswa Per Tahun Akademik
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-info">{{ $studentsPerAcademicYear->count() }} tahun akademik</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($studentsPerAcademicYear->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Tahun Akademik</th>
                                            <th scope="col" class="text-center">Jumlah Siswa</th>
                                            <th scope="col" class="text-center">Rata-rata Nilai</th>
                                            <th scope="col" class="text-center">Grade</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col">Periode</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($studentsPerAcademicYear as $academicYearData)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-3">
                                                            <span class="avatar-title bg-light text-dark rounded-circle">
                                                                <i class="ri-calendar-line"></i>
                                                            </span>
                                                        </div>
                                                        <h6 class="mb-0">{{ $academicYearData['academic_year'] }}</h6>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-medium fs-5">{{ number_format($academicYearData['student_count']) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-medium">{{ $academicYearData['average_grade'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($academicYearData['average_grade'] > 0)
                                                        @php
                                                            $gradeLetter = $this->getGradeLetter($academicYearData['average_grade']);
                                                            $gradeBadgeClass = $this->getGradeBadgeClass($academicYearData['average_grade']);
                                                        @endphp
                                                        <span class="badge {{ $gradeBadgeClass }}">{{ $gradeLetter }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($academicYearData['is_active'])
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="ri-checkbox-circle-line me-1"></i>Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">
                                                            <i class="ri-archive-line me-1"></i>Arsip
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="text-muted small">
                                                        @if($academicYearData['start_date'] && $academicYearData['end_date'])
                                                            {{ \Carbon\Carbon::parse($academicYearData['start_date'])->format('d/m/Y') }} -
                                                            {{ \Carbon\Carbon::parse($academicYearData['end_date'])->format('d/m/Y') }}
                                                        @else
                                                            -
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ri-graduation-cap-line fs-24 mb-2 d-block"></i>
                                    Belum ada data tahun akademik
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="row">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm quick-nav-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-24">
                                <i class="ri-user-3-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Kelola Siswa</h5>
                        <p class="text-muted mb-3">Manajemen data siswa dan kelas</p>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-primary">
                            <i class="ri-user-settings-line me-1"></i>Kelola Siswa
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm quick-nav-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-success text-white rounded-circle fs-24">
                                <i class="ri-user-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Kelola Guru</h5>
                        <p class="text-muted mb-3">Manajemen data guru dan pengajaran</p>
                        <a href="{{ route('admin.teachers.index') }}" class="btn btn-success">
                            <i class="ri-user-settings-line me-1"></i>Kelola Guru
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm quick-nav-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-info text-white rounded-circle fs-24">
                                <i class="ri-building-2-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Kelola Kelas</h5>
                        <p class="text-muted mb-3">Manajemen kelas dan mata pelajaran</p>
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-info">
                            <i class="ri-building-line me-1"></i>Kelola Kelas
                        </a>
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

.quick-nav-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.quick-nav-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12);
}

.avatar-title {
    font-weight: 600;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.card-header {
    background-color: rgba(var(--bs-light-rgb), 0.05);
    border-bottom: 1px dashed rgba(var(--bs-border-color-rgb), 0.5);
}

.table-borderless td {
    padding: 0.5rem 0;
}

.small-stat-card {
    transition: all 0.2s ease;
}

.small-stat-card:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

@media (max-width: 768px) {
    .col-lg-2 {
        margin-bottom: 1rem;
    }

    .dashboard-card .avatar-md {
        width: 2.5rem;
        height: 2.5rem;
    }

    .dashboard-card h3 {
        font-size: 1.5rem;
    }
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.performance-indicator {
    position: relative;
    overflow: hidden;
}

.performance-indicator::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.performance-indicator:hover::before {
    left: 100%;
}
</style>
@endpush

@push('script')
<script>
document.addEventListener('livewire:initialized', () => {
    // Auto-refresh dashboard every 5 minutes
    setInterval(() => {
        @this.call('refreshDashboard');
    }, 300000);

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

    // Add fade-in animation to cards
    document.querySelectorAll('.card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Real-time clock update
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        const clockElement = document.querySelector('.real-time-clock');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }

    // Update clock every second
    setInterval(updateClock, 1000);
    updateClock(); // Initial call

    // Listen for academic year changes
    @this.on('academic-year-changed', () => {
        // Add loading state or animation if needed
        console.log('Academic year changed, refreshing data...');
    });

    // Listen for dashboard refresh
    @this.on('dashboard-refreshed', () => {
        console.log('Dashboard refreshed');

        // Show refresh notification
        if (typeof Toastify !== 'undefined') {
            Toastify({
                text: "Dashboard berhasil diperbarui",
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            }).showToast();
        }
    });
});

// Performance chart initialization (if needed)
function initializeCharts() {
    // Add chart initialization logic here if using Chart.js or similar
    console.log('Charts initialized');
}

// Call chart initialization after DOM is loaded
document.addEventListener('DOMContentLoaded', initializeCharts);
</script>
@endpush
