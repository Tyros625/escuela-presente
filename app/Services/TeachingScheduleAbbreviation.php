<?php



namespace App\Services;



use App\Models\Tenants\AcademicGroup;

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

        return AcademicGroupColorService::groupKey($group);

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

}


