<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'date_birth' => $this->date_birth,
            'sex' => $this->sex,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'specialty_id' => $this->specialty_id,
            'specialty' => $this->specialty->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
