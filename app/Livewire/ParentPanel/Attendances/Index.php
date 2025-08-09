<?php

namespace App\Livewire\ParentPanel\Attendances;

use App\Models\Academic\ClassStudent;
use App\Models\Academic\Schedule;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\User\ParentModel;
use App\Models\User\Student;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Absensi Anak')]
class Index extends Component
{
    use WithPagination;

    public ParentModel $parent;
    public $students = [];

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $attendance_status_filter = '';

    #[Url()]
    public string $subject_filter = '';

    #[Url()]
    public string $attendance_date = '';

    #[Url()]
    public $academic_year_id = '';

    #[Url()]
    public string $month_filter = '';

    #[Url()]
    public $selected_student_id = '';

    public array $listsForFields = [];

    public array $attendanceStatuses = [
        'present' => 'Hadir',
        'absent' => 'Tidak Hadir',
        'late' => 'Terlambat',
        'sick' => 'Sakit',
        'permission' => 'Izin'
    ];

    public function mount(): void
    {
        // Get current authenticated parent
        $this->parent = ParentModel::where('user_id', auth()->id())->firstOrFail();

        // Get all children of this parent
        $this->students = Student::where('id', $this->parent->student_id)
            ->orWhereHas('parents', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->get();

        // Set first student as default if no student selected
        if (!$this->selected_student_id && $this->students->count() > 0) {
            $this->selected_student_id = $this->students->first()->id;
        }

        $this->month_filter = now()->format('Y-m');

        // Set current academic year as default
        $currentAcademicYear = AcademicYear::active()->first();
        if ($currentAcademicYear) {
            $this->academic_year_id = $currentAcademicYear->id;
        }

        $this->initListsForFields();
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['attendance_statuses'] = $this->attendanceStatuses;
        $this->updateSubjectsList();
    }

    protected function updateSubjectsList(): void
    {
        if (!$this->academic_year_id || !$this->selected_student_id) {
            $this->listsForFields['subjects'] = collect();
            return;
        }

        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            $this->listsForFields['subjects'] = collect();
            return;
        }

        $this->listsForFields['subjects'] = $studentClass->class->teacherSubjects()
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->with('subject')
            ->get()
            ->pluck('subject.subject_name', 'subject.id')
            ->unique();
    }

    public function getAcademicYears()
    {
        return AcademicYear::orderBy('start_date', 'desc')->get();
    }

    public function getSelectedStudent()
    {
        if (!$this->selected_student_id) {
            return null;
        }

        return Student::find($this->selected_student_id);
    }

    public function getStudentClass()
    {
        if (!$this->academic_year_id || !$this->selected_student_id) {
            return null;
        }

        return ClassStudent::where('student_id', $this->selected_student_id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->with(['class'])
            ->first();
    }

    public function getTodaySchedules()
    {
        $today = strtolower(now()->format('l'));
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return collect();
        }

        return Schedule::whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubject.subject', 'teacherSubject.teacher', 'teacherSubject.class'])
            ->where('day', $today)
            ->where('status', 'active')
            ->orderBy('start_time')
            ->get();
    }

    public function getAttendanceStats()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'sick' => 0,
                'permission' => 0,
                'attendance_percentage' => 0
            ];
        }

        $query = StudentAttendance::where('student_id', $this->selected_student_id)
            ->whereHas('schedule.teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id);
            });

        if ($this->month_filter) {
            $date = Carbon::parse($this->month_filter . '-01');
            $query->whereMonth('attendance_date', $date->month)
                  ->whereYear('attendance_date', $date->year);
        }

        $attendances = $query->get();

        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->where('attendance_status', 'present')->count(),
            'absent' => $attendances->where('attendance_status', 'absent')->count(),
            'late' => $attendances->where('attendance_status', 'late')->count(),
            'sick' => $attendances->where('attendance_status', 'sick')->count(),
            'permission' => $attendances->where('attendance_status', 'permission')->count(),
        ];

        // Calculate attendance percentage
        $attended = $stats['present'] + $stats['late'];
        $stats['attendance_percentage'] = $stats['total'] > 0 ? round(($attended / $stats['total']) * 100, 1) : 0;

        return $stats;
    }

    public function getAttendances()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return collect()->paginate(15);
        }

        $query = StudentAttendance::with([
                'schedule.teacherSubject.subject',
                'schedule.teacherSubject.teacher',
                'schedule.teacherSubject.class'
            ])
            ->where('student_id', $this->selected_student_id)
            ->whereHas('schedule.teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id);
            });

        // Apply filters
        if ($this->attendance_date) {
            $query->whereDate('attendance_date', $this->attendance_date);
        }

        if ($this->month_filter) {
            $date = Carbon::parse($this->month_filter . '-01');
            $query->whereMonth('attendance_date', $date->month)
                  ->whereYear('attendance_date', $date->year);
        }

        if ($this->search) {
            $query->whereHas('schedule.teacherSubject', function ($q) {
                $q->whereHas('subject', function ($sq) {
                    $sq->where('subject_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('teacher', function ($tq) {
                    $tq->where('full_name', 'like', '%' . $this->search . '%');
                });
            });
        }

        if ($this->subject_filter) {
            $query->whereHas('schedule.teacherSubject', function ($q) {
                $q->where('subject_id', $this->subject_filter);
            });
        }

        if ($this->attendance_status_filter) {
            $query->where('attendance_status', $this->attendance_status_filter);
        }

        return $query->orderBy('attendance_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
    }

    public function updatedAcademicYearId(): void
    {
        $this->updateSubjectsList();
        $this->resetPage();
        $this->reset(['subject_filter']);
    }

    public function updatedSelectedStudentId(): void
    {
        $this->updateSubjectsList();
        $this->resetPage();
        $this->reset(['subject_filter']);
    }

    public function updatedAttendanceDate(): void
    {
        $this->resetPage();
        // Clear month filter when specific date is selected
        if ($this->attendance_date) {
            $this->month_filter = '';
        }
    }

    public function updatedMonthFilter(): void
    {
        $this->resetPage();
        // Clear specific date when month filter is selected
        if ($this->month_filter) {
            $this->attendance_date = '';
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'subject_filter', 'attendance_status_filter', 'attendance_date']);
        $this->month_filter = now()->format('Y-m');
        $this->resetPage();
    }

    public function render()
    {
        $academicYears = $this->getAcademicYears();
        $selectedStudent = $this->getSelectedStudent();
        $studentClass = $this->getStudentClass();
        $attendances = $this->getAttendances();
        $attendanceStats = $this->getAttendanceStats();
        $todaySchedules = $this->getTodaySchedules();

        return view('livewire.parent-panel.attendances.index', compact(
            'academicYears',
            'selectedStudent',
            'studentClass',
            'attendances',
            'attendanceStats',
            'todaySchedules'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }
}
