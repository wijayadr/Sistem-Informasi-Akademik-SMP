<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-2">Nilai Saya</h4>
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
                                    <div class="text-end">
                                        <div class="text-primary fw-bold fs-18">{{ $gradeStats['average'] }}</div>
                                        <small class="text-muted">Rata-rata Nilai</small>
                                    </div>
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
        <!-- Subject Grades Summary -->
        @if($subjectGrades->count() > 0)
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="ri-book-open-line me-2"></i>Nilai Per Mata Pelajaran
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($subjectGrades as $subjectGrade)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border h-100">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">{{ $subjectGrade['subject_name'] }}</h6>
                                                    <span class="badge {{ $subjectGrade['grade_class'] }} fs-6">{{ $subjectGrade['grade_letter'] }}</span>
                                                </div>
                                                <div class="text-center mb-3">
                                                    <div class="text-primary fw-bold fs-24">{{ number_format($subjectGrade['final_grade'], 1) }}</div>
                                                    <small class="text-muted">Nilai Akhir</small>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted fw-medium">Komponen Nilai:</small>
                                                    @foreach($subjectGrade['components'] as $component)
                                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                                            <small class="text-muted">{{ $component['component'] }}</small>
                                                            <div class="text-end">
                                                                <span class="fw-medium">{{ number_format($component['grade'], 1) }}</span>
                                                                <small class="text-muted">({{ $component['weight'] }}%)</small>
                                                            </div>
                                                        </div>
                                                    @endforeach
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

        <!-- Grade Statistics -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistik Nilai</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-primary text-white rounded-circle">{{ $gradeStats['total'] }}</span>
                                </div>
                                <h6 class="mb-0">Total Nilai</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-info text-white rounded-circle">{{ $gradeStats['average'] }}</span>
                                </div>
                                <h6 class="mb-0">Rata-rata</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-success text-white rounded-circle">{{ $gradeStats['highest'] }}</span>
                                </div>
                                <h6 class="mb-0">Tertinggi</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-danger text-white rounded-circle">{{ $gradeStats['lowest'] }}</span>
                                </div>
                                <h6 class="mb-0">Terendah</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-success text-white rounded-circle">{{ $gradeStats['above_75'] }}</span>
                                </div>
                                <h6 class="mb-0">≥ 75</h6>
                            </div>
                            <div class="col-md-2">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-warning text-white rounded-circle">{{ $gradeStats['below_60'] }}</span>
                                </div>
                                <h6 class="mb-0">< 60</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grades List -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Daftar Nilai Detail</h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ $grades->total() }} data</span>
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
                                <select wire:model.live="subject_filter" class="form-select">
                                    <option value="">Semua Mata Pelajaran</option>
                                    @foreach($listsForFields['subjects'] as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select wire:model.live="component_filter" class="form-select">
                                    <option value="">Semua Komponen</option>
                                    @foreach($listsForFields['grade_components'] as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="month" wire:model.live="month_filter" class="form-control" title="Filter Bulan">
                            </div>
                            <div class="col-md-2">
                                <input type="date" wire:model.live="input_date" class="form-control" title="Filter Tanggal">
                            </div>
                            <div class="col-md-1">
                                <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                                    <i class="ri-refresh-line align-bottom"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($grades->count() > 0)
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap" id="gradesTable">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                            <th class="text-uppercase">Tanggal</th>
                                            <th class="text-uppercase">Mata Pelajaran</th>
                                            <th class="text-uppercase">Guru</th>
                                            <th class="text-uppercase">Komponen</th>
                                            <th class="text-uppercase">Nilai</th>
                                            <th class="text-uppercase">Grade</th>
                                            <th class="text-uppercase">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @foreach($grades as $key => $grade)
                                            <tr wire:key="{{ $grade->id }}">
                                                <td class="text-center">
                                                    {{ $grades->firstItem() + $loop->index }}
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ $grade->input_date->format('d/m/Y') }}</div>
                                                </td>
                                                <td>
                                                    <span class="fw-medium">{{ $grade->teacherSubject->subject->subject_name }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-3">
                                                            <span class="avatar-title rounded-circle bg-primary text-white font-size-12">
                                                                {{ substr($grade->teacherSubject->teacher->full_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $grade->teacherSubject->teacher->full_name }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium">{{ $grade->gradeComponent->component_name }}</span>
                                                        <small class="text-muted">Bobot: {{ $grade->gradeComponent->weight_percentage }}%</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <h5 class="mb-0 text-primary">{{ number_format($grade->grade_value, 1) }}</h5>
                                                    </div>
                                                </td>
                                                <td>
                                                    @php
                                                        $gradeLetter = match(true) {
                                                            $grade->grade_value >= 90 => 'A',
                                                            $grade->grade_value >= 80 => 'B',
                                                            $grade->grade_value >= 70 => 'C',
                                                            $grade->grade_value >= 60 => 'D',
                                                            default => 'E'
                                                        };
                                                        $gradeBadgeClass = match(true) {
                                                            $grade->grade_value >= 80 => 'bg-success-subtle text-success',
                                                            $grade->grade_value >= 70 => 'bg-info-subtle text-info',
                                                            $grade->grade_value >= 60 => 'bg-warning-subtle text-warning',
                                                            default => 'bg-danger-subtle text-danger'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $gradeBadgeClass }} fs-6">{{ $gradeLetter }}</span>
                                                </td>
                                                <td>
                                                    @if($grade->notes)
                                                        <span class="text-muted">{{ $grade->notes }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{ $grades->links() }}
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-md mx-auto mb-4">
                                    <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                        <i class="ri-award-line"></i>
                                    </div>
                                </div>
                                <h5>Belum ada data nilai</h5>
                                <p class="text-muted">Belum ada data nilai untuk filter yang dipilih.</p>
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
.grade-card {
    transition: all 0.3s ease;
}

.grade-card:hover {
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

.grade-stats .avatar-title {
    font-weight: 600;
    font-size: 1.1rem;
}

.subject-grade-card {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.subject-grade-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.075);
}

.progress-card {
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.progress-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.075);
}

.progress {
    background-color: #f1f3f4;
}
</style>
@endpush

@push('script')
<script>
document.addEventListener('livewire:initialized', () => {
    // Auto-refresh grade stats every 60 seconds
    setInterval(() => {
        @this.call('$refresh');
    }, 60000);

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
