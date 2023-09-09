<?php

namespace App\Http\Requests\API;

use App\Models\Tenants\Grade;
use InfyOm\Generator\Request\APIRequest;

class CreateGradeAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return Grade::$rules;
    }
}
