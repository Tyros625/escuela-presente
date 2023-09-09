<?php

namespace App\Http\Controllers\API;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Tenants\Assist;
use App\Models\Tenants\Balances;
use App\Models\Tenants\Dashboard;
use App\Models\Tenants\Dinner;
use App\Models\Tenants\GeneralConfiguration;
use App\Models\Tenants\Incident;
use App\Models\Tenants\IncidentReport;
use App\Models\Tenants\Role;
use App\Models\Tenants\Specialty;
use App\Models\Tenants\Student;
use App\Models\Tenants\Teacher;
use App\Models\Tenants\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    protected function resourceAbilityMap()
    {
        $map = parent::resourceAbilityMap();
        $map['updatePermissions'] = 'update';
        $map['userList'] = 'view';

        return $map;
    }

    public function index()
    {
        $roles = Role::where('name', '!=', Role::ROLE_SUPER_ADMIN)
            ->get();

        $roles->each(function (Role $role) {
            $role->append('is_locked');
        });

        return response()->json([
            'data' => $roles,
            'success' => true,
            'message' => 'Roles retrieved successfully',
        ], 200);
    }

    public function show(Role $role)
    {
        $role->permissions;
        $role->system_modules = $this->systemModules();
        $role->users_total = $role->users()->count();
        $role->is_locked;

        return $role;
    }

    public function store(Request $request)
    {
        $inputs = $this->validate($request, [
            'name' => 'required|string|unique:roles',
        ]);

        if (Role::isLockedRole($inputs['name'])) {
            abort(422, 'Este rol no puede ser modificado.');
        }

        $role = new Role($inputs);
        $role->guard_name = 'api';
        $role->save();

        return response()->json($role, Response::HTTP_CREATED);
    }

    public function update(Request $request, Role $role)
    {
        $inputs = $this->validate($request, [
            'name' => 'required|string|unique:roles',
        ]);

        if ($role->isLocked()) {
            abort(422, 'Este rol no puede ser modificado.');
        }

        $role->fill($inputs);
        $role->save();

        return response()->json($role);
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $input = $this->validate($request, [
            'permissions' => 'required|array',
        ]);

        DB::transaction(function () use ($input, $role) {
            $role->syncPermissions($input['permissions']);
        });

        return response()->json($role);
    }

    public function destroy(Role $role)
    {
        if ($role->isLocked()) {
            abort(422, 'Este rol no puede ser eliminado.');
        }
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Eliminado Correctamente',
        ]);
    }

    public function userList(Role $role)
    {
        $users = $role->users()->paginate();

        return response()->json($users);
    }

    /**
     * Obtain all permissions of the system
     */
    private function systemModules()
    {
        $modules[] = [
            'name' => 'Módulo Asistencias',
            'permissions' => Helper::getPermissionsFromModel(Assist::class),
        ];

        $modules[] = [
            'name' => 'Módulo Balances',
            'permissions' => Helper::getPermissionsFromModel(Balances::class),
        ];

        $modules[] = [
            'name' => 'Módulo Panel de Control',
            'permissions' => Helper::getPermissionsFromModel(Dashboard::class),
        ];

        $modules[] = [
            'name' => 'Módulo Comedor',
            'permissions' => Helper::getPermissionsFromModel(Dinner::class),
        ];

        $modules[] = [
            'name' => 'Módulo Configuración General',
            'permissions' => Helper::getPermissionsFromModel(GeneralConfiguration::class),
        ];

        $modules[] = [
            'name' => 'Módulo Incidentes',
            'permissions' => Helper::getPermissionsFromModel(Incident::class),
        ];

        $modules[] = [
            'name' => 'Módulo Reporte de Incidentes',
            'permissions' => Helper::getPermissionsFromModel(IncidentReport::class),
        ];

        $modules[] = [
            'name' => 'Módulo Roles y Permisos',
            'permissions' => Helper::getPermissionsFromModel(Role::class),
        ];

        $modules[] = [
            'name' => 'Módulo Especialidades',
            'permissions' => Helper::getPermissionsFromModel(Specialty::class),
        ];

        $modules[] = [
            'name' => 'Módulo Estudiantes',
            'permissions' => Helper::getPermissionsFromModel(Student::class),
        ];

        $modules[] = [
            'name' => 'Módulo Profesores',
            'permissions' => Helper::getPermissionsFromModel(Teacher::class),
        ];

        $modules[] = [
            'name' => 'Módulo Usuarios',
            'permissions' => Helper::getPermissionsFromModel(User::class),
        ];

        return $modules;
    }
}
