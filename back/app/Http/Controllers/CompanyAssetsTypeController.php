<?php

namespace App\Http\Controllers;

use App\company_assets_type;
use App\company_assets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CompanyAssetsTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $type = company_assets_type::all();

        return response()->json($type);
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
        // $ret = company_assets_type::create($request->all());
        // return response()->json($ret);
        $assets_type = company_assets_type::where('type_name', "=", ucwords($request->type_name))->first();
        if (empty($assets_type)) {
            DB::table('company_assets_types')->insert([
                [
                    'type_name' => ucwords($request->type_name),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ]);
            return response()->json($this->index());
        } else {
            return response()->json(['error' => 'Asset type already exists!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\company_assets_type  $company_assets_type
     * @return \Illuminate\Http\Response
     */
    public function show(company_assets_type $company_assets_type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\company_assets_type  $company_assets_type
     * @return \Illuminate\Http\Response
     */
    public function edit(company_assets_type $company_assets_type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\company_assets_type  $company_assets_type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //return $id;
        DB::table('company_assets_types')->where('id', $id)->update(['type_name' => $request->type_name, 'updated_at' => Carbon::now()]);
        return response()->json($this->index()->original);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\company_assets_type  $company_assets_type
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company_assets = company_assets::where('company_assets_types_id', '=', $id)->first();

        if (empty($company_assets)) {
            company_assets_type::destroy($id);
            return response()->json($this->index()->original);
        } else {
            return response()->json(["error" => "Can't delete this Asset type, it is still in use."], 500);
        }
    }
}
