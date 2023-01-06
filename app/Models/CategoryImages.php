<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryImages extends Model
{
    use HasFactory;
    protected $appends = ['full_image'];

    public function getFullImageAttribute(){
        $path = '/images/'. $this->type == 0?'category/':'subcategory/'. $this->category_id. "/";
        return ($this->image) ? asset($path.$this->image) : "";
    }
    
}
