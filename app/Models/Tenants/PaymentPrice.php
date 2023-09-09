<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPrice extends Model
{
    use HasFactory;

    public const P_CREATE = 'create payment prices';

    public const P_READ = 'read payment prices';

    public const P_UPDATE = 'update payment prices';

    public const P_DELETE = 'delete payment prices';

    public $fillable = [
        'description',
        'amount',
    ];

    protected $casts = [
        'description' => 'string',
        'amount' => 'integer',
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public static $rules = [
        'description' => 'required|string',
        'amount' => 'required|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
    ];
}
