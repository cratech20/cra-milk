<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    function show(Device $device)
    {
        $json = '[
            {"id": "are77vr56e5ucsqva03j", "t": ' . time() . ', "d": {"text": "hello"}},
            {"id": "are77vr56e5ucsqva03j", "t": ' . time() . ', "d": {"text": "good bye"}}
        ]';
        $decoded = json_decode($json, 1);

        return view('devices.messages.show', compact('device', 'decoded'));
    }
}
