<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentReport extends Model
{
    public const P_CREATE = 'create incident report';

    public const P_READ = 'read incident report';

    public const P_UPDATE = 'update incident report';

    public const P_DELETE = 'delete incident report';

    public $table = 'incidents_report';

    public $fillable = [
        'student_id',
        'incident_id',
        'teacher_id',
        'specialty_id',
        'photo',
        'observations',
    ];

    protected $casts = [
        'photo' => 'string',
        'observations' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'student_id' => 'required',
        'incident_id' => 'required',
        'teacher_id' => 'required',
        'specialty_id' => 'required',
        'photo' => 'nullable',
        'observations' => 'nullable|string',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'specialty_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
