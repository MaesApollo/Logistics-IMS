<?php

namespace App;

use App\Type;
use App\Category;
use App\Warehouse;
use App\Stock;
use App\Supplier;
use App\DeliveryReceipt;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'description', 'category_id', 'type_id', 'image', 'price'
    ];

    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'item_id', 'id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function delivery_receipt_details()
    {
        return $this->belongsToMany(DeliveryReceipt::class)
            ->withPivot('qty');
    }
}
