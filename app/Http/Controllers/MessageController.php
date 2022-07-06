<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\RawDeviceMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    function show(Device $device)
    {
        $rawMessages = RawDeviceMessage::where('device_id', $device->device_id)->get();

        return view('devices.messages.show', ['device' => $device, 'rawMessages' => $rawMessages]);
    }
}
