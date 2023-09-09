<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentHealth extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'current_general_status',
        'blood_type',
        'chronic_disease',
        'has_medical_service',
        'medical_service_number',
        'medical_service_name',
        'familiar_affection',
        'medical_care',
    ];

    protected $casts = [
        'student_id' => 'string',
        'current_general_status' => 'string',
        'blood_type' => 'string',
        'chronic_disease' => 'string',
        'has_medical_service' => 'boolean',
        'medical_service_number' => 'string',
        'medical_service_name' => 'string',
        'familiar_affection' => Json::class,
        'medical_care' => Json::class,
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
