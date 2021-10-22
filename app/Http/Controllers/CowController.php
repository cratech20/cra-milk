<?php

namespace App\Http\Controllers;

use App\Models\Cow;
use App\Models\Device;
use App\Models\DeviceMessage;
use App\Models\DeviceOwnerChange;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class CowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $cows = DB::table('cows')
            ->where('cows.user_id', '=', $id)
            ->leftJoin('cow_groups', 'cow_groups.id', '=', 'cows.group_id')
            ->leftJoin('devices', 'devices.id', '=', 'cows.device_id')
            ->select('cows.*', 'cow_groups.name as g_name', 'devices.name as d_name')
            ->get();

        return response()->json(['cows' => $cows]);
    }

    public function linking()
    {
        $devices = Device::get(['user_id', 'device_id'])->keyBy('device_id');
        $deviceIds = $devices->pluck('device_id');

        $clientChanges = DeviceOwnerChange::whereIn('device_login', $deviceIds)
            ->get(['device_login', 'new_client_id', 'changed_at'])
            ->groupBy('device_login');

        $lastChanges = $clientChanges->map(function ($el) {
            return $el->last();
        });

        $data = DeviceMessage::query()->distinct()
            ->whereIn('device_login', $deviceIds)
            ->get(['cow_code', 'device_login', 'device_created_at']);

        foreach ($data as $message) {
            $cowId = $message->cow_code;
            $deviceId = $message->device_login;
            $messageDate = $message->device_created_at;

            $device = $devices[$deviceId];

            if (!isset($clientChanges[$deviceId])) {
                $userId = $device->user_id;
            } else if ($messageDate >= $lastChanges[$deviceId]->changed_at) {
                $userId = $lastChanges[$deviceId]->new_client_id;
            } else {
                continue;
            }

            Cow::updateOrCreate(['cow_id' => $cowId], ['user_id' => $userId, 'group_id' => null]);
        }

        return back()->with([
            'message' => 'Обновлено успешно',
            'alert-class' => 'alert-success'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Cow $cow
     * @return \Illuminate\Http\Response
     */
    public function show($cow_id)
    {
        $cow = Cow::find($cow_id);
        if (!$cow) {
            return response()->json(['error' => 'Корова не найдена']);
        }

        return view('clients.cows.edit', ['cows' => $cow]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Cow $cow
     * @return \Illuminate\Http\Response
     */
    public function edit(Cow $cow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Cow $cow
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->data;
        $cow = Cow::find($data['id']);
        $cow->internal_code = $data['internal_code'];
        $cow->group_id = $data['group_id'];
        $cow->device_id = $data['device_id'];
        $cow->save();

        return $this->sendResponse($cow, 'Информация успешно обновлена');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Cow $cow
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cow $cow)
    {
        //
    }
}
