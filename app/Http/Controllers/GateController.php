<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gate;

class GateController extends Controller
{
    public function getGate()
    {
        return response()->json(['gates' => Gate::all()]);
    }
}
