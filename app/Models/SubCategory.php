<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryImages;
use DB;

class SubCategory extends Model
{
    use HasFactory;
    protected $appends = ['sub_category_images'];

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function getSubCategoryImagesAttribute(){
        $asset_url = asset('images/subcategory');
        $images = CategoryImages::select('image',DB::raw("CONCAT('".$asset_url."/".$this->id."','/',image) AS full_image_path"))->where('category_id',$this->id)->where('type',1)->pluck('full_image_path')->toArray();
        return $images;
    }
    
}
