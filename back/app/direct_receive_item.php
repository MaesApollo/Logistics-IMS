<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class direct_receive_item extends Model
{
    protected $fillable = ['direct_receive_id', 'item_id', 'warehouse_id', 'qty', 'price',];
}
