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
        'date_birth',
        'sex',
        'email',
        'phone',
        'address',
        'specialty_id',
    ];

    protected $casts = [
        'name' => 'string',
        'last_name' => 'string',
        'date_birth' => 'date',
        'sex' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'address' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'name' => 'required|string',
        'last_name' => 'nullable|string',
        'date_birth' => 'nullable',
        'sex' => 'nullable|string',
        'email' => 'nullable|string',
        'phone' => 'nullable|string',
        'address' => 'nullable|string',
        'specialty_id' => 'nullable',
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            $model->name = Helper::unaccent($model->name);
            $model->last_name = Helper::unaccent($model->last_name);
            $model->sex = Helper::unaccent($model->sex);
            $model->address = Helper::unaccent($model->address);
        });
    }

    public function incidentsReports(): HasMany
    {
        return $this->hasMany(IncidentsReport::class, 'teacher_id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }
}
