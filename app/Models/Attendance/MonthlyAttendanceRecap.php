<?php

namespace App\Models\Attendance;

use App\Models\Academic\Classes;
use App\Models\User\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyAttendanceRecap extends Model
{
    use HasFactory;

    protected $table = 'monthly_attendance_recap';

    protected $fillable = [
        'student_id',
        'class_id',
        'month',
        'year',
        'present_count',
        'sick_count',
        'permission_count',
        'absent_count',
        'total_effective_days',
        'attendance_percentage'
    ];

    protected $casts = [
        'attendance_percentage' => 'decimal:2',
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

    // Scopes
    public function scopeByMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }
}
