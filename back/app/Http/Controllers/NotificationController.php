<?php

namespace App\Http\Controllers;

use App\Item;
use App\Type;
use App\Category;
use App\Warehouse;
use App\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function notification()
    {
        $sales_orders = SalesOrder::with(['client', 'user'])
            ->where('status', 'approved')
            ->orWhere('status', 'approval')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($sales_orders);
    }

    public function alert()
    {
        $items = DB::table('items')->get();
        $returnVal = array();
        $runningLow = array();
        $outOfStock = array();

        foreach ($items as $item) {
            $del = DB::table('delivery_receipt_item')
                ->join('delivery_receipts', 'delivery_receipts.id', '=', 'delivery_receipt_item.delivery_receipt_id')
                ->where('delivery_receipt_item.item_id', $item->id)
                ->where('delivery_receipts.status', 'delivering')
                ->where('created_at', '>', Carbon::now()->subDays(90)->toDateTimeString())
                ->where('created_at', '<', Carbon::now()->toDateTimeString())
                ->orWhere('delivery_receipts.status', 'delivered')
                ->where('delivery_receipt_item.item_id', $item->id)
                ->where('created_at', '>', Carbon::now()->subDays(90)->toDateTimeString())
                ->where('created_at', '<', Carbon::now()->toDateTimeString())
                ->sum('delivery_receipt_item.qty');

            $locale = DB::table('stocks')
                ->join('purchase_orders', 'purchase_orders.id', '=', 'stocks.purchase_order_id')
                ->join('suppliers', 'suppliers.id', '=', 'supplier_id')
                ->join('locales', 'locales.id', '=', 'suppliers.locale_id')
                ->where('stocks.item_id', $item->id)
                ->orderBy('received_at', 'desc')
                ->first();

            $totalQtyOfItem = DB::table('stocks')
                ->where('item_id', $item->id)
                ->sum(DB::raw('qty_in - qty_out'));

            $localeName = "";

            try {
                $localeName = $locale->name;
            } catch (\Throwable $th) {
                $localeName = 'local';
            }

            $chk = 1;

            if ($localeName == 'US') {
                $chk = $totalQtyOfItem - ($del * 3);
            } elseif ($localeName == 'China') {
                $chk = $totalQtyOfItem - ($del * 3);
            } elseif ($localeName == 'Manila') {
                $chk = $totalQtyOfItem - $del;
            } else {
                $chk = $totalQtyOfItem - ($del / 4);
            }

            $status = '';

            if ($chk > 0) {
                $status = 'ok';
            } else {
                $status = 'no';

                $temp = (object)[
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'locale' => $localeName,
                    'totalItem' => $totalQtyOfItem,
                    'totalDeliver' => $del,
                    'totalQtyLeft' => $chk,
                    'status' => $status,
                ];

                if ($totalQtyOfItem > 0)
                    array_push($runningLow, $temp);
                else if ($totalQtyOfItem < 1)
                    array_push($outOfStock, $temp);

                array_push($returnVal, $temp);
            }
        }

        // return response()->json($returnVal);
        return json_encode([
            'alerts' => $returnVal,
            'runningLow' => $runningLow,
            'outOfStock' => $outOfStock
        ]);
    }

    public function forecast($id)
    {
        $item = DB::table('items')->where('id', $id)->first();

        $del = DB::table('delivery_receipt_item')
            ->join('delivery_receipts', 'delivery_receipts.id', '=', 'delivery_receipt_item.delivery_receipt_id')
            ->where('delivery_receipt_item.item_id', $id)
            ->where('delivery_receipts.status', 'delivering')
            ->where('created_at', '>', Carbon::now()->subDays(90)->toDateTimeString())
            ->where('created_at', '<', Carbon::now()->toDateTimeString())
            ->orWhere('delivery_receipts.status', 'delivered')
            ->where('delivery_receipt_item.item_id', $id)
            ->where('created_at', '>', Carbon::now()->subDays(90)->toDateTimeString())
            ->where('created_at', '<', Carbon::now()->toDateTimeString())
            ->sum('delivery_receipt_item.qty');

        $locale = DB::table('stocks')
            ->join('purchase_orders', 'purchase_orders.id', '=', 'stocks.purchase_order_id')
            ->join('suppliers', 'suppliers.id', '=', 'supplier_id')
            ->join('locales', 'locales.id', '=', 'suppliers.locale_id')
            ->where('stocks.item_id', $id)
            ->orderBy('received_at', 'desc')
            ->first();

        $totalQtyOfItem = DB::table('stocks')
            ->where('item_id', $id)
            ->sum(DB::raw('qty_in - qty_out'));

        $localeName = "";

        try {
            $localeName = $locale->name;
        } catch (\Throwable $th) {
            $localeName = 'local';
        }

        $chk = 1;

        if ($localeName == 'US') {
            $chk = $totalQtyOfItem - ($del * 3);
        } elseif ($localeName == 'China') {
            $chk = $totalQtyOfItem - ($del * 3);
        } elseif ($localeName == 'Manila') {
            $chk = $totalQtyOfItem - $del;
        } else {
            $chk = $totalQtyOfItem - ($del / 4);
        }

        $status = '';

        if ($chk > 0) {
            $status = 'ok';
        } else {
            $status = 'no';
        }

        $temp = (object)[
            'item_id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'locale' => $localeName,
            'totalItem' => $totalQtyOfItem,
            'totalDeliver' => $del,
            'totalQtyLeft' => $chk,
            'status' => $status,
        ];

        return response()->json($temp);
    }
}
