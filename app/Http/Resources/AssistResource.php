<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssistResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'grade' => $this->student->academicGroup->grade->description,
            'group' => $this->student->academicGroup->section->description,
            'student' => new StudentResource($this->student),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
