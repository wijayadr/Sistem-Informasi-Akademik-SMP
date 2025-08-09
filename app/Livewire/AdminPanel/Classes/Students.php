<?php

namespace App\Livewire\AdminPanel\Classes;

use App\Livewire\Forms\ClassStudentsForm;
use App\Models\Academic\Classes;
use App\Models\Academic\ClassStudent;
use App\Models\User\Student;
use App\Models\Master\AcademicYear;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kelola Siswa Kelas')]
class Students extends Component
{
    use WithPagination;

    public Classes $class;
    public ClassStudentsForm $form;

    #[Url()]
    public string $search = '';

    #[Url()]
    public string $status_filter = '';

    public array $selectedStudents = [];
    public bool $selectAll = false;

    public function mount(Classes $class): void
    {
        $this->class = $class->load(['academicYear', 'homeroomTeacher']);
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedStudents = $this->getAvailableStudents()->pluck('id')->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function updatedSelectedStudents(): void
    {
        $this->selectAll = count($this->selectedStudents) === $this->getAvailableStudents()->count();
    }

    public function assignStudents(): void
    {
        $this->validate([
            'selectedStudents' => 'required|array|min:1',
            'selectedStudents.*' => 'exists:students,id'
        ], [
            'selectedStudents.required' => 'Pilih minimal satu siswa untuk ditugaskan',
            'selectedStudents.min' => 'Pilih minimal satu siswa untuk ditugaskan',
        ]);

        $academicYear = AcademicYear::where('status', 'active')->first();

        if (!$academicYear) {
            $this->showToastr('error', 'Tidak ada tahun ajaran aktif');
            return;
        }

        // Check capacity
        $currentStudentsCount = $this->class->getActiveStudentsCount();
        $newStudentsCount = count($this->selectedStudents);

        if (($currentStudentsCount + $newStudentsCount) > $this->class->capacity) {
            $this->showToastr('error', 'Kapasitas kelas tidak mencukupi');
            return;
        }

        foreach ($this->selectedStudents as $studentId) {
            // Check if student already assigned to this class
            $exists = ClassStudent::where('student_id', $studentId)
                ->where('class_id', $this->class->id)
                ->where('status', 'active')
                ->exists();

            if (!$exists) {
                ClassStudent::create([
                    'student_id' => $studentId,
                    'class_id' => $this->class->id,
                    'academic_year_id' => $academicYear->id,
                    'class_entry_date' => now(),
                    'status' => 'active'
                ]);
            }
        }

        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->showToastr('success', 'Siswa berhasil ditugaskan ke kelas');
    }

    #[On('remove-student')]
    public function removeStudent($id): void
    {
        $classStudent = ClassStudent::where('student_id', $id)
            ->where('class_id', $this->class->id)
            ->where('status', 'active')
            ->first();

        if ($classStudent) {
            $classStudent->update([
                'status' => 'moved',
                'class_exit_date' => now()
            ]);
            $this->showToastr('success', 'Siswa berhasil dikeluarkan dari kelas');
        }
    }

    public function removeStudentConfirm($studentId): void
    {
        $this->dispatch('swal:confirm',
            title: 'Keluarkan Siswa?',
            text: 'Siswa akan dikeluarkan dari kelas ini!',
            icon: 'warning',
            confirmButtonText: 'Ya, Keluarkan!',
            cancelButtonText: 'Batal',
            method: 'remove-student',
            params: $studentId
        );
    }

    public function getAvailableStudents()
    {
        return Student::active()
            ->whereDoesntHave('classStudents', function ($query) {
                $query->where('status', 'active');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('full_name')
            ->get();
    }

    public function getCurrentStudents()
    {
        return $this->class->classStudents()
            ->with('student')
            ->where('status', 'active')
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);
    }

    public function render()
    {
        $currentStudents = $this->getCurrentStudents();
        $availableStudents = $this->getAvailableStudents();

        return view('livewire.admin-panel.classes.students', compact('currentStudents', 'availableStudents'));
    }

    public function showToastr($type, $message): void
    {
        $this->dispatch('show:toastify', type: $type, message: $message);
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status_filter', 'selectedStudents', 'selectAll']);
    }
}
