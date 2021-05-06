<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $client = null)
    {
        if ($client === null) {
            $devices = Device::all();
            $title = 'Список устройств';
        } else {
            $devices = Device::where('user_id', $client->id)->get();
            $title = 'Список устройств клиента ' . $client->name;
        }

        return view('devices.index', ['devices' => $devices, 'title' => $title]);
    }

    public function summaryTable(User $client = null)
    {
        $title = 'Итоговая таблица по устройствам клиента ' . $client->name;
        $devices = $client->devices;

        return view('devices.summary', ['devices' => $devices, 'title' => $title]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('devices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'device_id' => 'required',
            'serial_number' => 'required',
        ]);

        Device::create($request->input());

        return redirect()->route('devices.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        echo 'show';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Device $device)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index');
    }
}
