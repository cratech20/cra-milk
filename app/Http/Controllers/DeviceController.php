<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Division;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devices = Device::all();
        $title = 'Список устройств';

        return view('devices.index', [
            'devices' => $devices, 'title' => $title, 'clients' => User::role('client')->get()
        ]);
    }

    public function summaryTable(User $client = null)
    {
        $title = 'Итоговая таблица по устройствам клиента ' . $client->name;
        $devices = $client->devices;

        return view('devices.summary', [
            'devices' => $devices,
            'title' => $title,
            'client' => $client
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function create()
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
    public
    function store(Request $request)
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
    public
    function show(Device $device)
    {
        echo 'show';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Device $device
     * @return \Illuminate\Http\Response
     */
    public
    function edit(Device $device)
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
    public
    function update(Request $request, Device $device)
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
    public
    function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('devices.index');
    }

    function clientChange(Request $request)
    {
        $data = $request->input();

        if ($data['action'] === 'change_client') {
            foreach ($data['devices'] as $deviceId) {
                Device::where('id', $deviceId)->update(['user_id' => $data['user_id']]);
            }

            return back()
                ->with([
                    'message' => 'Устройства успешно прикреплены к клиенту',
                    'alert-class' => 'alert-success'
                ]);
        }

        if ($data['action'] === 'change_division') {
            foreach ($data['devices'] as $deviceId) {
                Device::where('id', $deviceId)->update(['division_id' => $data['item_id']]);
            }

            return back()
                ->with([
                    'message' => 'Устройства успешно прикреплены к подразделению',
                    'alert-class' => 'alert-success'
                ]);
        }

        if ($data['action'] === 'change_farm') {
            foreach ($data['devices'] as $deviceId) {
                Device::where('id', $deviceId)->update(['farm_id' => $data['item_id']]);
            }

            return back()
                ->with([
                    'message' => 'Устройства успешно прикреплены к ферме',
                    'alert-class' => 'alert-success'
                ]);
        }
    }
}
