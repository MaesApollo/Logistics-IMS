<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class receiving_report_item extends Model
{
    protected $fillable = ['report_id', 'item_id', 'unit', 'qty', 'remarks'];
}
