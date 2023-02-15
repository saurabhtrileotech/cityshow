<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Discount extends Model
{
    use HasFactory,SoftDeletes;

    //protected $appends = ['image_url'];

    public function getImageAttribute($value){
        $path =  'discount_image/';
        return ($value) ? asset($path.$value) : "";
    }

    public function DiscountProducts()
    {
        return $this->belongsToMany(Product::class,'shop_discounts','discount_id','product_id');
    }
    public function DiscountShop(){
        return $this->belongsToMany(Shop::class,'shop_discounts','discount_id','shop_id');
    }
}
