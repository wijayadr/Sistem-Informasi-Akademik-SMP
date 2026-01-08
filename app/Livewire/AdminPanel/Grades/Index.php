<?php

namespace App\Livewire\AdminPanel\Grades;

use App\Models\Academic\TeacherSubject;
use App\Models\Assessment\GradeComponent;
use App\Models\Assessment\StudentGrade;
use App\Models\Master\AcademicYear;
use App\Models\Master\Subject;
use App\Models\Academic\Classes;
use App\Models\User\Teacher;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Data Nilai Siswa')]
class Index extends Component
{
    use WithPagination;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $class_filter = '';

    #[Url()]
    public string $subject_filter = '';

    #[Url()]
    public string $component_filter = '';

    #[Url()]
    public string $teacher_filter = '';

    #[Url()]
    public string $input_date = '';

    #[Url()]
    public string $academic_year_id = '';

    #[Url()]
    public string $grade_range_filter = '';

    public array $listsForFields = [];

    public function mount(): void
    {
        $this->input_date = now()->format('Y-m-d');
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

        // Get grade components
        $this->listsForFields['grade_components'] = GradeComponent::active()
            ->orderBy('component_name')
            ->pluck('component_name', 'id');

        // Grade range filters
        $this->listsForFields['grade_ranges'] = [
            'excellent' => 'Sangat Baik (90-100)',
            'good' => 'Baik (80-89)',
            'satisfactory' => 'Cukup (70-79)',
            'needs_improvement' => 'Kurang (60-69)',
            'poor' => 'Sangat Kurang (0-59)',
        ];
    }

    public function updatedAcademicYearId(): void
    {
        $this->initListsForFields();
        $this->resetPage();
        $this->reset(['search', 'class_filter', 'subject_filter', 'component_filter', 'teacher_filter', 'grade_range_filter']);
    }

    public function getGrades()
    {
        if (empty($this->academic_year_id)) {
            return collect()->paginate(15);
        }

        return StudentGrade::with([
                'student',
                'teacherSubject.subject',
                'teacherSubject.class',
                'teacherSubject.teacher',
                'gradeComponent',
                'inputTeacher'
            ])
            ->whereHas('teacherSubject', function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->when($this->input_date, function ($query) {
                $query->whereDate('input_date', $this->input_date);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->class_filter, function ($query) {
                $query->whereHas('teacherSubject', function ($q) {
                    $q->where('class_id', $this->class_filter);
                });
            })
            ->when($this->subject_filter, function ($query) {
                $query->whereHas('teacherSubject', function ($q) {
                    $q->where('subject_id', $this->subject_filter);
                });
            })
            ->when($this->teacher_filter, function ($query) {
                $query->whereHas('teacherSubject', function ($q) {
                    $q->where('teacher_id', $this->teacher_filter);
                });
            })
            ->when($this->component_filter, function ($query) {
                $query->where('grade_component_id', $this->component_filter);
            })
            ->when($this->grade_range_filter, function ($query) {
                switch ($this->grade_range_filter) {
                    case 'excellent':
                        $query->where('grade_value', '>=', 90);
                        break;
                    case 'good':
                        $query->whereBetween('grade_value', [80, 89.99]);
                        break;
                    case 'satisfactory':
                        $query->whereBetween('grade_value', [70, 79.99]);
                        break;
                    case 'needs_improvement':
                        $query->whereBetween('grade_value', [60, 69.99]);
                        break;
                    case 'poor':
                        $query->where('grade_value', '<', 60);
                        break;
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getGradeStats()
    {
        if (empty($this->academic_year_id)) {
            return [
                'total' => 0,
                'average' => 0,
                'highest' => 0,
                'lowest' => 0,
                'above_75' => 0,
                'below_60' => 0,
            ];
        }

        $date = Carbon::parse($this->input_date);

        $grades = StudentGrade::whereHas('teacherSubject', function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->whereDate('input_date', $date)
            ->get();

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

    public function getGradesByClass()
    {
        if (empty($this->academic_year_id)) {
            return collect();
        }

        $date = Carbon::parse($this->input_date);

        return StudentGrade::with([
                'teacherSubject.class',
                'teacherSubject.subject',
                'teacherSubject.teacher'
            ])
            ->whereHas('teacherSubject', function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->whereDate('input_date', $date)
            ->get()
            ->groupBy('teacherSubject.class.class_name')
            ->map(function ($grades, $className) {
                $gradeValues = $grades->pluck('grade_value')->filter(function ($value) {
                    return $value > 0;
                });

                return [
                    'class_name' => $className,
                    'total' => $grades->count(),
                    'average' => $gradeValues->count() > 0 ? round($gradeValues->avg(), 2) : 0,
                    'highest' => $gradeValues->count() > 0 ? $gradeValues->max() : 0,
                    'lowest' => $gradeValues->count() > 0 ? $gradeValues->min() : 0,
                    'above_75' => $gradeValues->where('>', 75)->count(),
                    'below_60' => $gradeValues->where('<', 60)->count(),
                ];
            });
    }

    public function getTeacherSubjects()
    {
        if (empty($this->academic_year_id)) {
            return collect();
        }

        return TeacherSubject::where('status', 'active')
            ->where('academic_year_id', $this->academic_year_id)
            ->with(['subject', 'class', 'teacher', 'academicYear'])
            ->orderBy('created_at', 'desc')
            ->get();
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
        $grades = $this->getGrades();
        $gradeStats = $this->getGradeStats();
        $gradesByClass = $this->getGradesByClass();
        $teacherSubjects = $this->getTeacherSubjects();
        $selectedAcademicYear = $this->getSelectedAcademicYear();

        return view('livewire.admin-panel.grades.index', compact(
            'grades',
            'gradeStats',
            'gradesByClass',
            'teacherSubjects',
            'selectedAcademicYear'
        ));
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'class_filter', 'subject_filter', 'component_filter', 'teacher_filter', 'grade_range_filter']);
    }

    public function updatedInputDate(): void
    {
        $this->resetPage();
    }

    public static function getGradeLetterStatic($value): string
    {
        if ($value >= 90) return 'A';
        if ($value >= 80) return 'B';
        if ($value >= 70) return 'C';
        if ($value >= 60) return 'D';
        return 'E';
    }

    public static function getGradeBadgeClassStatic($value): string
    {
        if ($value >= 90) return 'bg-success-subtle text-success';
        if ($value >= 80) return 'bg-info-subtle text-info';
        if ($value >= 70) return 'bg-warning-subtle text-warning';
        if ($value >= 60) return 'bg-secondary-subtle text-secondary';
        return 'bg-danger-subtle text-danger';
    }
}
