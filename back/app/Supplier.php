<?php

namespace App;

use App\Locale;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'locale_id', 'name', 'contact', 'address'
    ];

    public function locale()
    {
        return $this->hasOne(Locale::class, 'id', 'locale_id');
    }
}
