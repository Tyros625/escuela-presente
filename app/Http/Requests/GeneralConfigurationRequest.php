<?php

namespace App\Http\Requests;

use App\Models\Tenants\GeneralConfiguration;
use Illuminate\Foundation\Http\FormRequest;

class GeneralConfigurationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return GeneralConfiguration::$rules;
    }

    public function attributes()
    {
        return [
            'name' => __('request.name'),
            'address' => __('request.address'),
            'email' => __('request.email'),
            'phone' => __('request.phone'),
            'logo' => __('request.logo'),
            'last_enrollment' => __('request.last_enrollment'),
        ];
    }
}
