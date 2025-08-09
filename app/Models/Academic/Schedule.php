<?php

namespace App\Models\Academic;

use App\Models\Attendance\StudentAttendance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_subject_id',
        'day',
        'start_time',
        'end_time',
        'classroom',
        'notes',
        'status'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // Relationships
    public function teacherSubject(): BelongsTo
    {
        return $this->belongsTo(TeacherSubject::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    /**
     * Get the academic year through teacher subject
     */
    public function academicYear(): HasOneThrough
    {
        return $this->hasOneThrough(
            AcademicYear::class,
            TeacherSubject::class,
            'id', // Foreign key on teacher_subjects table
            'id', // Foreign key on academic_years table
            'teacher_subject_id', // Local key on schedules table
            'academic_year_id' // Local key on teacher_subjects table
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByDay($query, $day)
    {
        return $query->where('day', $day);
    }

    public function scopeToday($query)
    {
        $today = strtolower(now()->format('l'));
        return $query->where('day', $today);
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->whereHas('teacherSubject', function ($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
    }

    public function scopeCurrentAcademicYear($query)
    {
        return $query->whereHas('teacherSubject.academicYear', function ($q) {
            $q->where('status', 'active');
        });
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->whereHas('teacherSubject', function ($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        });
    }

    public function scopeTimeRange($query, $startTime, $endTime)
    {
        return $query->where(function ($q) use ($startTime, $endTime) {
            $q->where(function ($subQ) use ($startTime) {
                $subQ->whereTime('start_time', '<=', $startTime)
                     ->whereTime('end_time', '>', $startTime);
            })->orWhere(function ($subQ) use ($endTime) {
                $subQ->whereTime('start_time', '<', $endTime)
                     ->whereTime('end_time', '>=', $endTime);
            })->orWhere(function ($subQ) use ($startTime, $endTime) {
                $subQ->whereTime('start_time', '>=', $startTime)
                     ->whereTime('end_time', '<=', $endTime);
            });
        });
    }

    public function scopeConflictsWith($query, $day, $startTime, $endTime, $excludeId = null)
    {
        $query = $query->where('day', $day)
            ->where('status', 'active')
            ->timeRange($startTime, $endTime);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }

    // Helper methods
    public function getDurationInMinutes(): int
    {
        return $this->end_time->diffInMinutes($this->start_time);
    }

    public function getFormattedDuration(): string
    {
        $minutes = $this->getDurationInMinutes();

        if ($minutes < 60) {
            return $minutes . ' menit';
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes == 0) {
            return $hours . ' jam';
        }

        return $hours . ' jam ' . $remainingMinutes . ' menit';
    }

    public function isCurrentlyActive(): bool
    {
        $today = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        return $this->day === $today &&
               $this->start_time->format('H:i') <= $currentTime &&
               $this->end_time->format('H:i') >= $currentTime &&
               $this->status === 'active';
    }

    public function isUpcoming(): bool
    {
        $today = strtolower(now()->format('l'));
        $currentTime = now()->format('H:i');

        return $this->day === $today &&
               $this->start_time->format('H:i') > $currentTime &&
               $this->status === 'active';
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusText(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'cancelled' => 'Dibatalkan',
            default => 'Tidak Diketahui'
        };
    }
}
