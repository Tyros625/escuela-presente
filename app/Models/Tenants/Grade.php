<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    public const P_CREATE = 'create grade';

    public const P_READ = 'read grade';

    public const P_UPDATE = 'update grade';

    public const P_DELETE = 'delete grade';

    public $fillable = [
        'description',
        'order',
    ];

    protected $casts = [
        'description' => 'string',
        'order' => 'integer',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'description' => 'required|string',
        'order' => 'required|integer|unique:grades,order',
    ];

    public function academicGroups(): HasMany
    {
        return $this->hasMany(AcademicGroup::class, 'grade_id');
    }
}
