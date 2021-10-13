<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index()
    {
        $users = User::all();
        foreach ($users as $key => $user) {
            $users[$key]['roles'] = $user->hasAllRoles(Role::all());
        }
        return response()->json([
            'users' => $users,
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request)
    {
        foreach ($request->input('users') as $id => $roles) {
            User::find($id)->syncRoles(array_keys($roles));
        }

        return back();
    }
}
