<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAcademic extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'udeei',
        'origin_school',
        'federal_entity_school',
    ];

    protected $casts = [
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
