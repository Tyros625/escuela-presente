<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialization extends Model
{
    public $table = 'specializations';

    public $fillable = [
        'description',
    ];

    protected $casts = [
        'description' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'description' => 'required|string|max:255',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class, 'specialization_id');
    }
}

