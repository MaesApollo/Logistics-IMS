<?php

namespace App\Http\Controllers;

use App\accountability;
use Auth;
use App\Item;
use App\User;
use App\Type;
use App\department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use stdClass;

class AccountabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accountability = accountability::with(['item_accountability_details', 'released_by'])->orderBy('id', 'asc')->get();

        // foreach ($accountability as $sret) {

        //     $collection = collect($sret);
        //     array_push($temp_accountability, $collection->all());
        // }


        // $accountability = $temp_accountability;

        return response()->json($accountability);
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
     * @param  \App\accountability  $accountability
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\accountability  $accountability
     * @return \Illuminate\Http\Response
     */
    public function edit(accountability $accountability)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\accountability  $accountability
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, accountability $accountability)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\accountability  $accountability
     * @return \Illuminate\Http\Response
     */
    public function destroy(accountability $accountability)
    {
        //
    }


    public function submit_accountable(Request $request)
    {
        // return $request;
        $accountable = (object) $request->item_accountability_details;
        $id = DB::table('accountabilities')->insertGetId([
            'accountable' => $request->accountable,
            'date_accounted' => Carbon::now(),
            'released_by' => $request->released_by,
            'department' => $request->department,
            'area' => $request->area,
            'remarks' => 'remarks',
            'status' => 'saved',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        foreach ($accountable as $accountable) {
            $accountable = (object) $accountable;

            DB::table('accountability_item')->insert(
                [
                    'accountability_id' => $id,
                    'item_id' => $accountable->id,
                    'qty' => $accountable->qty,
                    'remarks' => $accountable->remarks
                ]
            );
        }
    }
}
