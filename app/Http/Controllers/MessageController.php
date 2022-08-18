<?php

namespace App\Http\Controllers;

use App\Models\RawDeviceMessage;
class MessageController extends Controller
{
    function show($device_id)
    {
        $json = RawDeviceMessage::where('device_id', $device_id)->get();
        $decoded = json_decode($json, 1);

        return response()->json([
            'decoded' => $decoded,
            'json' => $json
        ]);
    }
}
