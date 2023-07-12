<?php

namespace App;


use App\Item;


use Illuminate\Database\Eloquent\Model;

class accountability extends Model
{
    protected $fillable = [
        'accountable', 'date_accounted', 'released_by', 'department', 'area', 'remarks', 'status'
    ];

    public function item_accountability_details()
    {
        return $this->belongsToMany(Item::class)
            ->withPivot('qty', 'remarks')
            ->orderBy('id');
    }
    public function released_by()
    {
        return $this->hasOne(User::class, 'id', 'released_by');
    }
}
