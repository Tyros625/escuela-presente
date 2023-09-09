<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    public const P_CREATE = 'create section';

    public const P_READ = 'read section';

    public const P_UPDATE = 'update section';

    public const P_DELETE = 'delete section';

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

    public function academicGroups(): HasMany
    {
        return $this->hasMany(AcademicGroup::class, 'section_id');
    }
}
