<?php

namespace App\Models\User;

use App\Models\Academic\ClassStudent;
use App\Models\Assessment\StudentGrade;
use App\Models\Attendance\MonthlyAttendanceRecap;
use App\Models\Attendance\StudentAttendance;
use App\Models\Report\ReportCard;
use App\Models\Report\StudentReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'national_student_id',
        'full_name',
        'birth_date',
        'gender',
        'birth_place',
        'address',
        'phone_number',
        'father_name',
        'mother_name',
        'father_occupation',
        'mother_occupation',
        'enrollment_date',
        'status'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parents(): HasMany
    {
        return $this->hasMany(ParentModel::class);
    }

    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function monthlyAttendanceRecaps(): HasMany
    {
        return $this->hasMany(MonthlyAttendanceRecap::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class);
    }

    public function studentReports(): HasMany
    {
        return $this->hasMany(StudentReport::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->whereHas('classStudents', function ($q) use ($classId) {
            $q->where('class_id', $classId)->where('status', 'active');
        });
    }

    // Helper methods
    public function getCurrentClass()
    {
        return $this->classStudents()
            ->where('status', 'active')
            ->with('class')
            ->first()?->class;
    }
}
