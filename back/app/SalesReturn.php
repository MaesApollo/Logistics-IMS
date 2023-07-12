<?php

namespace App;

use App\SalesOrder;
use App\Item;
use App\Client;
use App\User;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'from_client_id', 'to_client_id', 'returnee', 'status', 'remarks', 'date_return',
    ];
    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'from_client_id');
    }

    public function sales_return_details()
    {
        return $this->belongsToMany(Item::class)
            ->withPivot('qty')
            ->withPivot('status')
            ->withPivot('note')
            ->orderBy('id');
    }
}
