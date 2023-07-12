<?php

namespace App\Http\Controllers;

use App\Supplier;
use App\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with("locale")->get();
        return response()->json($suppliers);
    }

    public function store(Request $request)
    {
        if (DB::table('suppliers')->where('name', ucwords($request->name))->doesntExist()) {
            DB::table('suppliers')->insert([
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact' => $request->contact,
                    'address' => $request->address,
                    'tin' => $request->tin,
                    'locale_id' => $request->locale_id,
                    'created_at' =>  Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            ]);
        } else {
            return response()->json(['error' => 'Supplier already exists!'], 500);
        }
    }

    public function show($id)
    {
        $supplier = Supplier::with("locale")->find($id);

        if (!empty($supplier))
            return response()->json($supplier);

        return response()->json(['error' => 'Resource not found!'], 404);
    }

    public function update(Request $request, $id)
    {
        DB::table('suppliers')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'address' => $request->address,
                'contact' => $request->contact,
                'email' => $request->email,
                'tin' => $request->tin,
                'locale_id' => $request->locale_id
            ]);

        return response()->json($this->index()->original);
    }

    public function showSearch(Request $request)
    {
        $suppliers = Supplier::where('name', 'like', '%' . $request->supplier . '%')->get();
        return response()->json($suppliers);
    }
}
