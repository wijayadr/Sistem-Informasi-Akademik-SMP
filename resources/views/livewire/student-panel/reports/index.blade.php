
<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-2">Rapor Saya</h4>
                            <div class="text-muted">
                                <span class="me-3"><i class="ri-user-3-line me-1"></i>{{ $student->full_name }}</span>
                                <span class="me-3"><i class="ri-id-card-line me-1"></i>NIS: {{ $student->nis }}</span>
                                @if($studentClass)
                                    <span class="me-3"><i class="ri-building-2-line me-1"></i>{{ $studentClass->class->class_name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <select wire:model.live="academic_year_id" class="form-select">
                                        <option value="">-- Pilih Tahun Akademik --</option>
                                        @foreach($academicYears as $academicYear)
                                            <option value="{{ $academicYear->id }}">{{ $academicYear->academic_year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select wire:model.live="semester" class="form-select">
                                        @foreach($semesters as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$studentClass)
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
                        <p class="text-muted">Anda belum terdaftar di kelas manapun untuk tahun akademik yang dipilih. Silakan hubungi administrator sekolah.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Student Identity & Summary -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Identitas Siswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-medium" style="width: 120px;">Nama Lengkap</td>
                                        <td>: {{ $student->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">NIS</td>
                                        <td>: {{ $student->nis }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Kelas</td>
                                        <td>: {{ $studentClass->class->class_name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td class="fw-medium" style="width: 120px;">Tahun Akademik</td>
                                        <td>: {{ $studentClass->academicYear->academic_year }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Semester</td>
                                        <td>: {{ $semesters[$semester] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-medium">Wali Kelas</td>
                                        <td>: {{ $studentClass->class->homeroomTeacher->full_name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ringkasan Prestasi</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="avatar-lg mx-auto mb-2">
                                <span class="avatar-title bg-primary text-white rounded-circle fs-20">
                                    {{ $overallGPA }}
                                </span>
                            </div>
                            <h6 class="mb-0">Rata-rata Nilai</h6>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="text-success fw-bold fs-18">{{ $rankData['rank'] }}</div>
                                    <small class="text-muted">Peringkat</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="text-info fw-bold fs-18">{{ $attendanceSummary['percentage'] }}%</div>
                                    <small class="text-muted">Kehadiran</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade Distribution Chart -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Distribusi Nilai</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            @foreach($gradeDistribution as $grade => $count)
                                @php
                                    $gradeColor = match($grade) {
                                        'A' => 'bg-success',
                                        'B' => 'bg-info',
                                        'C' => 'bg-warning',
                                        'D' => 'bg-orange',
                                        'E' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <div class="col">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title {{ $gradeColor }} text-white rounded-circle">{{ $count }}</span>
                                    </div>
                                    <h6 class="mb-0">Grade {{ $grade }}</h6>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ringkasan Kehadiran</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-success" style="width: {{ $attendanceSummary['percentage'] }}%"></div>
                            </div>
                            <div class="mt-2">
                                <h5 class="text-success mb-0">{{ $attendanceSummary['percentage'] }}%</h5>
                                <small class="text-muted">Persentase Kehadiran</small>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-success fw-bold">{{ $attendanceSummary['present'] }}</div>
                                <small class="text-muted">Hadir</small>
                            </div>
                            <div class="col-4">
                                <div class="text-danger fw-bold">{{ $attendanceSummary['absent'] }}</div>
                                <small class="text-muted">Tidak Hadir</small>
                            </div>
                            <div class="col-4">
                                <div class="text-warning fw-bold">{{ $attendanceSummary['late'] }}</div>
                                <small class="text-muted">Terlambat</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Grades -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-book-open-line me-2"></i>Nilai Per Mata Pelajaran
                            </h5>
                            <div class="flex-shrink-0">
                                <button type="button" wire:click="exportReportCard" class="btn btn-outline-primary">
                                    <i class="ri-download-line me-1"></i> Unduh Rapor
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($subjectGrades->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-nowrap align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 60px;">No</th>
                                            <th>Mata Pelajaran</th>
                                            <th class="text-center">Komponen Nilai</th>
                                            <th class="text-center" style="width: 100px;">Nilai Akhir</th>
                                            <th class="text-center" style="width: 80px;">Grade</th>
                                            <th class="text-center" style="width: 100px;">Jumlah Penilaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subjectGrades as $index => $subjectGrade)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="fw-medium">{{ $subjectGrade['subject_name'] }}</div>
                                                </td>
                                                <td>
                                                    <div class="component-breakdown">
                                                        @foreach($subjectGrade['components'] as $component)
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <small class="text-muted">{{ $component['name'] }}</small>
                                                                <div class="text-end">
                                                                    <span class="fw-medium">{{ number_format($component['average'], 1) }}</span>
                                                                    <small class="text-muted">({{ $component['weight'] }}%)</small>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="text-primary fw-bold fs-18">{{ number_format($subjectGrade['final_grade'], 1) }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge {{ $subjectGrade['grade_class'] }} fs-6">{{ $subjectGrade['grade_letter'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark">{{ $subjectGrade['total_assessments'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="3" class="text-center fw-bold">RATA-RATA KESELURUHAN</td>
                                            <td class="text-center">
                                                <div class="text-primary fw-bold fs-18">{{ $overallGPA }}</div>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $overallLetter = match(true) {
                                                        $overallGPA >= 90 => 'A',
                                                        $overallGPA >= 80 => 'B',
                                                        $overallGPA >= 70 => 'C',
                                                        $overallGPA >= 60 => 'D',
                                                        default => 'E'
                                                    };
                                                    $overallClass = match(true) {
                                                        $overallGPA >= 80 => 'bg-success-subtle text-success',
                                                        $overallGPA >= 70 => 'bg-info-subtle text-info',
                                                        $overallGPA >= 60 => 'bg-warning-subtle text-warning',
                                                        default => 'bg-danger-subtle text-danger'
                                                    };
                                                @endphp
                                                <span class="badge {{ $overallClass }} fs-6">{{ $overallLetter }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $subjectGrades->count() }}</span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Academic Achievement -->
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="avatar-md mx-auto mb-2">
                                            <span class="avatar-title bg-success text-white rounded-circle">
                                                <i class="ri-trophy-line"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-1">Peringkat Kelas</h6>
                                        <div class="text-success fw-bold fs-20">{{ $rankData['rank'] }} / {{ $rankData['total'] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="avatar-md mx-auto mb-2">
                                            <span class="avatar-title bg-info text-white rounded-circle">
                                                <i class="ri-calendar-check-line"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-1">Kehadiran</h6>
                                        <div class="text-info fw-bold fs-20">{{ $attendanceSummary['percentage'] }}%</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <div class="avatar-md mx-auto mb-2">
                                            <span class="avatar-title bg-primary text-white rounded-circle">
                                                <i class="ri-award-line"></i>
                                            </span>
                                        </div>
                                        <h6 class="mb-1">Rata-rata Nilai</h6>
                                        <div class="text-primary fw-bold fs-20">{{ $overallGPA }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-md mx-auto mb-4">
                                    <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                        <i class="ri-file-text-line"></i>
                                    </div>
                                </div>
                                <h5>Belum ada data nilai</h5>
                                <p class="text-muted">Belum ada data nilai untuk semester yang dipilih.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Detail -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detail Kehadiran {{ $semesters[$semester] }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-primary text-white rounded-circle">{{ $attendanceSummary['total'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Total Hari</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-success text-white rounded-circle">{{ $attendanceSummary['present'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Hadir</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-danger text-white rounded-circle">{{ $attendanceSummary['absent'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Tidak Hadir</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-warning text-white rounded-circle">{{ $attendanceSummary['late'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Terlambat</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-info text-white rounded-circle">{{ $attendanceSummary['sick'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Sakit</h6>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <div class="avatar-sm mx-auto mb-2">
                                        <span class="avatar-title bg-secondary text-white rounded-circle">{{ $attendanceSummary['permission'] }}</span>
                                    </div>
                                    <h6 class="mb-0">Izin</h6>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Performance Indicator -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="text-center">
                                    @php
                                        $attendanceLevel = match(true) {
                                            $attendanceSummary['percentage'] >= 95 => ['level' => 'Sangat Baik', 'color' => 'success'],
                                            $attendanceSummary['percentage'] >= 85 => ['level' => 'Baik', 'color' => 'info'],
                                            $attendanceSummary['percentage'] >= 75 => ['level' => 'Cukup', 'color' => 'warning'],
                                            default => ['level' => 'Perlu Perbaikan', 'color' => 'danger']
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $attendanceLevel['color'] }}-subtle text-{{ $attendanceLevel['color'] }} fs-14 px-3 py-2">
                                        {{ $attendanceLevel['level'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Performance Notes -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Catatan Akademik</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">Kelebihan:</h6>
                                <ul class="list-unstyled">
                                    @if($overallGPA >= 80)
                                        <li><i class="ri-check-line text-success me-2"></i>Prestasi akademik yang baik</li>
                                    @endif
                                    @if($attendanceSummary['percentage'] >= 90)
                                        <li><i class="ri-check-line text-success me-2"></i>Kehadiran yang sangat baik</li>
                                    @endif
                                    @if($gradeDistribution['A'] > 0)
                                        <li><i class="ri-check-line text-success me-2"></i>Memiliki {{ $gradeDistribution['A'] }} mata pelajaran dengan nilai A</li>
                                    @endif
                                    @if($rankData['rank'] <= ceil($rankData['total'] * 0.3))
                                        <li><i class="ri-check-line text-success me-2"></i>Masuk dalam 30% terbaik di kelas</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning">Area yang Perlu Diperbaiki:</h6>
                                <ul class="list-unstyled">
                                    @if($gradeDistribution['E'] > 0)
                                        <li><i class="ri-error-warning-line text-warning me-2"></i>{{ $gradeDistribution['E'] }} mata pelajaran dengan nilai E</li>
                                    @endif
                                    @if($gradeDistribution['D'] > 0)
                                        <li><i class="ri-error-warning-line text-warning me-2"></i>{{ $gradeDistribution['D'] }} mata pelajaran dengan nilai D</li>
                                    @endif
                                    @if($attendanceSummary['percentage'] < 85)
                                        <li><i class="ri-error-warning-line text-warning me-2"></i>Tingkatkan kehadiran di kelas</li>
                                    @endif
                                    @if($attendanceSummary['late'] > 5)
                                        <li><i class="ri-error-warning-line text-warning me-2"></i>Kurangi keterlambatan ({{ $attendanceSummary['late'] }} kali terlambat)</li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- Teacher's Comment Section (Placeholder) -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="mb-2">Catatan Wali Kelas:</h6>
                            <p class="text-muted mb-0">
                                @if($overallGPA >= 80 && $attendanceSummary['percentage'] >= 90)
                                    Siswa menunjukkan prestasi yang sangat baik dalam akademik dan kedisiplinan. Pertahankan prestasi ini dan terus tingkatkan kemampuan.
                                @elseif($overallGPA >= 70 && $attendanceSummary['percentage'] >= 80)
                                    Siswa menunjukkan prestasi yang baik. Tingkatkan lagi usaha belajar dan kehadiran untuk mencapai hasil yang lebih optimal.
                                @elseif($overallGPA >= 60)
                                    Siswa perlu meningkatkan usaha belajar. Manfaatkan waktu belajar dengan lebih efektif dan jangan ragu untuk bertanya kepada guru.
                                @else
                                    Siswa perlu bimbingan khusus untuk meningkatkan prestasi akademik. Disarankan untuk mengikuti program remedial dan konsultasi dengan guru mata pelajaran.
                                @endif
                            </p>
                        </div>

                        <!-- Print/Export Actions -->
                        <div class="text-center mt-4">
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" wire:click="exportReportCard" class="btn btn-primary">
                                    <i class="ri-download-line me-1"></i> Unduh Rapor PDF
                                </button>
                                <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="ri-printer-line me-1"></i> Cetak Rapor
                                </button>
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
.grade-card {
    transition: all 0.3s ease;
}

.grade-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.component-breakdown {
    font-size: 0.875rem;
}

.table-card .table td {
    vertical-align: middle;
}

.progress {
    background-color: #f1f3f4;
}

.achievement-card {
    transition: all 0.2s ease;
}

.achievement-card:hover {
    transform: translateY(-2px);
}

.report-card-table th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.report-card-table td {
    border-color: #dee2e6;
}

@media print {
    .btn, .card-header {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .container-fluid {
        padding: 0 !important;
    }
}

.bg-orange {
    background-color: #fd7e14 !important;
}
</style>
@endpush

@push('script')
<script>
document.addEventListener('livewire:initialized', () => {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-refresh every 5 minutes
    setInterval(() => {
        @this.call('$refresh');
    }, 300000);
});

// Handle print functionality
window.addEventListener('beforeprint', () => {
    document.title = 'Rapor - {{ $student->full_name }} - {{ $semesters[$semester] }}';
});
</script>
@endpush
