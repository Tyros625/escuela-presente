<?php

namespace App\Http\Resources;

use App\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'enrollment' => $this->enrollment,
            'name' => $this->name,
            'last_name_father' => $this->last_name_father,
            'last_name_mother' => $this->last_name_mother,
            'status' => $this->status,
            'nationality' => $this->nationality,
            'curp' => $this->curp,
            'age' => $this->age,
            'age_diff' => $this->age_diff,
            'date_birth' => Helper::formatOnlyDate($this->date_birth),
            'place_birth' => $this->place_birth,
            'sex' => $this->sex,
            'weight' => $this->weight,
            'height' => $this->height,
            'is_migrant' => $this->is_migrant,
            'indigenous_group' => $this->indigenous_group,
            'indigenous_language' => $this->indigenous_language,
            'disability' => $this->disability,
            'health_insurance' => $this->health_insurance,
            'scholarship' => $this->scholarship,
            'address' => $this->address,
            'colony' => $this->colony,
            'postal_code' => $this->postal_code,
            'municipality' => $this->municipality,
            'federal_entity' => $this->federal_entity,
            'home_phone' => $this->home_phone,
            'email' => $this->email,
            'photo' => $this->photo,
            'active' => $this->active,
            'academic_group_id' => $this->academic_group_id,
            'academic' => $this->academic,
            'grade' => $this->academicGroup->grade->description ?? null,
            'group' => $this->academicGroup->section->description ?? null,
            'school_cycle' => $this->academicGroup->schoolCycle->description ?? null,
            'relatives' => $this->relative,
            'socioeconomics' => $this->socioeconomic,
            'healths' => $this->health,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
