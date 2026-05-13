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
            
            // Load relationships if they exist (original code for server compatibility)
            if (method_exists($user, 'accountConfiguration')) {
                $user->accountConfiguration;
            }
            if (method_exists($user, 'accesses')) {
                $user->accesses;
            }
            
            // Ensure is_admin field is visible in response (for local admin login)
            if ($user->getAttribute('is_admin') !== null) {
                $user->makeVisible(['is_admin']);
            }
            
            $success['token'] = $user->createToken('authToken')->accessToken;
            $success['user'] = $user;

            return $this->handleResponse($success, 'Inicio Correcto');
        } else {
            return $this->handleError(__('auth.unauthorized'), ['error' => __('auth.unauthorized')], 401);
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

    public function notifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            return $this->handleError('Unauthorized', [], 401);
        }

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'href' => $data['href'] ?? 'javascript:void(0)',
                    'icon' => $data['icon'] ?? 'fa fa-fw fa-bell',
                    'time' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                    'data' => $data,
                ];
            });

        return $this->handleResponse($notifications, 'Notifications retrieved successfully');
    }

    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return $this->handleError('Unauthorized', [], 401);
        }

        $notification = $user->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return $this->handleResponse([], 'Notification marked as read');
        }

        return $this->handleError('Notification not found', [], 404);
    }
}
