<?php

namespace App\Livewire\StudentPanel\Reports;

use App\Models\Academic\ClassStudent;
use App\Models\Assessment\StudentGrade;
use App\Models\Attendance\StudentAttendance;
use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Rapor Saya')]
class Index extends Component
{
    public Student $student;

    #[Url()]
    public $academic_year_id = '';

    #[Url()]
    public string $semester = '';

    public array $semesters = [
        '1' => 'Semester 1',
        '2' => 'Semester 2'
    ];

    public function mount(): void
    {
        // Get current authenticated student
        $this->student = Student::where('user_id', auth()->id())->firstOrFail();

        // Set current academic year as default
        $currentAcademicYear = AcademicYear::active()->first();
        if ($currentAcademicYear) {
            $this->academic_year_id = $currentAcademicYear->id;
        }

        // Set current semester based on current date
        $this->semester = $this->getCurrentSemester();
    }

    protected function getCurrentSemester(): string
    {
        $currentMonth = now()->month;
        // Semester 1: January - June, Semester 2: July - December
        return $currentMonth <= 6 ? '1' : '2';
    }

    public function getAcademicYears()
    {
        return AcademicYear::orderBy('start_date', 'desc')->get();
    }

    public function getStudentClass()
    {
        if (!$this->academic_year_id) {
            return null;
        }

        return ClassStudent::where('student_id', $this->student->id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->with(['class', 'academicYear'])
            ->first();
    }

    public function getSubjectGrades()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return collect();
        }

        // Get date range for semester
        $dateRange = $this->getSemesterDateRange();

        $grades = StudentGrade::with(['teacherSubject.subject', 'gradeComponent'])
            ->where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->when($dateRange, function ($query) use ($dateRange) {
                $query->whereBetween('input_date', [$dateRange['start'], $dateRange['end']]);
            })
            ->get()
            ->groupBy('teacherSubject.subject.subject_name');

        return $grades->map(function ($subjectGrades, $subjectName) {
            $components = $subjectGrades->groupBy('gradeComponent.component_name');
            $finalGrade = 0;
            $totalWeight = 0;
            $componentDetails = [];

            foreach ($components as $componentName => $componentGrades) {
                $averageGrade = $componentGrades->avg('grade_value');
                $weight = $componentGrades->first()->gradeComponent->weight_percentage;

                $finalGrade += ($averageGrade * $weight / 100);
                $totalWeight += $weight;

                $componentDetails[] = [
                    'name' => $componentName,
                    'average' => round($averageGrade, 2),
                    'weight' => $weight,
                    'count' => $componentGrades->count()
                ];
            }

            return [
                'subject_name' => $subjectName,
                'final_grade' => round($finalGrade, 2),
                'grade_letter' => $this->getGradeLetter($finalGrade),
                'grade_class' => $this->getGradeBadgeClass($finalGrade),
                'components' => $componentDetails,
                'total_assessments' => $subjectGrades->count()
            ];
        })->sortBy('subject_name');
    }

    public function getAttendanceSummary()
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
                'percentage' => 0
            ];
        }

        // Get date range for semester
        $dateRange = $this->getSemesterDateRange();

        $query = StudentAttendance::where('student_id', $this->student->id)
            ->whereHas('schedule.teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id);
            });

        if ($dateRange) {
            $query->whereBetween('attendance_date', [$dateRange['start'], $dateRange['end']]);
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
        $stats['percentage'] = $stats['total'] > 0 ? round(($attended / $stats['total']) * 100, 1) : 0;

        return $stats;
    }

    public function getOverallGPA()
    {
        $subjectGrades = $this->getSubjectGrades();

        if ($subjectGrades->isEmpty()) {
            return 0;
        }

        $totalGrade = $subjectGrades->sum('final_grade');
        $subjectCount = $subjectGrades->count();

        return $subjectCount > 0 ? round($totalGrade / $subjectCount, 2) : 0;
    }

    public function getGradeDistribution()
    {
        $subjectGrades = $this->getSubjectGrades();

        if ($subjectGrades->isEmpty()) {
            return [
                'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0
            ];
        }

        $distribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];

        foreach ($subjectGrades as $subjectGrade) {
            $letter = $subjectGrade['grade_letter'];
            $distribution[$letter]++;
        }

        return $distribution;
    }

    public function getRank()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return ['rank' => 0, 'total' => 0];
        }

        $dateRange = $this->getSemesterDateRange();

        // Get all students in the same class
        $classStudents = $studentClass->class->classStudents()
            ->where('status', 'active')
            ->with('student')
            ->get();

        $studentGPAs = [];

        foreach ($classStudents as $classStudent) {
            $grades = StudentGrade::where('student_id', $classStudent->student_id)
                ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                    $query->where('class_id', $studentClass->class_id)
                          ->where('academic_year_id', $this->academic_year_id)
                          ->where('status', 'active');
                })
                ->when($dateRange, function ($query) use ($dateRange) {
                    $query->whereBetween('input_date', [$dateRange['start'], $dateRange['end']]);
                })
                ->get()
                ->groupBy('teacherSubject.subject_id');

            $gpa = 0;
            $subjectCount = 0;

            foreach ($grades as $subjectGrades) {
                $finalGrade = 0;
                $totalWeight = 0;

                foreach ($subjectGrades as $grade) {
                    $weight = $grade->gradeComponent->weight_percentage;
                    $finalGrade += ($grade->grade_value * $weight / 100);
                    $totalWeight += $weight;
                }

                if ($totalWeight > 0) {
                    $gpa += $finalGrade;
                    $subjectCount++;
                }
            }

            if ($subjectCount > 0) {
                $studentGPAs[$classStudent->student_id] = $gpa / $subjectCount;
            } else {
                $studentGPAs[$classStudent->student_id] = 0;
            }
        }

        // Sort by GPA descending
        arsort($studentGPAs);

        $rank = array_search($this->student->id, array_keys($studentGPAs)) + 1;
        $total = count($studentGPAs);

        return ['rank' => $rank, 'total' => $total];
    }

    protected function getSemesterDateRange()
    {
        if (!$this->academic_year_id || !$this->semester) {
            return null;
        }

        $academicYear = AcademicYear::find($this->academic_year_id);

        if (!$academicYear) {
            return null;
        }

        $startDate = $academicYear->start_date;
        $endDate = $academicYear->end_date;

        if ($this->semester == '1') {
            // First semester: start date to middle of academic year
            $semesterEnd = Carbon::parse($startDate)->addMonths(6)->endOfMonth();
            return [
                'start' => $startDate,
                'end' => $semesterEnd > $endDate ? $endDate : $semesterEnd
            ];
        } else {
            // Second semester: middle of academic year to end date
            $semesterStart = Carbon::parse($startDate)->addMonths(6)->startOfMonth();
            return [
                'start' => $semesterStart < $startDate ? $startDate : $semesterStart,
                'end' => $endDate
            ];
        }
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

    public function exportReportCard()
    {
        // This method can be implemented to generate PDF report card
        $this->dispatch('show:toastify', type: 'info', message: 'Fitur ekspor rapor akan segera tersedia');
    }

    public function render()
    {
        $academicYears = $this->getAcademicYears();
        $studentClass = $this->getStudentClass();
        $subjectGrades = $this->getSubjectGrades();
        $attendanceSummary = $this->getAttendanceSummary();
        $overallGPA = $this->getOverallGPA();
        $gradeDistribution = $this->getGradeDistribution();
        $rankData = $this->getRank();

        return view('livewire.student-panel.reports.index', compact(
            'academicYears',
            'studentClass',
            'subjectGrades',
            'attendanceSummary',
            'overallGPA',
            'gradeDistribution',
            'rankData'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function updatedAcademicYearId(): void
    {
        // Reset semester when academic year changes
        $this->semester = '1';
    }
}
