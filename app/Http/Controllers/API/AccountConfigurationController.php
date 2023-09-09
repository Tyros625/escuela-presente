<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountConfigurationRequest;
use App\Http\Resources\AccountConfigurationResource;
use App\Models\Tenants\AccountConfiguration;
use App\Models\Tenants\User;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountConfigurationController extends Controller
{
    public function index(User $user)
    {
        $config = AccountConfiguration::where('user_id', $user->id)->first();

        return response()->json($config);
    }

    public function update(AccountConfigurationRequest $request, User $user): JsonResource
    {
        $config = AccountConfiguration::where('user_id', $user->id)->first();

        $format = [
            'country' => $request['country'],
            'timezone' => $request['timezone'],
            'city' => $request['city'],
            'language' => $request['language'],
            'files_location' => $request['files_location'],
            'user_id' => $user->id,
        ];

        if ($config !== null) {
            $config->update($format);
        } else {
            $config = AccountConfiguration::create($format);
        }

        return new AccountConfigurationResource($config);
    }
}
