<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSocioeconomic extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'general',
        'ownerships',
        'nutrition',
    ];

    protected $casts = [
        'student_id' => 'string',
        'general' => Json::class,
        'ownerships' => Json::class,
        'nutrition' => Json::class,
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
