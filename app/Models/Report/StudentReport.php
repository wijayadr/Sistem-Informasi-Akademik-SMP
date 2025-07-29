<?php

namespace App\Models\Report;

use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'semester',
        'class_rank',
        'average_grade',
        'total_school_days',
        'present_days',
        'sick_days',
        'permission_days',
        'absent_days',
        'homeroom_teacher_notes',
        'principal_notes',
        'status',
        'publish_date'
    ];

    protected $casts = [
        'average_grade' => 'decimal:2',
        'publish_date' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeBySemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }

    // Helper methods
    public function getAttendancePercentageAttribute()
    {
        if ($this->total_school_days == 0) return 0;
        return round(($this->present_days / $this->total_school_days) * 100, 2);
    }
}
