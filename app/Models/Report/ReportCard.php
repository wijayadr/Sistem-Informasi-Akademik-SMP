<?php

namespace App\Models\Report;

use App\Models\Academic\Classes;
use App\Models\Master\AcademicYear;
use App\Models\Master\Subject;
use App\Models\User\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'class_id',
        'academic_year_id',
        'semester',
        'knowledge_grade',
        'skill_grade',
        'attitude_grade',
        'teacher_notes',
        'status'
    ];

    protected $casts = [
        'knowledge_grade' => 'decimal:2',
        'skill_grade' => 'decimal:2',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Scopes
    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }
}
