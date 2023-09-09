<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralConfigurationRequest;
use App\Models\Tenants\GeneralConfiguration;

class GeneralConfigurationController extends Controller
{
    public function index()
    {
        $config = GeneralConfiguration::first();

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
