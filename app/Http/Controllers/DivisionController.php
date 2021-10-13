<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($client_id)
    {
        $divisions = Division::where('user_id', $client_id)->get();

        return response()->json(['divisions' => $divisions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $division = Division::create([
            'user_id' => $request->id,
            'name' => $request->newDevision
        ]);

        return $this->sendResponse($division, 'Подразделение успешно добавлено');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Division $division
     * @return \Illuminate\Http\Response
     */
    public function show(Division $division)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Division $division
     * @return \Illuminate\Http\Response
     */
    public function edit(Division $division)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Division $division
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Division $division)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Division $division
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $division = Division::find($request->id);
        $division->delete();

        return $this->sendResponse($division, 'Подразделение успешно удалено');
    }
}
