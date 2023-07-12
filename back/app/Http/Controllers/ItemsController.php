<?php

namespace App\Http\Controllers;

use Auth;
use App\Item;
use App\Type;
use App\Category;
use App\Warehouse;
use App\Stock;
use App\item_group;
use App\Events\NotifyUpdatedSalesOrder;
use App\Events\NotifyUpdatedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ItemsController extends Controller
{
    public function index()
    {
        $items = Item::with(['type', 'category', 'stocks'])
            ->orderBy('name', 'asc')
            ->get();

        $temp = [];

        foreach ($items as $item) {
            $total_qty_in = 0;
            $total_qty_out = 0;

            $total_qty_in = DB::table('stocks')
                ->where('item_id', $item->id)
                ->sum('qty_in');

            $total_qty_out = DB::table('stocks')
                ->where('item_id', $item->id)
                ->sum('qty_out');


            $forecast = (object) app(NotificationController::class)->forecast($item->id);
            $forecast = (object) $forecast->original;

            $collection = collect($item);
            $collection->put('total_qty', $total_qty_in - $total_qty_out);
            $collection->put('unit_cost', 0);
            $collection->put('ordered_qty', 0);
            $collection->put('forecast', $forecast);
            array_push($temp, $collection->all());
        }

        $items = $temp;

        return response()->json($items);
    }

    public function limited()
    {
        $items = Item::with(['type', 'category', 'stocks'])
            ->orderBy('name', 'asc')
            ->take(20)
            ->get();

        $temp = [];

        foreach ($items as $item) {
            $total_qty_in = 0;
            $total_qty_out = 0;

            $total_qty_in = DB::table('stocks')
                ->where('item_id', $item->id)
                ->sum('qty_in');

            $total_qty_out = DB::table('stocks')
                ->where('item_id', $item->id)
                ->sum('qty_out');

            $unit_cost = Db::table('items')->where('id', $item->id)->value('price');

            $forecast = (object) app(NotificationController::class)->forecast($item->id);
            $forecast = (object) $forecast->original;

            $collection = collect($item);
            $collection->put('total_qty', $total_qty_in - $total_qty_out);
            $collection->put('unit_cost', $unit_cost);
            $collection->put('ordered_qty', 0);
            $collection->put('forecast', $forecast);
            array_push($temp, $collection->all());
        }

        $items = $temp;

        return response()->json($items);
    }
    public function addGroup(Request $request)
    {
        $items = (object)$request->orders;
        $id = DB::table('item_groups')->insertGetId(
            [
                'group_name' => $request->group_name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
        foreach ($items as $item) {
            $item = (object)$item;

            DB::table('item_item_group')->insert(
                [
                    'item_group_id' => $id,
                    'item_id' => $item->id,
                    'qty' => $item->qty

                ]
            );
        }
    }


    public function showItemGroup()
    {
        $groups = item_group::with('items')->get();

        return response()->json($groups);
    }

    public function showItems()
    {
        $items = Item::with(['type', 'category', 'stocks'])
            ->orderBy('name', 'asc')->get();
        return response()->json($items);
    }





    public function store(Request $request)
    {
        try {
            $fileName = "";
            if ($request->image != "") {

                $exploded = explode(',', $request->image);

                $decoded = base64_decode($exploded[1]);

                if (str_contains($exploded[0], "jpeg"))
                    $extension = "jpeg";
                elseif (str_contains($exploded[0], "jpg"))
                    $extension = "jpg";
                elseif (str_contains($exploded[0], "png"))
                    $extension = "png";
                elseif (str_contains($exploded[0], "gif"))
                    $extension = "gif";
                elseif (str_contains($exploded[0], "tiff"))
                    $extension = "tiff";
                elseif (str_contains($exploded[0], "bmp"))
                    $extension = "bmp";
                else
                    $extension = "txt";

                $fileName = str_random() . rand(100000, 999999) . "." . $extension;

                $path = public_path() . "/attachments/" . $fileName;

                file_put_contents($path, $decoded);
            }


            $item = Item::where('description', '=', ucwords($request->name))->first();

            if (empty($item)) {
                Item::create([
                    'name' => ucwords($request->name),
                    'description' => ucfirst($request->description),
                    'type_id' => $request->type_id,
                    'category_id' => $request->category_id,
                    'supplier_id' => $request->supplier_id,
                    'image' => $fileName
                ]);

                //event(new NotifyUpdatedItem);
                // return $this->index();
            } else {

                return response()->json(['error' => 'Product/Item already exists!'], 500);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $item = Item::with(['type', 'category', 'stocks'])
            ->find($id);
        $define = array('qty' => 0, 'price' => 0, 'tax_rate' => 0);

        $temp = [];
        $serial = [];
        $total_qty_in = 0;
        $total_qty_out = 0;


        try {
            foreach ($item->stocks as $stock) {

                $warehouse = DB::table('warehouses')
                    ->where('id', $stock->warehouse_id)
                    ->first();

                $temp_serial = DB::table('stock_serial')
                    ->where('stock_id', $stock->id)->get();

                foreach ($temp_serial as $value) {
                    array_push($serial, $value);
                }

                $collection = collect($stock);
                $collection->put('location', $warehouse->name);
                array_push($temp, $collection->all());
            }
        } catch (\Throwable $th) {
        }

        $total_qty_in = DB::table('stocks')
            ->where('item_id', $id)
            ->sum('qty_in');

        $total_qty_out = DB::table('stocks')
            ->where('item_id', $id)
            ->sum('qty_out');

        $collection = collect($item);
        $collection->put('stocks', $temp);
        $collection->put('pivot', $define);
        $collection->put('serials', $serial);
        // $collection->put('serials', (object) $serial); and serials kay gi himo ug object kung naa error e balik ni
        $collection->put('total_qty', $total_qty_in - $total_qty_out);
        $item = $collection->all();

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $request->category = (object) $request->category;
        $request->type = (object) $request->type;

        DB::table('items')->where('id', $id)
            ->update([
                'type_id' => $request->type->id,
                'category_id' => $request->category->id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        //event(new NotifyUpdatedItem);
    }

    public function destroy($id)
    {
        try {
            Item::destroy($id);

            //event(new NotifyUpdatedItem);
            // return $this->index();
            return "deleted!";
        } catch (\Exception $e) {
            return response()->json(['error' => 'There was a problem deleting this item.'], 500);
        }
    }

    public function updateQty(Request $request, $id)
    {
        $item = Item::with('warehouses')->find($id);
        $warehouses = Warehouse::all();

        $x = 0;

        foreach ($warehouses as $warehouse) {
            $exists = false;

            foreach ($item->warehouses as $item_warehouse) {
                if ($item_warehouse->id == $warehouse->id) {
                    $request[$x] = $request[$x] + $item_warehouse->pivot->qty;
                    $exists = true;
                }
            }

            if ($exists) {
                $item->warehouses()->syncWithoutDetaching([$warehouse->id => ['qty' => $request[$x]]]);
            } else {
                $item->warehouses()->attach($warehouse->id, ['qty' => $request[$x]]);
            }
            $x++;
        }

        //event(new NotifyUpdatedItem);
        return $this->index();
    }

    public function createSerials(Request $request, $id)
    {
        $item = Item::with('warehouses')->find($id);

        if (empty($request->warehouse_id)) {
            return response()->json(['error' => 'Select a warehouse.'], 500);
        }

        if (empty($request->serial)) {
            return response()->json(['error' => 'Empty serials.'], 500);
        }

        $serials = explode("\n", $request->serial);

        foreach ($serials as $serial) {
            try {
                $item->warehouses()->attach($request->warehouse_id, ['qty' => 1, 'serial' => trim(strtoupper($serial))]);
            } catch (\Exception $e) {
                return response()->json(['error' => trim(strtoupper($serial)) . ' serial already exists.'], 500);
            }
        }

        //event(new NotifyUpdatedItem);
    }

    public function showSearch(Request $request)
    {

        $items = Item::with(['type', 'category', 'stocks'])
            // ->where('id', 'like', "%" . $request->item . "%")
            // ->orWhere('name', 'like', "%" . $request->item . "%")
            ->where('name', 'like', '%' . $request->item . "%")
            ->orWhere('description', 'like', "%" . $request->item . "%")
            ->orderBy('name', 'asc')->get();

        if (!empty($request->category)) {
            $items = $items->where('category_id', $request->category);
        }

        if (!empty($request->type)) {
            $items = $items->where('type_id', $request->type);
        }

        $temp = [];

        foreach ($items as $item) {
            $total_qty_in = 0;
            $total_qty_out = 0;

            $total_qty_in = DB::table('stocks')
                ->where('item_id', $item->id)
                ->sum('qty_in');

            $total_qty_out = DB::table('stocks')
                ->where('item_id', $item->id)
                ->sum('qty_out');

            $unit_cost = Db::table('items')->where('id', $item->id)->value('price');

            $forecast = (object) app(NotificationController::class)->forecast($item->id);
            $forecast = (object) $forecast->original;

            $collection = collect($item);
            $collection->put('total_qty', $total_qty_in - $total_qty_out);
            $collection->put('unit_cost', $unit_cost);
            $collection->put('forecast', $forecast);
            array_push($temp, $collection->all());
        }
        //return response()->json($temp);
        $items = $temp;

        if (!empty($request->category)) {
            $collection = collect($items);
            $filtered = $collection->where('category_id', $request->category);
            $items = $filtered->all();
        }

        if (!empty($request->type)) {
            $collection = collect($items);
            $filtered = $collection->where('type_id', $request->type);
            $items = $filtered->all();
        }

        return response()->json($items);
    }

    public function serial($id)
    {
        $serials = DB::table('item_warehouse')
            ->join('warehouses', 'item_warehouse.warehouse_id', '=', 'warehouses.id')
            ->where('serial', $id)
            ->first();

        return response()->json($serials);
    }

    public function barcode($barcode)
    {
        $stock = new \stdClass();
        $temp_serial = [];

        if (DB::table('stock_serial')->where('serial', $barcode)->exists()) {
            $stock = DB::table('stock_serial')
                ->join('stocks', 'stock_serial.stock_id', '=', 'stocks.id')
                ->where('serial', $barcode)
                ->first();

            $collection = collect($stock);

            $item = DB::table('items')
                ->where('id', $stock->item_id)
                ->first();

            $warehouse = DB::table('warehouses')
                ->where('id', $stock->warehouse_id)
                ->first();

            $type = DB::table('types')
                ->where('id', $item->type_id)
                ->first();

            $category = DB::table('categories')
                ->where('id', $item->category_id)
                ->first();

            $total_qty_in = DB::table('stocks')
                ->where('item_id', $stock->item_id)
                ->sum('qty_in');

            array_push($temp_serial, $stock->serial);

            $collection->put('item', $item);
            $collection->put('warehouse', $warehouse);
            $collection->put('type', $type);
            $collection->put('category', $category);
            $collection->put('ordered_serial', $temp_serial);
            $collection->put('total_qty', $total_qty_in);
            $collection->put('ordered_qty', 1);

            $stock = $collection->all();
        } else {
            $item_id = substr($barcode, -8);
            $stock_id = substr($barcode, 0, -8);

            if (DB::table('items')->where('id', $item_id)->exists()) {
                if (DB::table('stocks')->where('id', $stock_id)->exists()) {
                    $stock = DB::table('stocks')
                        ->where('id', $stock_id)
                        ->first();

                    $item = DB::table('items')
                        ->where('id', $item_id)
                        ->first();

                    $warehouse = DB::table('warehouses')
                        ->where('id', $stock->warehouse_id)
                        ->first();

                    $type = DB::table('types')
                        ->where('id', $item->type_id)
                        ->first();

                    $category = DB::table('categories')
                        ->where('id', $item->category_id)
                        ->first();

                    $total_qty_in = DB::table('stocks')
                        ->where('item_id', $stock->item_id)
                        ->sum('qty_in');

                    $collection = collect($stock);
                    $collection->put('item', $item);
                    $collection->put('warehouse', $warehouse);
                    $collection->put('type', $type);
                    $collection->put('ordered_serial', []);
                    $collection->put('category', $category);
                    $collection->put('total_qty', $total_qty_in);
                    $collection->put('ordered_qty', 0);

                    $stock = $collection->all();
                }
            }
        }

        return response()->json($stock);
    }

    public function updateSerial(Request $request, $serial)
    {
        DB::table('item_warehouse')
            ->where('serial', '=', $serial)
            ->update([
                'status' => $request->status,
                'warehouse_id' => $request->warehouse_id,
                'updated_at' => \Carbon\Carbon::now()
            ]);

        // event(new NotifyUpdatedItem);
        return response()->json($request);
    }

    public function checkSerial($serial)
    {
        // $serial_item_id = substr($serial, 0, 8);
        // $item_id = substr($serial, -8);

        $serial = substr($serial, 0, -8);
        $available = false;


        if (DB::table('stock_serial')->where('serial', $serial)->where('status', 'stocked in')->exists()) {
            if (DB::table('logs')->where('serial', $serial)->where('status', 'defective')->doesntExist()) {
                $available = true;
            }
        }


        return response()->json($available);
    }

    public function checkSerialOnly($serial)
    {
        // $serial_item_id = substr($serial, 0, 8);
        // $item_id = substr($serial, -8);
        // $serial = substr($serial, 0, -8);
        $available = false;


        if (DB::table('stock_serial')->where('serial', $serial)->exists()) {
            $available = true;
        }


        return response()->json($available);
    }

    public function updateImage(Request $request)
    {
        try {

            $item =  DB::table('items')->where('id', $request->id)->first();
            $fileName = "";
            if ($request->image != "") {

                $exploded = explode(',', $request->image);

                $decoded = base64_decode($exploded[1]);

                if (str_contains($exploded[0], "jpeg"))
                    $extension = "jpeg";
                elseif (str_contains($exploded[0], "jpg"))
                    $extension = "jpg";
                elseif (str_contains($exploded[0], "png"))
                    $extension = "png";
                elseif (str_contains($exploded[0], "gif"))
                    $extension = "gif";
                elseif (str_contains($exploded[0], "tiff"))
                    $extension = "tiff";
                elseif (str_contains($exploded[0], "bmp"))
                    $extension = "bmp";
                else
                    $extension = "txt";

                if ($item->image != null)
                    $fileName = $item->image;
                else
                    $fileName = str_random() . rand(100000, 999999) . "." . $extension;

                $path = public_path() . "/attachments/" . $fileName;

                file_put_contents($path, $decoded);
            }

            DB::table('items')->where('id', $request->id)
                ->update([
                    'image' => $fileName,
                    'updated_at' => \Carbon\Carbon::now()
                ]);

            return $fileName;
            // event(new NotifyUpdatedItem);
            //return response()->json($ret);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    // public function searchItem(Request $request)
    // {
    //     return $request;

    //     $items = Item::with(['type', 'category', 'stocks'])
    //         ->where('description', 'like', $request->item . '%')
    //         ->get();

    //     $temp = [];

    //     foreach ($items as $item) {
    //         $total_qty_in = 0;
    //         $total_qty_out = 0;

    //         $total_qty_in = DB::table('stocks')
    //             ->where('item_id', $item->id)
    //             ->sum('qty_in');

    //         $total_qty_out = DB::table('stocks')
    //             ->where('item_id', $item->id)
    //             ->sum('qty_out');

    //         $forecast = (object) app(NotificationController::class)->forecast($item->id);
    //         $forecast = (object) $forecast->original;

    //         $collection = collect($item);
    //         $collection->put('total_qty', $total_qty_in - $total_qty_out);
    //         $collection->put('price', 0);
    //         $collection->put('ordered_qty', 0);
    //         $collection->put('forecast', $forecast);
    //         array_push($temp, $collection->all());
    //     }

    //     $items = $temp;

    //     return response()->json($items);
    // }

    public function searchGroup(Request $request)
    {
        $items = DB::table('item_groups')
            ->where('group_name', 'like', $request->group . '%')
            ->get();


        return response()->json($items);
    }
}
