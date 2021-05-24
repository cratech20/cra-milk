<?php

namespace App\Http\Controllers;

use App\Exports\ExportBasic;
use App\Exports\ExportBladeBasic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::all()]);
    }

    public function inn()
    {
        return view('users.register_by_inn');
    }

    public function create(Request $request)
    {
        $inn = $request->input('inn');

        $data = null;

        if ($inn !== null) {
            $data = User::getDataByInn($inn);

            if ($data === null) {
                return back()->withErrors(['ИНН с ошибкой']);
            }
        }

        return view('users.register_form', ['data' => $data]);
    }

    public function store(Request $request)
    {
        $data = $request->input();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'inn' => !empty($data['inn']) ? $data['inn'] : null,
        ]);

        if (!empty($data['inn'])) {
            $user->assignRole('client');
        } else {
            $user->assignRole('employee');
        }

        return redirect()->route('users.roles.index');
    }
}
