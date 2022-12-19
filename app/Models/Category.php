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
        return $this->hasMany(CategoryImages::class,'category_id');
    }
}
