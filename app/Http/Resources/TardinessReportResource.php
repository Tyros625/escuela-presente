<?php

namespace App\Http\Resources;

use App\Services\TardinessService;
use Illuminate\Http\Resources\Json\JsonResource;

class TardinessReportResource extends JsonResource
{
    public function toArray($request): array
    {
        $student = $this->student;
        $group = $student?->academicGroup;
        $service = app(TardinessService::class);

        $fullName = trim(implode(' ', array_filter([
            $student?->last_name_father,
            $student?->last_name_mother,
            $student?->name,
        ])));

        return [
            'id' => $this->id,
            'enrollment' => $student?->enrollment,
            'full_name' => mb_strtoupper($fullName),
            'grade' => $group?->grade?->description,
            'group' => $group?->section?->description,
            'time' => $service->formatArrivalTime($this->created_at),
            'created_at' => $this->created_at,
        ];
    }
}
