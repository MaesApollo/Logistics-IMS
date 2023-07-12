<?php

namespace App\Http\Controllers;

use App\mode_of_payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ModeOfPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mode = mode_of_payment::all();

        return response()->json($mode);
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
        // return response()->json($request->name);
        $mode = mode_of_payment::where('mode', "=", ucwords($request->name))->first();
        if (empty($mode)) {
            DB::table('mode_of_payments')->insert([
                [
                    'mode' => ucwords($request->name),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ]);

            // return response()->json($this->index());
            return response()->json($request->name);
        } else {
            return response()->json(['error' => 'Mode of Payment already exists!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\mode_of_payment  $mode_of_payment
     * @return \Illuminate\Http\Response
     */
    public function show(mode_of_payment $mode_of_payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\mode_of_payment  $mode_of_payment
     * @return \Illuminate\Http\Response
     */
    public function edit(mode_of_payment $mode_of_payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\mode_of_payment  $mode_of_payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return response()->json($request->mode);
        DB::table('mode_of_payments')->where('id', $id)->update(['mode' => $request->mode, 'updated_at' => Carbon::now()]);
        return response()->json($this->index()->original);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\mode_of_payment  $mode_of_payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(mode_of_payment $mode_of_payment)
    {
        //
    }
}
