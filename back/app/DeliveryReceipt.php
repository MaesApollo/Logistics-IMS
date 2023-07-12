<?php

namespace App;

use App\SalesOrder;
use App\Item;
use App\User;
use App\Client;
use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    protected $fillable = [
        'sales_order_id', 'user_id', 'sales_return_id', 'memo', 'delivered_at', 'received_at'
    ];

    public function delivery_receipt_details()
    {
        return $this->belongsToMany(Item::class)
            ->withPivot('qty')
            ->withPivot('note')
            ->orderBy('id');
    }






    public function sales_order()
    {
        return $this->hasOne(SalesOrder::class, 'id', 'sales_order_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }
}
