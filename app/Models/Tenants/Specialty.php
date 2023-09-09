<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Helper;
use Illuminate\Database\Eloquent\Model;
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
    ];

    protected $casts = [
        'description' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'description' => 'required|string',
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
}
