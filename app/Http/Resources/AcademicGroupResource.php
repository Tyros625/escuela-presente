<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AcademicGroupResource extends JsonResource
{
    public function toArray($request)
    {
        $studentsCount = isset($this->students_count) ? (int) $this->students_count : 0;
        $limit = (int) ($this->student_limit ?? 0);
        $coverage = $limit > 0 ? (int) round(min(100, ($studentsCount / $limit) * 100)) : 0;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'grade_id' => $this->grade_id,
            'grade' => $this->grade->description,
            'section_id' => $this->section_id,
            'section' => $this->section->description,
            'group_label' => trim($this->grade->description.' '.$this->section->description),
            'school_cycle_id' => $this->school_cycle_id,
            'school_year' => $this->schoolCycle->description,
            'shift' => $this->shift,
            'room_name' => $this->room_name,
            'student_limit' => $this->student_limit,
            'subjects' => $this->subjects,
            'subjects_count' => is_array($this->subjects) ? count($this->subjects) : 0,
            'students_count' => $studentsCount,
            'coverage_percent' => $coverage,
            'students' => new StudentResource($this->whenLoaded('students')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
