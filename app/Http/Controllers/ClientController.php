<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::role('client')->get();

        return response()->json(['clients' => $clients]);
    }

    public function getClientById($id)
    {
        return response()->json(['client' => User::find($id)]);
    }
}
