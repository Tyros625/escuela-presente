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
            $config->school_schedule = TardinessService::mergeSchoolSchedule($config->school_schedule);
            $config->tardiness_schedule = TardinessService::mergeTardinessSchedule($config->tardiness_schedule);
        }

        return response()->json($config, 200);
    }

    public function update(GeneralConfigurationRequest $request)
    {
        $config = GeneralConfiguration::first();
        $config->fill($request->all());
        $config->update();

        return response()->json($config);
    }
}
