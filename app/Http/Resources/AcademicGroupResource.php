<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AcademicGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'grade_id' => $this->grade_id,
            'grade' => $this->grade->description,
            'section_id' => $this->section_id,
            'section' => $this->section->description,
            'school_cycle_id' => $this->school_cycle_id,
            'school_year' => $this->schoolCycle->description,
            'student_limit' => $this->student_limit,
            'students' => new StudentResource($this->whenLoaded('students')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
