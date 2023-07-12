<?php

namespace App\Http\Controllers;

use App\item_group;
use Illuminate\Http\Request;

class ItemGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = item_group::all();
        return response()->json($groups);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\item_group  $item_group
     * @return \Illuminate\Http\Response
     */
    public function show(item_group $item_group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\item_group  $item_group
     * @return \Illuminate\Http\Response
     */
    public function edit(item_group $item_group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\item_group  $item_group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, item_group $item_group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\item_group  $item_group
     * @return \Illuminate\Http\Response
     */
    public function destroy(item_group $item_group)
    {
        //
    }
}
