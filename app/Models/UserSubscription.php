<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    
    public function getMetadataAttribute($value)
    {   
        if(!empty($value)){
            return json_decode($value);
        }else{
           return (object)[];
        }
                
    }
}
