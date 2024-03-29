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
    public function index(User $client)
    {
        $divisions = Division::where('user_id', $client->id)->get();

        return view('clients.divisions.index', ['divisions' => $divisions, 'client' => $client]);
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
        $division = Division::create($request->input());

        return back()
            ->with([
                'message' => 'Подразделение ' . $division->name . ' успешно создано',
                'alert-class' => 'alert-success'
            ]);
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
    public function destroy(Division $division)
    {
        //
    }
}
