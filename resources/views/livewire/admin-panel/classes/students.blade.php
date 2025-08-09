<div>
    <!-- Class Info Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">{{ $class->class_name }}</h4>
                            <div class="text-muted">
                                <span class="badge bg-info-subtle text-info me-2">Kelas {{ $class->grade_level }}</span>
                                <span class="me-3">Tahun Ajaran: {{ $class->academicYear->academic_year ?? '-' }}</span>
                                <span class="me-3">Wali Kelas: {{ $class->homeroomTeacher->full_name ?? '-' }}</span>
                                <span>Kapasitas: {{ $class->getActiveStudentsCount() }}/{{ $class->capacity }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                                <i class="ri-arrow-left-line me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Available Students -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Siswa Tersedia</h5>
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary">{{ count($availableStudents) }} siswa</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 border-bottom border-bottom-dashed">
                    <div class="row g-3 p-3">
                        <div class="col-md-8">
                            <div class="search-box">
                                <input type="text" wire:model.live.debounce.150ms="search" class="form-control search border py-2" placeholder="Cari siswa...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                                <i class="ri-refresh-line align-bottom me-1"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @if($availableStudents->count() > 0)
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model.live="selectAll" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Pilih Semua
                                </label>
                            </div>
                        </div>

                        <div class="list-group">
                            @foreach($availableStudents as $student)
                                <div class="list-group-item">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model.live="selectedStudents" value="{{ $student->id }}" id="student_{{ $student->id }}">
                                        <label class="form-check-label w-100" for="student_{{ $student->id }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $student->full_name }}</h6>
                                                    <small class="text-muted">NIS: {{ $student->nis }}</small>
                                                </div>
                                                <span class="badge bg-success-subtle text-success">Tersedia</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3">
                            <button type="button" wire:click="assignStudents" class="btn btn-primary w-100"
                                    @disabled(count($selectedStudents) === 0)>
                                <i class="ri-user-add-line me-1"></i>
                                Tugaskan ke Kelas ({{ count($selectedStudents) }} siswa)
                            </button>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                    <i class="ri-user-search-line"></i>
                                </div>
                            </div>
                            <h5>Tidak ada siswa tersedia</h5>
                            <p class="text-muted">Semua siswa sudah ditugaskan ke kelas atau tidak ada yang sesuai dengan pencarian.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Current Students -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">Siswa di Kelas</h5>
                        <div class="flex-shrink-0">
                            <span class="badge bg-success">{{ $class->getActiveStudentsCount() }} siswa</span>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @if($currentStudents->count() > 0)
                        <div class="list-group">
                            @foreach($currentStudents as $classStudent)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $classStudent->student->full_name }}</h6>
                                            <div class="text-muted small">
                                                <div>NIS: {{ $classStudent->student->nis }}</div>
                                                <div>Bergabung: {{ $classStudent->class_entry_date?->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button type="button"
                                                    wire:click="removeStudentConfirm({{ $classStudent->student->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Keluarkan dari kelas">
                                                <i class="ri-user-unfollow-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($currentStudents->hasPages())
                            <div class="mt-3">
                                {{ $currentStudents->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                    <i class="ri-user-line"></i>
                                </div>
                            </div>
                            <h5>Belum ada siswa</h5>
                            <p class="text-muted">Kelas ini belum memiliki siswa. Pilih siswa dari daftar sebelah kiri untuk menugaskannya ke kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
