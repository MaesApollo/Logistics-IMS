<?php

namespace App\Http\Controllers;

use Auth;
use App\Item;
use App\User;
use App\Type;
use App\SalesOrder;
use App\DeliveryReceipt;
use App\department;
use App\Events\NotifyCreatedSalesOrder;
use App\Events\NotifyUpdatedSalesOrder;
use App\Events\NotifyUpdatedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use stdClass;

class SalesOrderController extends Controller
{
    private $cname = "SalesOrderController";
    public function index()
    {
        $temp_sales_orders = [];

        $sales_orders = SalesOrder::with(['client', 'user'])
            ->where('status', "!=", "order complete")
            ->where('status', "!=", "declined")
            ->orderBy('id', 'desc')
            ->get();
        // $sales_orders = DB::table('sales_orders')->get();

        foreach ($sales_orders as $sales_order) {
            $total_delivered_qty = 0;
            $total_sales_ordered_qty = 0;
            $percent = 0;

            $delivery_receipts = DB::table('delivery_receipts')
                ->where('sales_order_id', $sales_order->id)->get();

            $total_sales_ordered_qty = DB::table('item_sales_order')
                ->where('sales_order_id', $sales_order->id)
                ->sum('qty');

            foreach ($delivery_receipts as $delivery_receipt) {
                if ($delivery_receipt->status != 'for delivery') {
                    $delivered_qty = DB::table('delivery_receipt_item')
                        ->where('delivery_receipt_id', $delivery_receipt->id)
                        ->sum('qty');

                    $total_delivered_qty += $delivered_qty;
                }
            }

            if ($total_delivered_qty > 0 && $total_sales_ordered_qty > 0) {
                $percent = round(($total_delivered_qty / $total_sales_ordered_qty) * 100);
            }

            $collection = collect($sales_order);
            $collection->put('percent', $percent);
            $collection->put('total_delivered_qty', $total_delivered_qty);
            $collection->put('total_sales_ordered_qty', $total_sales_ordered_qty);
            array_push($temp_sales_orders, $collection->all());
        }

        $sales_orders = $temp_sales_orders;
        return response()->json($sales_orders);
    }

    public function store(Request $request)
    {
        $sales_order = SalesOrder::create([
            'user_id' => $request->id
        ]);

        return response()->json($sales_order);
    }

    public function show($id)
    {
        $orders = [];

        $sales_order = SalesOrder::with(['client', 'user'])->find($id);

        $sales_order_details = DB::table('item_sales_order')
            ->where('sales_order_id', $id)->get();

        $delivery_receipts = DB::table('delivery_receipts')
            ->where('sales_order_id', $id)->get();

        if (!empty($sales_order_details)) {
            foreach ($sales_order_details as $order) {
                $total_delivered_qty = 0;
                $item = (object) app(ItemsController::class)->show($order->item_id);
                $item = (object) $item->original;

                if (!empty($delivery_receipts)) {
                    foreach ($delivery_receipts as $delivery_receipt) {
                        if ($delivery_receipt->status != 'for delivery') {
                            $total_delivered_qty += DB::table('delivery_receipt_item')
                                ->where('delivery_receipt_id', $delivery_receipt->id)
                                ->where('item_id', $order->item_id)
                                ->sum('qty');
                        }
                    }
                }

                $collection = collect($item);
                $collection->put('delivered_qty', $total_delivered_qty);
                $collection->put('price', $order->price);
                $collection->put('ordered_qty', $order->qty);
                array_push($orders, $collection->all());
            }
        }

        $collection = collect($sales_order);
        $collection->put('sales_order_details', $orders);
        $sales_order = $collection->all();

        return response()->json($sales_order);
    }

    public function showforReceipt($id)
    {
        $orders = [];

        $sales_order = SalesOrder::with(['client', 'user'])
            ->find($id);

        $sales_order_details = DB::table('item_sales_order')
            ->where('sales_order_id', $id)
            ->get();

        $delivery_receipts = DB::table('delivery_receipts')
            ->where('sales_order_id', $id)->get();

        if (!empty($sales_order_details)) {
            foreach ($sales_order_details as $order) {
                $total_delivered_qty = 0;
                $item = (object) app(ItemsController::class)->show($order->item_id);
                $item = (object) $item->original;

                if (!empty($delivery_receipts)) {
                    foreach ($delivery_receipts as $delivery_receipt) {
                        if ($delivery_receipt->status != 'for delivery') {
                            $total_delivered_qty += DB::table('delivery_receipt_item')
                                ->where('delivery_receipt_id', $delivery_receipt->id)
                                ->where('item_id', $order->item_id)
                                ->sum('qty');
                        }
                    }
                }

                $collection = collect($item);
                $collection->put('price', $order->price);
                $collection->put('ordered_qty', $order->qty);
                $collection->put('delivered_qty', $total_delivered_qty);
                $collection->put('delivering_qty', 0);
                $collection->put('ordered_serial', []);
                array_push($orders, $collection->all());
            }
        }

        $collection = collect($sales_order);
        $collection->put('sales_order_details', $orders);
        $sales_order = $collection->all();

        return  response()->json($sales_order);
    }

    public function update(Request $request, $id)
    {
        try {
            $dr = DeliveryReceipt::where('sales_order_id', $id)->first();


            $request = (object) $request;
            $request->client = (object) $request->client;
            $request->user = (object) $request->user;
            $request->amount = (object) $request->amount;

            try {
                $client_id = $request->client->id;
            } catch (\Throwable $th) {
                $client_id = null;
            }

            if (empty($dr)) {
                DB::table('sales_orders')
                    ->where('id', $id)
                    ->update([
                        'client_id' => $client_id,
                        'user_id' => $request->user->id,
                        'note' => $request->note,
                        'requestor' => $request->requestor,
                        'updated_at' => Carbon::now()
                    ]);

                DB::table('item_sales_order')->where('sales_order_id', $id)->delete();

                foreach ($request->orders as $order) {
                    $order = (object) $order;
                    //$order->type = (object)$order->type;

                    DB::table('item_sales_order')->insert(
                        [
                            'sales_order_id' => $id,
                            'item_id' => $order->id,
                            'qty' => $order->ordered_qty,
                            'price' => $order->price,
                        ]
                    );
                }
            } else {
                return response()->json(['error' => 'Can\'t update this Material Request. Delivery Receipt already exist.'], 500);
            }
        } catch (\Exception $ex) {

            return response()->json(['error' => $ex->getMessage()], 500);
        }



        //event(new NotifyUpdatedSalesOrder($sales_order));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param App\SalesOrder $sales_order
     * @return \Illuminate\Http\Response
     */
    public function destroy($sales_order)
    {
        try {
            SalesOrder::destroy($sales_order);
            return "ok";
        } catch (\Exception $e) {
            return response(['Problem deleting data', 500]);
        }
    }


    public function showClientOrdered($id)
    {
        $sales_order = SalesOrder::with('sales_order_details')
            ->where('client_id', $id)->get();

        $items = [];

        foreach ($sales_order as $order) {
            $delivery_receipt = DeliveryReceipt::with('sales_order')
                ->where('sales_order_id', $order->id)->first();

            if (!empty($delivery_receipt)) {
                foreach ($order->sales_order_details as $item) {
                    $delivery_receipt = (object) $delivery_receipt;

                    $collection = collect($item);
                    $collection->put('delivery_receipt', $delivery_receipt);

                    array_push($items, $collection->all());
                }
            }
        }

        return response()->json($items);
    }

    public function countPending()
    {
        $pending = SalesOrder::where('status', '=', 'approval')->get();
        return count($pending);
    }

    public function countVerified()
    {
        $verified = SalesOrder::where('status', 'verified')->orWhere('status', 'approved')->get();
        return count($verified);
    }

    public function countDeclined()
    {
        $canceled = SalesOrder::where('status', 'declined')->get();
        return count($canceled);
    }

    public function emailRequest(Request $request)
    {
        try {
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
            $temp1->name = "Developers Team";
            array_push($CCTO, $temp1);

            // email function
            $msg = "
              <html>
                    <head>
                    </head>
                    <body style=' font-family:  Helvetica, Arial, sans-serif;font-size:12px'>
                    <p>Dear Ma'am/Sir,</p>
                    <p>Good day! A new <b>Material Request</b> is waiting for your approval.</p>
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
                "FOR APPROVAL: MATERIAL REQUEST #" . $request->id,
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
                "Sent Material Request: #" . $request->id . " to Email for Approval"
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
    // new adding of material request
    public function submitApproval(Request $request)
    {

        try {
            DB::beginTransaction();
            $request = (object) $request;
            $request->client = (object) $request->client;
            $request->user = (object) $request->user;
            $request->amount = (object) $request->amount;
            $client_id = $request->client->id;

            $id = DB::table('sales_orders')
                ->insertGetId([
                    'client_id' => $client_id,
                    'user_id' => $request->user->id,
                    'note' => $request->note,
                    'requestor' => $request->requestor,
                    'remarks' => $request->remarks,
                    'status' => "approval",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

            foreach ($request->orders as $order) {
                $order = (object) $order;

                DB::table('item_sales_order')->insert(
                    [
                        'sales_order_id' => $id,
                        'item_id' => $order->id,
                        'qty' => $order->ordered_qty,
                        'price' => $order->price,
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
                "Create new Material Request: #" . $id . ", For Client: " . $request->client->name
            );

            DB::commit();
            // $this->emailRequest($request, $id);
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

        // DB::table('sales_orders')
        //     ->where('id', $id)
        //     ->update([
        //         'status' => "approval",
        //         'updated_at' => \Carbon\Carbon::now()
        //     ]);

        // $sales_order = SalesOrder::with(['client', 'user'])->find($id);
        // broadcast(new NotifyCreatedSalesOrder($sales_order));

        // event(new NotifyCreatedSalesOrder($sales_order));


    }

    public function approve(Request $request)
    {
        $request->user = (object) $request->user;
        DB::table('sales_orders')
            ->where('id', $request->id)
            ->update([
                'status' => "approved",
                'updated_at' => \Carbon\Carbon::now()
            ]);


        \Logger::instance()->log(
            Carbon::now(),
            $request->user->id,
            $request->user->name,
            $this->cname,
            "store",
            "message",
            "Approve Material Request #: " . $request->id
        );

        $sales_order = SalesOrder::with(['client', 'user'])->find($request->id);
        // broadcast(new NotifyUpdatedSalesOrder($sales_order));
    }
    public function publicApprove($id)
    {
        $sales_order = DB::table('sales_orders')->select('status')->where('id', $id)->value('status');
        if ($sales_order == "approval") {
            DB::table('sales_orders')
                ->where('id', $id)
                ->update([
                    'status' => "approved",
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            return "
            <center>
                <div>
                <p>
                This Material Request # " . $id . " has been accepted!
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
                This Material Request No." . $id . " status has already been updated!
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

    public function publicDecline($id)
    {
        $sales_order = DB::table('sales_orders')->select('status')->where('id', $id)->value('status');
        if ($sales_order == "approval") {
            DB::table('sales_orders')
                ->where('id', $id)
                ->update([
                    'status' => "declined",
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            return "
             <center>
                <div>
                <p>
                This Material Request # " . $id . " has been declined!
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
                This Material Request No." . $id . " status has been updated already!
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
        DB::table('sales_orders')
            ->where('id', $id)
            ->update([
                'status' => "declined",
                'updated_at' => \Carbon\Carbon::now()
            ]);

        $sales_order = SalesOrder::with(['client', 'user'])->find($id);
        // broadcast(new NotifyUpdatedSalesOrder($sales_order));
    }
    public function cancel($id)
    {
        DB::table('sales_orders')
            ->where('id', $id)
            ->update([
                'status' => "approval",
                'updated_at' => \Carbon\Carbon::now()
            ]);

        $sales_order = SalesOrder::with(['client', 'user'])->find($id);
        // broadcast(new NotifyUpdatedSalesOrder($sales_order));
    }

    public function item_exist($item_id, $orders, $serial)
    {
        $bool = false;
        $index = 0;

        foreach ($orders as $item) {
            $item = (object) $item;

            if ($item->item_id == $item_id) {
                $bool = true;

                break;
            }

            $index++;
        }

        return array($bool, $index);
    }

    public function searchItem(Request $request)
    {
        $temp = Item::with(['type', 'category', 'stocks'])

            ->where('name', 'like', '%' . $request->item . '%')
            ->orWhere('description', 'like', '%' . $request->item . '%')
            ->get();
        return response()->json($temp);
    }
    public function showSearch(Request $request)
    {
        // return $request;
        $temp_sales_orders = [];
        $filter = $request->filter;

        if ($request->date_to == null)
            $request->date_to = $request->date_from;


        $from = new Carbon($request->date_from);
        $to = new Carbon($request->date_to);
        $to = $to->addDay();

        $sales_orders = SalesOrder::with(['sales_order_details', 'client', 'user']);

        if ($request->sort == "1") {
            $sales_orders = SalesOrder::with(['sales_order_details', 'client', 'user'])
                ->orderBy('id', 'desc');
        } else if ($request->sort == "2") {
            $sales_orders = SalesOrder::with(['sales_order_details', 'client', 'user'])
                ->orderBy('id', 'asc');
        }

        if ($filter == "number") {
            if ($request->number != null) {
                $sales_orders = $sales_orders->where('id', $request->number)->get();
            } else $sales_orders = $sales_orders->get();
        } else if ($filter == "client") {
            if ($request->text != null) {
                $clientSelected = $request->text;
                // $sales_orders = $sales_orders->where('client_id', $request->clientSelected)->get();
                $sales_orders = $sales_orders
                    ->whereHas('client', function ($query) use ($clientSelected) {
                        $query->where('name', 'like', '%' . $clientSelected . '%');
                    })
                    ->get();
                // return "ok";
            } else $sales_orders = $sales_orders->get();
        } else if ($filter == "date") {
            if ($from != null && $to != null) {
                $sales_orders = $sales_orders->whereBetween("created_at", [$from->toDateString(), $to->toDateString()])->get();
            } else $sales_orders = $sales_orders->get();
        } else if ($filter == "status") {
            if ($request->statusSelected != null) {
                $sales_orders = $sales_orders->where("status", $request->statusSelected)->get();
            } else $sales_orders = $sales_orders->get();
        }

        foreach ($sales_orders as $sales_order) {
            $total_delivered_qty = 0;
            $total_sales_ordered_qty = 0;
            $percent = 0;

            $delivery_receipts = DB::table('delivery_receipts')
                ->where('sales_order_id', $sales_order->id)->get();

            $total_sales_ordered_qty = DB::table('item_sales_order')
                ->where('sales_order_id', $sales_order->id)
                ->sum('qty');

            foreach ($delivery_receipts as $delivery_receipt) {
                if ($delivery_receipt->status != 'for delivery') {
                    $delivered_qty = DB::table('delivery_receipt_item')
                        ->where('delivery_receipt_id', $delivery_receipt->id)
                        ->sum('qty');

                    $total_delivered_qty += $delivered_qty;
                }
            }

            if ($total_delivered_qty > 0 && $total_sales_ordered_qty > 0) {
                $percent = round(($total_delivered_qty / $total_sales_ordered_qty) * 100);
            }

            $collection = collect($sales_order);
            $collection->put('percent', $percent);
            $collection->put('total_delivered_qty', $total_delivered_qty);
            $collection->put('total_sales_ordered_qty', $total_sales_ordered_qty);
            array_push($temp_sales_orders, $collection->all());
        }

        $sales_orders = $temp_sales_orders;
        return response()->json($sales_orders);
    }
}
