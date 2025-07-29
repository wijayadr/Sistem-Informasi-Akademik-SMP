<?php

namespace App\Models\Assessment;

use App\Models\Academic\TeacherSubject;
use App\Models\User\Student;
use App\Models\User\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_subject_id',
        'grade_component_id',
        'grade_value',
        'input_date',
        'notes',
        'input_teacher_id'
    ];

    protected $casts = [
        'grade_value' => 'decimal:2',
        'input_date' => 'date',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacherSubject(): BelongsTo
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function gradeComponent(): BelongsTo
    {
        return $this->belongsTo(GradeComponent::class);
    }

    public function inputTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'input_teacher_id');
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->whereHas('teacherSubject', function ($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }
}
