<?php

namespace App\Services;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Section;

class AcademicGroupColorService
{
    public static function resolveForGroup(AcademicGroup $group): ?string
    {
        $group->loadMissing(['grade', 'section']);

        $degree = self::resolveDegree($group->grade);
        $cluster = self::resolveCluster($group->section, $group->name);

        if ($degree === null || $cluster === null) {
            return null;
        }

        return config("academic_group_colors.{$degree}.{$cluster}");
    }

    public static function resolveDegree(?Grade $grade): ?int
    {
        if ($grade === null) {
            return null;
        }

        $order = (int) ($grade->order ?? 0);
        if ($order >= 1 && $order <= 3) {
            return $order;
        }

        $fromDescription = (int) preg_replace('/\D/', '', (string) ($grade->description ?? ''));
        if ($fromDescription >= 1 && $fromDescription <= 3) {
            return $fromDescription;
        }

        return null;
    }

    public static function resolveCluster(?Section $section, ?string $groupName = null): ?string
    {
        if ($section !== null) {
            $letter = self::normalizeClusterLetter($section->description);
            if ($letter !== null) {
                return $letter;
            }
        }

        if ($groupName !== null && $groupName !== '') {
            if (preg_match('/(\d)\s*[°º]?\s*([A-Fa-f])\b/u', $groupName, $m)) {
                return strtoupper($m[2]);
            }
            if (preg_match('/\b([A-Fa-f])\b/u', $groupName, $m)) {
                return strtoupper($m[1]);
            }
        }

        return null;
    }

    public static function normalizeClusterLetter(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $trimmed = strtoupper(trim($value));
        if (strlen($trimmed) === 1 && $trimmed >= 'A' && $trimmed <= 'F') {
            return $trimmed;
        }

        if (preg_match('/\b([A-F])\b/', $trimmed, $m)) {
            return $m[1];
        }

        return null;
    }
}
