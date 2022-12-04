<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory,SoftDeletes;

    public function Shopkeeper()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
