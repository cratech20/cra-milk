<?php

namespace App\Http\Controllers;

use App\Exports\ExportBasic;
use App\Exports\ExportBladeBasic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use DB;

class UserController extends Controller
{
    public function getAllUsers()
    {
        $users = User::all();
        foreach ($users as $key => $user) {
            $roles = [];
            foreach ($user->roles as $role) {
                $roles[] = [
                    'name' => $role['name'],
                    'code' => $role['id']
                ];
            }
            $users[$key]['role'] = $roles;
        }
        return response()->json(['users' => $users]);
    }

    public function delUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => "User not found"]);
        }
        // $roles = $user->getRoleNames();
        // foreach ($roles as $role) {
        //     $user->removeRole($role);
        // }
        $user->delete();
        return redirect()->back();
    }

    public function inn()
    {
        return view('users.register_by_inn');
    }

    public function create(Request $request)
    {
        $inn = $request['inn'];

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
        if (count($request->role) == 0) {
            return response()->json(['Error' => 'User role not found']);
        };

        $data = $request->input();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'inn' => !empty($data['inn']) ? $data['inn'] : null,
        ]);

        foreach ($request->role as $role) {
            $user->assignRole($role['name']);
        }

        return $this->sendResponse($user, 'Пользователь успешно добавлен');
    }

    public function changePasswordForm(User $user)
    {
        echo 'Смена пароля для ' . $user->name . '<br>';
        echo '<form action="' . route('users.change.password.save', $user) . '" method="POST">'
            . csrf_field() .
            '<input type="text" name="password">
            <input type="submit" value="Сохранить">
            </form>';
    }

    public function blockUser(Request $request) {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['error' => "User not found"]);
        }
        $user->status = !$request->status;
        $user->save();
    }

    public function changePassword(Request $request)
    {
        // dd($request->all());
        if ($request['password']) {
            $user = DB::transaction(function () use ($request) {
                $user = User::find($request['id']);
                if (!$user) {
                    return response()->json(['error' => "Пользователь не найден"]);
                }
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->password = Hash::make($request['password']);
                $user->save();

                foreach ($user->getRoleNames() as $role) {
                    $user->removeRole($role);
                }

                foreach ($request->role as $r) {
                    $user->assignRole($r['name']);
                }

                return $user;
            });
        } else {
            $user = DB::transaction(function () use ($request) {
                $user = User::find($request['id']);
                if (!$user) {
                    return response()->json(['error' => "Пользователь не найден"]);
                }
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->save();

                foreach ($user->getRoleNames() as $role) {
                    $user->removeRole($role);
                }

                foreach ($request->role as $r) {
                    $user->assignRole($r['name']);
                }

                return $user;
            });
        }


        return $this->sendResponse($user, 'Пользователь успешно обновлен');
    }
}
