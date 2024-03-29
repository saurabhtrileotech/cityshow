<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Favourite;
use App\Models\Shop;
use Auth;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $appends = ['is_fav','discount'];

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
    public function ProductImage()
    {
        return $this->hasMany('App\Models\ProductImage','product_id');
    }
    public function Product_Shop()
    {
        return $this->hasMany('App\Models\ShopProduct','product_id');
    }

    public function ProductShop(){
        return $this->belongsToMany(Shop::class,'shop_products','product_id','shop_id');
    }
    public function getIsFavAttribute(){
        $isFavourite =  Favourite::where('user_id',Auth::user()->id)->where('product_id',$this->id)->first();
        if($isFavourite){
            return 1;
        }else{
            return 0;
        }
    }

    public function getDiscountAttribute() {
        $discount = (object)[];
        $discount_id = ShopDiscount::select('discount_id')->where('product_id',$this->id)->latest()->first();
       // dd($discount_id);        
        if($discount_id){
            $discount_obj = Discount::where('id',$discount_id->discount_id)->whereDate('end_date','>=', date('Y-m-d'))->first();
            if($discount_obj){
                $discount = $discount_obj->toArray();
            }
        }
        return $discount;
    }
    
}
