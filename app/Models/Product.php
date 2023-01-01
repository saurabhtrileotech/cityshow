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
    public function Category()
    {
        return $this->belongsTo('App\Models\Category', 'cat_id', 'id');
    }

    public function Sub_Category()
    {
        return $this->belongsTo('App\Models\SubCategory', 'subcat_id', 'id');
    }

    public function Product_Image()
    {
        return $this->hasMany('App\Models\ProductImage','product_id');
    }
    public function Product_Shop()
    {
        return $this->hasMany('App\Models\ShopProduct','product_id');
    }
    
}
