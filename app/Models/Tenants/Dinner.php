<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dinner extends Model
{
    use HasFactory;

    public const P_CREATE = 'create dinner';

    public const P_READ = 'read dinner';

    public const P_UPDATE = 'update dinner';

    public const P_DELETE = 'delete dinner';
}
