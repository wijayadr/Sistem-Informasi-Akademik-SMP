<?php

namespace App\Models\Academic;

use App\Models\Master\AcademicYear;
use App\Models\User\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'class_entry_date',
        'class_exit_date',
        'status'
    ];

    protected $casts = [
        'class_entry_date' => 'date',
        'class_exit_date' => 'date',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
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
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrentYear($query)
    {
        return $query->whereHas('academicYear', function ($q) {
            $q->where('status', 'active');
        });
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeActiveInAcademicYear($query, $academicYearId)
    {
        return $query->where('status', 'active')
                     ->where('academic_year_id', $academicYearId);
    }
}
