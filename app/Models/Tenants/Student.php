<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Helper;
use App\Scopes\ActiveScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Request;

class Student extends Model
{
    use HasFactory;
    use Notifiable;

    public const P_CREATE = 'create student';

    public const P_READ = 'read student';

    public const P_UPDATE = 'update student';

    public const P_DELETE = 'delete student';

    public $fillable = [
        'enrollment',
        'name',
        'last_name_father',
        'last_name_mother',
        'nationality',
        'curp',
        'date_birth',
        'place_birth',
        'sex',
        'weight',
        'height',
        'is_migrant',
        'indigenous_group',
        'indigenous_language',
        'disability',
        'health_insurance',
        'scholarship',
        'address',
        'colony',
        'postal_code',
        'municipality',
        'federal_entity',
        'home_phone',
        'email',
        'photo',
        'active',
        'academic_group_id',
        'mercado_pago_id',
    ];

    protected $casts = [
        'enrollment' => 'string',
        'name' => 'string',
        'last_name_father' => 'string',
        'last_name_mother' => 'string',
        'nationality' => 'string',
        'curp' => 'string',
        'date_birth' => 'date',
        'place_birth' => 'string',
        'sex' => 'string',
        'weight' => 'string',
        'height' => 'string',
        'is_migrant' => 'boolean',
        'indigenous_group' => 'string',
        'indigenous_language' => 'string',
        'disability' => 'string',
        'health_insurance' => 'string',
        'scholarship' => 'string',
        'address' => 'string',
        'colony' => 'string',
        'postal_code' => 'string',
        'municipality' => 'string',
        'federal_entity' => 'string',
        'home_phone' => 'string',
        'email' => 'string',
        'photo' => 'string',
        'active' => 'boolean',
        'academic_group_id' => 'integer',
        'mercado_pago_id' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'name' => 'required|string',
        'last_name_father' => 'required|string',
        'last_name_mother' => 'required|string',
        'nationality' => 'required|string',
        'curp' => 'required|string|size:18|unique:students',
        // 'curp' => 'required|string|size:18|unique:students,curp,' . $this->id,
        'date_birth' => 'required',
        'place_birth' => 'required|string',
        'sex' => 'required|string',
        'weight' => 'required|numeric',
        'height' => 'required|numeric',
        'is_migrant' => 'required|boolean',
        'indigenous_group' => 'required|string',
        'indigenous_language' => 'required|string',
        'disability' => 'required|string',
        'health_insurance' => 'required|string',
        'scholarship' => 'required|string',
        'address' => 'required|string',
        'colony' => 'required|string',
        'postal_code' => 'required',
        'municipality' => 'required|string',
        'federal_entity' => 'required|string',
        'home_phone' => 'required|between:8,12',
        'email' => 'required|email|indisposable',
        'photo' => 'nullable|string',
        'academic_group_id' => 'nullable',
        'academic' => 'required',
        'academic.udeei' => 'nullable',
        'academic.origin_school' => 'required',
        'academic.federal_entity_school' => 'required',
        'relatives.student_live_with' => 'required',
        //
        'relatives.father_data.name' => 'required_if:relatives.student_live_with,ambos|required_if:relatives.student_live_with,papa',
        'relatives.father_data.occupation' => 'required_if:relatives.student_live_with,ambos|required_if:relatives.student_live_with,papa',
        'relatives.father_data.relationship' => 'required_if:relatives.student_live_with,ambos|required_if:relatives.student_live_with,papa',
        'relatives.father_data.email' => 'nullable|email|indisposable',
        'relatives.father_data.work_phone' => 'nullable|between:8,12',
        'relatives.father_data.work_address' => 'nullable',
        'relatives.father_data.phone_whatsapp' => 'nullable|between:8,12',
        //
        'relatives.mother_data.name' => 'required_if:relatives.student_live_with,ambos|required_if:relatives.student_live_with,mama',
        'relatives.mother_data.occupation' => 'required_if:relatives.student_live_with,ambos|required_if:relatives.student_live_with,mama',
        'relatives.mother_data.relationship' => 'required_if:relatives.student_live_with,ambos|required_if:relatives.student_live_with,mama',
        'relatives.mother_data.email' => 'nullable|email|indisposable',
        'relatives.mother_data.work_phone' => 'nullable|between:8,12',
        'relatives.mother_data.work_address' => 'nullable',
        'relatives.mother_data.phone_whatsapp' => 'nullable|between:8,12',
        //
        'relatives.authorized_persons' => 'required',
        'relatives.roommates' => 'required',
        'socioeconomics.general' => 'required',
        'socioeconomics.ownerships' => 'required',
        'socioeconomics.nutrition' => 'required',
        'healths.current_general_status' => 'required',
        'healths.blood_type' => 'required',
        'healths.chronic_disease' => 'required',
        'healths.has_medical_service' => 'required',
        'healths.medical_service_number' => 'required',
        'healths.medical_service_name' => 'required',
        'healths.familiar_affection' => 'required',
        'healths.medical_care' => 'required',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ActiveScope);
    }

    public static function booted()
    {
        static::creating(function ($model) {
            if (! str_contains(Request::getRequestUri(), 'import')) {
                $config = GeneralConfiguration::first();
                $model->enrollment = str_pad(intval($config->last_enrollment) + 1, 7, '0', STR_PAD_LEFT);
            }
            $model->name = Helper::unaccent($model->name);
            $model->last_name_father = Helper::unaccent($model->last_name_father);
            $model->last_name_mother = Helper::unaccent($model->last_name_mother);
            $model->nationality = Helper::unaccent($model->nationality);
            $model->curp = Helper::unaccent($model->curp);
            $model->place_birth = Helper::unaccent($model->place_birth);
            $model->sex = Helper::unaccent($model->sex);
            $model->indigenous_group = Helper::unaccent($model->indigenous_group);
            $model->indigenous_language = Helper::unaccent($model->indigenous_language);
            $model->disability = Helper::unaccent($model->disability);
            $model->health_insurance = Helper::unaccent($model->health_insurance);
            $model->scholarship = Helper::unaccent($model->scholarship);
            $model->address = Helper::unaccent($model->address);
            $model->colony = Helper::unaccent($model->colony);
            $model->municipality = Helper::unaccent($model->municipality);
            $model->federal_entity = Helper::unaccent($model->federal_entity);
        });

        static::updating(function ($model) {
            $model->name = Helper::unaccent($model->name);
            $model->last_name_father = Helper::unaccent($model->last_name_father);
            $model->last_name_mother = Helper::unaccent($model->last_name_mother);
            $model->nationality = Helper::unaccent($model->nationality);
            $model->curp = Helper::unaccent($model->curp);
            $model->place_birth = Helper::unaccent($model->place_birth);
            $model->sex = Helper::unaccent($model->sex);
            $model->indigenous_group = Helper::unaccent($model->indigenous_group);
            $model->indigenous_language = Helper::unaccent($model->indigenous_language);
            $model->disability = Helper::unaccent($model->disability);
            $model->health_insurance = Helper::unaccent($model->health_insurance);
            $model->scholarship = Helper::unaccent($model->scholarship);
            $model->address = Helper::unaccent($model->address);
            $model->colony = Helper::unaccent($model->colony);
            $model->municipality = Helper::unaccent($model->municipality);
            $model->federal_entity = Helper::unaccent($model->federal_entity);
        });

        static::deleting(function ($student) {
            $student->academic()->delete();
            $student->health()->delete();
            $student->relative()->delete();
            $student->socioeconomic()->delete();
            $student->payments()->delete();
            $student->assists()->delete();
            $student->incidentReports()->delete();
        });
    }

    public function academic(): HasOne
    {
        return $this->hasOne(StudentAcademic::class);
    }

    public function health(): HasOne
    {
        return $this->hasOne(StudentHealth::class);
    }

    public function relative(): HasOne
    {
        return $this->hasOne(StudentRelative::class);
    }

    public function socioeconomic(): HasOne
    {
        return $this->hasOne(StudentSocioeconomic::class);
    }

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class, 'academic_group_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    public function assists(): HasMany
    {
        return $this->hasMany(Assist::class, 'student_id');
    }

    public function incidentReports(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'student_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->date_birth)->age;
    }

    public function getAgeDiffAttribute(): string
    {
        return Carbon::parse($this->date_birth)
            ->diff(Carbon::now())
            ->format('%y years, %m months and %d days');
    }

    // Querys
    public static function actives()
    {
        return Student::where('active', true)->get();
    }

    public static function inactives(): Student
    {
        return Student::withoutGlobalScope('active');
    }

    // Scopes
    public function scopeActive($query): mixed
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query): mixed
    {
        return $query->where('active', false);
    }

    public function scopeGrade($query, $grade): mixed
    {
        return $query->whereHas('academicGroup.grade', function ($q) use ($grade) {
            $q->where('description', $grade);
        });
    }

    public function scopeGroup($query, $group): mixed
    {
        return $query->whereHas('academicGroup.section', function ($q) use ($group) {
            $q->where('description', $group);
        });
    }

    public function scopeName($query, $name): mixed
    {
        return $query->where('name', 'like', '%'.$name.'%')
            ->orWhere('last_name_father', 'like', '%'.$name.'%')
            ->orWhere('last_name_mother', 'like', '%'.$name.'%');
    }
}
