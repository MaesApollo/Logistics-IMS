<?php

namespace App\Http\Controllers;

use App\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index()
    {
    	$types = Type::orderBy('id', 'DESC')->get();

    	return response()->json($types);
    }

    public function store(Request $request)
    {
        $types = Type::where('name', '=', ucwords($request->name))->first();

        if(empty($types)) {

            Type::create([
                'name' => ucwords($request->name),
                'description' => $request->description,
            ]);
        } else {

            return response()->json(['error' => 'Type already exists!'], 500);
        }
    }

    public function show($id)
    {
      $type = Type::find($id);

      if(!empty($type))
        return response()->json($type);

      return response()->json(['error' => 'Resource not found!'], 404);
    }
}
