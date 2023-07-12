<?php

namespace App\Http\Controllers;

use App\Log;
use App\Item;
use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class LogController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $log = Log::all();

    return $log;
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
    /*
        $log = new Log;
        $log->item_id = $request->item_id;
        $log->serial = $request->serial;
        $log->status = $status->status;
        $log->remark = $remark->remark;
        $log->save();
        */
    Log::create($request->all());
    /*
        Log::create([
        'item_id' = $request->item_id,
        'serial' = $request->serial,
        'status' = $status->status,
        'remark' = $remark->remark,
        ]);*/
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Log  $log
   * @return \Illuminate\Http\Response
   */
  public function show($item_id)
  {
    $logs = DB::table('logs')
        ->where('item_id',$item_id)
        ->get();
    
    return response()->json($logs);
   // return $item_id;
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Log  $log
   * @return \Illuminate\Http\Response
   */
  public function edit(Log $log)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Log  $log
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Log $log)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Log  $log
   * @return \Illuminate\Http\Response
   */
  public function destroy(Log $log)
  {
    //
  }

  public function toPrint($id)
  {


    $item = Item::with(['type', 'category', 'stocks'])
      ->find($id);

    // $container = (object) [
    //     0 => [
    //     'serial1' => 'SERIAL1',
    //     'serial2' => 'SERIAL2'
    //     ],
    //     1 => [
    //         'serial1' => 'SERIAL1',
    //         'serial2' => 'SERIAL2'
    //     ]
    //  ];
    $container = array();
    $x = 0;
    $serial1 = array();
    $serial2 = array();
    $c = 0;


    foreach ($item->stocks as $stock) {

      $temp_serial = DB::table('stock_serial')
        ->where('stock_id', $stock->id)->get();

      foreach ($temp_serial as $value) {
        if ($x % 2 == 0) {
          array_push($serial1, $value->serial);
        } else {
          array_push($serial2, $value->serial);
        }
        $x++;
      }
    }

    for ($z = 0; $z < count($serial1); $z++) {
      
      if(!isset($serial2[$z]))
      {
        $temp = (object)[
          'serial1' => $serial1[$z]
        ];
      }
      else
      {
        $temp = (object)[
          'serial1' => $serial1[$z],
          'serial2' => $serial2[$z]
        ];
      }
      
      array_push($container, $temp);
    }

    return response()->json($container);
  }

  public function salesReturn(Request $request)
  {

    $status = 0;
    $serial_code = '';
    $item = Item::with(['type', 'category', 'stocks'])
      ->find($request->productCode);

    if ($item->type->name == 'Consumable') {
      $serial_code = null;
      DB::table('stocks')->insert(
        [
          'item_id' => $item->id,
          'warehouse_id' => $item->stocks[0]->warehouse_id,

          'qty_in' => $request->salesReturn_qty,
          'received_at' => $request->date_recieve,
          'created_at' => Carbon::now(),
          'updated_at' => Carbon::now(),
        ]
      );
      $status = 1;
    } else {
      $serial_code = $request->serial;
      $serial = DB::table('stock_serial')->where('serial', $request->serial)->first();

      DB::table('stock_serial')
        ->where('serial', $request->serial)
        ->update([
          'status' => 'stocked in'
        ]);

      if ($request->salesReturn_status == 'Defective') {
        DB::table('stocks')
          ->where('id', $serial->stock_id)
          ->decrement(
            'qty_out',
            $request->salesReturn_qty
          );
      } else {
        $query = DB::table('stocks')
          ->where('id', $serial->stock_id);

        $query->decrement('qty_out', $request->salesReturn_qty);
        //$query->increment('qty_in', $request->salesReturn_qty);
      }
      $status = 1;
    }

    DB::table('logs')->insert(
      [
        'item_id' => $item->id,
        'serial' => $serial_code,

        'status' => $request->salesReturn_status,
        'remarks' => $request->salesReturn_remarks,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
      ]
    );


    return $status;
    //return $request;

  }
}
