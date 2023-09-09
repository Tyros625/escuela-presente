<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterTenantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'domain' => 'required|string|max:255|unique:domains',
            'school_name' => 'required|string|max:255',
            'cct' => 'required|string||size:10',
            'email' => 'required|email|max:255|indisposable',
            'password' => 'string|confirmed|min:6|max:200',
            'country_code' => 'required',
            'phone' => 'required',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'domain' => $this->domain.'.'.config('tenancy.central_domains')[0],
        ]);
    }
}
