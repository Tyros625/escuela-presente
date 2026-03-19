<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGradeHistory extends Model
{
    use HasFactory;

    protected $table = 'student_grade_histories';

    protected $fillable = [
        'student_grade_id',
        'changed_at',
        'field_changed',
        'old_value',
        'new_value',
        'reason',
        'changed_by',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'old_value' => 'float',
        'new_value' => 'float',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function studentGrade(): BelongsTo
    {
        return $this->belongsTo(StudentGrade::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

