<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralConfiguration extends Model
{
    use HasFactory;

    public const P_CREATE = 'create general configuration';

    public const P_READ = 'read general configuration';

    public const P_UPDATE = 'update general configuration';

    public const P_DELETE = 'delete general configuration';

    protected $table = 'general_configuration';

    protected $fillable = [
        'name',
        'cct',
        'modality',
        'address',
        'coordinates',
        'email',
        'phone',
        'website',
        'fiscal_data',
        'logo',
        'last_enrollment',
        'plan',
        'prices',
        'custom_messages',
    ];

    protected $casts = [
        'name' => 'string',
        'cct' => 'string',
        'modality' => 'string',
        'address' => 'string',
        'coordinates' => Json::class,
        'email' => 'string',
        'phone' => 'string',
        'website' => 'string',
        'fiscal_data' => Json::class,
        'logo' => 'string',
        'last_enrollment' => 'integer',
        'plan' => Json::class,
        'prices' => Json::class,
        'custom_messages' => Json::class,
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'name' => 'required',
        'cct' => 'required',
        'modality' => 'required',
        'address' => 'required',
        'coordinates' => 'required',
        'email' => 'required|email',
        'phone' => 'required|integer',
        'website' => 'required',
        'fiscal_data' => 'required',
        'logo' => 'nullable',
        'last_enrollment' => 'required',
    ];
}
