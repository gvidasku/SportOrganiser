<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class organisator extends Model
{
    use HasFactory;

    public function getcity()
    {
        return $this->hasOne('App\Models\city', 'id', 'organisator_city_id');
    }
    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }
}
