<?php

namespace App\Http\Controllers;

use App\term;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class TermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $term = term::all();

        return response()->json($term);
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
        $term = term::where('term', "=", ucwords($request->name))->first();
        if (empty($term)) {
            DB::table('terms')->insert([
                [
                    'term' => ucwords($request->name),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ]);

            // return response()->json($this->index());
            return response()->json($request->name);
        } else {
            return response()->json(['error' => 'Term of Payment already exists!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\term  $term
     * @return \Illuminate\Http\Response
     */
    public function show(term $term)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\term  $term
     * @return \Illuminate\Http\Response
     */
    public function edit(term $term)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\term  $term
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return response()->json($request->term);
        DB::table('terms')->where('id', $id)->update(['term' => $request->term, 'updated_at' => Carbon::now()]);
        return response()->json($this->index()->original);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\term  $term
     * @return \Illuminate\Http\Response
     */
    public function destroy(term $term)
    {
        //
    }
}
