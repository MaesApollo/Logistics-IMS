<?php

namespace App;

use App\Item;
use App\Supplier;
use App\User;
use App\Stock;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'user_id', 'supplier_id', 'status', 'shipping_fee', 'tax', 'file', 'delivery_date', 'created_at', 'updated_at'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function item()
    {
        return $this->belongsToMany(Item::class)
            ->withPivot('purchase_order_id', 'item_id', 'qty', 'unit', 'price', 'tax_rate')
            ->withTimestamps();
    }
}
