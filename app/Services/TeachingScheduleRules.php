<?php

namespace App\Services;

use App\Models\Tenants\Specialty;

class TeachingScheduleRules
{
    public static function isServicioSpecialty(?Specialty $specialty): bool
    {
        if ($specialty === null) {
            return false;
        }

        $description = strtoupper(trim((string) ($specialty->description ?? '')));
        $code = strtoupper(trim((string) ($specialty->code ?? '')));

        return $description === 'SERVICIO' || $code === 'SERV';
    }
}
