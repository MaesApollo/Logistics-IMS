<?php

namespace App\Http\Controllers;

use App\company_assets;
use App\company_assets_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyAssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_assets = DB::table('company_assets_types')
            ->join('company_assets', 'company_assets_types.id', 'company_assets.company_assets_types_id')
            ->get();


        return response()->json($company_assets);
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
        $ret = company_assets::create($request->all());


        return response()->json($this->index()->original);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\company_assets  $company_assets
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asset = company_assets::find($id);
        return response()->json($asset);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\company_assets  $company_assets
     * @return \Illuminate\Http\Response
     */
    public function edit(company_assets $company_assets)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\company_assets  $company_assets
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$asset = company_assets::find($request->id)->update($request->all());
        //return $id;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\company_assets  $company_assets
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        company_assets::destroy($id);
        return response()->json($this->index()->original);
    }


    public function updateAsset(Request $request)
    {
        $asset  = company_assets::findOrFail($request->id);

        $input = $request->all();

        $asset->fill($input)->save();
        //company_assets::find($request->id)->update($request->all());
        return response()->json($asset);
    }


    public function searchAsset(Request $request)
    {
        $assets = DB::table('company_assets_types')
            ->join('company_assets', 'company_assets_types.id', 'company_assets.company_assets_types_id')
            ->where('name', 'like', "%" . $request->assets . "%")
            ->orWhere('model', 'like', "%" . $request->assets . "%")
            ->orWhere('area', 'like', "%" . $request->assets . "%")
            ->orWhere('accountable_name', 'like', "%" . $request->assets . "%")
            ->get();

        if (!empty($request->type)) {
            $collection = collect($assets);
            $filtered = $collection->where('company_assets_types_id', $request->type);
            $assets = $filtered->all();
        }
        return response()->json($assets);
    }
}
