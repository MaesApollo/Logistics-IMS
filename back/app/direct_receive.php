<?php

namespace App;

use App\Item;
use App\Supplier;
use App\User;
use App\Warehouse;
use Illuminate\Database\Eloquent\Model;

class direct_receive extends Model
{
    protected $fillable = ['user_id', 'supplier_id', 'total', 'class', 'note', 'received_date'];

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
            ->withPivot('direct_receive_id', 'item_id', 'warehouse_id', 'qty', 'price');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
