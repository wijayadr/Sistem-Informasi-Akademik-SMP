<?php

namespace App\Models\User;

use App\Models\Academic\Classes;
use App\Models\Academic\TeacherSubject;
use App\Models\Assessment\StudentGrade;
use App\Models\Attendance\StudentAttendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'full_name',
        'birth_date',
        'gender',
        'phone_number',
        'address',
        'last_education',
        'employment_status'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(Classes::class, 'homeroom_teacher_id');
    }

    public function teacherSubjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function inputAttendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class, 'input_teacher_id');
    }

    public function inputGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'input_teacher_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('status', 'active');
        });
    }
}
