<?php

namespace App;

use App\User;
use App\Item;
use App\Client;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'client_id', 'user_id', 'remarks',
    ];

    public function sales_order_details()
    {
        return $this->belongsToMany(Item::class)
            ->withPivot('qty')
            ->orderBy('id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function dr_client()
    {
        return $this->hasMany(Client::class, 'client_id', 'id');
    }
}
