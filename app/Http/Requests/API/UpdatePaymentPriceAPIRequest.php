<?php

namespace App\Http\Requests\API;

use App\Models\Tenants\PaymentPrice;
use InfyOm\Generator\Request\APIRequest;

class UpdatePaymentPriceAPIRequest extends APIRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = PaymentPrice::$rules;

        return $rules;
    }
}
