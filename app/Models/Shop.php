<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ShopImage;
use App\Models\Product;

class Shop extends Model
{
    use HasFactory,SoftDeletes;

    protected $appends = ['full_video','banner_image'];

    public function Shopkeeper()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function shop_images(){
        return $this->hasMany('App\Models\ShopImage', 'shop_id', 'id');
    }

    public function getFullVideoAttribute(){
        $path =  '/banner_video/' . $this->user_id . "/";
        return ($this->video) ? asset($path.$this->video) : "";
    }
    public function getBannerImageAttribute(){
        $path =  '/banner_image/' . $this->user_id. "/";
        return ($this->banner) ? asset($path.$this->banner) : "";
    }
    public function shopImages(){
        return $this->hasMany(ShopImage::class);
    }

    public function products(){
        return $this->belongsToMany(Product::class,'shop_products','shop_id','product_id')->with('ProductImage');
    }
}
