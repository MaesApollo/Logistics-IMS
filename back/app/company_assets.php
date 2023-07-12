<?php

namespace App;

use App\company_assets_type;

use Illuminate\Database\Eloquent\Model;

class company_assets extends Model
{
    protected $fillable = [
        'company_assets_types_id', 'name', 'model', 'area', 'accountable_name', 'date_accounted', 'remarks', 'date_in'
    ];

    public function company_assets_types()
    {
        return $this->hasOne(company_assets_type::class, 'id', 'company_assets_types_id');
    }
}
