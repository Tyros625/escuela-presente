<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    public const P_CREATE = 'create incident';

    public const P_READ = 'read incident';

    public const P_UPDATE = 'update incident';

    public const P_DELETE = 'delete incident';

    public $table = 'incidents';

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
        return $this->hasMany(IncidentsReport::class, 'incident_id');
    }
}
