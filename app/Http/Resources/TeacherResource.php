<?php

namespace App\Http\Resources;

use App\Services\TeacherHoursService;
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
        $assignedHours = $this->assigned_hours_count
            ?? app(TeacherHoursService::class)->assignedHours($this->id);

        $hoursAvailable = null;
        if ($this->max_hours_per_week !== null) {
            $hoursAvailable = max(0, (float) $this->max_hours_per_week - $assignedHours);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'last_name_father' => $this->last_name_father,
            'last_name_mother' => $this->last_name_mother,
            'rfc' => $this->rfc,
            'date_birth' => $this->date_birth,
            'sex' => $this->sex,
            'email' => $this->email,
            'institutional_email' => $this->institutional_email,
            'phone' => $this->phone,
            'address' => $this->address,
            'specialization_id' => $this->specialization_id,
            'specialization' => $this->specialization?->description,
            'subject_id' => $this->subject_id,
            'subject' => $this->subject?->description,
            'max_hours_per_week' => $this->max_hours_per_week,
            'available_hours' => $this->available_hours,
            'assigned_hours' => $assignedHours,
            'hours_available' => $hoursAvailable,
            'schedule_availability' => $this->schedule_availability,
            'display_name' => $this->display_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
