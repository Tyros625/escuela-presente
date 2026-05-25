<?php

namespace App\Services;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Section;
use App\Models\Tenants\Specialty;

class TeachingScheduleAbbreviation
{
    /** @var array<string, string> */
    private const SUBJECT_ALIASES = [
        'ESPAÑOL' => 'ESP',
        'ESPANOL' => 'ESP',
        'MATEMATICAS' => 'MAT',
        'MATEMÁTICAS' => 'MAT',
        'TUTORIA' => 'TUT',
        'TUTORÍA' => 'TUT',
        'SERVICIO' => 'SERV',
        'INGLES' => 'ING',
        'INGLÉS' => 'ING',
        'FISICA' => 'FIS',
        'FÍSICA' => 'FIS',
        'QUIMICA' => 'QUI',
        'QUÍMICA' => 'QUI',
        'BIOLOGIA' => 'BIO',
        'BIOLOGÍA' => 'BIO',
        'HISTORIA' => 'HIS',
        'GEOGRAFIA' => 'GEO',
        'GEOGRAFÍA' => 'GEO',
        'FORMACION CIVICA' => 'FC',
        'FORMACIÓN CÍVICA' => 'FC',
    ];

    public static function subjectKey(?Specialty $specialty): string
    {
        if ($specialty === null) {
            return '';
        }

        $code = strtoupper(trim((string) ($specialty->code ?? '')));
        if ($code !== '') {
            return substr($code, 0, 4);
        }

        $description = strtoupper(trim((string) ($specialty->description ?? '')));
        if ($description === '') {
            return '';
        }

        if (isset(self::SUBJECT_ALIASES[$description])) {
            return self::SUBJECT_ALIASES[$description];
        }

        $normalized = preg_replace('/\s+/', ' ', $description) ?? $description;
        if (isset(self::SUBJECT_ALIASES[$normalized])) {
            return self::SUBJECT_ALIASES[$normalized];
        }

        $words = preg_split('/\s+/', $normalized) ?: [];
        if (count($words) >= 2) {
            return substr(implode('', array_map(
                static fn (string $w) => $w[0] ?? '',
                array_slice($words, 0, 4)
            )), 0, 4);
        }

        return substr($normalized, 0, 4);
    }

    public static function groupAbbrev(?AcademicGroup $group): string
    {
        if ($group === null) {
            return 'SG';
        }

        $group->loadMissing(['grade', 'section']);

        $degree = AcademicGroupColorService::resolveDegree($group->grade);
        $cluster = AcademicGroupColorService::resolveCluster($group->section, $group->name);

        if ($degree !== null && $cluster !== null) {
            return $degree.$cluster;
        }

        $name = trim((string) ($group->name ?? ''));
        if ($name !== '' && preg_match('/^(\d)\s*([A-Fa-f])\b/u', $name, $m)) {
            return $m[1].strtoupper($m[2]);
        }

        if ($name !== '') {
            $parts = preg_split('/\s+/', $name) ?: [];
            $first = $parts[0] ?? $name;

            return substr(preg_replace('/\s+/', '', $first) ?? $first, 0, 6);
        }

        $gradePart = self::shortGradeLabel($group->grade);
        $sectionPart = self::shortSectionLabel($group->section);

        return trim($gradePart.' '.$sectionPart) !== ''
            ? trim($gradePart.$sectionPart)
            : 'SG';
    }

    public static function cellLabel(?AcademicGroup $group, ?Specialty $specialty): string
    {
        $groupPart = self::groupAbbrev($group);
        $subjectPart = self::subjectKey($specialty);

        if ($groupPart === '' && $subjectPart === '') {
            return '';
        }

        if ($subjectPart === '') {
            return $groupPart;
        }

        return trim($groupPart.' '.$subjectPart);
    }

    private static function shortGradeLabel(?Grade $grade): string
    {
        if ($grade === null) {
            return '';
        }

        $order = (int) ($grade->order ?? 0);
        if ($order > 0) {
            return (string) $order;
        }

        return substr((string) ($grade->description ?? ''), 0, 2);
    }

    private static function shortSectionLabel(?Section $section): string
    {
        if ($section === null) {
            return '';
        }

        $letter = AcademicGroupColorService::normalizeClusterLetter($section->description);

        return $letter ?? '';
    }
}
