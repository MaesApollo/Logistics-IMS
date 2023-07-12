<?php

namespace App\Http\Controllers;

use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::all();
        return response()->json($warehouses);
    }

    public function store(Request $request)
    {
        // Warehouse::firstOrCreate([
        // 	'name' => $request->name,
        // 	'address' => $request->address,
        // ]);

        $warehouse = Warehouse::where('name', '=', ucwords($request->name))->first();

        if (empty($warehouse)) {
            DB::table('warehouses')->insert([
                [
                    'name' =>
                    ucwords($request->name),
                    'address' => ucwords($request->address),
                    'created_at' =>  \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()


                ]
            ]);
            return response()->json($this->index());
        } else {
            return response()->json(['error' => 'Warehouse already exists!'], 500);
        }
    }

    public function update(Request $request, $id)
    {

        DB::table('warehouses')->where('id', $id)->update([
            'name' => $request->name,
            'address' => $request->address,
            'updated_at' => \Carbon\Carbon::now()
        ]);
        return response()->json($this->index()->original);
    }

    public function show($id)
    {
        $warehouse = Warehouse::find($id);

        if (!empty($warehouse))
            return response()->json($warehouse);

        return response()->json(['error' => 'Resource not found!'], 404);
    }

    public function showSearch(Request $request)
    {
        $warehouses = Warehouse::where('name', 'like', '%' . $request->warehouse . '%')->get();
        return response()->json($warehouses);
    }
}
