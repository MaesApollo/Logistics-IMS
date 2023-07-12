<?php

namespace App\Http\Controllers;

use App\PurchaseOrder;
use App\department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use stdClass;

class PurchaseOrderController extends Controller
{
    private $cname = "PurchaseOrderController";
    public function index()
    {
        $temp = [];
        $purchase_orders = PurchaseOrder::with(['user', 'supplier', 'item'])
            ->orderBy('id', 'desc')
            ->get();

        foreach ($purchase_orders as $purchase_order) {
            $stocksQty = DB::table('stocks')
                ->where('purchase_order_id', $purchase_order->id)
                ->sum('qty_in');

            $orderedQty = DB::table('item_purchase_order')
                ->where('purchase_order_id', $purchase_order->id)
                ->sum('qty');

            $collection = collect($purchase_order);
            $collection->put('total_qty_ordered', $orderedQty);
            $collection->put('total_qty_received', $stocksQty);
            array_push($temp, $collection->all());
        }

        $purchase_orders = $temp;

        return response()->json($purchase_orders);
    }

    public function store(Request $request)
    {
        $purchase_order = PurchaseOrder::create([
            'user_id' => $request->id,
            'status' => 'draft'
        ]);

        return response()->json($purchase_order);
    }

    public function show($id)
    {
        $purchase_order = PurchaseOrder::with('user')
            ->with('supplier')
            ->with('item')
            ->where('id', $id)
            ->first();

        $temp = [];
        foreach ($purchase_order->item as $item) {
            $item = (object)$item;
            $collection = collect($item);

            $total_qty_received = DB::table('stocks')
                ->where('purchase_order_id', $id)
                ->where('item_id', $item->id)
                ->sum('qty_in');

            $type = DB::table('types')
                ->where('id', $item->type_id)->first();

            $collection->put('total_qty_received', $total_qty_received);
            $collection->put('type', $type);
            array_push($temp, $collection->all());
        }

        $collection = collect($purchase_order);
        $collection->put('item', $temp);
        $purchase_order = $collection->all();

        return response()->json($purchase_order);
    }

    public function update(Request $request, $id)
    {


        $request->supplier = (object)$request->supplier;
        $request->amount = (object)$request->amount;

        try {
            $supplier_id = $request->supplier->id;
        } catch (\Throwable $th) {
            $supplier_id = null;
        }

        $shipping_fee = $request->amount->shipping;
        $total = $request->amount->total;
        $tax = $request->amount->tax;

        DB::table('purchase_orders')
            ->where('id', '=', $id)
            ->update([
                'delivery_date' => $request->delivery_date,
                'shipping_fee' => $shipping_fee,
                'tax' => $tax,
                'total' => $total,
                'supplier_id' => $supplier_id,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        DB::table('item_purchase_order')->where('purchase_order_id', $id)->delete();

        foreach ($request->orders as $order) {
            $order = (object)$order;
            $order->pivot = (object)$order->pivot;

            $collection = collect($order);

            try {
                $collection->put('qty', $order->pivot->qty);
            } catch (\Throwable $th) {
                $collection->put('qty', null);
            }

            try {
                $collection->put('unit', $order->pivot->unit);
            } catch (\Throwable $th) {
                $collection->put('unit', null);
            }


            try {
                $collection->put('price', $order->pivot->price);
            } catch (\Throwable $th) {
                $collection->put('price', null);
            }

            try {
                $collection->put('tax', $order->pivot->tax_rate);
            } catch (\Throwable $th) {
                $collection->put('tax', null);
            }

            $order = $collection->all();
            $order = (object)$order;
            $order->pivot = (object)$order->pivot;

            DB::table('item_purchase_order')->insert(
                [
                    'purchase_order_id' => $id,
                    'item_id' => $order->id,
                    'qty' => $order->qty,
                    'unit' => $order->unit,
                    'price' => $order->price,
                    'tax_rate' => $order->tax,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param App\PurchaseOrder $purchase_order
     * @return \Illuminate\Http\Response
     */
    public function destroy($purchase_order)
    {
        try {
            PurchaseOrder::destroy($purchase_order);
            return $this->index();
        } catch (\Exception $e) {
            return response(['Problem deleting data', 500]);
        }
    }

    // public function submitApproval($id)
    // {
    //     DB::table('purchase_orders')
    //         ->where('id', $id)
    //         ->update([
    //             'status' => "approval",
    //             'updated_at' => \Carbon\Carbon::now()
    //         ]);
    // }
    public function submitApproval(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->supplier = (object)$request->supplier;
            $request->amount = (object)$request->amount;
            $request->user = (object)$request->user;

            $shipping_fee = $request->amount->shipping;
            $total = $request->amount->total;
            $tax = $request->amount->tax;

            $id = DB::table('purchase_orders')
                ->insertGetId([
                    'user_id' => $request->user->id,
                    'status' => 'approval',
                    'delivery_date' => $request->delivery_date,
                    'shipping_fee' => $shipping_fee,
                    'tax' => $tax,
                    'total' => $total,
                    'supplier_id' => $request->supplier->id,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            foreach ($request->orders as $order) {
                $order = (object)$order;
                $order->pivot = (object)$order->pivot;

                $collection = collect($order);

                try {
                    $collection->put('qty', $order->pivot->qty);
                } catch (\Throwable $th) {
                    $collection->put('qty', null);
                }

                try {
                    $collection->put('unit', $order->pivot->unit);
                } catch (\Throwable $th) {
                    $collection->put('unit', null);
                }


                try {
                    $collection->put('price', $order->pivot->price);
                } catch (\Throwable $th) {
                    $collection->put('price', null);
                }

                try {
                    $collection->put('tax', $order->pivot->tax_rate);
                } catch (\Throwable $th) {
                    $collection->put('tax', null);
                }

                $order = $collection->all();
                $order = (object)$order;
                $order->pivot = (object)$order->pivot;

                DB::table('item_purchase_order')->insert(
                    [
                        'purchase_order_id' => $id,
                        'item_id' => $order->id,
                        'qty' => $order->qty,
                        'unit' => $order->unit,
                        'price' => $order->price,
                        'tax_rate' => $order->tax,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now()
                    ]
                );
            }

            \Logger::instance()->log(
                Carbon::now(),
                $request->user->id,
                $request->user->name,
                $this->cname,
                "store",
                "message",
                "Create new Purchase Order: #" . $id . ", For Supplier: " . $request->supplier->name
            );

            DB::commit();
            return $id;
        } catch (Exception $ex) {
            DB::rollback();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user->id,
                $request->user->name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
        }
    }

    public function emailRequest(Request $request)
    {
        try {
            // return $request;
            $request->user = (object) $request->user;
            $tbl = department::with('user')->where("id", $request->user->department_id)->get();

            $sendTo = [];
            foreach ($tbl as $user) {
                $user = (object)$user->user;
                $mail = new stdClass;
                $mail->email = $user->email;
                $mail->name = $user->name;

                array_push($sendTo, $mail);
            }

            // $sendTo = [];
            // $temp = new stdClass;
            // $temp->email = "mdmorta@dctechmicro.com";
            // $temp->name = "Mice Gwapa";
            // array_push($sendTo, $temp);

            $CCTO = [];
            $temp1 = new stdClass;
            $temp1->email = "mdmorta@dctechmicro.com";
            $temp1->name = "Mice Gwapa";
            array_push($CCTO, $temp1);



            // email function
            $msg = "
              <html>
                    <head>
                    </head>
                    <body style=' font-family:  Helvetica, Arial, sans-serif;font-size:12px'>
                    <p>Dear Ma'am/Sir " . $mail->name . ",</p>
                    <p>Good day! A new <b>Purchase Order</b> is waiting for your approval.</p>
                    <br/>
                    <br/>
                    " . $request->email_body . "
                    </body>
                    <style>
                       .invoice-box {
                        max-width: 800px;
                        margin: auto;
                        padding: 30px;
                        border: 1px solid #eee;
                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                        font-size: 8px;
                        line-height: 22px;
                        font-family:  Helvetica, Arial, sans-serif;
                        color: #555;
                        }

                        .invoice-box table {
                        width: 100%;
                        line-height: inherit;
                        text-align: left;
                        }

                        .invoice-box table td {
                        padding: 5px;
                        vertical-align: top;
                        }

                        .invoice-box table tr td:nth-child(n + 2) {
                        text-align: right;
                        }

                        .invoice-box table tr.top table td {
                        padding-bottom: 20px;
                        }

                        .invoice-box table tr.top table td.title {
                        font-size: 10px;
                        line-height: 45px;
                        color: #333;
                        }

                        .invoice-box table tr.information table td {

                        padding-bottom: 40px;
                        }
                        .invoice-box table tr.notes table td {

                        padding-bottom: 30px;
                        padding-top: 30px;
                        }

                        .invoice-box table tr.heading td {
                        background: #eee;
                        border-bottom: 1px solid #ddd;

                        }

                        .invoice-box table tr.details td {
                        padding-bottom: 20px;
                        }

                        .invoice-box table tr.item td {
                        border-bottom: 1px solid #eee;
                        }

                        .invoice-box table tr.item.last td {
                        border-bottom: none;
                        }

                        .invoice-box table tr.item input {
                        padding-left: 5px;
                        }

                        .invoice-box table tr.item td:first-child input {
                        margin-left: -5px;
                        width: 100%;
                        }

                        .invoice-box table tr.total td:nth-child(2) {
                        border-top: 2px solid #eee;
                        font-weight: bold;
                        }




                        @media only screen and (max-width: 600px) {
                        .invoice-box table tr.top table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }

                        .invoice-box table tr.information table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                        .invoice-box table tr.notes table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                             padding-top: 30px;
                        }
                        }



                        }

                    </style>
                    </html>";

            \Logger::instance()->mailerZimbra(
                "FOR APPROVAL: PURCHASE ORDER #" . $request->id,
                $msg,
                "",
                "LOGISTICS IMS",
                $sendTo,
                $CCTO
            );

            \Logger::instance()->log(
                Carbon::now(),
                $request->user->id,
                $request->user->name,
                $this->cname,
                "store",
                "message",
                "Sent Purchase Order: #" . $request->id . " to Email for Approval"
            );

            return "sent!";
        } catch (Exception $ex) {
            DB::rollback();
            \Logger::instance()->log(
                Carbon::now(),
                $request->user_id,
                $request->user_name,
                $this->cname,
                "store",
                "Error",
                $ex->getMessage()
            );
        }
    }

    public function submitSupplier($id)
    {
        DB::table('purchase_orders')
            ->where('id', $id)
            ->update([
                'status' => "on order",
                'updated_at' => \Carbon\Carbon::now()
            ]);
    }

    public function approve($id)
    {
        DB::table('purchase_orders')
            ->where('id', $id)
            ->update([
                'status' => "approved",
                'updated_at' => \Carbon\Carbon::now()
            ]);
    }
    public function publicApprove($id)
    {

        $purchase_order = DB::table('purchase_orders')->select('status')->where('id', $id)->value('status');
        if ($purchase_order == "approval") {
            DB::table('purchase_orders')
                ->where('id', $id)
                ->update([
                    'status' => "approved",
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            return "
            <center>
                <div>
                <p>
                This Purchase Order # " . $id . " has been accepted!
                </p>
                </div>
            </center>
            <style>
                div {
                    margin: 20px;
                    margin-top:50px;
                    width: 300px;
                    border: 10px solid green;
                    padding: 50px;
                    font-size:14px;
                    font-family:  Helvetica, Arial, sans-serif;
                }
            </style>
        ";
        } else {
            return "
            <center>
                <div>
                <p>
                This Purchase Order." . $id . " status has already been updated!
                </p>
                </div>
            </center>
            <style>
                div {
                margin: 20px;
                margin-top:50px;
                width: 300px;
                border: 10px solid red;
                padding: 50px;
                font-size:14px;
                font-family:  Helvetica, Arial, sans-serif;
                }
            </style>
            ";
        }
    }

    public function decline($id)
    {
        DB::table('purchase_orders')
            ->where('id', $id)
            ->update([
                'status' => "declined",
                'updated_at' => \Carbon\Carbon::now()
            ]);
    }
    public function publicDecline($id)
    {
        $purchase_order = DB::table('purchase_orders')->select('status')->where('id', $id)->value('status');
        if ($purchase_order == "approval") {
            DB::table('purchase_orders')
                ->where('id', $id)
                ->update([
                    'status' => "declined",
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            return "
            <center>
                <div>
                <p>
                This Purchase Order # " . $id . " has been declined!
                </p>
                </div>
            </center>
            <style>
                div {
                    margin: 20px;
                    margin-top:50px;
                    width: 300px;
                    border: 10px solid green;
                    padding: 50px;
                    font-size:14px;
                    font-family:  Helvetica, Arial, sans-serif;
                }
            </style>
        ";
        } else {
            return "
            <center>
                <div>
                <p>
                This Purchase Order." . $id . " status has already been updated!
                </p>
                </div>
            </center>
            <style>
                div {
                margin: 20px;
                margin-top:50px;
                width: 300px;
                border: 10px solid red;
                padding: 50px;
                font-size:14px;
                font-family:  Helvetica, Arial, sans-serif;
                }
            </style>
            ";
        }
    }
    public function search(Request $request)
    {

        $container = [];
        $filter = $request->filter;

        if ($request->date_to == null)
            $request->date_to = $request->date_from;

        $from = new Carbon($request->date_from);
        $to = new Carbon($request->date_to);
        $to = $to->addDay();

        $tbl = PurchaseOrder::with('supplier');
        if ($filter == "number") {
            $tbl = $tbl->where('id', $request->number)->get();
        } else if ($filter == "supplier") {
            $tbl = $tbl->where('supplier_id', $request->supplierSelected)->orderBy('id', 'desc')->get();
        } else if ($filter == "date") {
            $tbl = $tbl->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])->get();
        } else if ($filter == "status") {
            $tbl = $tbl->where("status", $request->statusSelected)->orderBy('id', 'desc')->get();
        }

        foreach ($tbl as $items) {
            $collection = collect($items);
            array_push($container, $collection->all());
        }

        $tbl = $container;
        return response()->json($tbl);
    }

    public function countPending()
    {
        $pending = PurchaseOrder::where('status', '=', 'approval')->get();
        return count($pending);
    }
}
