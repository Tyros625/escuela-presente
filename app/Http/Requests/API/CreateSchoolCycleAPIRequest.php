<?php

namespace App\Http\Requests\API;

use App\Models\Tenants\SchoolCycle;
use InfyOm\Generator\Request\APIRequest;

class CreateSchoolCycleAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return SchoolCycle::$rules;
    }
}
