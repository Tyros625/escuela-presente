<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Tenants\Role;
use App\Models\Tenants\Student;
use App\Models\Tenants\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TenantLoginController extends BaseController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::find(Auth::id());
            $user->accountConfiguration;

            $success['token'] = $user->createToken('authToken')->accessToken;
            $success['user'] = $user;
            $success['permissions'] = Helper::getUserPermissions($user);

            return $this->handleResponse($success, 'Inicio Correcto');
        }

        return $this->handleError(__('auth.unauthorized'), ['error' => __('auth.unauthorized')]);
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
