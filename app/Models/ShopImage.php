<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopImage extends Model
{
    use HasFactory;

    protected $appends = ['full_image'];

    public function getFullImageAttribute(){
        $path =  '/public/images/' . $this->shop_id . "/";
        return ($this->image) ? asset($path.$this->image) : "";
    }
}
