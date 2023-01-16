<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(){
        $path =  '/images/product/' . $this->product_id . "/";
        return ($this->image) ? asset($path.$this->image) : "";
    }
}
