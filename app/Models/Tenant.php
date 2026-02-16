<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory;

    /**
     * So that VirtualColumn (HasDataColumn) persists 'active' to the real DB column,
     * not into the JSON 'data' column. Otherwise toggleActive() never updates the table.
     */
    public static function getCustomColumns(): array
    {
        return array_merge(parent::getCustomColumns(), ['active', 'access_start', 'access_end']);
    }

    public static function booted()
    {
        static::creating(function ($tenant) {
            $tenant->password = Hash::make($tenant->password);
        });
    }
}
