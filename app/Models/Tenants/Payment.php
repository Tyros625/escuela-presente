<?php

namespace App\Models\Tenants;

use App\Casts\ActualTimeZone;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'preference_id',
        'status',
        'payment_id',
        'merchant_order_id',
        'payment_method',
        'amount',
        'student_id',
        'data',
    ];

    protected $casts = [
        'preference_id' => 'string',
        'status' => 'string',
        'payment_id' => 'string',
        'merchant_order_id' => 'string',
        'payment_method' => 'string',
        'amount' => 'integer',
        'student_id' => 'integer',
        'data' => Json::class,
        'created_at' => ActualTimeZone::class,
        'updated_at' => ActualTimeZone::class,
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'like', '%'.'paid'.'%');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'like', '%'.'pending'.'%');
    }
}
