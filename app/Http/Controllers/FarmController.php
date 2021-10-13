<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($client_id)
    {
        $farm = Farm::where('user_id', $client_id)->get();

        return response()->json(['farms' => $farm]);
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
        $item = Farm::create([
            'user_id' => $request->id,
            'name' => $request->newFarm
        ]);

        return $this->sendResponse($item, 'Ферма успешно добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Farm $farm
     * @return \Illuminate\Http\Response
     */
    public function show(Farm $farm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Farm $farm
     * @return \Illuminate\Http\Response
     */
    public function edit(Farm $farm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Farm $farm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Farm $farm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Farm $farm
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $farm = Farm::find($request->id);
        $farm->delete();

        return $this->sendResponse($farm, 'Ферма успешно удалена');
    }
}
