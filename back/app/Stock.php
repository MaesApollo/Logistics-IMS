<?php

namespace App;

use App\Item;
use App\Warehouse;
use App\Supplier;
use App\PurchaseOrder;
use App\stock_serial;
use DB;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'item_id', 'warehouse_id', 'purchase_order_id', 'direct_receive_id', 'sales_return_id', 'unit_price', 'qty_in', 'qty_out', 'received_at'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }


    public function purchase_order()
    {
        return $this->hasOne(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'warehouse_id', 'id');
    }
}
