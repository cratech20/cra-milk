<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    function show($device_id)
    {
        $json = DB::connection('pgsql')->table('iot_events')
            ->whereJsonContains('payload->l', $device_id)->get();

        $decoded = json_decode($json, 1);

        return response()->json([
            'decoded' => $decoded,
            'json' => $json
        ]);

        // return view('devices.messages.show', compact('device', 'decoded', 'json'));
    }
}
