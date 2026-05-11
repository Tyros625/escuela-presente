<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeachingAssignmentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $group = $this->academicGroup;
        $clusterName = null;
        if ($group) {
            $clusterName = $group->name;
            if (! $clusterName) {
                $clusterName = trim(
                    ($group->grade?->description ?? '').' '.($group->section?->description ?? '')
                ) ?: null;
            }
        }

        return [
            'id' => $this->id,
            'teacher_id' => $this->teacher_id,
            'teacher_name' => $this->teacher?->display_name,
            'specialty_id' => $this->specialty_id,
            'subject_name' => $this->specialty?->description,
            'specialty_hours_per_week' => $this->specialty?->hours_per_week,
            'academic_group_id' => $this->academic_group_id,
            'cluster_name' => $clusterName,
            'shift' => $this->shift,
            'day_of_week' => $this->day_of_week,
            'time_slot' => $this->time_slot,
            'assignment_type' => $this->assignment_type,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
