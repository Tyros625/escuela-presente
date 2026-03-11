<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    public const P_CREATE = 'create specialty';

    public const P_READ = 'read specialty';

    public const P_UPDATE = 'update specialty';

    public const P_DELETE = 'delete specialty';

    public $table = 'specialties';

    public $fillable = [
        'description',
        'code',
        'grade_id',
        'school_cycle_id',
        'hours_per_week',
        'credits',
        'training_field',
    ];

    protected $casts = [
        'description' => 'string',
        'code' => 'string',
        'grade_id' => 'integer',
        'school_cycle_id' => 'integer',
        'hours_per_week' => 'integer',
        'credits' => 'integer',
        'training_field' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'description' => 'required|string',
        'code' => 'nullable|string|max:50',
        'grade_id' => 'nullable|integer',
        'school_cycle_id' => 'nullable|integer',
        'hours_per_week' => 'nullable|integer|min:1|max:40',
        'credits' => 'nullable|integer|min:1|max:20',
        'training_field' => 'nullable|string|max:255',
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            $model->description = Helper::unaccent($model->description);
        });
    }

    public function incidentsReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'specialty_id');
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class, 'specialty_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function schoolCycle(): BelongsTo
    {
        return $this->belongsTo(SchoolCycle::class, 'school_cycle_id');
    }
}
