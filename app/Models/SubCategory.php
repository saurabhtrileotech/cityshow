<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoryImages;

class SubCategory extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }
    public function subCategoryImage(){
        return $this->hasMany(CategoryImages::class,'category_id')->where('type','=', 1);
    }
}
