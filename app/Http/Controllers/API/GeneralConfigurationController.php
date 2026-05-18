<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralConfigurationRequest;
use App\Models\Tenants\GeneralConfiguration;
use App\Services\TardinessService;

class GeneralConfigurationController extends Controller
{
    public function index()
    {
        $config = GeneralConfiguration::first();

        if ($config) {
            $this->applyConfigDefaults($config);
        }

        return response()->json($config, 200);
    }

    public function update(GeneralConfigurationRequest $request)
    {
        $config = GeneralConfiguration::first();
        $config->fill($request->all());
        $config->update();
        $this->applyConfigDefaults($config);

        return response()->json($config);
    }

    private function applyConfigDefaults(GeneralConfiguration $config): void
    {
        $config->school_schedule = TardinessService::mergeSchoolSchedule($config->school_schedule);
        $config->tardiness_schedule = TardinessService::mergeTardinessSchedule($config->tardiness_schedule);
        $config->custom_messages = array_merge(
            ['incidents' => ''],
            $config->custom_messages ?? []
        );
        $config->fiscal_data = array_merge(
            [
                'billing_name' => '',
                'rfc' => '',
                'tax_regime' => '',
                'postal_code' => '',
                'billing_address' => '',
            ],
            $config->fiscal_data ?? []
        );
        $config->plan = array_merge(
            ['name' => 'Gratis', 'limit' => 50],
            $config->plan ?? []
        );
        $config->prices = array_merge(
            ['credentials' => 0, 'reentry' => 0, 'replacement' => 0],
            $config->prices ?? []
        );
        $config->coordinates = $config->coordinates ?? ['lat' => '', 'lng' => ''];
    }
}
