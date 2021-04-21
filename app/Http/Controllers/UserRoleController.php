<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index()
    {
        return view('users.roles.index', [
            'users' => User::all(),
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
