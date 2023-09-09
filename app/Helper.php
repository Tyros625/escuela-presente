<?php

namespace App;

use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Student;
use App\Models\Tenants\User;
use Carbon\Carbon;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Faker\Factory;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;

class Helper
{
    public static function userHavePermission(User $user, $permission)
    {
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        if (in_array($permission, $permissions)) {
            return true;
        }

        return false;
    }

    public static function getUserPermissions(User $user)
    {
        return $user->getAllPermissions()->pluck('name')->toArray();
    }

    public static function formatOnlyDate(string $utc)
    {
        return Carbon::parse($utc)->toDateString();
    }

    public static function fromUtcToLocalTimezone(string $utc)
    {
        return Carbon::parse($utc)->setTimezone('America/Mexico_City')->toDateTimeString();
    }

    public static function getPermissionsFromModel($model, &$permissions = [])
    {
        $reflection = new ReflectionClass($model);
        $constants = $reflection->getConstants();

        foreach ($constants as $constant => $value) {
            if (strpos($constant, 'P_') !== false) {
                $permissions[] = $value;
            }
        }

        return $permissions;
    }

    public static function saveInCloudinary($file)
    {
        return Cloudinary::uploadFile($file->getRealPath())->getSecurePath();
    }

    public static function saveFileInLocal($file, $folderName)
    {
        $name = Factory::create()->bothify('?#?#?#?#?#?#');
        $fileName = "img-{$name}.{$file->getClientOriginalExtension()}";

        $path = Storage::putFileAs(
            "public/{$folderName}",
            $file,
            $fileName
        );

        return $path;
    }

    public static function unaccent($string)
    {
        return rtrim(ltrim(
            mb_strtoupper(
                preg_replace(
                    '~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i',
                    '$1',
                    htmlentities($string, ENT_QUOTES, 'UTF-8')
                )
            )
        ));
    }

    public static function getQuantityStudentsByGradeAndGroup($grade)
    {
        $students = Student::with('academicGroup.grade', 'academicGroup.section')->get();

        $grouped = $students->groupBy(function ($item, $key) {
            return "{$item->academicGroup->grade->description}-{$item->academicGroup->section->description}";
        })->map(function ($item, $key) {
            return collect($item)->count();
        })->sortBy(function ($item, $key) {
            return $key;
        });

        return $grouped[$grade];
    }

    public static function verifySpaceInGrades($academicId)
    {
        $group = AcademicGroup::where('id', $academicId)->first();

        $groups = AcademicGroup::where('grade_id', $group->grade_id)
            ->with('grade', 'section', 'schoolCycle')
            ->get();

        $section = $groups->first(function ($group) {
            $gradeGroup = "{$group->grade->description}-{$group->section->description}";
            $quantity = Helper::getQuantityStudentsByGradeAndGroup($gradeGroup);
            $limit = $group->student_limit;

            return $quantity < $limit;
        });

        if (empty($section)) {
            return false;
        }

        return $section;
    }
}
