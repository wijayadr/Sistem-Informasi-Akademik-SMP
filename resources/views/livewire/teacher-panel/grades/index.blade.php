<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">Penilaian Siswa</h4>
                            <div class="text-muted">
                                <span class="me-3">Tanggal: {{ \Carbon\Carbon::parse($input_date)->format('d F Y') }}</span>
                                <span class="me-3">Guru: {{ auth()->user()->teacher->full_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <input type="date" wire:model.live="input_date" class="form-control d-inline-block w-auto me-2">
                            <button type="button" wire:click="showGradeModal" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="ri-add-line me-1"></i> Tambah Nilai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Classes and Subjects -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            <i class="ri-book-open-line me-2"></i>Mata Pelajaran Yang Diajar
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    @if($teacherSubjects->count() > 0)
                        <div class="row">
                            @foreach($teacherSubjects as $teacherSubject)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border h-100">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">{{ $teacherSubject->subject->subject_name }}</h6>
                                                <span class="badge bg-primary-subtle text-primary">{{ $teacherSubject->class->class_name }}</span>
                                            </div>
                                            <div class="text-muted small mb-3">
                                                <div><i class="ri-calendar-line me-1"></i>{{ $teacherSubject->academicYear->academic_year ?? '-' }}</div>
                                                <div><i class="ri-user-line me-1"></i>{{ $teacherSubject->class->getActiveStudentsCount() }} siswa</div>
                                                @if($teacherSubject->weekly_teaching_hours)
                                                    <div><i class="ri-time-line me-1"></i>{{ $teacherSubject->weekly_teaching_hours }} jam/minggu</div>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                        wire:click="$set('form.teacher_subject_id', '{{ $teacherSubject->id }}')"
                                                        class="btn btn-sm btn-outline-primary flex-fill"
                                                        data-bs-toggle="modal" data-bs-target="#bulkGradeModal">
                                                    <i class="ri-file-add-line me-1"></i>Buat Template
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <div class="text-muted">
                                <i class="ri-book-line fs-24 mb-2"></i>
                                <p class="mb-0">Belum ada mata pelajaran yang diajar</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

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

    <!-- Grades List -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Daftar Nilai Siswa</h5>
                        @if(count($selectedGrades) > 0)
                            <div class="flex-shrink-0 me-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="number" wire:model="bulkGradeValue" class="form-control form-control-sm"
                                           placeholder="Nilai" style="width: 80px;" min="0" max="100" step="0.01">
                                    <button type="button" wire:click="bulkUpdateGrade" class="btn btn-sm btn-warning">
                                        <i class="ri-edit-line me-1"></i>Update Nilai
                                    </button>
                                </div>
                            </div>
                        @endif
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary">{{ $grades->total() }} data</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 border-bottom border-bottom-dashed">
                    <div class="row g-3 p-3">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
                            <select wire:model.live="subject_filter" class="form-select">
                                <option value="">Semua Mata Pelajaran</option>
                                @foreach($this->listsForFields['subjects'] as $id => $name)
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
                                        <th style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" wire:model.live="selectAll" id="selectAllGrades">
                                                <label class="form-check-label" for="selectAllGrades"></label>
                                            </div>
                                        </th>
                                        <th class="text-center text-uppercase" style="width: 60px;">No</th>
                                        <th class="text-uppercase">Siswa</th>
                                        <th class="text-uppercase">Kelas</th>
                                        <th class="text-uppercase">Mata Pelajaran</th>
                                        <th class="text-uppercase">Komponen</th>
                                        <th class="text-uppercase">Nilai</th>
                                        <th class="text-uppercase">Grade</th>
                                        <th class="text-uppercase">Tanggal Input</th>
                                        <th class="text-uppercase" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    @foreach($grades as $key => $grade)
                                        <tr wire:key="{{ $grade->id }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" wire:model.live="selectedGrades" value="{{ $grade->id }}" id="grade_{{ $grade->id }}">
                                                    <label class="form-check-label" for="grade_{{ $grade->id }}"></label>
                                                </div>
                                            </td>
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
                                                    $gradeLetter = \App\Livewire\Forms\StudentGradeForm::getGradeLetterStatic($grade->grade_value);
                                                    $gradeBadgeClass = \App\Livewire\Forms\StudentGradeForm::getGradeBadgeClassStatic($grade->grade_value);
                                                @endphp
                                                <span class="badge {{ $gradeBadgeClass }} fs-6">{{ $gradeLetter }}</span>
                                            </td>
                                            <td>
                                                <div class="text-muted small">
                                                    {{ $grade->input_date->format('d/m/Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                <ul class="list-inline hstack gap-2 mb-0">
                                                    <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Edit">
                                                        <a href="javascript:void(0)" wire:click="editGrade({{ $grade->id }})" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#showModal">
                                                            <i class="ri-pencil-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Hapus">
                                                        <a href="javascript:void(0)" class="text-danger d-inline-block remove-item-btn" wire:click="deleteGradeConfirm({{ $grade->id }})">
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

                        <x-pagination :items="$grades" />
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                    <i class="ri-award-line"></i>
                                </div>
                            </div>
                            <h5>Belum ada data nilai</h5>
                            <p class="text-muted">Belum ada data nilai untuk tanggal yang dipilih. Klik tombol "Tambah Nilai" atau gunakan fitur "Buat Template" pada mata pelajaran yang diajar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Modal -->
    <x-modal name="showModal" :title="$editing ? 'Edit Nilai Siswa' : 'Tambah Nilai Siswa'" :maxWidth="'xl'">
        <form wire:submit.prevent="saveGrade">
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <x-input-label for="teacher_subject_id" value="Mata Pelajaran" required/>
                        <select wire:model.live="form.teacher_subject_id" id="teacher_subject_id" class="form-select @error('form.teacher_subject_id') is-invalid @enderror">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($this->listsForFields['teacher_subjects'] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.teacher_subject_id')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="student_id" value="Siswa" required/>
                        <select wire:model.live="form.student_id" id="student_id" class="form-select @error('form.student_id') is-invalid @enderror">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($this->listsForFields['students'] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.student_id')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="grade_component_id" value="Komponen Nilai" required/>
                        <select wire:model="form.grade_component_id" id="grade_component_id" class="form-select @error('form.grade_component_id') is-invalid @enderror">
                            <option value="">-- Pilih Komponen --</option>
                            @foreach($this->listsForFields['grade_components'] as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.grade_component_id')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="grade_value" value="Nilai" required/>
                        <input type="number" wire:model.live="form.grade_value" id="grade_value"
                                class="form-control @error('form.grade_value') is-invalid @enderror"
                                placeholder="0-100" min="0" max="100" step="0.01">
                        <x-input-error :messages="$errors->get('form.grade_value')"/>
                    </div>

                    <div class="col-md-6">
                        <x-input-label for="input_date" value="Tanggal Input" required/>
                        <input type="date" wire:model="form.input_date" id="input_date"
                                class="form-control @error('form.input_date') is-invalid @enderror">
                        <x-input-error :messages="$errors->get('form.input_date')"/>
                    </div>

                    @if($form->grade_value)
                        <div class="col-md-6">
                            <x-input-label value="Preview Grade"/>
                            <div class="d-flex align-items-center gap-3 mt-2">
                                <span class="badge {{ $form->getGradeBadgeClass() }} fs-5">{{ $form->getGradeLetter() }}</span>
                                <span class="text-muted">{{ $form->getGradeCategory() }}</span>
                            </div>
                        </div>
                    @endif

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

    <!-- Bulk Grade Creation Modal -->
    <x-modal name="bulkGradeModal" :title="'Buat Template Nilai'" :maxWidth="'xl'">
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        Template nilai akan membuat data nilai dengan nilai 0 untuk semua siswa di kelas. Anda dapat mengedit nilai secara individual setelahnya.
                    </div>
                </div>

                <div class="col-md-6">
                    <x-input-label for="bulk_teacher_subject_id" value="Mata Pelajaran" required/>
                    <select wire:model="form.teacher_subject_id" id="bulk_teacher_subject_id" class="form-select">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($this->listsForFields['teacher_subjects'] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <x-input-label for="bulk_grade_component_id" value="Komponen Nilai" required/>
                    <select wire:model="form.grade_component_id" id="bulk_grade_component_id" class="form-select">
                        <option value="">-- Pilih Komponen --</option>
                        @foreach($this->listsForFields['grade_components'] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <x-input-label for="bulk_input_date" value="Tanggal Input" required/>
                    <input type="date" wire:model="form.input_date" id="bulk_input_date" class="form-control">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="cancelEdit()">Batal</button>
            <button type="button" wire:click="createBulkGrades" class="btn btn-primary">
                <i class="ri-file-add-line me-1"></i> Buat Template
            </button>
        </div>
    </x-modal>
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

.grade-preview {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.grade-letter {
    font-size: 2rem;
    font-weight: bold;
}

.component-card {
    cursor: pointer;
    transition: all 0.2s ease;
}

.component-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(13, 110, 253, 0.075);
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

    // Handle grade value input formatting
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[type="number"][step="0.01"]')) {
            let value = parseFloat(e.target.value);
            if (value > 100) e.target.value = 100;
            if (value < 0) e.target.value = 0;
        }
    });
});

// Handle modal events
window.addEventListener('closeModal', event => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('showModal'));
    if (modal) {
        modal.hide();
    }
});

// Auto-close bulk modal after successful creation
Livewire.on('bulk-grades-created', () => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('bulkGradeModal'));
    if (modal) {
        modal.hide();
    }
});
</script>
@endpush
