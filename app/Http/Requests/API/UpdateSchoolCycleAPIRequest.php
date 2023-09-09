<?php

namespace App\Http\Requests\API;

use App\Models\Tenants\SchoolCycle;
use InfyOm\Generator\Request\APIRequest;

class UpdateSchoolCycleAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = SchoolCycle::$rules;

        return $rules;
    }
}
