<?php

namespace App\Livewire\TeacherPanel\Attendances;

use App\Livewire\Forms\StudentAttendanceForm;
use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Absensi Siswa')]
class Index extends Component
{
    use WithPagination;

    public StudentAttendanceForm $form;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $attendance_status_filter = '';

    #[Url()]
    public string $subject_filter = '';

    #[Url()]
    public string $attendance_date = '';

    #[Url()]
    public string $academic_year_id = '';

    public bool $showAttendanceModal = false;
    public bool $editing = false;
    public array $listsForFields = [];
    public array $selectedStudents = [];
    public bool $selectAll = false;
    public string $bulkAttendanceStatus = '';

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

        // Get teacher subjects for current teacher and selected academic year
        $teacherId = auth()->user()->teacher->id;

        $teacherSubjectsQuery = TeacherSubject::where('teacher_id', $teacherId)
            ->where('status', 'active');

        if ($this->academic_year_id) {
            $teacherSubjectsQuery->where('academic_year_id', $this->academic_year_id);
        }

        $this->listsForFields['teacher_subjects'] = $teacherSubjectsQuery
            ->with(['subject', 'class'])
            ->get()
            ->mapWithKeys(function ($ts) {
                return [$ts->id => $ts->class->class_name . ' - ' . $ts->subject->subject_name];
            });

        $this->listsForFields['schedules'] = Schedule::whereHas('teacherSubject', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->where('status', 'active')
                    ->when($this->academic_year_id, function ($q) {
                        $q->where('academic_year_id', $this->academic_year_id);
                    });
            })
            ->where('status', 'active')
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
            ->get()
            ->mapWithKeys(function ($schedule) {
                $dayName = $this->getDayName($schedule->day);
                return [$schedule->id => $schedule->teacherSubject->class->class_name . ' - ' .
                                      $schedule->teacherSubject->subject->subject_name . ' (' .
                                      $dayName . ', ' . $schedule->start_time->format('H:i') . '-' .
                                      $schedule->end_time->format('H:i') . ')'];
            });

        $this->listsForFields['attendance_statuses'] = [
            'present' => 'Hadir',
            'absent' => 'Tidak Hadir',
            'late' => 'Terlambat',
            'sick' => 'Sakit',
            'permission' => 'Izin',
        ];

        $this->listsForFields['subjects'] = TeacherSubject::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->when($this->academic_year_id, function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->with('subject')
            ->get()
            ->pluck('subject.subject_name', 'subject.id')
            ->unique();
    }

    public function updatedAcademicYearId(): void
    {
        $this->initListsForFields();
        $this->resetPage();
        $this->reset(['search', 'attendance_status_filter', 'subject_filter']);
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

    public function showAttendanceModal(): void
    {
        if (empty($this->academic_year_id)) {
            $this->showToastr('error', 'Pilih tahun akademik terlebih dahulu');
            return;
        }

        $this->editing = false;
        $this->showAttendanceModal = true;
        $this->form->reset();
        $this->form->attendance_date = $this->attendance_date;
        $this->form->input_teacher_id = auth()->user()->teacher->id;
        $this->form->academic_year_id = $this->academic_year_id;
    }

    public function editAttendance($attendanceId): void
    {
        $attendance = StudentAttendance::with(['student', 'schedule.teacherSubject'])->findOrFail($attendanceId);
        $this->editing = true;
        $this->showAttendanceModal = true;
        $this->form->setAttendance($attendance);
    }

    public function cancelEdit(): void
    {
        $this->showAttendanceModal = false;
        $this->editing = false;
        $this->form->reset();
        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->dispatch('closeModal');
    }

    public function saveAttendance(): void
    {
        if ($this->editing) {
            $this->form->update();
            $this->showToastr('success', 'Data absensi berhasil diperbarui');
        } else {
            $this->form->store();
            $this->showToastr('success', 'Data absensi berhasil ditambahkan');
        }

        $this->cancelEdit();
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $attendances = $this->getAttendances();
            $this->selectedStudents = $attendances->pluck('id')->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function updatedSelectedStudents(): void
    {
        $attendances = $this->getAttendances();
        $this->selectAll = count($this->selectedStudents) === $attendances->count();
    }

    public function bulkUpdateStatus(): void
    {
        if (empty($this->selectedStudents) || empty($this->bulkAttendanceStatus)) {
            $this->showToastr('error', 'Pilih siswa dan status absensi terlebih dahulu');
            return;
        }

        $this->validate([
            'bulkAttendanceStatus' => 'required|in:present,absent,late,sick,permission'
        ]);

        StudentAttendance::whereIn('id', $this->selectedStudents)->update([
            'attendance_status' => $this->bulkAttendanceStatus,
            'input_teacher_id' => auth()->user()->teacher->id
        ]);

        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->bulkAttendanceStatus = '';
        $this->showToastr('success', 'Status absensi berhasil diperbarui untuk siswa yang dipilih');
    }

    public function createAttendanceForSchedule($scheduleId): void
    {
        if (empty($this->academic_year_id)) {
            $this->showToastr('error', 'Pilih tahun akademik terlebih dahulu');
            return;
        }

        $schedule = Schedule::with(['teacherSubject.class.classStudents.student'])
            ->whereHas('teacherSubject', function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->findOrFail($scheduleId);

        $date = Carbon::parse($this->attendance_date);

        // Check if attendance already exists for this schedule and date
        $existingCount = StudentAttendance::where('schedule_id', $scheduleId)
            ->whereDate('attendance_date', $date)
            ->count();

        if ($existingCount > 0) {
            $this->showToastr('error', 'Absensi untuk jadwal ini sudah dibuat');
            return;
        }

        // Get active students for this class in the selected academic year
        $students = $schedule->teacherSubject->class->classStudents()
            ->where('status', 'active')
            ->where('academic_year_id', $this->academic_year_id)
            ->with('student')
            ->get();

        foreach ($students as $classStudent) {
            StudentAttendance::create([
                'student_id' => $classStudent->student->id,
                'schedule_id' => $scheduleId,
                'attendance_date' => $date,
                'attendance_status' => 'present', // Default to present
                'input_teacher_id' => auth()->user()->teacher->id
            ]);
        }

        $this->showToastr('success', 'Absensi berhasil dibuat untuk ' . $students->count() . ' siswa');
    }

    #[On('delete-attendance')]
    public function deleteAttendance($id): void
    {
        StudentAttendance::findOrFail($id)->delete();
        $this->showToastr('success', 'Data absensi berhasil dihapus');
    }

    public function deleteAttendanceConfirm($attendanceId): void
    {
        $this->dispatch('swal:confirm',
            title: 'Hapus Absensi?',
            text: 'Data absensi akan dihapus secara permanen!',
            icon: 'warning',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            method: 'delete-attendance',
            params: $attendanceId
        );
    }

    public function getAttendances()
    {
        if (empty($this->academic_year_id)) {
            return collect()->paginate(15);
        }

        $teacherId = auth()->user()->teacher->id;

        return StudentAttendance::with(['student', 'schedule.teacherSubject.subject', 'schedule.teacherSubject.class'])
            ->whereHas('schedule.teacherSubject', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->where('academic_year_id', $this->academic_year_id);
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
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getTodaySchedules()
    {
        if (empty($this->academic_year_id)) {
            return collect();
        }

        $teacherId = auth()->user()->teacher->id;
        $today = strtolower(now()->format('l'));

        return Schedule::whereHas('teacherSubject', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->where('status', 'active')
                    ->where('academic_year_id', $this->academic_year_id);
            })
            ->where('day', $today)
            ->where('status', 'active')
            ->with(['teacherSubject.subject', 'teacherSubject.class'])
            ->orderBy('start_time')
            ->get();
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

        $teacherId = auth()->user()->teacher->id;
        $date = Carbon::parse($this->attendance_date);

        $attendances = StudentAttendance::whereHas('schedule.teacherSubject', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->where('academic_year_id', $this->academic_year_id);
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

    public function render()
    {
        $attendances = $this->getAttendances();
        $todaySchedules = $this->getTodaySchedules();
        $attendanceStats = $this->getAttendanceStats();
        $selectedAcademicYear = $this->getSelectedAcademicYear();

        return view('livewire.teacher-panel.attendances.index', compact(
            'attendances',
            'todaySchedules',
            'attendanceStats',
            'selectedAcademicYear'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'attendance_status_filter', 'subject_filter']);
    }

    public function updatedAttendanceDate(): void
    {
        $this->resetPage();
    }
}
