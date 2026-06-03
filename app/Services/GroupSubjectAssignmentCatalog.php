<?php

namespace App\Services;

use App\Helper;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Specialty;
use Illuminate\Support\Collection;

class GroupSubjectAssignmentCatalog
{
    /** @var array<int, list<string>> */
    private const GRADE_SUBJECT_ORDER = [
        1 => [
            'ESPANOL',
            'MATEMATICAS',
            'INGLES',
            'ARTES',
            'BIOLOGIA',
            'GEOGRAFIA',
            'HISTORIA',
            'FCYE',
            'ED_FISICA',
            'TUTORIA',
            'INTEGRACION_CURRICULAR',
            'TALLER',
        ],
        2 => [
            'ESPANOL',
            'MATEMATICAS',
            'INGLES',
            'ARTES',
            'FISICA',
            'HISTORIA',
            'FCYE',
            'ED_FISICA',
            'TUTORIA',
            'INTEGRACION_CURRICULAR',
            'TALLER',
        ],
        3 => [
            'ESPANOL',
            'MATEMATICAS',
            'INGLES',
            'ARTES',
            'QUIMICA',
            'HISTORIA',
            'FCYE',
            'ED_FISICA',
            'TUTORIA',
            'INTEGRACION_CURRICULAR',
            'TALLER',
        ],
    ];

    /** @var array<string, string> */
    private const COLUMN_LABELS = [
        'ESPANOL' => 'ESPANOL',
        'MATEMATICAS' => 'MATEMATICAS',
        'INGLES' => 'INGLES',
        'ARTES' => 'ARTES',
        'BIOLOGIA' => 'BIOLOGIA',
        'GEOGRAFIA' => 'GEOGRAFIA',
        'FISICA' => 'FISICA',
        'QUIMICA' => 'QUIMICA',
        'HISTORIA' => 'HISTORIA',
        'FCYE' => 'FCYE',
        'ED_FISICA' => 'ED.FISICA',
        'TUTORIA' => 'TUTORIA',
        'INTEGRACION_CURRICULAR' => 'INTEGRACION C.',
        'TALLER' => 'TALLER',
    ];

    /** @var array<string, string> */
    private const SUBJECT_ALIASES = [
        'ESP' => 'ESPANOL',
        'ESPAÑOL' => 'ESPANOL',
        'MAT' => 'MATEMATICAS',
        'MATEMÁTICAS' => 'MATEMATICAS',
        'ING' => 'INGLES',
        'INGLÉS' => 'INGLES',
        'ART' => 'ARTES',
        'ARTES VISUALES' => 'ARTES',
        'BIO' => 'BIOLOGIA',
        'BIOLOGÍA' => 'BIOLOGIA',
        'GEO' => 'GEOGRAFIA',
        'GEOGRAFÍA' => 'GEOGRAFIA',
        'FIS' => 'FISICA',
        'FÍSICA' => 'FISICA',
        'QUI' => 'QUIMICA',
        'QUÍMICA' => 'QUIMICA',
        'HIS' => 'HISTORIA',
        'FC' => 'FCYE',
        'FORMACION CIVICA' => 'FCYE',
        'FORMACIÓN CÍVICA' => 'FCYE',
        'FORMACION CIVICA Y ETICA' => 'FCYE',
        'EDUCACION FISICA' => 'ED_FISICA',
        'EDUCACIÓN FÍSICA' => 'ED_FISICA',
        'ED FISICA' => 'ED_FISICA',
        'ED. FISICA' => 'ED_FISICA',
        'ED.FISICA' => 'ED_FISICA',
        'TUT' => 'TUTORIA',
        'TUTORÍA' => 'TUTORIA',
        'INTEGRACION CURRICULAR' => 'INTEGRACION_CURRICULAR',
        'INTEGRACION C' => 'INTEGRACION_CURRICULAR',
        'INTEGRACION C.' => 'INTEGRACION_CURRICULAR',
        'INT CURRICULAR' => 'INTEGRACION_CURRICULAR',
        'INT.CURRICULAR' => 'INTEGRACION_CURRICULAR',
    ];

    public static function gradeNumber(?Grade $grade): int
    {
        if ($grade === null) {
            return 0;
        }

        $digits = preg_replace('/\D+/', '', (string) $grade->description) ?? '';

        return (int) $digits;
    }

    public static function normalizeSubjectKey(?Specialty $specialty): string
    {
        if ($specialty === null) {
            return '';
        }

        $code = strtoupper(trim((string) ($specialty->code ?? '')));
        if ($code !== '' && isset(self::SUBJECT_ALIASES[$code])) {
            return self::SUBJECT_ALIASES[$code];
        }

        $description = strtoupper(trim(Helper::unaccent((string) ($specialty->description ?? ''))));
        $description = preg_replace('/\s+/', ' ', $description) ?? $description;

        if ($description === '') {
            return '';
        }

        if (isset(self::SUBJECT_ALIASES[$description])) {
            return self::SUBJECT_ALIASES[$description];
        }

        $compact = str_replace(['.', ' '], ['', '_'], $description);
        if (isset(self::COLUMN_LABELS[$compact])) {
            return $compact;
        }

        return $compact;
    }

    public static function columnLabel(?Specialty $specialty): string
    {
        $key = self::normalizeSubjectKey($specialty);

        if ($key !== '' && isset(self::COLUMN_LABELS[$key])) {
            return self::COLUMN_LABELS[$key];
        }

        $description = trim((string) ($specialty?->description ?? ''));

        return $description !== '' ? strtoupper(Helper::unaccent($description)) : '—';
    }

    /**
     * @param  Collection<int, Specialty>  $specialties
     * @return Collection<int, Specialty>
     */
    public static function sortForGrade(Collection $specialties, ?Grade $grade): Collection
    {
        $gradeNumber = self::gradeNumber($grade);
        $order = self::GRADE_SUBJECT_ORDER[$gradeNumber] ?? [];

        if ($order === []) {
            return $specialties->sortBy('description')->values();
        }

        $rank = array_flip($order);

        return $specialties
            ->sortBy(function (Specialty $specialty) use ($rank) {
                $key = self::normalizeSubjectKey($specialty);

                return [($rank[$key] ?? 999), $specialty->description];
            })
            ->values();
    }

    public static function expectedSubjectCount(?Grade $grade): ?int
    {
        $gradeNumber = self::gradeNumber($grade);
        $order = self::GRADE_SUBJECT_ORDER[$gradeNumber] ?? null;

        return $order !== null ? count($order) : null;
    }
}
