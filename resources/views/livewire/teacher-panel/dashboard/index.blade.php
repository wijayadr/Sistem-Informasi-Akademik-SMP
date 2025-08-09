<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title text-white mb-2">Dashboard Guru</h4>
                            <div class="text-white-50">
                                <span class="me-3"><i class="ri-user-line me-1"></i>{{ $teacher->full_name }}</span>
                                @if($selectedSubject)
                                    <span class="me-3"><i class="ri-book-line me-1"></i>Fokus: {{ $selectedSubject->subject->subject_name }} - {{ $selectedSubject->class->class_name }}</span>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="col-md-4">
                            @if($teacherSubjects->count() > 1)
                                <select wire:model.live="selectedSubjectId" class="form-select">
                                    <option value="">Semua Mata Pelajaran</option>
                                    @foreach($teacherSubjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->subject->subject_name }} - {{ $subject->class->class_name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$currentAcademicYear)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                <i class="ri-calendar-line"></i>
                            </div>
                        </div>
                        <h5>Tidak ada tahun akademik aktif</h5>
                        <p class="text-muted">Belum ada tahun akademik yang sedang berjalan.</p>
                    </div>
                </div>
            </div>
        </div>
    @elseif($teacherSubjects->count() == 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                <i class="ri-book-line"></i>
                            </div>
                        </div>
                        <h5>Belum ada mata pelajaran</h5>
                        <p class="text-muted">Anda belum ditugaskan untuk mengajar mata pelajaran pada tahun akademik {{ $currentAcademicYear->academic_year }}.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-24">
                                <i class="ri-building-2-line"></i>
                            </span>
                        </div>
                        <h3 class="text-primary mb-1">{{ $monthlyTeachingStats['total_classes'] }}</h3>
                        <p class="text-muted mb-0">Kelas Diampu</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-success text-white rounded-circle fs-24">
                                <i class="ri-book-line"></i>
                            </span>
                        </div>
                        <h3 class="text-success mb-1">{{ $monthlyTeachingStats['total_subjects'] }}</h3>
                        <p class="text-muted mb-0">Mata Pelajaran</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-info text-white rounded-circle fs-24">
                                <i class="ri-user-3-line"></i>
                            </span>
                        </div>
                        <h3 class="text-info mb-1">{{ $monthlyTeachingStats['total_students'] }}</h3>
                        <p class="text-muted mb-0">Total Siswa</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-warning text-white rounded-circle fs-24">
                                <i class="ri-calendar-check-line"></i>
                            </span>
                        </div>
                        <h3 class="text-warning mb-1">{{ $monthlyTeachingStats['completed_schedules'] }}</h3>
                        <p class="text-muted mb-0">Jadwal Selesai Bulan Ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedules and Attendance Summary -->
        <div class="row mb-4">
            <!-- Today's Teaching Schedule -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-calendar-line me-2"></i>Jadwal Mengajar Hari Ini
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ $todaySchedules->count() }} jadwal</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($todaySchedules->count() > 0)
                            @foreach($todaySchedules as $schedule)
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3 schedule-item">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-light text-primary rounded-circle">
                                            <i class="ri-book-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $schedule->teacherSubject->subject->subject_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-building-2-line me-1"></i>{{ $schedule->teacherSubject->class->class_name }}
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
                                    Tidak ada jadwal mengajar hari ini
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Today's Attendance Summary -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-user-check-line me-2"></i>Ringkasan Absensi Hari Ini
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-success">{{ $todayAttendanceSummary->count() }} kelas</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($todayAttendanceSummary->count() > 0)
                            @foreach($todayAttendanceSummary as $summary)
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3 attendance-item">
                                    <div class="avatar-sm me-3">
                                        @php
                                            $percentage = $summary['attendance_percentage'];
                                            $bgClass = $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-warning' : 'bg-danger');
                                        @endphp
                                        <span class="avatar-title {{ $bgClass }} text-white rounded-circle">
                                            <i class="ri-user-check-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $summary['schedule']->teacherSubject->subject->subject_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            {{ $summary['schedule']->teacherSubject->class->class_name }} -
                                            {{ $summary['schedule']->start_time->format('H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-medium">{{ $summary['attended_students'] }}/{{ $summary['total_students'] }}</div>
                                        <small class="text-muted">{{ $summary['attendance_percentage'] }}%</small>
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

        <!-- Recent Grades and Upcoming Schedules -->
        <div class="row mb-4">
            <!-- Recent Grades Input -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-award-line me-2"></i>Nilai yang Baru Diinput
                            </h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('teacher.grades.index') }}" class="btn btn-sm btn-outline-primary">
                                    Lihat Semua <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($recentGrades->count() > 0)
                            @foreach($recentGrades as $grade)
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-light text-primary rounded-circle">
                                            <i class="ri-user-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $grade->student->full_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            {{ $grade->teacherSubject->subject->subject_name }} - {{ $grade->gradeComponent->component_name }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-primary fw-medium fs-5">{{ number_format($grade->grade_value, 1) }}</div>
                                        @php
                                            $gradeLetter = $this->getGradeLetter($grade->grade_value);
                                            $gradeBadgeClass = $this->getGradeBadgeClass($grade->grade_value);
                                        @endphp
                                        <span class="badge {{ $gradeBadgeClass }}">{{ $gradeLetter }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="ri-award-line fs-24 mb-2 d-block"></i>
                                    Belum ada nilai yang diinput
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Upcoming Schedules -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-calendar-todo-line me-2"></i>Jadwal Besok
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-info">{{ $upcomingSchedules->count() }} jadwal</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($upcomingSchedules->count() > 0)
                            @foreach($upcomingSchedules as $schedule)
                                <div class="d-flex align-items-center border-bottom pb-3 mb-3 schedule-item">
                                    <div class="avatar-sm me-3">
                                        <span class="avatar-title bg-light text-info rounded-circle">
                                            <i class="ri-book-open-line"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $schedule->teacherSubject->subject->subject_name }}</h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-building-2-line me-1"></i>{{ $schedule->teacherSubject->class->class_name }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-info fw-medium">{{ $schedule->start_time->format('H:i') }}</div>
                                        <small class="text-muted">{{ $schedule->end_time->format('H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="ri-calendar-todo-line fs-24 mb-2 d-block"></i>
                                    Tidak ada jadwal mengajar besok
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm quick-nav-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-24">
                                <i class="ri-calendar-event-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Jadwal Mengajar</h5>
                        <p class="text-muted mb-3">Lihat semua jadwal mengajar</p>
                        <a href="{{ route('teacher.schedules.index') }}" class="btn btn-primary">
                            <i class="ri-calendar-line me-1"></i>Lihat Jadwal
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm quick-nav-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-success text-white rounded-circle fs-24">
                                <i class="ri-calendar-check-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Absensi Siswa</h5>
                        <p class="text-muted mb-3">Kelola absensi siswa</p>
                        <a href="{{ route('teacher.attendances.index') }}" class="btn btn-success">
                            <i class="ri-calendar-check-line me-1"></i>Kelola Absensi
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm quick-nav-card">
                    <div class="card-body text-center p-4">
                        <div class="avatar-lg mx-auto mb-3">
                            <span class="avatar-title bg-warning text-white rounded-circle fs-24">
                                <i class="ri-award-line"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Input Nilai</h5>
                        <p class="text-muted mb-3">Input dan kelola nilai siswa</p>
                        <a href="{{ route('teacher.grades.index') }}" class="btn btn-warning">
                            <i class="ri-edit-line me-1"></i>Input Nilai
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
