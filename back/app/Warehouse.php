<?php

namespace App;

use App\Item;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
	protected $fillable = [
        'name', 'address'
    ];

    public function items()
    {
    	return $this->belongsToMany(Item::class);
    }
}

