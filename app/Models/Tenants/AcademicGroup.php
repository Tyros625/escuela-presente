<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicGroup extends Model
{
    public $table = 'academic_groups';

    public $fillable = [
        'grade_id',
        'section_id',
        'school_cycle_id',
        'shift',
        'room_name',
        'student_limit',
        'subjects',
    ];

    protected $casts = [
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
        'subjects' => 'array',
    ];

    public static $rules = [
        'grade_id' => 'required',
        'section_id' => 'required',
        'school_cycle_id' => 'required',
        'shift' => 'required|in:morning,afternoon',
        'room_name' => 'nullable|string|max:255',
        'student_limit' => 'required',
        'subjects' => 'nullable|array|max:20',
        'subjects.*' => 'nullable|string|max:255',
    ];

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function schoolCycle(): BelongsTo
    {
        return $this->belongsTo(SchoolCycle::class, 'school_cycle_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'academic_group_id');
    }
}
