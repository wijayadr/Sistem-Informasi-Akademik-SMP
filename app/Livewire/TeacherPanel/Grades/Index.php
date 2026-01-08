<?php

namespace App\Livewire\TeacherPanel\Grades;

use App\Livewire\Forms\StudentGradeForm;
use App\Models\Academic\TeacherSubject;
use App\Models\Assessment\GradeComponent;
use App\Models\Assessment\StudentGrade;
use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Nilai Siswa')]
class Index extends Component
{
    use WithPagination;

    public StudentGradeForm $form;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $class_filter = '';

    #[Url()]
    public string $subject_filter = '';

    #[Url()]
    public string $component_filter = '';

    #[Url()]
    public string $input_date = '';

    #[Url()]
    public string $academic_year_id = '';

    public bool $showGradeModal = false;
    public bool $editing = false;
    public array $listsForFields = [];
    public array $selectedGrades = [];
    public bool $selectAll = false;
    public string $bulkGradeValue = '';

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
            $this->form->academic_year_id = $this->academic_year_id;
        }
    }

    protected function initListsForFields(): void
    {
        // Get all academic years
        $this->listsForFields['academic_years'] = AcademicYear::orderBy('start_date', 'desc')
            ->pluck('academic_year', 'id');

        $teacherId = auth()->user()->teacher->id;

        // Get teacher subjects for current teacher and selected academic year
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

        // Get classes taught by teacher in selected academic year
        $this->listsForFields['classes'] = TeacherSubject::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->when($this->academic_year_id, function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->with('class')
            ->get()
            ->pluck('class.class_name', 'class.id')
            ->unique();

        // Get subjects taught by teacher in selected academic year
        $this->listsForFields['subjects'] = TeacherSubject::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->when($this->academic_year_id, function ($query) {
                $query->where('academic_year_id', $this->academic_year_id);
            })
            ->with('subject')
            ->get()
            ->pluck('subject.subject_name', 'subject.id')
            ->unique();

        // Get grade components
        $this->listsForFields['grade_components'] = GradeComponent::active()
            ->pluck('component_name', 'id');

        $this->listsForFields['students'] = collect();
    }

    public function updatedAcademicYearId(): void
    {
        $this->form->academic_year_id = $this->academic_year_id;
        $this->initListsForFields();
        $this->resetPage();
        $this->reset(['search', 'class_filter', 'subject_filter', 'component_filter']);
    }

    public function updatedFormTeacherSubjectId(): void
    {
        if ($this->form->teacher_subject_id) {
            $teacherSubject = TeacherSubject::with('class.classStudents.student')
                ->where('academic_year_id', $this->academic_year_id)
                ->find($this->form->teacher_subject_id);

            if ($teacherSubject) {
                $this->listsForFields['students'] = $teacherSubject->class->classStudents
                    ->where('status', 'active')
                    ->where('academic_year_id', $this->academic_year_id)
                    ->pluck('student.full_name', 'student.id');
            }
        } else {
            $this->listsForFields['students'] = collect();
        }
    }

    public function showGradeModal(): void
    {
        if (empty($this->academic_year_id)) {
            $this->showToastr('error', 'Pilih tahun akademik terlebih dahulu');
            return;
        }

        $this->editing = false;
        $this->showGradeModal = true;
        $this->form->reset();
        $this->form->input_date = $this->input_date;
        $this->form->input_teacher_id = auth()->user()->teacher->id;
        $this->form->academic_year_id = $this->academic_year_id;
        $this->listsForFields['students'] = collect();
    }

    public function editGrade($gradeId): void
    {
        $grade = StudentGrade::with(['student', 'teacherSubject', 'gradeComponent'])->findOrFail($gradeId);
        $this->editing = true;
        $this->showGradeModal = true;
        $this->form->setGrade($grade);
        $this->updatedFormTeacherSubjectId();
    }

    public function cancelEdit(): void
    {
        $this->showGradeModal = false;
        $this->editing = false;
        $this->form->reset();
        $this->selectedGrades = [];
        $this->selectAll = false;
        $this->dispatch('closeModal');
    }

    public function saveGrade(): void
    {
        try {
            if ($this->editing) {
                $this->form->update();
                $this->showToastr('success', 'Nilai siswa berhasil diperbarui');
            } else {
                $this->form->store();
                $this->showToastr('success', 'Nilai siswa berhasil ditambahkan');
            }

            $this->cancelEdit();
        } catch (\Exception $e) {
            $this->showToastr('error', $e->getMessage());
        }
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $grades = $this->getGrades();
            $this->selectedGrades = $grades->pluck('id')->toArray();
        } else {
            $this->selectedGrades = [];
        }
    }

    public function updatedSelectedGrades(): void
    {
        $grades = $this->getGrades();
        $this->selectAll = count($this->selectedGrades) === $grades->count();
    }

    public function bulkUpdateGrade(): void
    {
        if (empty($this->selectedGrades) || empty($this->bulkGradeValue)) {
            $this->showToastr('error', 'Pilih siswa dan masukkan nilai terlebih dahulu');
            return;
        }

        $this->validate([
            'bulkGradeValue' => 'required|numeric|min:0|max:100'
        ], [
            'bulkGradeValue.required' => 'Nilai harus diisi',
            'bulkGradeValue.numeric' => 'Nilai harus berupa angka',
            'bulkGradeValue.min' => 'Nilai minimal 0',
            'bulkGradeValue.max' => 'Nilai maksimal 100'
        ]);

        StudentGrade::whereIn('id', $this->selectedGrades)->update([
            'grade_value' => $this->bulkGradeValue,
            'input_teacher_id' => auth()->user()->teacher->id
        ]);

        $this->selectedGrades = [];
        $this->selectAll = false;
        $this->bulkGradeValue = '';
        $this->showToastr('success', 'Nilai berhasil diperbarui untuk siswa yang dipilih');
    }

    public function createBulkGrades(): void
    {
        if (empty($this->academic_year_id)) {
            $this->showToastr('error', 'Pilih tahun akademik terlebih dahulu');
            return;
        }

        if (!$this->form->teacher_subject_id || !$this->form->grade_component_id) {
            $this->showToastr('error', 'Pilih mata pelajaran dan komponen nilai terlebih dahulu');
            return;
        }

        $teacherSubject = TeacherSubject::with('class.classStudents.student')
            ->where('academic_year_id', $this->academic_year_id)
            ->find($this->form->teacher_subject_id);

        if (!$teacherSubject) {
            $this->showToastr('error', 'Mata pelajaran tidak ditemukan');
            return;
        }

        $students = $teacherSubject->class->classStudents
            ->where('status', 'active')
            ->where('academic_year_id', $this->academic_year_id);

        $createdCount = 0;
        foreach ($students as $classStudent) {
            // Check if grade already exists
            $exists = StudentGrade::where('student_id', $classStudent->student->id)
                ->where('teacher_subject_id', $this->form->teacher_subject_id)
                ->where('grade_component_id', $this->form->grade_component_id)
                ->whereDate('input_date', $this->form->input_date)
                ->exists();

            if (!$exists) {
                StudentGrade::create([
                    'student_id' => $classStudent->student->id,
                    'teacher_subject_id' => $this->form->teacher_subject_id,
                    'grade_component_id' => $this->form->grade_component_id,
                    'grade_value' => 0, // Default value
                    'input_date' => $this->form->input_date,
                    'input_teacher_id' => auth()->user()->teacher->id
                ]);
                $createdCount++;
            }
        }

        if ($createdCount > 0) {
            $this->showToastr('success', "Template nilai berhasil dibuat untuk {$createdCount} siswa");
        } else {
            $this->showToastr('info', 'Semua siswa sudah memiliki nilai untuk komponen ini');
        }

        $this->cancelEdit();
    }

    #[On('delete-grade')]
    public function deleteGrade($id): void
    {
        StudentGrade::findOrFail($id)->delete();
        $this->showToastr('success', 'Data nilai berhasil dihapus');
    }

    public function deleteGradeConfirm($gradeId): void
    {
        $this->dispatch('swal:confirm',
            title: 'Hapus Nilai?',
            text: 'Data nilai akan dihapus secara permanen!',
            icon: 'warning',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            method: 'delete-grade',
            params: $gradeId
        );
    }

    public function getGrades()
    {
        if (empty($this->academic_year_id)) {
            return collect()->paginate(15);
        }

        $teacherId = auth()->user()->teacher->id;

        return StudentGrade::with(['student', 'teacherSubject.subject', 'teacherSubject.class', 'gradeComponent', 'inputTeacher'])
            ->whereHas('teacherSubject', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->where('academic_year_id', $this->academic_year_id);
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
            ->when($this->component_filter, function ($query) {
                $query->where('grade_component_id', $this->component_filter);
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

        $teacherId = auth()->user()->teacher->id;
        $date = Carbon::parse($this->input_date);

        $grades = StudentGrade::whereHas('teacherSubject', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId)
                    ->where('academic_year_id', $this->academic_year_id);
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

    public function getTeacherSubjects()
    {
        if (empty($this->academic_year_id)) {
            return collect();
        }

        $teacherId = auth()->user()->teacher->id;

        return TeacherSubject::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->where('academic_year_id', $this->academic_year_id)
            ->with(['subject', 'class', 'academicYear'])
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
        $teacherSubjects = $this->getTeacherSubjects();
        $selectedAcademicYear = $this->getSelectedAcademicYear();

        return view('livewire.teacher-panel.grades.index', compact(
            'grades',
            'gradeStats',
            'teacherSubjects',
            'selectedAcademicYear'
        ));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'class_filter', 'subject_filter', 'component_filter']);
    }

    public function updatedInputDate(): void
    {
        $this->resetPage();
    }
}
