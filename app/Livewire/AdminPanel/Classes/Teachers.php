<?php

namespace App\Livewire\AdminPanel\Classes;

use App\Livewire\Forms\TeacherSubjectForm;
use App\Models\Academic\Classes;
use App\Models\Academic\TeacherSubject;
use App\Models\User\Teacher;
use App\Models\Master\Subject;
use App\Models\Master\AcademicYear;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kelola Guru Kelas')]
class Teachers extends Component
{
    use WithPagination;

    public Classes $class;
    public TeacherSubjectForm $form;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $subject_filter = '';

    public bool $showAssignModal = false;
    public array $listsForFields = [];

    public function mount(Classes $class): void
    {
        $this->class = $class->load(['academicYear', 'homeroomTeacher']);
        $this->initListsForFields();
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['teachers'] = Teacher::active()->pluck('full_name', 'id');
        $this->listsForFields['subjects'] = Subject::active()->pluck('subject_name', 'id');
    }

    public function showModalTeacher(): void
    {
        $this->showAssignModal = true;
        $this->form->class_id = $this->class->id;

        $academicYear = AcademicYear::where('status', 'active')->first();
        if ($academicYear) {
            $this->form->academic_year_id = $academicYear->id;
        }
    }

    public function cancelEdit(): void
    {
        $this->showAssignModal = false;
        $this->form->reset();
        $this->dispatch('closeModal');
    }

    public function assignTeacher(): void
    {
        $this->form->class_id = $this->class->id;

        // Check if teacher-subject combination already exists for this class
        $exists = TeacherSubject::where('teacher_id', $this->form->teacher_id)
            ->where('subject_id', $this->form->subject_id)
            ->where('class_id', $this->class->id)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            $this->showToastr('error', 'Guru sudah mengajar mata pelajaran ini di kelas ini');
            return;
        }

        $this->form->store();
        $this->showToastr('success', 'Guru berhasil ditugaskan ke mata pelajaran');
        $this->dispatch('closeModal');
    }

    #[On('remove-teacher-subject')]
    public function removeTeacherSubject($teacherSubjectId): void
    {
        $teacherSubject = TeacherSubject::findOrFail($teacherSubjectId);
        $teacherSubject->update(['status' => 'inactive']);
        $this->showToastr('success', 'Penugasan guru berhasil dihapus');
    }

    public function removeTeacherSubjectConfirm($teacherSubjectId): void
    {
        $this->dispatch('swal:confirm',
            title: 'Hapus Penugasan?',
            text: 'Guru akan dihapus dari mata pelajaran ini!',
            icon: 'warning',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            method: 'remove-teacher-subject',
            params: $teacherSubjectId
        );
    }

    public function getTeacherSubjects()
    {
        return $this->class->teacherSubjects()
            ->with(['teacher', 'subject', 'academicYear'])
            ->where('status', 'active')
            ->when($this->search, function ($query) {
                $query->whereHas('teacher', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('subject', function ($q) {
                    $q->where('subject_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->subject_filter, function ($query) {
                $query->where('subject_id', $this->subject_filter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function render()
    {
        $teacherSubjects = $this->getTeacherSubjects();
        $availableSubjects = Subject::active()->get();

        return view('livewire.admin-panel.classes.teachers', compact('teacherSubjects', 'availableSubjects'));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'subject_filter']);
    }
}
