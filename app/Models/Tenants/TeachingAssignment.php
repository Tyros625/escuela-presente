<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeachingAssignment extends Model
{
    protected $table = 'teaching_assignments';

    protected $fillable = [
        'teacher_id',
        'specialty_id',
        'academic_group_id',
        'shift',
        'day_of_week',
        'time_slot',
        'assignment_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'academic_group_id');
    }
}
