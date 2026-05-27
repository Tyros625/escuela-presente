<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Services\AcademicGroupColorService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicGroup extends Model
{
    public $table = 'academic_groups';

    public $fillable = [
        'name',
        'color',
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

    public static function booted(): void
    {
        static::saving(function (AcademicGroup $group) {
            $resolved = AcademicGroupColorService::resolveForGroup($group);
            if ($resolved !== null) {
                $group->attributes['color'] = $resolved;
            }
        });
    }

    protected function color(): Attribute
    {
        return Attribute::get(function (?string $value) {
            $resolved = AcademicGroupColorService::resolveForGroup($this);

            return $resolved ?? $value;
        });
    }

    public static $rules = [
        'name' => 'required|string|max:255',
        'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'grade_id' => 'required',
        'section_id' => 'nullable|exists:sections,id',
        'school_cycle_id' => 'required',
        'shift' => 'required|in:morning,afternoon',
        'room_name' => 'nullable|string|max:255',
        'student_limit' => 'required|integer|min:1',
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
