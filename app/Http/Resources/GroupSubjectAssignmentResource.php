<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupSubjectAssignmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'academic_group_id' => $this->academic_group_id,
            'specialty_id' => $this->specialty_id,
            'teacher_id' => $this->teacher_id,
            'school_cycle_id' => $this->school_cycle_id,
            'teacher_name' => $this->teacher?->display_name,
            'subject_name' => $this->specialty?->description,
            'group_label' => $this->academicGroup?->name
                ?? trim(
                    ($this->academicGroup?->grade?->description ?? '').' '
                    .($this->academicGroup?->section?->description ?? '')
                ),
            'hours_per_week' => max(1, (int) ($this->specialty?->hours_per_week ?? 1)),
            'assignment_type' => $this->assignment_type,
            'is_active' => $this->is_active,
        ];
    }
}
