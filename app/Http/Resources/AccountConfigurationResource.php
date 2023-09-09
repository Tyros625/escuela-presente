<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountConfigurationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'country' => $this->country,
            'timezone' => $this->timezone,
            'city' => $this->city,
            'language' => $this->language,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
