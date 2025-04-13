<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Tenants\Role;
use App\Models\Tenants\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserAPIController extends Controller
{
    public function index()
    {
        $users = User::get();

        $users->each(function ($item) {
            $item->append(['role']);
        });

        return response()->json([
            'data' => $users,
            'success' => true,
            'message' => 'Users retrieved successfully',
        ], 200);
    }

    public function show(User $user)
    {
        $user->role;

        return $user;
    }

    public function store(UserRequest $request)
    {
        $user = new User;
        $user->fill($request->all());
        $roles = Role::find($request['role_id']);
        $user->save();
        $user->syncRoles($roles);

        return response()->json($user, Response::HTTP_CREATED);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->syncRoles([]);
        $user->update($request->all());
        $roles = Role::find($request['role_id']);
        $user->syncRoles($roles);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        if ($user->id == Auth::user()->id) {
            abort(422, 'No puedes eliminarte a ti mismo');
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('general.removed-successfully'),
        ]);
    }
}
