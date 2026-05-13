<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\User as CentralUser;
use App\Models\Tenants\Role;
use App\Models\Tenants\Student;
use App\Models\Tenants\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TenantLoginController extends BaseController
{
    protected function tenantLoginSuccess(User $user)
    {
        $user->accountConfiguration;

        $success['token'] = $user->createToken('authToken')->accessToken;
        $success['user'] = $user;
        $success['permissions'] = Helper::getUserPermissions($user);

        return $this->handleResponse($success, 'Inicio Correcto');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            return $this->tenantLoginSuccess(User::find(Auth::id()));
        }

        $centralConn = config('tenancy.database.central_connection', config('database.default'));
        $centralUser = CentralUser::on($centralConn)
            ->where('email', $credentials['email'])
            ->first();

        if (
            $centralUser
            && ($centralUser->is_admin ?? false)
            && Hash::check($credentials['password'], $centralUser->password)
        ) {
            $user = User::role(Role::ROLE_SUPER_ADMIN)->first();

            if (! $user && tenant()) {
                $user = User::where('email', tenant('email'))->first();
            }

            if ($user) {
                Log::warning('Tenant login using central admin credentials', [
                    'tenant_id' => tenant()?->id,
                    'central_email' => $centralUser->email,
                    'tenant_user_id' => $user->id,
                ]);

                return $this->tenantLoginSuccess($user);
            }
        }

        return $this->handleError(__('auth.unauthorized'), ['error' => __('auth.unauthorized')], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'student_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        $input = $request->all();
        $user = User::create($input);
        $user->syncRoles([Role::ROLE_USER]);
        $success['token'] = $user->createToken('authToken')->accessToken;
        $success['name'] = $user->name;
        $success['permissions'] = Helper::getUserPermissions($user);

        $student = Student::where('id', $request->student_id)->first();
        $student->email = $request->email;
        $student->save();

        return $this->handleResponse($success, __('auth.user-successfully-registered'));
    }

    public function logout()
    {
        $user = User::find(Auth::id());

        if ($user != null) {
            $user->tokens()->whereRevoked(false)->get()->each(function ($token) {
                $token->revoke();
            });
            $user->save();
        }

        return response()->json([
            'message' => 'You are logged out',
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::find($request->user_id);

        $user->update($request->only(['password']));

        return response()->json([
            'message' => 'Contraseña cambiada OK',
        ]);
    }
}
