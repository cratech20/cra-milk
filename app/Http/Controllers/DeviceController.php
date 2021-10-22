<?php

namespace App\Http\Controllers;

use App\Models\Cow;
use App\Models\Device;
use App\Models\Gate;
use App\Models\DeviceOwnerChange;
use App\Services\YaCloud\YaCloud;
use App\Models\Division;
use App\Models\Farm;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

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

    public function detach(Request $request)
    {
        $data = $request->data;
        $cow = Cow::find($data['id']);
        $cow->device_id = NULL;
        $cow->save();

        return $this->sendResponse($cow, 'Устройство отвязано');
    }

    public function getAllDevices()
    {
        $devices = Device::all();

        foreach($devices as $k => $d) {
            if (!$d->user) {
                $devices[$k]['u_name'] = '';
            } else {
                $devices[$k]['u_name'] = $d->user['name'];
            }
        }

        return response()->json(['devices' => $devices]);
    }

    public function summaryTable($clientId)
    {
        $device = DB::table('devices')
            ->where('devices.user_id', '=', $clientId)
            ->leftJoin('divisions', 'divisions.user_id', '=', 'devices.user_id')
            ->leftJoin('farms', 'farms.id', '=', 'devices.farm_id')
            ->select('devices.*', 'farms.name as f_name', 'divisions.name as d_name')
            ->get();

        return response()->json(['devices' => $device]);
    }

    public function getEmptyDevice($clientId)
    {
        // dd($clientId);
        $arr = Cow::whereNotNull('device_id')
            // ->list('device_id')
            ->get('device_id');
        // dd($arr);
        $device = Device::whereNotIn('id', $arr)
            ->where('user_id', '=', $clientId)
            ->get();

        return response()->json(['devices' => $device]);
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

    public function migrate(Request $request)
    {
        if (isset($request->checked)) {
            dd($request->all());
            foreach ($request->checked as $item) {
                $device = Device::find($item['id']);
                $device->user_id = $item['user_id'];
                $device->division_id = $item['division_id'];
                $device->farm_id = $item['farm_id'];
                $device->save();
            };
        } else {
            $device = Device::find($request->id);
            $device->user_id = $request->user_id;
            $device->division_id = $request->division_id;
            $device->farm_id = $request->farm_id;
            $device->save();
        };

        return $this->sendResponse($device, 'Устройство успешно перемещено');
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

        $device = Device::create($request->input());

        return $this->sendResponse($device, 'Устройство успешно добавлено');
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
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public
    function update(Request $request, Device $device)
    {
        $device = Device::find($request->id);
        $device->name = $request->name;
        $device->gate_id = $request->gate_id;
        $device->device_id = $request->device_id;
        $device->password = $request->password;
        $device->serial_number = $request->serial_number;
        $device->save();
        return $this->sendResponse($device, 'Устройство успешно обновлено');
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
                $newUserId = $data['user_id'];
                $device = Device::find($deviceId);
                DeviceOwnerChange::create([
                    'old_client_id' => $device->user_id,
                    'new_client_id' => $newUserId,
                    'device_login' => $device->device_id,
                    'changed_at' => Carbon::now()
                ]);
                $device->update(['user_id' => $newUserId]);
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

    public function command(Request $request)
    {
        if (!$request->checked) {
            return response()->json(['Error' => 'Нет выбранных устройств']);
        };
        $yacloud = new YaCloud();
        foreach ($request->checked as $item) {
            $registry = $yacloud->getRegistry($item['device_id']);
            $time = time();
            $json = DB::connection('pgsql')->table('iot_events')
                ->whereJsonContains('payload->l', $item['device_id'])->get();
            $payload = json_decode($json[count($json)-1]->payload);

            if (!$payload->a) {
                return $this->sendResponse($item, 'Ошибка при выполнении команды');
            }

            $gate = Gate::find($item['gate_id']);

            return $yacloud->commands($registry, $request->selectedManagment, $payload->a, $gate->device_id);
        };
    }
}
