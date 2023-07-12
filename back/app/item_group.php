<?php

namespace App;

use App\Item;

use Illuminate\Database\Eloquent\Model;

class item_group extends Model
{
    protected $fillable = [
        'group_name'
    ];

    public function items()
    {
        //return $this->hasMany(Item::class, 'id',)

        return $this->belongsToMany(Item::class)
            ->withPivot('qty')
            ->orderBy('id');
    }
}
