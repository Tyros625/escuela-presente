<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentGrade extends Model
{
    use HasFactory;

    protected $table = 'student_grades';

    protected $fillable = [
        'student_id',
        'academic_group_id',
        'subject',
        'partial_1',
        'partial_2',
        'partial_3',
        'average',
        'status',
    ];

    protected $casts = [
        'partial_1' => 'float',
        'partial_2' => 'float',
        'partial_3' => 'float',
        'average' => 'float',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(StudentGradeHistory::class);
    }
}

