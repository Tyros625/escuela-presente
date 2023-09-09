<?php

namespace App\Models\Tenants;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;

    public const P_CREATE = 'create dashboard';

    public const P_READ = 'read dashboard';

    public const P_UPDATE = 'update dashboard';

    public const P_DELETE = 'delete dashboard';
}
