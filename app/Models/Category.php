<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CategoryImages;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    public function categoryImage(){
        return $this->hasMany(CategoryImages::class,'category_id')->where('type','=', 0);
    }

    

    public function subCategory(){
        return $this->hasMany(SubCategory::class,'category_id')->with('subCategoryImage');
    }
}
