<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    function show(Device $device)
    {
        $json = DB::connection('pgsql')->table('iot_events')
            ->whereJsonContains('payload->id', $device->device_id)->get();

        $decoded = json_decode($json, 1);

        return view('devices.messages.show', compact('device', 'decoded', 'json'));
    }
}
