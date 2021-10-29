<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Models\Gate;
use DB;

class GateController extends Controller
{
    public function getGate()
    {
        $gates = Gate::all();
        foreach ($gates as $k => $item) {
            $device = Device::where('gate_id', '=', $item->id)->get(['id as code', 'name']);
            $gates[$k]['devices'] = $device;
        }

        return response()->json(['gates' => $gates]);
    }

    public function create(Request $request)
    {
        $gate = Gate::create([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'password' => $request->password,
            'registry_id' => 'arerbdnuk54prrjanos2',
            'description' => $request->description,
            'serial_number' => $request->serial_number,
        ]);

        foreach ($request->devices as $item) {
            $device = Device::find($item['code']);
            $device->gate_id = $gate->id;
            $device->save();
        }

        return $this->sendResponse($gate, 'Шлюз успешно добавлен');
    }

    public function delete($id)
    {
        $devices = Device::where('gate_id', '=', $id)->get();
        foreach ($devices as $item) {
            $item->gate_id = NULL;
            $item->save();
        }
        $gate = Gate::find($id);
        $gate->delete();
        return $this->sendResponse($gate, 'Шлюз успешно удален');
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $gate = Gate::find($request->id);
        $gate->name = $request->name;
        $gate->device_id = $request->device_id;
        $gate->password = $request->password;
        $gate->registry_id = 'arerbdnuk54prrjanos2';
        $gate->description = $request->description;
        $gate->serial_number = $request->serial_number;
        $gate->save();

        foreach ($request->devices as $item) {
            $device = Device::find($item['code']);
            $device->gate_id = $request->id;
            $device->save();
        }

        return $this->sendResponse($gate, 'Шлюз успешно обновлен');
    }
}
