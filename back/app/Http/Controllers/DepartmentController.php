<?php

namespace App\Http\Controllers;

use App\department;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = department::with('user')->orderBy('id', 'asc')->get();

        return response()->json($departments);
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
        // return $request;
        if (DB::table('departments')->where('name', ucwords($request->name))->doesntExist()) {
            DB::table('departments')->insert([
                [
                    'name' => $request->name,
                    'approver_id' => $request->approver_id,
                    'created_at' =>  Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            ]);
        } else {
            return response()->json(['error' => 'Department already exists!'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\department  $department
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return $id;
        $department = department::with("user")->find($id);

        if (!empty($department))
            return response()->json($department);

        return response()->json(['error' => 'Resource not found!'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::table('departments')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'approver_id' => $request->approver_id,
                'updated_at' => Carbon::now()
            ]);

        return response()->json($this->index()->original);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(department $department)
    {
        //
    }
}
