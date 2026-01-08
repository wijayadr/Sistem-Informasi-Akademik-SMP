<div>
    <!-- Academic Year Selection -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="card-title mb-2">Data Nilai Siswa</h4>
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
                                <input type="date" wire:model.live="input_date" class="form-control" style="width: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($academic_year_id)
        <!-- Grade Statistics -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistik Nilai - {{ \Carbon\Carbon::parse($input_date)->format('d F Y') }}</h5>
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

        <!-- Grades by Class -->
        @if($gradesByClass->count() > 0)
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="ri-group-line me-2"></i>Nilai Per Kelas
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($gradesByClass as $classData)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border h-100 class-grade-card">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="card-title mb-0">{{ $classData['class_name'] }}</h6>
                                                    <span class="badge bg-primary-subtle text-primary">{{ $classData['total'] }} nilai</span>
                                                </div>
                                                <div class="row text-center g-2 mb-3">
                                                    <div class="col-6">
                                                        <div class="text-info">
                                                            <div class="fs-18 fw-semibold">{{ $classData['average'] }}</div>
                                                            <div class="fs-12 text-muted">Rata-rata</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-success">
                                                            <div class="fs-18 fw-semibold">{{ $classData['highest'] }}</div>
                                                            <div class="fs-12 text-muted">Tertinggi</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row text-center g-2">
                                                    <div class="col-6">
                                                        <div class="text-success">
                                                            <div class="fs-16 fw-semibold">{{ $classData['above_75'] }}</div>
                                                            <div class="fs-12 text-muted">≥ 75</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-warning">
                                                            <div class="fs-16 fw-semibold">{{ $classData['below_60'] }}</div>
                                                            <div class="fs-12 text-muted">< 60</div>
                                                        </div>
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

        <!-- Teacher Subjects Overview -->
        @if($teacherSubjects->count() > 0)
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="ri-book-open-line me-2"></i>Mata Pelajaran Aktif
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($teacherSubjects as $teacherSubject)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border h-100 subject-card">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0">{{ $teacherSubject->subject->subject_name }}</h6>
                                                    <span class="badge bg-primary-subtle text-primary">{{ $teacherSubject->class->class_name }}</span>
                                                </div>
                                                <div class="text-muted small mb-3">
                                                    <div><i class="ri-user-line me-1"></i>{{ $teacherSubject->teacher->full_name }}</div>
                                                    <div><i class="ri-calendar-line me-1"></i>{{ $teacherSubject->academicYear->academic_year ?? '-' }}</div>
                                                    @if($teacherSubject->weekly_teaching_hours)
                                                        <div><i class="ri-time-line me-1"></i>{{ $teacherSubject->weekly_teaching_hours }} jam/minggu</div>
                                                    @endif
                                                </div>
                                                <div class="d-flex justify-content-between text-center">
                                                    <div class="flex-fill">
                                                        <div class="text-success fw-semibold">
                                                            {{ $teacherSubject->class->getActiveStudentsCount($this->academic_year_id) }}
                                                        </div>
                                                        <small class="text-muted">Siswa Aktif</small>
                                                    </div>
                                                    <div class="flex-fill">
                                                        <div class="text-info fw-semibold">
                                                            {{ $teacherSubject->studentGrades()->whereDate('input_date', $this->input_date)->count() }}
                                                        </div>
                                                        <small class="text-muted">Nilai Hari Ini</small>
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

        <!-- Grades List -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Daftar Nilai Siswa</h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ $grades->total() }} data</span>
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
                                <select wire:model.live="component_filter" class="form-select">
                                    <option value="">Semua Komponen</option>
                                    @foreach($this->listsForFields['grade_components'] as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 px-3 pb-3">
                            <div class="col-md-2">
                                <select wire:model.live="grade_range_filter" class="form-select">
                                    <option value="">Semua Range</option>
                                    @foreach($this->listsForFields['grade_ranges'] as $key => $name)
                                        <option value="{{ $key }}">{{ $name }}</option>
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
                        @if($grades->count() > 0)
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap" id="gradesTable">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                            <th class="text-uppercase">Siswa</th>
                                            <th class="text-uppercase">Kelas</th>
                                            <th class="text-uppercase">Mata Pelajaran</th>
                                            <th class="text-uppercase">Guru</th>
                                            <th class="text-uppercase">Komponen</th>
                                            <th class="text-uppercase">Nilai</th>
                                            <th class="text-uppercase">Grade</th>
                                            <th class="text-uppercase">Tanggal Input</th>
                                            <th class="text-uppercase">Input Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        @foreach($grades as $key => $grade)
                                            <tr wire:key="{{ $grade->id }}">
                                                <td class="text-center">
                                                    {{ $grades->firstItem() + $loop->index }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-3">
                                                            <span class="avatar-title rounded-circle bg-primary text-white font-size-12">
                                                                {{ substr($grade->student->full_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $grade->student->full_name }}</h6>
                                                            <small class="text-muted">NIS: {{ $grade->student->nis }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info">{{ $grade->teacherSubject->class->class_name }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-medium">{{ $grade->teacherSubject->subject->subject_name }}</span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <span class="fw-medium">{{ $grade->teacherSubject->teacher->full_name }}</span>
                                                        <small class="text-muted d-block">{{ $grade->teacherSubject->teacher->employee_id ?? '-' }}</small>
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
                                                        $gradeLetter = \App\Livewire\AdminPanel\Grades\Index::getGradeLetterStatic($grade->grade_value);
                                                        $gradeBadgeClass = \App\Livewire\AdminPanel\Grades\Index::getGradeBadgeClassStatic($grade->grade_value);
                                                    @endphp
                                                    <span class="badge {{ $gradeBadgeClass }} fs-6">{{ $gradeLetter }}</span>
                                                </td>
                                                <td>
                                                    <div class="text-muted small">
                                                        {{ $grade->input_date->format('d/m/Y') }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-muted small">
                                                        <div>{{ $grade->inputTeacher->full_name }}</div>
                                                        <div>{{ $grade->created_at->format('d/m/Y H:i') }}</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <x-pagination :items="$grades" />
                        @else
                            <div class="text-center py-5">
                                <div class="avatar-md mx-auto mb-4">
                                    <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                        <i class="ri-award-line"></i>
                                    </div>
                                </div>
                                <h5>Belum ada data nilai</h5>
                                <p class="text-muted">Belum ada data nilai untuk tanggal yang dipilih.</p>
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
                        <p class="text-muted">Silakan pilih tahun akademik terlebih dahulu untuk melihat data nilai siswa.</p>
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

.class-grade-card {
    transition: all 0.2s ease;
}

.class-grade-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.075);
}

.subject-card {
    transition: all 0.2s ease;
}

.subject-card:hover {
    border-color: #198754;
    box-shadow: 0 0.125rem 0.25rem rgba(25, 135, 84, 0.075);
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

.academic-year-selector {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0.5rem;
}

.fs-18 {
    font-size: 1.125rem;
}

.fs-16 {
    font-size: 1rem;
}

.fs-12 {
    font-size: 0.75rem;
}

.overview-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
}

.stats-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.5rem;
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

    // Handle grade value display formatting
    document.addEventListener('DOMContentLoaded', function() {
        // Format displayed grade values
        const gradeElements = document.querySelectorAll('.grade-value');
        gradeElements.forEach(function(element) {
            const value = parseFloat(element.textContent);
            if (!isNaN(value)) {
                element.textContent = value.toFixed(1);
            }
        });
    });

    // Real-time clock for better admin experience
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const clockElement = document.getElementById('admin-clock');
        if (clockElement) {
            clockElement.textContent = timeString;
        }
    }

    // Update clock every second
    setInterval(updateClock, 1000);
    updateClock(); // Initial call
});

// Enhanced filter interactions
Livewire.on('filters-updated', () => {
    // Smooth scroll to results after filter update
    const resultsSection = document.getElementById('gradesTable');
    if (resultsSection) {
        resultsSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
});
</script>
@endpush
