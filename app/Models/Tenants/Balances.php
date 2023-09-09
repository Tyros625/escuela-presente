<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balances extends Model
{
    public const P_CREATE = 'create balance';

    public const P_READ = 'read balance';

    public const P_UPDATE = 'update balance';

    public const P_DELETE = 'delete balance';

    public $table = 'balances';

    public $fillable = [
        'student_id',
        'amount',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'student_enrollment' => 'required',
        'amount' => 'required',
        'type' => 'required|string',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function scopeGrade($query, $grade)
    {
        return $query->whereHas('student.academic', function ($q) use ($grade) {
            $q->where('grade', $grade);
        });
    }

    public function scopeGroup($query, $group)
    {
        return $query->whereHas('student.academic', function ($q) use ($group) {
            $q->where('group', $group);
        });
    }
}
