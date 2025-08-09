<div>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card student-header-card bg-danger text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title text-white mb-2">Dashboard Siswa</h4>
                            <div class="text-white-50">
                                <span class="me-3"><i class="ri-user-3-line me-1"></i>{{ $student->full_name }}</span>
                                @if($currentClass)
                                    <span class="me-3"><i class="ri-building-2-line me-1"></i>{{ $currentClass->class->class_name }}</span>
                                    <span><i class="ri-calendar-line me-1"></i>{{ $currentAcademicYear->academic_year }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex flex-column">
                                <span class="text-white-50 small">NIS: {{ $student->nis }}</span>
                                @if($student->national_student_id)
                                    <span class="text-white-50 small">NISN: {{ $student->national_student_id }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$currentClass)
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
                        <p class="text-muted">Anda belum terdaftar di kelas untuk tahun akademik aktif. Silakan hubungi admin sekolah.</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card border-0">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-success text-white rounded-circle fs-24">
                                <i class="ri-calendar-check-line"></i>
                            </span>
                        </div>
                        <h3 class="text-success mb-1">{{ $monthlyAttendanceStats['percentage'] }}%</h3>
                        <p class="text-muted mb-0">Kehadiran Bulan Ini</p>
                        <small class="text-muted">{{ $monthlyAttendanceStats['present'] + $monthlyAttendanceStats['late'] }}/{{ $monthlyAttendanceStats['total'] }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card border-0">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-primary text-white rounded-circle fs-24">
                                <i class="ri-award-line"></i>
                            </span>
                        </div>
                        <h3 class="text-primary mb-1">{{ $monthlyGradeAverage }}</h3>
                        <p class="text-muted mb-0">Rata-rata Bulan Ini</p>
                        <small class="text-muted">{{ $this->getGradeLetter($monthlyGradeAverage) }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card border-0">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-info text-white rounded-circle fs-24">
                                <i class="ri-trophy-line"></i>
                            </span>
                        </div>
                        <h3 class="text-info mb-1">{{ $semesterGradeAverage }}</h3>
                        <p class="text-muted mb-0">Rata-rata Semester</p>
                        <small class="text-muted">{{ $this->getGradeLetter($semesterGradeAverage) }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dashboard-card border-0">
                    <div class="card-body text-center">
                        <div class="avatar-md mx-auto mb-3">
                            <span class="avatar-title bg-warning text-white rounded-circle fs-24">
                                <i class="ri-book-open-line"></i>
                            </span>
                        </div>
                        <h3 class="text-warning mb-1">{{ $todaySchedules->count() }}</h3>
                        <p class="text-muted mb-0">Pelajaran Hari Ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedule and Attendance -->
        <div class="row mb-4">
            <!-- Today's Schedule -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-calendar-line me-2"></i>Jadwal Hari Ini
                            </h5>
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary">{{ now()->format('l, d F Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($todaySchedules->count() > 0)
                            @foreach($todaySchedules as $schedule)
                                @php
                                    $currentTime = now();
                                    // $scheduleStart = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time);
                                    // $scheduleEnd = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time);
                                    $isOngoing = $currentTime->format('H:i:s') >= $schedule->start_time && $currentTime->format('H:i:s') <= $schedule->end_time;
                                    $isPast = $currentTime->format('H:i:s') > $schedule->end_time;
                                    $isUpcoming = $currentTime->format('H:i:s') < $schedule->start_time;
                                @endphp
                                <div class="schedule-item {{ $isOngoing ? 'bg-success-subtle' : ($isPast ? 'bg-light' : '') }} rounded mb-3">
                                    <div class="d-flex align-items-center p-3">
                                        <div class="avatar-sm me-3">
                                            <span class="avatar-title {{ $isOngoing ? 'bg-success' : ($isPast ? 'bg-secondary' : 'bg-primary') }} text-white rounded-circle">
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
                                            <div class="{{ $isOngoing ? 'text-success' : 'text-primary' }} fw-medium">
                                                {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }}
                                            </div>
                                            @if($isOngoing)
                                                <small class="badge bg-success">Sedang Berlangsung</small>
                                            @elseif($isPast)
                                                <small class="badge bg-secondary">Selesai</small>
                                            @else
                                                <small class="badge bg-primary">Akan Datang</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ri-calendar-line fs-24 mb-2 d-block"></i>
                                    Tidak ada jadwal hari ini
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Today's Attendance -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ri-flashlight-line me-2"></i>Aksi Cepat
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.schedules.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="ri-calendar-2-line me-1"></i>Lihat Jadwal Lengkap
                            </a>
                            <a href="{{ route('student.grades.index') }}" class="btn btn-outline-success btn-sm">
                                <i class="ri-award-line me-1"></i>Lihat Semua Nilai
                            </a>
                            <a href="{{ route('student.attendances.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="ri-calendar-check-line me-1"></i>Riwayat Absensi
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Today's Attendance Summary -->
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h6 class="card-title mb-0">
                            <i class="ri-user-check-line me-2"></i>Absensi Hari Ini
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($todayAttendance && $todayAttendance->count() > 0)
                            @foreach($todayAttendance as $attendance)
                                @php
                                    $statusBadge = $this->getAttendanceStatusBadge($attendance->attendance_status);
                                @endphp
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-xs me-2">
                                        <span class="avatar-title {{ str_replace('-subtle', '', $statusBadge['class']) }} text-white rounded-circle">
                                            <i class="{{ $statusBadge['icon'] }}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 small">{{ $attendance->schedule->teacherSubject->subject->subject_name }}</p>
                                        <small class="text-muted">
                                            {{ $statusBadge['text'] }}
                                            @if($attendance->check_in_time)
                                                - {{ $attendance->check_in_time->format('H:i') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <div class="text-muted">
                                    <i class="ri-user-check-line fs-18 mb-2 d-block"></i>
                                    <small>Belum ada absensi hari ini</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Grades and Subject Summary -->
        <div class="row mb-4">
            <!-- Recent Grades -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">
                                <i class="ri-award-line me-2"></i>Nilai Terbaru
                            </h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('student.grades.index') }}" class="btn btn-sm btn-outline-primary">
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
                                                            <h6 class="mb-0 small">{{ $grade->teacherSubject->subject->subject_name }}</h6>
                                                            <small class="text-muted">{{ $grade->teacherSubject->teacher->full_name }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-medium small">{{ $grade->gradeComponent->component_name }}</span>
                                                    <small class="text-muted d-block">{{ $grade->gradeComponent->weight_percentage }}%</small>
                                                </td>
                                                <td class="text-center">
                                                    <h6 class="mb-0 text-primary">{{ number_format($grade->grade_value, 1) }}</h6>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $gradeLetter = $this->getGradeLetter($grade->grade_value);
                                                        $gradeBadgeClass = $this->getGradeBadgeClass($grade->grade_value);
                                                    @endphp
                                                    <span class="badge {{ $gradeBadgeClass }}">{{ $gradeLetter }}</span>
                                                </td>
                                                <td>
                                                    <div class="text-muted small">{{ $grade->input_date->format('d/m/Y') }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
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
    @endif
</div>
