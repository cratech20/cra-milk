<?php

namespace App\Http\Controllers;

use App\Models\Cow;
use App\Models\Device;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;

class CowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $client)
    {
        $cows = Cow::where('user_id', $client->id)->get();

        return view('clients.cows.index', ['cows' => $cows, 'client' => $client]);
    }

    public function linking()
    {
        // TODO Postgres
        $json = file_get_contents('https://functions.yandexcloud.net/d4e4jl13h6mqnbcm64qj');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $devices = Device::all()->pluck('user_id', 'device_id');

        foreach ($data as $message) {
            if (!isset($message[3]['c'])) {
                continue;
            }

            $cell = $message[3];
            $cowId = $cell['c'];
            $deviceId = $cell['l'];

            if (!isset($devices[$deviceId])) {
                continue;
            }

            $userId = $devices[$deviceId];
            Cow::firstOrCreate(['cow_id' => $cowId, 'user_id' => $userId]);

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
    public function show(Cow $cow)
    {
        //
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
    public function update(Request $request, Cow $cow)
    {
        //
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
