<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'timezone',
        'city',
        'language',
        'files_location',
        'user_id',
    ];

    protected $casts = [
        'observation' => 'string',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'country' => 'required',
        'timezone' => 'required',
        'city' => 'required',
        'language' => 'required',
        'files_location' => 'required',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
