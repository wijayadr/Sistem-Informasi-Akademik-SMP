<?php

namespace App\Models\Academic;

use App\Models\Attendance\MonthlyAttendanceRecap;
use App\Models\Master\AcademicYear;
use App\Models\Report\ReportCard;
use App\Models\User\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'grade_level',
        'capacity',
        'academic_year_id',
        'homeroom_teacher_id',
        'status'
    ];

    // Relationships
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class, 'class_id');
    }

    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class, 'class_id');
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class, 'class_id');
    }

    public function monthlyAttendanceRecaps(): HasMany
    {
        return $this->hasMany(MonthlyAttendanceRecap::class, 'class_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByGrade($query, $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    // Helper methods
    public function getActiveStudentsCount()
    {
        return $this->classStudents()->where('status', 'active')->count();
    }

    public function getAvailableCapacity()
    {
        return $this->capacity - $this->getActiveStudentsCount();
    }
}
