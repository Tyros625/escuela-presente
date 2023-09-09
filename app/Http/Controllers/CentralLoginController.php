<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CentralLoginController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->handleError($validator->errors());
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user->accountConfiguration;
            $user->accesses;
            $success['token'] = $user->createToken('authToken')->accessToken;
            $success['user'] = $user;

            return $this->handleResponse($success, 'Inicio Correcto');
        } else {
            return $this->handleError(__('auth.unauthorized'), ['error' => __('auth.unauthorized')]);
        }
    }

    public function me()
    {
        return response()->json(auth()->user());
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
}
