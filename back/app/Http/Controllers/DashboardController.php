<?php

namespace App\Http\Controllers;

use App\Stock;
use App\Type;
use App\Category;
use App\Warehouse;
use App\User;
use App\SalesOrder;
use App\DeliveryReceipt;
use App\direct_receive;
use App\Item;
use App\PurchaseOrder;
use App\SalesReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use PhpParser\Node\Stmt\Else_;

class DashboardController extends Controller
{

    public function showItemInventoryReport(Request $request)
    {
        try {

            // return $request;
            // if (empty($request->from) && empty($request->to)) {
            //     return "empty";
            // }elseif (!empty($request->from)) {

            // }
            $client_name  = "";
            $filter = $request->filterBy;
            $client = $request->clientSelected;
            if (!empty($client)) {
                $client_name = DB::table('clients')->where('id', $client)->value('name');
            }
            if ($request->end == null) {
                $request->end = $request->from;
            }

            $from = new Carbon($request->from);
            $to = new Carbon($request->to);

            $temp_purchase = [];
            $temp_delivery = [];
            $temp_return = [];
            $temp_items = [];
            $temp_client_items = [];

            // purchase summary
            if ($filter == 'purchaseSum') {
                if (empty($request->from) && empty($request->to)) {
                    if (!empty($request->supplierSelected)) {

                        $tbl = PurchaseOrder::with(['user', 'supplier'])
                            ->where('supplier_id', $request->supplierSelected)
                            ->get();
                    } else
                        $tbl = PurchaseOrder::with('user', 'supplier')->all();
                } elseif (!empty($request->from)) {
                    if (!empty($request->supplierSelected)) {

                        $tbl = PurchaseOrder::with(['user', 'supplier'])
                            ->where('supplier_id', $request->supplierSelected)
                            ->whereBetween('created_at', [$from, $to])->get();
                    } else
                        $tbl = PurchaseOrder::with('user', 'supplier')->whereBetween('created_at', [$from, $to])->get();
                }



                foreach ($tbl as $item) {
                    $collection = collect($item);
                    array_push($temp_purchase, $collection->all());
                }

                // delivery receipts summary
            } elseif ($filter == 'deliverySum') {
                if (empty($request->from) && empty($request->to)) {
                    $from = DeliveryReceipt::with(['delivery_receipt_details', 'sales_order.client'])->first()->value('created_at');
                    $to = DeliveryReceipt::with(['delivery_receipt_details', 'sales_order.client'])->latest()->value('created_at');
                }
                if (empty($client)) {
                    $tbl = DeliveryReceipt::with(['delivery_receipt_details', 'sales_order.client'])
                        ->whereBetween("delivery_receipts.created_at", [$from, $to])
                        ->get();
                } else
                    $tbl = DeliveryReceipt::with(['delivery_receipt_details', 'sales_order.client'])
                        ->whereBetween("delivery_receipts.created_at", [$from, $to])
                        ->whereHas("sales_order.client",  function ($query) use ($client) {
                            $query->where("clients.id", $client);
                        })->get();


                foreach ($tbl as $item) {
                    $collection = collect($item);
                    array_push($temp_delivery, $collection->all());
                }

                // sales return summary
            } elseif ($filter == 'items') {


                if (!empty($request->itemSelected)) {
                    $tbl = Stock::with(['item'])
                        ->where('item_id', $request->itemSelected)
                        ->join('items', 'stocks.item_id', 'items.id')
                        ->groupBy('item_id')
                        ->get();
                } else {
                    $tbl = Stock::with(['item'])->join('items', 'stocks.item_id', 'items.id')->orderBy('items.name')->groupBy('item_id')->get();
                }
                foreach ($tbl as $item) {
                    if (empty($request->from) && empty($request->to) && !empty($request->itemSelected)) {
                        $from = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')->where('item_id', $item->item_id)->first()->value('created_at');
                        $to = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')->where('item_id', $item->item_id)->latest()->value('created_at');
                    }
                    $total_qty_in = 0;

                    $total_deduct = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])
                        ->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')
                        ->where('item_id', $item->item_id)
                        ->whereBetween("created_at", [$from, $to])
                        ->selectRaw('*,sum(delivery_receipt_item.qty)as total_qty ')
                        ->value('total_qty');

                    $total_deduct_2 = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])
                        ->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')
                        ->where('item_id', $item->item_id)
                        ->where("created_at", "<=", $to)
                        ->selectRaw('*,sum(delivery_receipt_item.qty)as total_qty ')
                        ->value('total_qty');



                    $total_qty_in = DB::table('stocks')->where('item_id', $item->item_id)->where('created_at', "<=", $to)->sum('qty_in');
                    // $total_qty_out = DB::table('stocks')->where('item_id', $item->item_id)->where('created_at', "<=", $to)->sum('qty_out');
                    $qty_on_hand = $total_qty_in - $total_deduct_2;
                    $name = DB::table('items')->where('id', $item->item_id)->value('name');
                    $desc = DB::table('items')->where('id', $item->item_id)->value('description');
                    $price = DB::table('items')->where('id', $item->item_id)->value('price');
                    // $price = DB::table('stocks')->where('item_id', $item->item_id)->where('created_at', "<=", $to)->value('price');


                    $collect2 = collect();
                    $collect2->put('stock_id', $item->item_id);
                    $collect2->put('stock_desc', $name . ' - ' . $desc);
                    $collect2->put('stock_qty', $qty_on_hand);
                    $collect2->put('overall_qty', $total_qty_in);
                    $collect2->put('deduct_qty', $total_deduct);
                    $collect2->put('price', $price);
                    $collect2->put(
                        'item',
                        $item
                    );
                    array_push($temp_items, $collect2);
                }
            } elseif ($filter == 'client') {
                if (empty($request->from) && empty($request->to)) {
                    $from = DeliveryReceipt::with(['delivery_receipt_details', 'sales_order.client'])->first()->value('created_at');
                    $to = DeliveryReceipt::with(['delivery_receipt_details', 'sales_order.client'])->latest()->value('created_at');
                }
                $tbl = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])
                    ->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')
                    ->whereBetween("delivery_receipts.created_at", [$from, $to])
                    ->whereHas("sales_order.client",  function ($query) use ($client) {
                        $query->where("clients.id", $client);
                    })
                    ->selectRaw('*,sum(delivery_receipt_item.qty)as total_qty ')
                    ->groupBy('item_id')
                    ->get();


                foreach ($tbl as $item) {
                    $desc = DB::table('items')->where('id', $item->item_id)->value('description');
                    $name = DB::table('items')->where('id', $item->item_id)->value('name');
                    $coll = collect();
                    $coll->put('dr_id', $item->id);
                    $coll->put('memo', $item->sales_order->note);
                    $coll->put('created', $item->created_at->toDateString());
                    $coll->put('item_id', $item->item_id);
                    $coll->put('desc', $desc);
                    $coll->put('item_name', $name);
                    $coll->put('total_qty', $item->total_qty);
                    array_push($temp_client_items, $coll);
                    // $collection = collect($item);
                    // array_push($temp_client_items, $collection->all());
                }
            } elseif ($filter == 'salesReturn') {
                if (empty($request->from) && empty($request->to)) {
                    $from = SalesReturn::with("sales_return_details")->first()->value('date_return');
                    $to = SalesReturn::with("sales_return_details")->latest()->value('date_return');
                }
                if (empty($client)) {
                    $tbl = SalesReturn::with("sales_return_details")
                        ->whereBetween("date_return", [$from, $to])
                        ->get();
                } else
                    $tbl = SalesReturn::with("sales_return_details")
                        ->whereBetween("date_return", [$from, $to])
                        ->where("from_client_id", $client)->get();


                foreach ($tbl as $item) {
                    $collection = collect($item);
                    array_push($temp_return, $collection->all());
                }
            }

            $data = collect();
            $data->put('purchase', $temp_purchase);
            $data->put('delivery', $temp_delivery);
            $data->put('returns', $temp_return);
            $data->put('items', $temp_items);
            $data->put('clientItems', $temp_client_items);
            $data->put('clientName', $client_name);

            return response()->json($data);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function showClientInventoryReport(Request $request)
    {
        try {

            $dateselected = (object) $request->dateSelected;
            $from = new Carbon($dateselected->from);
            $to = new Carbon($dateselected->to);
            $c_id = $request->clientSelected;
            $item = $request->itemSelected;

            if (empty($c_id)) {
                $tbl = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])
                    ->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')
                    ->where('item_id', $item)
                    ->selectRaw('*,sum(delivery_receipt_item.qty)as total_qty ')
                    ->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])
                    ->groupBy('id')
                    ->get();
                $tbl2 = SalesReturn::with("sales_return_details")
                    ->where("status", "!=", "For Approval")
                    ->whereBetween("updated_at", [$from->toDateString(), $to->toDateString()])
                    ->whereHas("sales_return_details", function ($query) use ($item) {
                        $query->where("id", $item);
                    })
                    ->get();
            } else {
                $tbl = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])
                    ->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')
                    ->whereHas("sales_order.client",  function ($query) use ($c_id) {
                        $query->where("clients.id", $c_id);
                    })
                    ->where('item_id', $item)
                    ->selectRaw('*,sum(delivery_receipt_item.qty)as total_qty ')
                    ->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])
                    ->groupBy('id')
                    ->get();

                $tbl2 = SalesReturn::with("sales_return_details")
                    ->whereHas("client",  function ($query) use ($c_id) {
                        $query->where("clients.id", $c_id);
                    })
                    ->where("status", "!=", "For Approval")
                    ->whereBetween("updated_at", [$from->toDateString(), $to->toDateString()])
                    ->whereHas("sales_return_details", function ($query) use ($item) {
                        $query->where("id", $item);
                    })
                    ->get();
            }

            $tbl3 = DB::table('stocks')->where("item_id", $item)->whereBetween("received_at", [$from->toDateString(), $to->toDateString()])->get();



            $stocks = DB::table('stocks')
                ->latest('created_at')
                ->where('item_id', $item)
                ->get();

            foreach ($stocks as $stock) {

                $dateStocks = new Carbon($stock->created_at);
                $stocks = $stock->qty_in;
            }


            $data = [];
            $data2 = [];
            $data3 = [];
            $total_qty = 0;
            $type = "";
            $dateStocks = $dateStocks->toFormattedDateString();

            // for DR & S.O
            // foreach ($tbl as $item) {
            //     $total_delivered_qty = 0;

            //     $collect = collect();
            //     $date = new Carbon($item->updated_at);
            //     $sales_order = $item->sales_order;
            //     $client = $sales_order->client;
            //     $type = "Invoice";
            //     $dateSales =
            //         new Carbon($sales_order->created_at);

            //     $desc = "";
            //     foreach ($item->delivery_receipt_details as $dr) {

            //         $pivot = $dr->pivot;
            //         $total_delivered_qty += $pivot->qty;
            //         $desc = $dr->description;
            //         $dr_qty = $pivot->qty;
            //     }

            //     $collect->put('type', $type);
            //     $collect->put('date', $date->toFormattedDateString());
            //     $collect->put('dateSales', $dateSales->toFormattedDateString());
            //     $collect->put('sales_order_id', $item->sales_order_id);
            //     $collect->put('dr_id', $item->id);
            //     $collect->put('name', $client->name);
            //     $collect->put('memo', $item->memo);
            //     $collect->put('class', $client->class);
            //     $collect->put('desc', $desc);
            //     $collect->put('qty', $dr_qty);

            //     $total_qty += $dr_qty;

            //     $collect->put('item', $item);
            //     array_push($data, $collect);
            // }

            foreach ($tbl as $item) {
                $total_delivered_qty = 0;

                $collect = collect();
                $client = (object)$item->sales_order->client;
                $request = (object)$item->sales_order;
                $dr_item = (object)$item->delivery_receipt_details;
                foreach ($dr_item as $dr_item) {
                    $dr_item_desc = $dr_item->description;
                }


                $collect->put('type', 'Invoice');
                $collect->put('date', $item->created_at->toFormattedDateString());
                $collect->put('dateSales', $request->created_at->toFormattedDateString());
                $collect->put('sales_order_id', $request->id);
                $collect->put('dr_id', $item->id);
                $collect->put('name', $client->name);
                $collect->put('memo', $request->note);
                $collect->put('class', $client->class);
                $collect->put('desc', $dr_item_desc);
                $collect->put('qty', $item->total_qty);

                // $total_qty += $dr_qty;

                $collect->put('item', $item);
                array_push($data, $collect);
            }

            // for purchase orders
            foreach ($tbl3 as $item3) {

                $collect3 = collect();
                $date_receive = new Carbon($item3->received_at);
                $purchase = $item3->purchase_order_id;
                $stock_qty = $item3->qty_in;
                $note = direct_receive::where('id', $item3->direct_receive_id)->value('note');
                $type = "Item Receive";

                $collect3->put('type', $type);
                $collect3->put('date_receive', $date_receive->toFormattedDateString());
                $collect3->put('purchase', $purchase);
                $collect3->put('stock_qty', $stock_qty);
                $collect3->put('note', $note);


                $collect3->put('item', $item3);
                array_push($data3, $collect3);
            }


            $ret = collect();
            $ret->put("totalQty", $total_qty);
            $ret->put("dateStocks", $dateStocks);
            $ret->put("stocks", $stocks);
            $ret->put("data", $data);
            $ret->put("return", $data2);
            $ret->put("receive", $data3);
            return response()->json($ret);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function showAllInventoryReport(Request $request)
    {
        try {


            $item = $request->itemSelected;

            $tbl = DeliveryReceipt::with(['sales_order', 'delivery_receipt_details'])
                ->join('delivery_receipt_item', 'delivery_receipts.id', 'delivery_receipt_item.delivery_receipt_id')
                ->where('item_id', $item)
                ->selectRaw('*,sum(delivery_receipt_item.qty)as total_qty ')
                ->groupBy('id')
                ->get();


            $tbl2 = SalesReturn::with("sales_return_details");
            $tbl3 = DB::table('stocks')->where("item_id", $item)->get();


            $tbl2 = $tbl2
                ->whereHas("sales_return_details", function ($query) use ($item) {
                    $query->where("id", $item);
                })
                ->where("status", "!=", "For Approval")
                ->get();



            $stocks = DB::table('stocks')
                ->latest('created_at')
                ->where('item_id', $item)
                ->get();


            foreach ($stocks as $stock) {

                $dateStocks = new Carbon($stock->created_at);
                $stocks = $stock->qty_in;
            }


            $data = [];
            $data2 = [];
            $data3 = [];
            $total_qty = 0;
            $type = "";
            $dateStocks = $dateStocks->toFormattedDateString();

            // return $tbl;
            // for DR & S.O
            foreach ($tbl as $item) {
                $total_delivered_qty = 0;

                $collect = collect();
                $client = (object)$item->sales_order->client;
                $request = (object)$item->sales_order;
                $dr_item = (object)$item->delivery_receipt_details;
                foreach ($dr_item as $dr_item) {
                    $dr_item_desc = $dr_item->description;
                }


                $collect->put('type', 'Invoice');
                $collect->put('date', $item->created_at->toFormattedDateString());
                $collect->put('dateSales', $request->created_at->toFormattedDateString());
                $collect->put('sales_order_id', $request->id);
                $collect->put('dr_id', $item->id);
                $collect->put('name', $client->name);
                $collect->put('memo', $request->note);
                $collect->put('class', $client->class);
                $collect->put('desc', $dr_item_desc);
                $collect->put('qty', $item->total_qty);

                // $total_qty += $dr_qty;

                $collect->put('item', $item);
                array_push($data, $collect);
            }
            // for sales  returns
            foreach ($tbl2 as $item2) {

                $collect2 = collect();
                $date_return = new Carbon($item2->date_return);
                $return_id = $item2->id;
                $client = $item2->client;
                $type = "Return";


                $desc = "";
                foreach ($item2->sales_return_details as $sr) {

                    $pivot = $sr->pivot;
                    $sr_qty = $pivot->qty;
                    $desc = $sr->description;
                }

                $collect2->put('type', $type);
                $collect2->put('date', $date_return->toFormattedDateString());
                $collect2->put('return_id', $return_id);
                $collect2->put('name', $client->name);
                $collect2->put('desc', $desc);
                $collect2->put('qty', $sr_qty);

                $total_qty += $sr_qty;

                $collect2->put('item', $item2);
                array_push($data2, $collect2);
            }

            // for purchase order
            foreach ($tbl3 as $item3) {

                $collect3 = collect();
                $date_receive = new Carbon($item3->received_at);
                $purchase = $item3->purchase_order_id;
                $stock_qty = $item3->qty_in;
                $note = direct_receive::where('id', $item3->direct_receive_id)->value('note');
                $type = "Item Receive";

                $collect3->put('type', $type);
                $collect3->put('date_receive', $date_receive->toFormattedDateString());
                $collect3->put('purchase', $purchase);
                $collect3->put('stock_qty', $stock_qty);
                $collect3->put('note', $note);


                $collect3->put(
                    'item',
                    $item3
                );
                array_push($data3, $collect3);
            }


            $ret = collect();
            $ret->put("totalQty", $total_qty);
            $ret->put("dateStocks", $dateStocks);
            $ret->put("stocks", $stocks);
            $ret->put("data", $data);
            $ret->put("return", $data2);
            $ret->put("receive", $data3);

            return response()->json($ret);


            // return response()->json($tbl);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function componentSearch(Request $request)
    {
        $tbl1 = DB::table('warehouses')->where('name', 'like', '%' . $request->warehouse . '%')->get();
        $tbl2 = DB::table('categories')->where('name', 'like', '%' . $request->category . '%')->get();
        $tbl3 = DB::table('company_assets_types')->where('type_name', 'like', '%' . $request->type . '%')->get();
        $tbl4 = DB::table('company_assets')->where('name', 'like', '%' . $request->asset . '%')->get();
        $tbl5 = DB::table('mode_of_payments')->where('mode', 'like', '%' . $request->mode . '%')->get();
        $tbl6 = DB::table('terms')->where('term', 'like', '%' . $request->term . '%')->get();

        $data = collect();
        $data->put("warehouse", $tbl1);
        $data->put("category", $tbl2);
        $data->put("type", $tbl3);
        $data->put("asset", $tbl4);
        $data->put("mode", $tbl5);
        $data->put("term", $tbl6);

        return response()->json($data);
    }
}
