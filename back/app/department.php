<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class department extends Model
{
    protected $fillable = [
        'approver_id', 'name'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'approver_id');
    }
}
