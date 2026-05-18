<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Tenants\GeneralConfiguration;
use App\Models\Tenants\Role;
use App\Models\Tenants\User;
use App\Services\TardinessService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateTenantAdmin implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Tenant $tenant)
    {
        //
    }

    public function handle()
    {
        $this->tenant->run(function ($tenant) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => $tenant->email,
                'password' => $tenant->password,
            ]);
            $user->assignRole(Role::ROLE_SUPER_ADMIN);

            GeneralConfiguration::create([
                'name' => $tenant->school_name,
                'cct' => $tenant->cct,
                'modality' => 'MATUTINA',
                'address' => 'Dirección',
                'coordinates' => [
                    'lat' => '19.5096103',
                    'lng' => '-99.1593793',
                ],
                'email' => 'hola@hola.com',
                'phone' => '987654321',
                'website' => 'https://www.google.com.pe/',
                'fiscal_data' => [
                    'billing_name' => 'DAVID HERRERA ALMERAYA',
                    'rfc' => 'HEAD840903M63',
                    'tax_regime' => 'Incorporación Fiscal',
                    'postal_code' => '07969',
                    'billing_address' => 'CALZADA VALLEJO #2421, CIUDAD DE MÉXICO',
                ],
                'plan' => [
                    'name' => 'Gratis',
                    'limit' => 50,
                ],
                'prices' => [
                    'reentry' => 50,
                    'credentials' => 50,
                    'replacement' => 50,
                ],
                'last_enrollment' => '1000000',
                'school_schedule' => TardinessService::defaultSchoolSchedule(),
                'tardiness_schedule' => TardinessService::defaultTardinessSchedule(),
            ]);
        });
    }
}
