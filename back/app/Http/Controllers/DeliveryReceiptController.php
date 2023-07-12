<?php

namespace App\Http\Controllers;

use App\Item;
use App\SalesOrder;
use App\DeliveryReceipt;
use App\Stock;
use App\Client;
use App\Events\NotifyUpdatedSalesOrder;
use App\Events\NotifyUpdatedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\Log;

class DeliveryReceiptController extends Controller
{
    private $cname = "DeliveryReceiptController";
    public function index()
    {
        $delivery_receipts = DeliveryReceipt::with(['sales_order', 'user'])->orderBy('id', 'desc')->take(10)->get();

        $temp_delivery_receipts = [];

        foreach ($delivery_receipts as $delivery_receipt) {

            $client = Client::find($delivery_receipt->sales_order->client_id);

            if (empty($client)) {
                $client = [];
            }

            $collection = collect($delivery_receipt);
            $collection->put('client', $client);
            array_push($temp_delivery_receipts, $collection->all());
        }

        $delivery_receipts = $temp_delivery_receipts;

        return response()->json($delivery_receipts);
    }

    public function store(Request $request, $id)
    {
        $temp = DB::table('sales_orders')->where('id', $id)->first();

        $delivery_receipt_id = DB::table('delivery_receipts')->insertGetId(
            [
                "sales_order_id" => $id,
                "memo" => $temp->note,
                "user_id" => $request->id,
                "status" => "for delivery",
                "created_at" =>  Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        );

        //$sales_order = DB::table('sales_orders')
        //	->where('id', $id)->first();
        //broadcast(new NotifyUpdatedSalesOrder($sales_order));

        return response()->json($delivery_receipt_id);
    }

    public function show($id)
    {


        $delivery_receipt =
            DeliveryReceipt::with(['sales_order', 'user'])
            ->find($id);


        $temp_orders =
            DB::table('delivery_receipt_item')
            ->where('delivery_receipt_id', $id)
            ->get();

        $orders = [];


        if (!empty($temp_orders)) {

            foreach ($temp_orders as $value) {
                $delivered_qty = 0;
                $item = (object) app(ItemsController::class)->show($value->item_id);
                $item = (object) $item->original;
                $item->type = (object) $item->type;


                if ($item->type->name == "Serialize") {

                    if ($this->item_exist($item->id, $orders, $value->barcode)[0]) {

                        $index = $this->item_exist($item->id, $orders, $value->barcode)[1];
                        $orders[$index] = (object) $orders[$index];
                        array_push($orders[$index]->ordered_serial, $value->barcode);
                    } else {

                        $temp_ordered_serial = [];
                        array_push($temp_ordered_serial, $value->barcode);


                        $sales_order_details =
                            DB::table('item_sales_order')
                            ->where('sales_order_id', $delivery_receipt->sales_order_id)
                            ->where('item_id', $item->id)
                            ->first();

                        if ($delivery_receipt->status != 'for delivery') {
                            $delivered_qty = DB::table('delivery_receipt_item')
                                ->where('delivery_receipt_id', $delivery_receipt->id)
                                ->where('item_id', $value->item_id)
                                ->sum('qty');
                            $value->qty_return = DB::table(
                                'delivery_receipt_item'
                            )->where('delivery_receipt_id', $delivery_receipt->id)
                                ->sum('qty_return');
                        }

                        $collection = collect($item);
                        $collection->put('price', $value->price);
                        $collection->put('ordered_qty', $sales_order_details->qty);
                        $collection->put('delivering_qty', $value->qty);
                        $collection->put('note', $value->note);
                        $collection->put('return_qty', $value->qty_return);
                        $collection->put('ordered_serial', $temp_ordered_serial);
                        $collection->put('delivered_qty', $delivered_qty);
                        $collection->put('saveVisibility', 'hide');

                        array_push($orders, $collection->all());
                    }
                } else if ($item->type->name == "Consumable") {
                    $sales_order_details =
                        DB::table('item_sales_order')
                        ->where('sales_order_id', $delivery_receipt->sales_order_id)
                        ->where('item_id', $item->id)
                        ->first();

                    if ($delivery_receipt->status != 'for delivery') {
                        $delivered_qty = DB::table('delivery_receipt_item')
                            ->where('delivery_receipt_id', $delivery_receipt->id)
                            ->where('item_id', $value->item_id)
                            ->sum('qty');
                    }

                    $collection = collect($item);
                    $collection->put('price', $value->price);
                    $collection->put('delivering_qty', $value->qty);
                    $collection->put('note', $value->note);
                    $collection->put('return_qty', $value->qty_return);
                    $collection->put('ordered_qty', $sales_order_details->qty);
                    $collection->put('delivered_qty', $delivered_qty);
                    $collection->put('saveVisibility', 'hide');
                    array_push($orders, $collection->all());
                }
            }
        }


        $collection = collect($delivery_receipt);
        $collection->put('orders', $orders);
        $delivery_receipt = $collection->all();

        return response()->json($delivery_receipt);
    }

    public function update(Request $request, $id)
    {
        $request = (object) $request;

        DB::table('delivery_receipts')
            ->where('id', '=', $request->id)
            ->update(['updated_at' => Carbon::now()]);

        DB::table('delivery_receipt_item')->where('delivery_receipt_id', $request->id)->delete();

        foreach ($request->orders as $order) {
            $order = (object) $order;
            $order->type = (object) $order->type;

            if ($order->type->name == "Serialize") {
                $order->ordered_serial = (object) $order->ordered_serial;

                foreach ($order->ordered_serial as $serial) {

                    DB::table('delivery_receipt_item')->insert(
                        [
                            'delivery_receipt_id' => $id,
                            'item_id' => $order->id,
                            'barcode' => $serial,
                            'qty' => 1,
                            'price' => $order->price
                        ]
                    );
                }
            } else {
                DB::table('delivery_receipt_item')->insert(
                    [
                        'delivery_receipt_id' => $id,
                        'item_id' => $order->id,
                        'qty' => $order->delivering_qty,
                        'price' => $order->price
                    ]
                );
            }
        }
    }

    public function confirm($id)
    {
        DB::beginTransaction();
        try {
            DB::table('delivery_receipts')
                ->where('id', $id)
                ->update([
                    'status' => 'delivering',
                    'updated_at' => Carbon::now()
                ]);
            $orders = DB::table('delivery_receipt_item')
                ->where('delivery_receipt_id', '=', $id)->get();

            $client = DB::table('clients')
                ->join('sales_orders', 'clients.id', 'sales_orders.client_id')
                ->join('delivery_receipts', 'delivery_receipts.sales_order_id', 'sales_orders.id')
                ->where('delivery_receipts.id', $id)
                ->get();
            foreach ($orders as $order) {
                if (!empty($order->barcode)) {
                    DB::table('stock_serial')
                        ->where('serial', '=', $order->barcode)
                        ->update([
                            'status' => 'deployed',
                            'updated_at' => Carbon::now()
                        ]);
                    DB::table('stocks')
                        ->where('item_id', '=', $order->item_id)
                        ->increment('qty_out', 1);
                    $log = new Log;
                    $log->item_id = $order->item_id;
                    $log->serial = $order->barcode;
                    $log->status = 'deployed';
                    $log->remarks = 'Sold to ' . $client[0]->name;
                    $log->save();
                } else {
                    $order_remain = $order->qty;
                    $item_stocks = DB::table('stocks')
                        ->where('item_id', '=', $order->item_id)
                        ->orderBy('id', 'asc')->get();
                    foreach ($item_stocks as $stock) {
                        if (
                            $stock->qty_in > $stock->qty_out
                            && $order_remain > 0
                        ) {
                            $stock_available = $stock->qty_in - $stock->qty_out;
                            if ($stock_available >= $order_remain) {
                                DB::table('stocks')
                                    ->where('id', '=', $stock->id)
                                    ->increment('qty_out', $order_remain);
                                $order_remain = 0;
                            } else {
                                DB::table('stocks')
                                    ->where('id', '=', $stock->id)
                                    ->increment('qty_out', $stock_available);
                                $order_remain = $order_remain - $stock_available;
                            }
                        }
                        if ($order_remain < 1) {
                            break;
                        }
                    }
                    $log = new Log;
                    $log->item_id = $order->item_id;
                    $log->serial = $order->barcode;
                    $log->status = 'deployed';
                    $log->remarks = 'Sold to ' . $client[0]->name . ' Quantity ' . $order->qty;
                    $log->save();
                }
                //update sales order if all delivered items are completed
                $this->checkSalesOrderComplete($id);
                $delivery_receipt = DB::table('delivery_receipts')
                    ->where('id', $id)->first();
                $sales_order = DB::table('sales_orders')
                    ->where('id', $delivery_receipt->sales_order_id)->first();
                //broadcast(new NotifyUpdatedSalesOrder($sales_order));
            }
            DB::commit();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex], 500);
        }
    }

    public function item_exist($item_id, $orders, $serial)
    {
        $bool = false;
        $index = 0;

        foreach ($orders as $item) {
            $item = (object) $item;

            if ($item->id == $item_id) {
                $bool = true;

                break;
            }

            $index++;
        }

        return array($bool, $index);
    }

    public function checkSalesOrderComplete($id)
    {
        $delivery_receipt = DB::table('delivery_receipts')
            ->where('id', $id)->first();

        $sales_order_details = DB::table('item_sales_order')
            ->where('sales_order_id', $delivery_receipt->sales_order_id)->get();

        $delivery_receipts = DB::table('delivery_receipts')
            ->where('sales_order_id', $delivery_receipt->sales_order_id)->get();

        $total_delivered_qty = 0;

        if (!empty($sales_order_details)) {
            foreach ($sales_order_details as $ordered) {
                foreach ($delivery_receipts as $receipt) {
                    if ($receipt->status != 'for delivery') {
                        $total_delivered_qty += DB::table('delivery_receipt_item')
                            ->where('delivery_receipt_id', $receipt->id)
                            ->where('item_id', $ordered->item_id)
                            ->sum('qty');
                    }
                }
            }
        }

        $total_sales_order_qty = DB::table('item_sales_order')
            ->where('sales_order_id', $delivery_receipt->sales_order_id)
            ->sum('qty');

        if ($total_delivered_qty == $total_sales_order_qty) {
            DB::table('sales_orders')
                ->where('id', $delivery_receipt->sales_order_id)
                ->update([
                    'status' => 'order complete',
                    'updated_at' => Carbon::now()
                ]);

            DB::table('delivery_receipts')
                ->where('id', '=', $id)
                ->update(['status' => 'delivered', 'updated_at' => Carbon::now()]);
        }
    }

    public function changeStatus($id)
    {
        try {
            DB::table('delivery_receipts')
                ->where('id', '=', $id)
                ->update(['status' => 'delivered', 'updated_at' => Carbon::now()]);
            return 'ok';
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(["error" => "Update status failed please try again"], 500);
        }
    }

    public function showSearch(Request $request)
    {

        // return $request;
        $temp_delivery_receipts = [];
        $filter = $request->filter;

        if ($request->date_to == null)
            $request->date_to = $request->date_from;


        $from = new Carbon($request->date_from);
        $to = new Carbon($request->date_to);
        $to = $to->addDay();


        $delivery_receipts = DeliveryReceipt::with(['sales_order', 'client', 'user', 'delivery_receipt_details']);

        if ($request->sort == "1") {
            $delivery_receipts = DeliveryReceipt::with(['sales_order', 'sales_order.client', 'user', 'delivery_receipt_details'])->orderBy('id', 'desc');
        } elseif ($request->sort == "2") {
            $delivery_receipts = DeliveryReceipt::with(['sales_order', 'sales_order.client', 'user', 'delivery_receipt_details'])->orderBy('id', 'asc');
        }


        if ($filter == "number") {
            if ($request->number != null) {
                $delivery_receipts = $delivery_receipts->where('id', $request->number)->get();
            } else $delivery_receipts = $delivery_receipts->get();
        } else if ($filter == "note") {
            $noteselected = $request->note;
            $delivery_receipts = $delivery_receipts
                ->whereHas('delivery_receipt_details', function ($query) use ($noteselected) {
                    $query->where('note', 'like', '%' . $noteselected . '%');
                })
                ->get()
                ->unique('delivery_receipt_id');
        } else if ($filter == "memo") {
            $memo = $request->memo;

            $delivery_receipts = $delivery_receipts
                ->whereHas("sales_order", function ($query) use ($memo) {
                    $query->where('note', 'like', '%' . $memo . '%');
                })
                ->get()
                ->unique('id');
        } else if ($filter == "sales_no") {
            if ($request->sales_no != null) {
                $delivery_receipts = $delivery_receipts->where("sales_order_id", $request->sales_no)->get();
            } else $delivery_receipts = $delivery_receipts->get();
        } else if ($filter == "date") {
            if ($from != null && $to != null) {
                $delivery_receipts = $delivery_receipts->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])->get();
            } else $delivery_receipts = $delivery_receipts->get();
        } else if ($filter == "status") {
            if ($request->statusSelected != null) {
                $delivery_receipts = $delivery_receipts->where("status", $request->statusSelected)->get();
            } else $delivery_receipts = $delivery_receipts->get();
        } else if ($filter == "client") {
            if ($request->text != null) {
                $clientSelected = $request->text;
                $delivery_receipts = $delivery_receipts
                    ->whereHas('sales_order.client', function ($query) use ($clientSelected) {
                        $query->where('name', 'like', '%' . $clientSelected . '%');
                    })
                    ->get();
            } else $delivery_receipts = $delivery_receipts->get();
        }

        foreach ($delivery_receipts as $delivery_receipt) {
            $client = Client::find($delivery_receipt->sales_order->client_id);

            if (empty($client)) {
                $client = [];
            }

            //  if($filter == "client") {
            //     if ($request->clientSelected != null) {
            //        $delivery_receipt = $delivery_receipt->where("")
            //     } else
            //         $delivery_receipts = $delivery_receipts->get();
            // }

            $collection = collect($delivery_receipt);
            $collection->put('client', $client);
            array_push($temp_delivery_receipts, $collection->all());
        }


        $delivery_receipts = $temp_delivery_receipts;
        return response()->json($delivery_receipts);
    }

    public function create_delivery(Request $request)
    {
        try {
            // return $request;
            $mr = (object) $request->material_request;
            $dr = (object)$request->delivery_receipt;
            $user = (object)$request->user;
            $client = (object)$mr->client;

            $dr_id = DB::table('delivery_receipts')->insertGetId(
                [
                    "sales_order_id" => $mr->id,
                    "user_id" => $user->id,
                    "status" => 'delivering',
                    "memo" => 'test',
                    "created_at" =>  Carbon::now(),
                    "updated_at" => Carbon::now()
                ]
            );

            foreach ($dr->orders as $order) {

                $order = (object) $order;
                $order->type = (object) $order->type;

                if (empty($order->remarks)) {
                    $order->remarks = $order->id;
                }

                if ($order->type->name == "Serialize") {
                    $ordered_serials = [];
                    $temp = [];
                    $order_remain = $order->delivering_qty;
                    $item_stocks = DB::table('stocks')
                        ->where('item_id', '=', $order->id)
                        ->where(DB::raw('qty_in - qty_out'), '>', 0)
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($item_stocks as $stock) {
                        if (
                            $stock->qty_in > $stock->qty_out
                            && $order_remain > 0
                        ) {
                            $stock_available = $stock->qty_in - $stock->qty_out;
                            if ($stock_available >= $order_remain) {
                                $temp = DB::table('stock_serial')
                                    ->select('serial')
                                    ->where('stock_id', $stock->id)
                                    ->where('status', 'stocked in')
                                    ->take($order_remain)
                                    ->get();

                                array_push($ordered_serials, $temp);
                                foreach ($temp as $serial) {

                                    DB::table('delivery_receipt_item')->insert(
                                        [
                                            'delivery_receipt_id' => $dr_id,
                                            'item_id' => $order->id,
                                            'barcode' => $serial->serial,
                                            'qty' => 1,
                                            'note' => $order->remarks,
                                            'price' => $order->price
                                        ]
                                    );

                                    // change stocks & serials status
                                    DB::table('stock_serial')
                                        ->where('serial', '=', $serial->serial)
                                        ->update([
                                            'status' => 'deployed',
                                            'updated_at' => Carbon::now()
                                        ]);
                                    $log = new Log;
                                    $log->item_id = $order->id;
                                    $log->serial =
                                        $serial->serial;
                                    $log->status = 'deployed';
                                    $log->remarks = 'Sold to ' . $client->name;
                                    $log->save();
                                }

                                DB::table('stocks')
                                    ->where('id', '=', $stock->id)
                                    ->increment('qty_out', $order_remain);
                                $order_remain = 0;
                            } else {

                                $temp = DB::table('stock_serial')
                                    ->select('serial')
                                    ->where('stock_id', $stock->id)
                                    ->where('status', 'stocked in')
                                    ->take($order_remain)
                                    ->get();

                                array_push($ordered_serials, $temp);
                                foreach ($temp as $serial) {

                                    DB::table('delivery_receipt_item')->insert(
                                        [
                                            'delivery_receipt_id' => $dr_id,
                                            'item_id' => $order->id,
                                            'barcode' => $serial->serial,
                                            'qty' => 1,
                                            'note' => $order->remarks,
                                            'price' => $order->price
                                        ]
                                    );

                                    // change stocks & serials status
                                    DB::table('stock_serial')
                                        ->where('serial', '=', $serial->serial)
                                        ->update([
                                            'status' => 'deployed',
                                            'updated_at' => Carbon::now()
                                        ]);
                                    $log = new Log;
                                    $log->item_id = $order->id;
                                    $log->serial =
                                        $serial->serial;
                                    $log->status = 'deployed';
                                    $log->remarks = 'Sold to ' . $client->name;
                                    $log->save();
                                }
                                DB::table('stocks')
                                    ->where('id', '=', $stock->id)
                                    ->increment('qty_out', $stock_available);
                                $order_remain = $order_remain - $stock_available;
                            }
                        }
                        if ($order_remain < 1) {
                            break;
                        }
                    }
                } else {
                    DB::table('delivery_receipt_item')->insert(
                        [
                            'delivery_receipt_id' => $dr_id,
                            'item_id' => $order->id,
                            'qty' => $order->delivering_qty,
                            'note' => $order->remarks,
                            'price' => $order->price
                        ]
                    );

                    // change stock status

                    $order_remain = $order->delivering_qty;
                    $item_stocks = DB::table('stocks')
                        ->where('item_id', '=', $order->id)
                        ->where(DB::raw('qty_in - qty_out'), '>', 0)
                        ->orderBy('id', 'asc')->get();

                    foreach ($item_stocks as $stock) {
                        if (
                            $stock->qty_in > $stock->qty_out
                            && $order_remain > 0
                        ) {
                            $stock_available = $stock->qty_in - $stock->qty_out;
                            if ($stock_available >= $order_remain) {
                                DB::table('stocks')
                                    ->where('id', '=', $stock->id)
                                    ->increment('qty_out', $order_remain);
                                $order_remain = 0;
                            } else {
                                DB::table('stocks')
                                    ->where('id', '=', $stock->id)
                                    ->increment('qty_out', $stock_available);
                                $order_remain = $order_remain - $stock_available;
                            }
                        }
                        if ($order_remain < 1) {
                            break;
                        }
                    }
                    $log = new Log;
                    $log->item_id = $order->id;
                    $log->serial = '';
                    $log->status = 'deployed';
                    $log->remarks = 'Sold to ' . $client->name . ' Quantity ' . $order->delivering_qty;
                    $log->save();
                }
            }

            \Logger::instance()->log(
                Carbon::now(),
                $user->id,
                $user->name,
                $this->cname,
                "store",
                "message",
                "Created Delivery Receipt #" . $dr_id . " For Material Request #" . $mr->id
            );

            //update sales order if all delivered items are completed
            $this->checkSalesOrderComplete($dr_id);
            return $dr_id;
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(["error" => "Creating receipt failed please try again"], 500);
            DB::rollback();
            \Logger::instance()->log(
                Carbon::now(),
                $user->id,
                $user->name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
        }
    }

    public function update_row(Request $request)
    {

        try {

            $request->user = (object) $request->user;
            $order = $request->order;
            $dr_id = $request->id;

                DB::table('delivery_receipt_item')
                    ->where('delivery_receipt_id', $dr_id)
                    ->where('item_id', $order->id)
                    ->update(['note' => $order->note]);

                // logger
                \Logger::instance()->log(
                    Carbon::now(),
                    $request->user->id,
                    $request->user->name,
                    $this->cname,
                    "update",
                    "message",
                    "Update Item Status:" . $dr_id . ", For Delivery #: " . $order
                );




            return "ok";
        } catch (\Exception $ex) {
            DB::rollBack();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
            return response([$ex, 500]);
        }
    }

}
