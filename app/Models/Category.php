<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CategoryImages;
use App\Models\Shop;
use App\Models\Product;
use DB;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    protected $appends = ['category_images','product_count'];

    // public function categoryImage(){
    //     return $this->hasMany(CategoryImages::class,'category_id')->where('type','=', 0);
    // }

    public function subCategory(){
        return $this->hasMany(SubCategory::class,'category_id');
    }

    public function getCategoryImagesAttribute(){
        $asset_url = asset('images/category');
        $images = CategoryImages::select('image',DB::raw("CONCAT('".$asset_url."/".$this->id."','/',image) AS full_image_path"))->where('category_id',$this->id)->where('type',0)->pluck('full_image_path')->toArray();
        return $images;
    }

    public function shops(){
        return $this->hasMany(Shop::class,'category_id');
    }

    public function getProductCountAttribute(){
        return Product::where('cat_id',$this->id)->count();
    }
}
