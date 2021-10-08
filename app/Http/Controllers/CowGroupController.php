<?php

namespace App\Http\Controllers;

use App\Models\Cow;
use App\Models\CowGroup;
use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;

class CowGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $client)
    {
        $cowGroups = CowGroup::where('user_id', $client->id)->get();

        return response()->json(['cowGroups' => $cowGroups]);
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
        $item = CowGroup::create([
            'user_id' => $request->id,
            'name' => $request->newCowGroup
        ]);

        return $this->sendResponse($item, 'Группа успешно добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CowGroup $cowGroup
     * @return \Illuminate\Http\Response
     */
    public function show(CowGroup $cowGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CowGroup $cowGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(CowGroup $cowGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CowGroup $cowGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CowGroup $cowGroup)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CowGroup $cowGroup
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $group = CowGroup::find($request->id);
        $group->delete();

        return $this->sendResponse($group, 'Группа успешно удалена');
    }

    public function change(Request $request)
    {
        $data = $request->input();

        foreach ($data['devices'] as $deviceId) {
            Cow::where('id', $deviceId)->update(['group_id' => $data['item_id']]);
        }

        return back()
            ->with([
                'message' => 'Группа у коров успешно обновлена',
                'alert-class' => 'alert-success'
            ]);
    }
}
