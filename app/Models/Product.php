<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    public function Shopkeeper()
    {
        return $this->belongsTo('App\Models\User', 'shopkeeper_id', 'id');
    }

    public function Shop()
    {
        return $this->belongsTo('App\Models\Shop', 'shop_id', 'id');
    }
    
}
