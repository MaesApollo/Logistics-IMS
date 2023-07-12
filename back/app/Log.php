<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Item;
use App\Stock;

class Log extends Model
{
    protected $fillable = [
        'item_id', 'serial', 'status', 'remark'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function stocks()
    {
    	return $this->hasMany(Stock::class, 'item_id', 'id');
    }

}
