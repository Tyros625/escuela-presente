<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRelative extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'father_data',
        'mother_data',
        'authorized_persons',
        'roommates',
    ];

    protected $casts = [
        'student_id' => 'string',
        'father_data' => Json::class,
        'mother_data' => Json::class,
        'authorized_persons' => Json::class,
        'roommates' => Json::class,
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
