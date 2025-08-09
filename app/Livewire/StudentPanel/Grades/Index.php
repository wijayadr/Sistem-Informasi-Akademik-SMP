<?php

namespace App\Livewire\StudentPanel\Grades;

use App\Models\Academic\ClassStudent;
use App\Models\Assessment\GradeComponent;
use App\Models\Assessment\StudentGrade;
use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Nilai Saya')]
class Index extends Component
{
    use WithPagination;

    public Student $student;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $subject_filter = '';

    #[Url()]
    public string $component_filter = '';

    #[Url()]
    public string $input_date = '';

    #[Url()]
    public $academic_year_id = '';

    #[Url()]
    public string $month_filter = '';

    public array $listsForFields = [];

    public function mount(): void
    {
        // Get current authenticated student
        $this->student = Student::where('user_id', auth()->id())->firstOrFail();
        $this->input_date = now()->format('Y-m-d');
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
        $this->listsForFields['grade_components'] = GradeComponent::active()
            ->pluck('component_name', 'id');

        $this->updateSubjectsList();
    }

    protected function updateSubjectsList(): void
    {
        if (!$this->academic_year_id) {
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

    public function getStudentClass()
    {
        if (!$this->academic_year_id) {
            return null;
        }

        return ClassStudent::where('student_id', $this->student->id)
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->with(['class'])
            ->first();
    }

    public function updatedAcademicYearId(): void
    {
        $this->updateSubjectsList();
        $this->resetPage();
        $this->reset(['subject_filter']);
    }

    public function getGrades()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            // return collect()->paginate(15);
            return new LengthAwarePaginator([], 0, 15);
        }

        $query = StudentGrade::with([
                'teacherSubject.subject',
                'teacherSubject.teacher',
                'teacherSubject.class',
                'gradeComponent',
                'inputTeacher'
            ])
            ->where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            });

        // Apply filters
        if ($this->input_date) {
            $query->whereDate('input_date', $this->input_date);
        }

        if ($this->month_filter) {
            $date = Carbon::parse($this->month_filter . '-01');
            $query->whereMonth('input_date', $date->month)
                  ->whereYear('input_date', $date->year);
        }

        if ($this->search) {
            $query->whereHas('teacherSubject', function ($q) {
                $q->whereHas('subject', function ($sq) {
                    $sq->where('subject_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('teacher', function ($tq) {
                    $tq->where('full_name', 'like', '%' . $this->search . '%');
                });
            });
        }

        if ($this->subject_filter) {
            $query->whereHas('teacherSubject', function ($q) {
                $q->where('subject_id', $this->subject_filter);
            });
        }

        if ($this->component_filter) {
            $query->where('grade_component_id', $this->component_filter);
        }

        return $query->orderBy('input_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate(15);
    }

    public function getGradeStats()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return [
                'total' => 0,
                'average' => 0,
                'highest' => 0,
                'lowest' => 0,
                'above_75' => 0,
                'below_60' => 0,
            ];
        }

        $query = StudentGrade::where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id);
            });

        if ($this->month_filter) {
            $date = Carbon::parse($this->month_filter . '-01');
            $query->whereMonth('input_date', $date->month)
                  ->whereYear('input_date', $date->year);
        } elseif ($this->input_date) {
            $query->whereDate('input_date', $this->input_date);
        }

        $grades = $query->get();
        $gradeValues = $grades->pluck('grade_value')->filter(function ($value) {
            return $value > 0;
        });

        return [
            'total' => $grades->count(),
            'average' => $gradeValues->count() > 0 ? round($gradeValues->avg(), 2) : 0,
            'highest' => $gradeValues->count() > 0 ? $gradeValues->max() : 0,
            'lowest' => $gradeValues->count() > 0 ? $gradeValues->min() : 0,
            'above_75' => $gradeValues->where('>', 75)->count(),
            'below_60' => $gradeValues->where('<', 60)->count(),
        ];
    }

    public function getSubjectGrades()
    {
        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return collect();
        }

        return StudentGrade::with(['teacherSubject.subject', 'gradeComponent'])
            ->where('student_id', $this->student->id)
            ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                $query->where('class_id', $studentClass->class_id)
                      ->where('academic_year_id', $this->academic_year_id)
                      ->where('status', 'active');
            })
            ->get()
            ->groupBy('teacherSubject.subject.subject_name')
            ->map(function ($grades, $subjectName) {
                $totalWeightedScore = 0;
                $totalWeight = 0;
                $components = [];

                foreach ($grades as $grade) {
                    $weight = $grade->gradeComponent->weight_percentage;
                    $totalWeightedScore += ($grade->grade_value * $weight / 100);
                    $totalWeight += $weight;

                    $components[] = [
                        'component' => $grade->gradeComponent->component_name,
                        'grade' => $grade->grade_value,
                        'weight' => $weight,
                        'date' => $grade->input_date
                    ];
                }

                $finalGrade = $totalWeight > 0 ? round($totalWeightedScore, 2) : 0;

                return [
                    'subject_name' => $subjectName,
                    'final_grade' => $finalGrade,
                    'components' => $components,
                    'grade_letter' => $this->getGradeLetter($finalGrade),
                    'grade_class' => $this->getGradeBadgeClass($finalGrade)
                ];
            });
    }

    public function getGradeProgressChart()
    {
        if (!$this->academic_year_id) {
            return [];
        }

        $studentClass = $this->getStudentClass();

        if (!$studentClass) {
            return [];
        }

        $academicYear = AcademicYear::find($this->academic_year_id);
        $startDate = $academicYear->start_date;
        $endDate = $academicYear->end_date;

        $monthlyProgress = [];
        $current = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();

        while ($current <= $end) {
            $grades = StudentGrade::where('student_id', $this->student->id)
                ->whereHas('teacherSubject', function ($query) use ($studentClass) {
                    $query->where('class_id', $studentClass->class_id)
                          ->where('academic_year_id', $this->academic_year_id);
                })
                ->whereMonth('input_date', $current->month)
                ->whereYear('input_date', $current->year)
                ->get();

            $average = $grades->count() > 0 ? round($grades->avg('grade_value'), 2) : 0;

            $monthlyProgress[] = [
                'month' => $current->format('M Y'),
                'average' => $average,
                'count' => $grades->count()
            ];

            $current->addMonth();
        }

        return $monthlyProgress;
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

    public function render()
    {
        $grades = $this->getGrades();
        $gradeStats = $this->getGradeStats();
        $academicYears = $this->getAcademicYears();
        $studentClass = $this->getStudentClass();
        $subjectGrades = $this->getSubjectGrades();
        $progressChart = $this->getGradeProgressChart();

        return view('livewire.student-panel.grades.index', compact(
            'grades',
            'gradeStats',
            'academicYears',
            'studentClass',
            'subjectGrades',
            'progressChart'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'subject_filter', 'component_filter', 'input_date']);
        $this->month_filter = now()->format('Y-m');
    }

    public function updatedInputDate(): void
    {
        $this->resetPage();
        // Clear month filter when specific date is selected
        if ($this->input_date) {
            $this->month_filter = '';
        }
    }

    public function updatedMonthFilter(): void
    {
        $this->resetPage();
        // Clear specific date when month filter is selected
        if ($this->month_filter) {
            $this->input_date = '';
        }
    }
}
