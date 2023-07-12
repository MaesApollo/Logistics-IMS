<?php

namespace App\Http\Controllers;

use App\direct_receive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Exception;

class DirectReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $container = [];
        $directReceive = direct_receive::with(['user', 'supplier', 'item', 'warehouse'])->orderBy('id', 'desc')->take(20)->get();


        foreach ($directReceive as $sret) {

            $collection = collect($sret);
            $collection->put('saveVisibility', 'hide');
            array_push($container, $collection->all());
        }


        $directReceive = $container;
        return response()->json($directReceive);
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
     * @param  \App\direct_receive  $direct_receive
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\direct_receive  $direct_receive
     * @return \Illuminate\Http\Response
     */
    public function edit(direct_receive $direct_receive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\direct_receive  $direct_receive
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $request = (object) $request;
            // return $request;

            DB::table('direct_receives')
                ->where('id', $id)
                ->update([
                    'report_id' => $request->report_id,
                    'supplier_id' => $request->supplier_id,
                    'class' => $request->class,
                    'note' => $request->note,
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();;
            return "Updated!";
        } catch (Exception $ex) {
            DB::rollBack();

            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\direct_receive  $direct_receive
     * @return \Illuminate\Http\Response
     */
    public function destroy(direct_receive $direct_receive)
    {
        //
    }

    public function search(Request $request)
    {

        $container = [];
        $filter = $request->filter;

        if ($request->date_to == null)
            $request->date_to = $request->date_from;

        $from = new Carbon($request->date_from);
        $to = new Carbon($request->date_to);
        $to = $to->addDay();

        $tbl = direct_receive::with(['user', 'supplier', 'item', 'warehouse']);
        if ($filter == "user") {
            $tbl = $tbl->where('user_id', $request->userSelected)->orderBy('id', 'desc')->get();
        } else if ($filter == "supplier") {
            $tbl = $tbl->where('supplier_id', $request->supplierSelected)->orderBy('id', 'desc')->get();
        } else if ($filter == "date") {
            $tbl = $tbl->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])->get();
        }

        foreach ($tbl as $items) {
            $collection = collect($items);
            array_push($container, $collection->all());
        }

        $tbl = $container;
        return response()->json($tbl);
    }
}
