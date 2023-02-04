<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    //protected $appends = ['image_url'];

    public function getImageAttribute($value){
        $path =  'discount_image/';
        return ($value) ? asset($path.$value) : "";
    }
}
