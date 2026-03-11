<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecialtyResource extends JsonResource
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
            'description' => $this->description,
            'code' => $this->code,
            'grade_id' => $this->grade_id,
            'grade' => $this->grade?->description,
            'school_cycle_id' => $this->school_cycle_id,
            'school_cycle' => $this->schoolCycle?->description,
            'hours_per_week' => $this->hours_per_week,
            'credits' => $this->credits,
            'training_field' => $this->training_field,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
