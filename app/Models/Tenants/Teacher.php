<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    public const P_CREATE = 'create teacher';

    public const P_READ = 'read teacher';

    public const P_UPDATE = 'update teacher';

    public const P_DELETE = 'delete teacher';

    public $table = 'teachers';

    public $fillable = [
        'name',
        'last_name',
        'last_name_father',
        'last_name_mother',
        'rfc',
        'date_birth',
        'sex',
        'email',
        'institutional_email',
        'phone',
        'address',
        'specialization_id',
        'subject_id',
        'max_hours_per_week',
        'available_hours',
        'schedule_availability',
    ];

    protected $appends = ['display_name'];

    protected $casts = [
        'name' => 'string',
        'last_name' => 'string',
        'last_name_father' => 'string',
        'last_name_mother' => 'string',
        'rfc' => 'string',
        'date_birth' => 'date',
        'sex' => 'string',
        'email' => 'string',
        'institutional_email' => 'string',
        'phone' => 'string',
        'address' => 'string',
        'max_hours_per_week' => 'decimal:2',
        'available_hours' => 'string',
        'schedule_availability' => 'array',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'name' => 'required|string',
        'last_name' => 'nullable|string',
        'last_name_father' => 'nullable|string',
        'last_name_mother' => 'nullable|string',
        'rfc' => 'nullable|string',
        'date_birth' => 'nullable',
        'sex' => 'nullable|string',
        'email' => 'nullable|string',
        'institutional_email' => 'nullable|email',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'specialization_id' => 'nullable',
        'subject_id' => 'nullable',
        'max_hours_per_week' => 'nullable|numeric|min:0',
        'available_hours' => 'nullable|string',
        'schedule_availability' => 'nullable|array',
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            $model->name = $model->name ? Helper::unaccent($model->name) : null;
            $model->last_name = $model->last_name ? Helper::unaccent($model->last_name) : null;
            $model->last_name_father = $model->last_name_father ? Helper::unaccent($model->last_name_father) : null;
            $model->last_name_mother = $model->last_name_mother ? Helper::unaccent($model->last_name_mother) : null;
            $model->sex = $model->sex ? Helper::unaccent($model->sex) : null;
            $model->address = $model->address ? Helper::unaccent($model->address) : null;
        });
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(
            ($this->last_name_father ?? $this->last_name ?? '') . ' ' .
            ($this->last_name_mother ?? '') . ', ' .
            $this->name
        );
    }

    public function incidentsReports(): HasMany
    {
        return $this->hasMany(IncidentsReport::class, 'teacher_id');
    }

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'subject_id');
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class, 'teacher_id');
    }
}
