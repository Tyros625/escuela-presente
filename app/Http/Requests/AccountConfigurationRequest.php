<?php

namespace App\Http\Requests;

use App\Models\Tenants\AccountConfiguration;
use Illuminate\Foundation\Http\FormRequest;

class AccountConfigurationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return AccountConfiguration::$rules;
    }

    public function attributes()
    {
        return [
            'country' => __('request.country'),
            'timezone' => __('request.timezone'),
            'city' => __('request.city'),
            'language' => __('request.language'),
        ];
    }
}
