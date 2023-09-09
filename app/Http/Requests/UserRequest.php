<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:190',
            'email' => 'required|email|unique:users,email,'.$this->id,
            'password' => 'string|confirmed|min:6|max:200',
            'password_confirmation' => 'string|min:6|max:200',
            'role_id' => 'required|integer|exists:roles,id',
            'active' => 'required|boolean',
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $rules['code'] = 'nullable|string';
            $rules['email'] = 'email';
            $rules['password'] = 'nullable|string|confirmed|min:6|max:200';
            $rules['password_confirmation'] = 'nullable|string|min:6|max:200';
        }

        return $rules;
    }
}
