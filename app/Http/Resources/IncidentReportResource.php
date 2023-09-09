<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IncidentReportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            //'student' => $this->student,
            'student' => "{$this->student->last_name_father} {$this->student->last_name_mother}, {$this->student->name}",
            'student_info' => new StudentResource($this->student),
            'incident_id' => $this->incident_id,
            'incident' => $this->incident->description,
            //'incident' => $this->incident,
            'teacher_id' => $this->teacher_id,
            'teacher' => "{$this->teacher->last_name}, {$this->teacher->name}",
            //'teacher' => $this->teacher,
            'specialty_id' => $this->specialty_id,
            'specialty' => $this->specialty->description,
            //'specialty' => $this->specialty,
            'photo' => $this->photo,
            'observations' => $this->observations,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
