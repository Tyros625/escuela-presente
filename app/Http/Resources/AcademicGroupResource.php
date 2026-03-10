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
            'shift' => $this->shift,
            'room_name' => $this->room_name,
            'student_limit' => $this->student_limit,
            'subjects' => $this->subjects,
            'subjects_count' => is_array($this->subjects) ? count($this->subjects) : 0,
            'students' => new StudentResource($this->whenLoaded('students')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
