<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicGroupResource;
use App\Models\Tenants\AcademicGroup;
use App\Models\Tenants\Grade;
use App\Models\Tenants\Incident;
use App\Models\Tenants\Role;
use App\Models\Tenants\SchoolCycle;
use App\Models\Tenants\Section;
use App\Models\Tenants\Specialty;
use App\Models\Tenants\Student;
use App\Models\Tenants\Teacher;
use Illuminate\Http\Request;

class SelectListController extends Controller
{
    public function index($type, Request $request)
    {
        $input = $this->validate($request, [
            'search' => 'nullable|string|max:150',
        ]);

        $search = $input['search'] ?? null;

        switch ($type) {
            case 'students':
                return $this->getStudents($search);
            case 'incidents':
                return $this->getIncidents($search);
            case 'teachers':
                return $this->getTeachers($search);
            case 'specialties':
                return $this->getSpecialties($search);
            case 'roles':
                return $this->getRoles($search);
            case 'grades':
                return $this->getGrades($search);
            case 'sections':
                return $this->getSections($search);
            case 'school-cycles':
                return $this->getSchoolCicles($search);
            case 'academic-groups':
                return $this->getAcademicGroups();
            default:
                abort(422, 'No existe');
        }
    }

    public function show($type, $id)
    {
        switch ($type) {
            case 'roles':
                return Role::select('id', 'name')->findOrFail($id);
            default:
                abort(422, 'No existe');
        }
    }

    public function getRoles($search)
    {
        $this->ensureDefaultRolesExist();

        $query = Role::select('id', 'name')
            ->where('name', '!=', Role::ROLE_SUPER_ADMIN);

        if (! empty($search)) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Ensure the 4 default roles exist so they appear when adding users.
     */
    protected function ensureDefaultRolesExist(): void
    {
        $defaults = [
            Role::ROLE_ADMIN,
            Role::ROLE_TEACHER,
            Role::ROLE_STUDENT,
            Role::ROLE_PARENT,
        ];
        foreach ($defaults as $name) {
            if (! Role::where('name', $name)->exists()) {
                app()->make(\Database\Seeders\RolesSeeder::class)->run();
                return;
            }
        }
    }

    public function getSpecialties($search)
    {
        $query = Specialty::select('id', 'description');

        if (! empty($search)) {
            $query->where('description', 'like', '%'.$search.'%');
        }

        return $query->get();
    }

    public function getTeachers($search)
    {
        $query = Teacher::select('id', 'name', 'last_name', 'last_name_father', 'last_name_mother');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('last_name_father', 'like', '%'.$search.'%')
                    ->orWhere('last_name_mother', 'like', '%'.$search.'%')
                    ->orWhere('last_name', 'like', '%'.$search.'%');
            });
        }

        return $query->get();
    }

    public function getIncidents($search)
    {
        $query = Incident::select('id', 'description');

        if (! empty($search)) {
            $query->where('description', 'like', '%'.$search.'%');
        }

        return $query->get();
    }

    public function getGrades($search)
    {
        $query = Grade::select('id', 'description', 'order')->orderBy('order', 'asc');

        if (! empty($search)) {
            $query->where('description', 'like', '%'.$search.'%');
        }

        return $query->get();
    }

    public function getSections($search)
    {
        $query = Section::select('id', 'description');

        if (! empty($search)) {
            $query->where('description', 'like', '%'.$search.'%');
        }

        return $query->get();
    }

    public function getSchoolCicles($search)
    {
        $query = SchoolCycle::select('id', 'description');

        if (! empty($search)) {
            $query->where('description', 'like', '%'.$search.'%');
        }

        return $query->get();
    }

    public function getAcademicGroups()
    {
        $query = AcademicGroup::all();

        return AcademicGroupResource::collection($query);
    }

    public function getStudents($search)
    {
        $query = Student::select('id', 'name', 'last_name_father', 'last_name_mother')
            ->with('academic')
            ->get();

        $arr = [];
        foreach ($query->toArray() as $value) {
            $arr[] = [
                'id' => $value['id'],
                'name' => "{$value['last_name_father']} {$value['last_name_mother']}, {$value['name']}",
            ];
        }

        return $arr;
    }
}
