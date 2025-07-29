<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'component_name',
        'weight_percentage',
        'description',
        'status'
    ];

    protected $casts = [
        'weight_percentage' => 'decimal:2',
    ];

    // Relationships
    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
