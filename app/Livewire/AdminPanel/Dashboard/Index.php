<?php

namespace App\Livewire\AdminPanel\Dashboard;

use App\Models\Academic\ClassStudent;
use App\Models\Academic\Schedule;
use App\Models\Academic\TeacherSubject;
use App\Models\Assessment\StudentGrade;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\Master\Subject;
use App\Models\User\Student;
use App\Models\User\Teacher;
use App\Models\User\ParentModel;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

#[Title('Dashboard Admin')]
class Index extends Component
{
    public $selectedAcademicYearId = '';
    public $academicYears = [];

    public function mount(): void
    {
        // Get all academic years
        $this->academicYears = AcademicYear::orderBy('academic_year', 'desc')->get();

        // Set current active academic year as default
        $activeAcademicYear = AcademicYear::active()->first();
        if ($activeAcademicYear) {
            $this->selectedAcademicYearId = $activeAcademicYear->id;
        } elseif ($this->academicYears->count() > 0) {
            $this->selectedAcademicYearId = $this->academicYears->first()->id;
        }
    }

    public function getSelectedAcademicYear()
    {
        if (!$this->selectedAcademicYearId) {
            return null;
        }

        return AcademicYear::find($this->selectedAcademicYearId);
    }

    public function getOverallStats()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return [
                'total_students' => 0,
                'total_teachers' => 0,
                'total_parents' => 0,
                'total_classes' => 0,
                'total_subjects' => 0,
                'active_schedules' => 0
            ];
        }

        $totalStudents = ClassStudent::where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->count();

        $totalTeachers = TeacherSubject::where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->distinct('teacher_id')
            ->count();

        $totalParents = ParentModel::whereHas('student', function($query) use ($academicYear) {
            $query->whereHas('classStudents', function($subQuery) use ($academicYear) {
                $subQuery->where('academic_year_id', $academicYear->id)
                         ->where('status', 'active');
            });
        })->count();

        // $totalClasses = ClassRoom::whereHas('classStudents', function($query) use ($academicYear) {
        //     $query->where('academic_year_id', $academicYear->id)
        //           ->where('status', 'active');
        // })->where('status', 'active')->count();

        $totalSubjects = Subject::whereHas('teacherSubjects', function($query) use ($academicYear) {
            $query->where('academic_year_id', $academicYear->id)
                  ->where('status', 'active');
        })->where('status', 'active')->count();

        $activeSchedules = Schedule::whereHas('teacherSubject', function($query) use ($academicYear) {
            $query->where('academic_year_id', $academicYear->id)
                  ->where('status', 'active');
        })->where('status', 'active')->count();

        return [
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_parents' => $totalParents,
            // 'total_classes' => $totalClasses,
            'total_subjects' => $totalSubjects,
            'active_schedules' => $activeSchedules
        ];
    }

    public function getStudentsPerClass()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return collect();
        }

        return ClassRoom::withCount(['classStudents' => function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active');
            }])
            ->whereHas('classStudents', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active');
            })
            ->where('status', 'active')
            ->orderBy('class_name')
            ->get();
    }

    public function getMonthlyAttendanceOverview()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return [
                'total_records' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'sick' => 0,
                'permission' => 0,
                'percentage' => 0
            ];
        }

        $attendances = StudentAttendance::whereHas('schedule.teacherSubject', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->get();

        $total = $attendances->count();
        $present = $attendances->where('attendance_status', 'present')->count();
        $late = $attendances->where('attendance_status', 'late')->count();
        $absent = $attendances->where('attendance_status', 'absent')->count();
        $sick = $attendances->where('attendance_status', 'sick')->count();
        $permission = $attendances->where('attendance_status', 'permission')->count();

        $presentTotal = $present + $late;
        $percentage = $total > 0 ? round(($presentTotal / $total) * 100, 1) : 0;

        return [
            'total_records' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'sick' => $sick,
            'permission' => $permission,
            'percentage' => $percentage
        ];
    }

    public function getMonthlyGradeOverview()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return [
                'total_grades' => 0,
                'average_grade' => 0,
                'grade_distribution' => [
                    'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0
                ]
            ];
        }

        $grades = StudentGrade::whereHas('teacherSubject', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->whereMonth('input_date', now()->month)
            ->whereYear('input_date', now()->year)
            ->get();

        $total = $grades->count();
        $average = $total > 0 ? round($grades->avg('grade_value'), 2) : 0;

        $distribution = [
            'A' => $grades->where('grade_value', '>=', 90)->count(),
            'B' => $grades->whereBetween('grade_value', [80, 89.99])->count(),
            'C' => $grades->whereBetween('grade_value', [70, 79.99])->count(),
            'D' => $grades->whereBetween('grade_value', [60, 69.99])->count(),
            'E' => $grades->where('grade_value', '<', 60)->count(),
        ];

        return [
            'total_grades' => $total,
            'average_grade' => $average,
            'grade_distribution' => $distribution
        ];
    }

    public function getTodayAttendanceOverview()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return [
                'total_students' => 0,
                'total_attended' => 0,
                'attendance_percentage' => 0,
                'by_status' => []
            ];
        }

        $totalStudents = ClassStudent::where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->count();

        $todayAttendances = StudentAttendance::whereHas('schedule.teacherSubject', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->whereDate('attendance_date', today())
            ->get();

        $attended = $todayAttendances->whereIn('attendance_status', ['present', 'late'])->count();
        $percentage = $totalStudents > 0 ? round(($attended / $totalStudents) * 100, 1) : 0;

        $byStatus = [
            'present' => $todayAttendances->where('attendance_status', 'present')->count(),
            'late' => $todayAttendances->where('attendance_status', 'late')->count(),
            'absent' => $todayAttendances->where('attendance_status', 'absent')->count(),
            'sick' => $todayAttendances->where('attendance_status', 'sick')->count(),
            'permission' => $todayAttendances->where('attendance_status', 'permission')->count(),
        ];

        return [
            'total_students' => $totalStudents,
            'total_attended' => $attended,
            'attendance_percentage' => $percentage,
            'by_status' => $byStatus
        ];
    }

    public function getRecentActivities()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return collect();
        }

        // Get recent grades (last 7 days)
        $recentGrades = StudentGrade::with(['student', 'teacherSubject.subject', 'teacherSubject.teacher'])
            ->whereHas('teacherSubject', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            })
            ->where('input_date', '>=', now()->subDays(7))
            ->orderBy('input_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function($grade) {
                return [
                    'type' => 'grade',
                    'title' => 'Nilai baru diinput',
                    'description' => "{$grade->student->full_name} - {$grade->teacherSubject->subject->subject_name}",
                    'value' => $grade->grade_value,
                    'date' => $grade->input_date,
                    'icon' => 'ri-award-line',
                    'color' => 'primary'
                ];
            });

        return $recentGrades->take(5);
    }

    public function getClassPerformanceRanking()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return collect();
        }

        return ClassRoom::with(['classStudents' => function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active')
                      ->with('student');
            }])
            ->whereHas('classStudents', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active');
            })
            ->where('status', 'active')
            ->get()
            ->map(function($class) use ($academicYear) {
                $studentIds = $class->classStudents->pluck('student_id');

                $grades = StudentGrade::whereIn('student_id', $studentIds)
                    ->whereHas('teacherSubject', function($query) use ($academicYear) {
                        $query->where('academic_year_id', $academicYear->id);
                    })
                    ->get();

                $attendances = StudentAttendance::whereIn('student_id', $studentIds)
                    ->whereHas('schedule.teacherSubject', function($query) use ($academicYear) {
                        $query->where('academic_year_id', $academicYear->id);
                    })
                    ->whereMonth('attendance_date', now()->month)
                    ->whereYear('attendance_date', now()->year)
                    ->get();

                $totalAttendance = $attendances->count();
                $presentAttendance = $attendances->whereIn('attendance_status', ['present', 'late'])->count();
                $attendancePercentage = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 1) : 0;

                return [
                    'class_name' => $class->class_name,
                    'student_count' => $class->classStudents->count(),
                    'average_grade' => $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0,
                    'attendance_percentage' => $attendancePercentage,
                    'grade_letter' => $this->getGradeLetter($grades->count() > 0 ? $grades->avg('grade_value') : 0)
                ];
            })
            ->sortByDesc('average_grade');
    }

    public function getTopPerformingStudents()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return collect();
        }

        $studentGrades = Student::whereHas('classStudents', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active');
            })
            ->with(['classStudents' => function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active')
                      ->with('class');
            }])
            ->get()
            ->map(function($student) use ($academicYear) {
                $grades = StudentGrade::where('student_id', $student->id)
                    ->whereHas('teacherSubject', function($query) use ($academicYear) {
                        $query->where('academic_year_id', $academicYear->id);
                    })
                    ->get();

                $averageGrade = $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0;
                $currentClass = $student->classStudents->first();

                return [
                    'student' => $student,
                    'class_name' => $currentClass ? $currentClass->class->class_name : '-',
                    'average_grade' => $averageGrade,
                    'total_grades' => $grades->count(),
                    'grade_letter' => $this->getGradeLetter($averageGrade)
                ];
            })
            ->where('total_grades', '>', 0)
            ->sortByDesc('average_grade')
            ->take(5);

        return $studentGrades;
    }

    public function getWeeklyAttendanceTrend()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return collect();
        }

        $startOfWeek = now()->startOfWeek();
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);

            $attendances = StudentAttendance::whereHas('schedule.teacherSubject', function($query) use ($academicYear) {
                    $query->where('academic_year_id', $academicYear->id);
                })
                ->whereDate('attendance_date', $date)
                ->get();

            $total = $attendances->count();
            $present = $attendances->whereIn('attendance_status', ['present', 'late'])->count();
            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $days[] = [
                'day' => $date->format('l'),
                'date' => $date->format('d/m'),
                'total' => $total,
                'present' => $present,
                'percentage' => $percentage
            ];
        }

        return collect($days);
    }

    public function getSubjectPerformanceOverview()
    {
        $academicYear = $this->getSelectedAcademicYear();

        if (!$academicYear) {
            return collect();
        }

        return Subject::whereHas('teacherSubjects', function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active');
            })
            ->with(['teacherSubjects' => function($query) use ($academicYear) {
                $query->where('academic_year_id', $academicYear->id)
                      ->where('status', 'active');
            }])
            ->get()
            ->map(function($subject) use ($academicYear) {
                $grades = StudentGrade::whereHas('teacherSubject', function($query) use ($subject, $academicYear) {
                    $query->where('subject_id', $subject->id)
                          ->where('academic_year_id', $academicYear->id);
                })->get();

                $studentCount = ClassStudent::whereHas('student.grades', function($query) use ($subject, $academicYear) {
                    $query->whereHas('teacherSubject', function($subQuery) use ($subject, $academicYear) {
                        $subQuery->where('subject_id', $subject->id)
                                 ->where('academic_year_id', $academicYear->id);
                    });
                })
                ->where('academic_year_id', $academicYear->id)
                ->where('status', 'active')
                ->distinct('student_id')
                ->count();

                $averageGrade = $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0;

                return [
                    'subject_name' => $subject->subject_name,
                    'student_count' => $studentCount,
                    'average_grade' => $averageGrade,
                    'total_grades' => $grades->count(),
                    'grade_letter' => $this->getGradeLetter($averageGrade),
                    'teacher_count' => $subject->teacherSubjects->count()
                ];
            })
            ->where('total_grades', '>', 0)
            ->sortByDesc('average_grade');
    }

    public function getStudentsPerAcademicYear()
    {
        return $this->academicYears->map(function($academicYear) {
            $studentCount = ClassStudent::where('academic_year_id', $academicYear->id)
                ->where('status', 'active')
                ->count();

            $averageGrade = 0;
            if ($studentCount > 0) {
                $grades = StudentGrade::whereHas('teacherSubject', function($query) use ($academicYear) {
                    $query->where('academic_year_id', $academicYear->id);
                })->get();

                $averageGrade = $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0;
            }

            return [
                'academic_year' => $academicYear->academic_year,
                'student_count' => $studentCount,
                'average_grade' => $averageGrade,
                'is_active' => $academicYear->status === 'active',
                'start_date' => $academicYear->start_date,
                'end_date' => $academicYear->end_date
            ];
        })->sortByDesc('academic_year');
    }

    public function updatedSelectedAcademicYearId(): void
    {
        // Refresh data when academic year selection changes
        $this->dispatch('academic-year-changed');
    }

    protected function getGradeLetter($value): string
    {
        if ($value >= 90) return 'A';
        if ($value >= 80) return 'B';
        if ($value >= 70) return 'C';
        if ($value >= 60) return 'D';
        return 'E';
    }

    protected function getGradeBadgeClass($value): string
    {
        if ($value >= 80) return 'bg-success-subtle text-success';
        if ($value >= 70) return 'bg-info-subtle text-info';
        if ($value >= 60) return 'bg-warning-subtle text-warning';
        return 'bg-danger-subtle text-danger';
    }

    protected function getAttendanceStatusBadge($status): array
    {
        return match($status) {
            'present' => ['class' => 'bg-success-subtle text-success', 'text' => 'Hadir', 'icon' => 'ri-check-line'],
            'absent' => ['class' => 'bg-danger-subtle text-danger', 'text' => 'Tidak Hadir', 'icon' => 'ri-close-line'],
            'late' => ['class' => 'bg-warning-subtle text-warning', 'text' => 'Terlambat', 'icon' => 'ri-time-line'],
            'sick' => ['class' => 'bg-info-subtle text-info', 'text' => 'Sakit', 'icon' => 'ri-heart-pulse-line'],
            'permission' => ['class' => 'bg-secondary-subtle text-secondary', 'text' => 'Izin', 'icon' => 'ri-file-text-line'],
            default => ['class' => 'bg-light text-muted', 'text' => 'Tidak Diketahui', 'icon' => 'ri-question-line']
        };
    }

    public function refreshDashboard(): void
    {
        $this->dispatch('dashboard-refreshed');
    }

    public function render()
    {
        $selectedAcademicYear = $this->getSelectedAcademicYear();
        $overallStats = $this->getOverallStats();
        // $studentsPerClass = $this->getStudentsPerClass();
        $monthlyAttendanceOverview = $this->getMonthlyAttendanceOverview();
        $monthlyGradeOverview = $this->getMonthlyGradeOverview();
        $todayAttendanceOverview = $this->getTodayAttendanceOverview();
        $recentActivities = $this->getRecentActivities();
        // $classPerformanceRanking = $this->getClassPerformanceRanking();
        $topPerformingStudents = $this->getTopPerformingStudents();
        $weeklyAttendanceTrend = $this->getWeeklyAttendanceTrend();
        $subjectPerformanceOverview = $this->getSubjectPerformanceOverview();
        $studentsPerAcademicYear = $this->getStudentsPerAcademicYear();

        return view('livewire.admin-panel.dashboard.index', compact(
            'selectedAcademicYear',
            'overallStats',
            // 'studentsPerClass',
            'monthlyAttendanceOverview',
            'monthlyGradeOverview',
            'todayAttendanceOverview',
            'recentActivities',
            // 'classPerformanceRanking',
            'topPerformingStudents',
            'weeklyAttendanceTrend',
            'subjectPerformanceOverview',
            'studentsPerAcademicYear'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }
}
