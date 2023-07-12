<?php

namespace App\Http\Controllers;

use App\Stock;
use App\Events\NotifyUpdatedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class StockController extends Controller
{
    public function index()
    {
        // $items = DB::table('items')
        //     ->where('items.type_id', '2')
        //     ->join('stocks', 'stocks.item_id', 'items.id')
        //     ->get();

        // foreach ($items as $item) {
        //     for ($i = 1; $i <= $item->qty_in; $i++) {
        //         $serial = strtoupper($item->item_id . $item->id . substr(md5(uniqid('', true)), -8));

        //         while (DB::table('stock_serial')->where('serial', $serial)->exists()) {
        //             $serial = strtoupper($item->item_id . $item->id . substr(md5(uniqid('', true)), -8));
        //         }

        //         DB::table('stock_serial')->insert(
        //             [
        //                 'stock_id' => $item->id,
        //                 'serial' => $serial,
        //                 'status' => 'stocked in',
        //             ]
        //         );
        //     }
        // }
        // return response()->json($items);
    }

    public function store(Request $request)
    {

        foreach ($request->receives as $receive) {
            $receive = (object) $receive;
            $receive->pivot = (object) $receive->pivot;

            $id = DB::table('stocks')->insertGetId(
                [
                    'item_id' => $receive->id,
                    'warehouse_id' => $receive->received_to,
                    'purchase_order_id' => $request->purchase_order_id,
                    'price' => $receive->pivot->price,
                    'qty_in' => $receive->qty_received,
                    'received_at' => $request->date_receive,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );

            $receive->type = (object) $receive->type;

            if ($receive->type->name == "Serialize") {

                if ($request->barcodes != null) {

                    $data = [];
                    foreach ($receive->barcodes as $serial) {
                        $serial = (object) $serial;


                        $temp = [
                            'stock_id' => $id,
                            'serial' => $serial->serial,
                            'status' => 'stocked in',
                        ];
                        array_push($data, $temp);
                    }


                    while (DB::table('stock_serial')->where('serial', $serial->serial)->exists()) {
                        return response()->json("Serials already exist!");
                    }

                    DB::table('stock_serial')->insert(
                        $data
                    );

                    //   end of insert from file

                } else {
                    for ($i = 1; $i <= $receive->pivot->qty; $i++) {
                        $serial = strtoupper($receive->id . $id . substr(md5(uniqid('', true)), -8));

                        while (DB::table('stock_serial')->where('serial', $serial)->exists()) {
                            $serial = strtoupper($receive->id . $id . substr(md5(uniqid('', true)), -8));
                        }

                        DB::table('stock_serial')->insert(
                            [
                                'stock_id' => $id,
                                'serial' => $serial,
                                'status' => 'stocked in'
                            ]
                        );
                    }
                }
            }
        }

        $stocksQty = DB::table('stocks')
            ->where('purchase_order_id', $request->purchase_order_id)
            ->sum('qty_in');

        $orderedQty = DB::table('item_purchase_order')
            ->where('purchase_order_id', $request->purchase_order_id)
            ->sum('qty');

        if ($stocksQty == $orderedQty) {
            DB::table('purchase_orders')
                ->where('id', $request->purchase_order_id)
                ->update([
                    'status' => "order complete",
                    'updated_at' => \Carbon\Carbon::now()
                ]);
        }

        //event(new NotifyUpdatedItem);
    }

    public function directStore(Request $request)
    {
        // return response()->json($request);

        try {
            DB::beginTransaction();
            $supplier = (object) $request->supplier;
            $user = (object) $request->user;
            $receive_date = date_create($request->date_receive);
            $receive_date = date_format($receive_date, "Y-m-d H:i:s");

            $direct = DB::table('direct_receives')
                ->insertGetId([
                    'user_id' => $user->id,
                    'supplier_id' => $supplier->id,
                    'total' => $request->total,
                    'class' => $request->class,
                    'note' => $request->note,
                    'received_date' => $receive_date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            foreach ($request->receives as $receive) {
                $receive = (object) $receive;
                $receive->pivot = (object) $receive->pivot;
                $receive->type = (object) $receive->type;
                $supplier = (object) $request->supplier;
                $user = (object) $request->user;

                // $receive->barcodes = (object) $receive->barcodes;

                $receive_date = date_create($request->date_receive);
                $receive_date = date_format($receive_date, "Y-m-d H:i:s");



                DB::table('direct_receive_item')
                    ->insert([
                        'direct_receive_id' => $direct,
                        'item_id' => $receive->id,
                        'warehouse_id' => $receive->received_to,
                        'qty' => $receive->qty,
                        'price' => $receive->price
                    ]);


                // ADD TO STOCKS//
                $id = DB::table('stocks')->insertGetId(
                    [
                        'user' => $user->id,
                        'supplier' => $supplier->id,
                        'item_id' => $receive->id,
                        'direct_receive_id' => $direct,
                        'warehouse_id' => $receive->received_to,
                        'price' => $receive->price,
                        'qty_in' => $receive->qty,
                        'received_at' => $receive_date,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );



                if ($receive->type->name == "Serialize") {


                    if ($request->barcodes != null) {

                        $data = [];
                        foreach ($receive->barcodes as $serial) {
                            $serial = (object) $serial;


                            $temp = [
                                'stock_id' => $id,
                                'serial' => $serial->serial,
                                'status' => 'stocked in',
                            ];
                            array_push($data, $temp);
                        }


                        while (DB::table('stock_serial')->where('serial', $serial->serial)->exists()) {
                            return response()->json("Serials already exist!");
                        }

                        DB::table('stock_serial')->insert(
                            $data
                        );

                        //   end of insert from file

                    } else {

                        for ($i = 1; $i <= $receive->qty; $i++) {
                            $serial = strtoupper($receive->id . $id . substr(md5(uniqid('', true)), -8));

                            while (DB::table('stock_serial')->where('serial', $serial)->exists()) {
                                $serial = strtoupper($receive->id . $id . substr(md5(uniqid('', true)), -8));
                            }

                            DB::table('stock_serial')->insert(
                                [
                                    'stock_id' => $id,
                                    'serial' => $serial,
                                    'status' => 'stocked in',
                                ]
                            );
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response([$ex, 500]);
        }

        //event(new NotifyUpdatedItem);
    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function getSerialsPerItem(Request $request)
    {
        $item = $request->id;
        $qty = $request->delivering_qty;

        $tbl = Stock::where('item_id', $item)->get();

        $ret = [];
        foreach ($tbl as $item) {
            $temp = DB::table('stock_serial')
                ->where('stock_id', $item->id)
                ->where('status', 'stocked in')
                ->take($qty)
                ->get('serial');
            // array_push($ret, $temp);
            $collection = collect($temp);
        }


        return $collection;
    }
}
