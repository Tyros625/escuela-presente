<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assist extends Model
{
    public $table = 'assists';

    protected $guard_name = 'api';

    public const P_CREATE = 'create assist';

    public const P_READ = 'read assist';

    public const P_UPDATE = 'update assist';

    public const P_DELETE = 'delete assist';

    public $fillable = [
        'student_id',
        'observation',
    ];

    protected $casts = [
        'observation' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'student_id' => 'required',
        'observation' => 'nullable|string',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function scopeGrade($query, $grade)
    {
        return $query->whereHas('student.academicGroup.grade', function ($q) use ($grade) {
            $q->where('description', $grade);
        });
    }

    public function scopeGroup($query, $group)
    {
        return $query->whereHas('student.academicGroup.section', function ($q) use ($group) {
            $q->where('description', $group);
        });
    }
}
