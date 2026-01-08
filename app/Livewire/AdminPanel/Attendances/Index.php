<?php

namespace App\Livewire\AdminPanel\Attendances;

use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\Master\Subject;
use App\Models\Academic\Classes;
use App\Models\User\Teacher;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Data Absensi Siswa')]
class Index extends Component
{
    use WithPagination;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $attendance_status_filter = '';

    #[Url()]
    public string $subject_filter = '';

    #[Url()]
    public string $class_filter = '';

    #[Url()]
    public string $teacher_filter = '';

    #[Url()]
    public string $attendance_date = '';

    #[Url()]
    public string $academic_year_id = '';

    public array $listsForFields = [];

    public function mount(): void
    {
        $this->attendance_date = now()->format('Y-m-d');
        $this->setDefaultAcademicYear();
        $this->initListsForFields();
    }

    protected function setDefaultAcademicYear(): void
    {
        if (empty($this->academic_year_id)) {
            $activeAcademicYear = AcademicYear::where('status', 'active')->first();
            $this->academic_year_id = $activeAcademicYear?->id ?? '';
        }
    }

    protected function initListsForFields(): void
    {
        // Get all academic years
        $this->listsForFields['academic_years'] = AcademicYear::orderBy('start_date', 'desc')
            ->pluck('academic_year', 'id');

        // Get all active subjects
        $this->listsForFields['subjects'] = Subject::where('status', 'active')
            ->orderBy('subject_name')
            ->pluck('subject_name', 'id');

        // Get all active classes for selected academic year
        $this->listsForFields['classes'] = Classes::where('status', 'active')
            ->when($this->academic_year_id, function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->orderBy('class_name')
            ->pluck('class_name', 'id');

        // Get all active teachers
        $this->listsForFields['teachers'] = Teacher::orderBy('full_name')->pluck('full_name', 'id');

        $this->listsForFields['attendance_statuses'] = [
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
            'sick' => 'Sakit',
            'permission' => 'Izin',
        ];
    }

    public function updatedAcademicYearId(): void
    {
        $this->initListsForFields();
        $this->resetPage();
        $this->reset(['search', 'attendance_status_filter', 'subject_filter', 'class_filter', 'teacher_filter']);
    }

    public function getAttendances()
    {
        if (empty($this->academic_year_id)) {
            return collect()->paginate(15);
        }

        return StudentAttendance::with([
                'student',
                'schedule.teacherSubject.subject',
                'schedule.teacherSubject.class',
                'schedule.teacherSubject.teacher',
                'inputTeacher'
            ])
            ->whereHas('schedule.teacherSubject', function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->when($this->attendance_date, function ($query) {
                $query->whereDate('attendance_date', $this->attendance_date);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->attendance_status_filter, function ($query) {
                $query->where('attendance_status', $this->attendance_status_filter);
            })
            ->when($this->subject_filter, function ($query) {
                $query->whereHas('schedule.teacherSubject', function ($q) {
                    $q->where('subject_id', $this->subject_filter);
                });
            })
            ->when($this->class_filter, function ($query) {
                $query->whereHas('schedule.teacherSubject', function ($q) {
                    $q->where('class_id', $this->class_filter);
                });
            })
            ->when($this->teacher_filter, function ($query) {
                $query->whereHas('schedule.teacherSubject', function ($q) {
                    $q->where('teacher_id', $this->teacher_filter);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getAttendanceStats()
    {
        if (empty($this->academic_year_id)) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'sick' => 0,
                'permission' => 0,
            ];
        }

        $date = Carbon::parse($this->attendance_date);

        $attendances = StudentAttendance::whereHas('schedule.teacherSubject', function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->whereDate('attendance_date', $date)
            ->get();

        return [
            'total' => $attendances->count(),
            'present' => $attendances->where('attendance_status', 'present')->count(),
            'absent' => $attendances->where('attendance_status', 'absent')->count(),
            'late' => $attendances->where('attendance_status', 'late')->count(),
            'sick' => $attendances->where('attendance_status', 'sick')->count(),
            'permission' => $attendances->where('attendance_status', 'permission')->count(),
        ];
    }

    public function getSelectedAcademicYear()
    {
        if ($this->academic_year_id) {
            return AcademicYear::find($this->academic_year_id);
        }
        return null;
    }

    public function getAttendancesByClass()
    {
        if (empty($this->academic_year_id)) {
            return collect();
        }

        $date = Carbon::parse($this->attendance_date);

        return StudentAttendance::with([
                'schedule.teacherSubject.class',
                'schedule.teacherSubject.subject',
                'schedule.teacherSubject.teacher'
            ])
            ->whereHas('schedule.teacherSubject', function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->whereDate('attendance_date', $date)
            ->get()
            ->groupBy('schedule.teacherSubject.class.class_name')
            ->map(function ($attendances, $className) {
                return [
                    'class_name' => $className,
                    'total' => $attendances->count(),
                    'present' => $attendances->where('attendance_status', 'present')->count(),
                    'absent' => $attendances->where('attendance_status', 'absent')->count(),
                    'late' => $attendances->where('attendance_status', 'late')->count(),
                    'sick' => $attendances->where('attendance_status', 'sick')->count(),
                    'permission' => $attendances->where('attendance_status', 'permission')->count(),
                ];
            });
    }

    public function render()
    {
        $attendances = $this->getAttendances();
        $attendanceStats = $this->getAttendanceStats();
        $selectedAcademicYear = $this->getSelectedAcademicYear();
        $attendancesByClass = $this->getAttendancesByClass();

        return view('livewire.admin-panel.attendances.index', compact(
            'attendances',
            'attendanceStats',
            'selectedAcademicYear',
            'attendancesByClass'
        ));
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'attendance_status_filter', 'subject_filter', 'class_filter', 'teacher_filter']);
    }

    public function updatedAttendanceDate(): void
    {
        $this->resetPage();
    }

protected function getDayName($day): string
    {
        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu'
        ];

        return $days[$day] ?? $day;
    }
}
