<div>
    <!-- Header with Teacher Info -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-2">Jadwal Mengajar Saya</h4>
                            <div class="text-muted">
                                <span class="me-3"><i class="ri-user-3-line me-1"></i>{{ $teacher->full_name }}</span>
                                <span class="me-3"><i class="ri-id-card-line me-1"></i>ID: {{ $teacher->employee_id }}</span>
                                <span><i class="ri-calendar-line me-1"></i>{{ now()->format('d F Y') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <div class="text-center">
                                    <div class="text-success fw-bold fs-18">{{ $weeklyStats['total_schedules'] }}</div>
                                    <small class="text-muted">Total Jadwal</small>
                                </div>
                                <div class="text-center ms-3">
                                    <div class="text-primary fw-bold fs-18">{{ $weeklyStats['today_schedules'] }}</div>
                                    <small class="text-muted">Hari Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current & Next Schedule Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-header bg-success-subtle">
                    <h6 class="card-title mb-0 text-success">
                        <i class="ri-play-circle-line me-1"></i>Sedang Berlangsung
                    </h6>
                </div>
                <div class="card-body">
                    @if($currentSchedule)
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-success text-white rounded-circle">
                                        <i class="ri-book-open-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $currentSchedule->teacherSubject->subject->subject_name }}</h6>
                                <p class="text-muted mb-1">Kelas: {{ $currentSchedule->teacherSubject->class->class_name }}</p>
                                <small class="text-muted">
                                    {{ $currentSchedule->start_time->format('H:i') }} - {{ $currentSchedule->end_time->format('H:i') }}
                                    @if($currentSchedule->classroom)
                                        | {{ $currentSchedule->classroom }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-2">
                            <i class="ri-time-line text-muted fs-24 mb-2"></i>
                            <p class="text-muted mb-0">Tidak ada jadwal yang sedang berlangsung</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-header bg-warning-subtle">
                    <h6 class="card-title mb-0 text-warning">
                        <i class="ri-time-line me-1"></i>Jadwal Selanjutnya
                    </h6>
                </div>
                <div class="card-body">
                    @if($nextSchedule)
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm">
                                    <span class="avatar-title bg-warning text-white rounded-circle">
                                        <i class="ri-book-line"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $nextSchedule->teacherSubject->subject->subject_name }}</h6>
                                <p class="text-muted mb-1">Kelas: {{ $nextSchedule->teacherSubject->class->class_name }}</p>
                                <small class="text-muted">
                                    {{ $nextSchedule->start_time->format('H:i') }} - {{ $nextSchedule->end_time->format('H:i') }}
                                    @if($nextSchedule->classroom)
                                        | {{ $nextSchedule->classroom }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-2">
                            <i class="ri-check-double-line text-muted fs-24 mb-2"></i>
                            <p class="text-muted mb-0">Tidak ada jadwal lagi hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Day Selector & Stats Sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pilih Hari</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($days as $dayKey => $dayName)
                            <a href="javascript:void(0)"
                               wire:click="$set('selectedDay', '{{ $dayKey }}')"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $selectedDay == $dayKey ? 'active' : '' }}">
                                {{ $dayName }}
                                @if(isset($allSchedules[$dayKey]))
                                    <span class="badge bg-primary rounded-pill">{{ $allSchedules[$dayKey]->count() }}</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">0</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Weekly Summary -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Ringkasan Mengajar</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-primary text-white rounded-circle fs-16">
                                        {{ $weeklyStats['total_schedules'] }}
                                    </span>
                                </div>
                                <h6 class="mb-0">Jadwal</h6>
                                <small class="text-muted">Mingguan</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-info text-white rounded-circle fs-16">
                                        {{ $weeklyStats['total_classes'] }}
                                    </span>
                                </div>
                                <h6 class="mb-0">Kelas</h6>
                                <small class="text-muted">Diampu</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-center">
                                <div class="avatar-sm mx-auto mb-2">
                                    <span class="avatar-title bg-success text-white rounded-circle fs-16">
                                        {{ $weeklyStats['total_subjects'] }}
                                    </span>
                                </div>
                                <h6 class="mb-0">Mata Pelajaran</h6>
                                <small class="text-muted">Diajarkan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule List -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            Jadwal {{ $days[$selectedDay] }}
                        </h5>
                        <div class="flex-shrink-0">
                            <span class="badge bg-primary">{{ $schedules->count() }} jadwal</span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if($schedules->count() > 0)
                        <div class="timeline-container">
                            @foreach($schedules as $schedule)
                                <div class="schedule-item border rounded p-3 mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="time-block text-center">
                                                <div class="fw-bold text-primary fs-16">{{ $schedule->start_time->format('H:i') }}</div>
                                                <small class="text-muted">{{ $schedule->end_time->format('H:i') }}</small>
                                                <div class="mt-1">
                                                    <small class="badge bg-light text-dark">
                                                        {{ $schedule->end_time->diffInMinutes($schedule->start_time) }} menit
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="schedule-info">
                                                <h6 class="mb-1 text-dark">
                                                    {{ $schedule->teacherSubject->subject->subject_name }}
                                                </h6>
                                                <div class="text-muted mb-1">
                                                    <i class="ri-building-2-line me-1"></i>
                                                    <span class="fw-medium">{{ $schedule->teacherSubject->class->class_name }}</span>
                                                    <span class="ms-2 badge bg-info-subtle text-info">
                                                        Kelas {{ $schedule->teacherSubject->class->grade_level }}
                                                    </span>
                                                </div>
                                                @if($schedule->classroom)
                                                    <div class="text-muted mb-1">
                                                        <i class="ri-door-line me-1"></i>
                                                        {{ $schedule->classroom }}
                                                    </div>
                                                @endif
                                                @if($schedule->notes)
                                                    <div class="text-muted">
                                                        <small><i class="ri-information-line me-1"></i>{{ $schedule->notes }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-lg mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle fs-24">
                                    <i class="ri-calendar-line"></i>
                                </div>
                            </div>
                            <h5>Tidak ada jadwal mengajar</h5>
                            <p class="text-muted">Anda tidak memiliki jadwal mengajar pada hari {{ $days[$selectedDay] }}.</p>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="ri-information-line me-1"></i>
                                    Jika ada kesalahan, silakan hubungi administrator sekolah.
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Schedule Summary (if not today is selected) -->
    @if($selectedDay !== strtolower(now()->format('l')) && $todaySchedules->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary-subtle">
                        <h6 class="card-title mb-0 text-primary">
                            <i class="ri-calendar-check-line me-1"></i>Jadwal Hari Ini ({{ $days[strtolower(now()->format('l'))] ?? 'Hari Ini' }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($todaySchedules as $todaySchedule)
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-center p-2 border rounded">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="text-primary fw-bold">{{ $todaySchedule->start_time->format('H:i') }}</div>
                                            <small class="text-muted">{{ $todaySchedule->end_time->format('H:i') }}</small>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fs-14">{{ $todaySchedule->teacherSubject->subject->subject_name }}</h6>
                                            <small class="text-muted">{{ $todaySchedule->teacherSubject->class->class_name }}</small>
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
</div>

@push('style')
<style>
.schedule-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef !important;
}

.schedule-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-color: #dee2e6 !important;
}

.time-block {
    border-right: 2px solid #e9ecef;
    padding-right: 1rem;
}

.list-group-item.active {
    background-color: var(--vz-primary);
    border-color: var(--vz-primary);
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.card-header.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.card-header.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.card-header.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.border-success {
    border-color: #198754 !important;
}

.border-warning {
    border-color: #ffc107 !important;
}

.border-primary {
    border-color: #0d6efd !important;
}
</style>
@endpush
