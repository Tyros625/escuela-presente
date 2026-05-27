<?php

namespace App\Services;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Section;

class AcademicGroupColorService
{
    /**
     * @return array{degree: int, cluster: string}|null
     */
    public static function resolveDegreeCluster(AcademicGroup $group): ?array
    {
        $group->loadMissing(['grade', 'section']);

        $fromName = self::parseDegreeClusterFromText($group->name);
        if ($fromName !== null) {
            return ['degree' => $fromName[0], 'cluster' => $fromName[1]];
        }

        if ($group->section !== null) {
            $fromSection = self::parseDegreeClusterFromText($group->section->description);
            if ($fromSection !== null) {
                return ['degree' => $fromSection[0], 'cluster' => $fromSection[1]];
            }
        }

        $degree = self::resolveDegree($group->grade);
        $cluster = self::normalizeClusterLetter($group->section?->description);

        if ($cluster === null) {
            $cluster = self::extractClusterLetterFromText($group->name);
        }

        if ($degree !== null && $cluster !== null) {
            return ['degree' => $degree, 'cluster' => $cluster];
        }

        return null;
    }

    public static function groupKey(?AcademicGroup $group): string
    {
        if ($group === null) {
            return 'SG';
        }

        $pair = self::resolveDegreeCluster($group);
        if ($pair !== null) {
            return $pair['degree'].$pair['cluster'];
        }

        $name = trim((string) ($group->name ?? ''));
        if ($name !== '') {
            $parts = preg_split('/\s+/', $name) ?: [];
            $first = $parts[0] ?? $name;

            return substr(preg_replace('/\s+/', '', $first) ?? $first, 0, 6);
        }

        return 'SG';
    }

    public static function resolveForGroup(AcademicGroup $group): ?string
    {
        $pair = self::resolveDegreeCluster($group);
        if ($pair === null) {
            return null;
        }

        return config("academic_group_colors.{$pair['degree']}.{$pair['cluster']}");
    }

    public static function consistencyError(?string $name, ?Grade $grade, ?Section $section): ?string
    {
        $fromName = self::parseDegreeClusterFromText($name);
        if ($fromName === null) {
            return null;
        }

        $fromGradeSection = null;
        if ($section !== null) {
            $fromGradeSection = self::parseDegreeClusterFromText($section->description);
        }

        if ($fromGradeSection === null) {
            $degree = self::resolveDegree($grade);
            $cluster = self::normalizeClusterLetter($section?->description);
            if ($degree !== null && $cluster !== null) {
                $fromGradeSection = [$degree, $cluster];
            }
        }

        if ($fromGradeSection === null) {
            return null;
        }

        if ($fromName[0] !== $fromGradeSection[0] || $fromName[1] !== $fromGradeSection[1]) {
            return 'El nombre del grupo no coincide con el grado y la sección seleccionados.';
        }

        return null;
    }

    /**
     * @return array{0: int, 1: string}|null
     */
    public static function parseDegreeClusterFromText(?string $text): ?array
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $trimmed = strtoupper(trim($text));

        if (preg_match('/^(\d)\s*[°º]?\s*([A-F])\b/u', $trimmed, $m)) {
            $degree = (int) $m[1];
            if ($degree >= 1 && $degree <= 3) {
                return [$degree, $m[2]];
            }
        }

        return null;
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

    /**
     * @deprecated Use resolveDegreeCluster(); kept for callers that need only the cluster letter.
     */
    public static function resolveCluster(?Section $section, ?string $groupName = null): ?string
    {
        if ($section !== null) {
            $fromSection = self::parseDegreeClusterFromText($section->description);
            if ($fromSection !== null) {
                return $fromSection[1];
            }
        }

        if ($groupName !== null && $groupName !== '') {
            $fromName = self::parseDegreeClusterFromText($groupName);
            if ($fromName !== null) {
                return $fromName[1];
            }
        }

        if ($section !== null) {
            return self::normalizeClusterLetter($section->description);
        }

        return self::extractClusterLetterFromText($groupName);
    }

    public static function normalizeClusterLetter(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $fromText = self::parseDegreeClusterFromText($value);
        if ($fromText !== null) {
            return $fromText[1];
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

    private static function extractClusterLetterFromText(?string $text): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $fromText = self::parseDegreeClusterFromText($text);
        if ($fromText !== null) {
            return $fromText[1];
        }

        if (preg_match('/\b([A-Fa-f])\b/u', $text, $m)) {
            return strtoupper($m[1]);
        }

        return null;
    }
}
