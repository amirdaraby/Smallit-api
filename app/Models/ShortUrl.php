<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    use HasFactory;
    protected $guarded = ["id"];


    public function url(){

        return $this->belongsTo(Url::class, "url_id");
    }

    public function getRouteKeyName()
    {
        return "short_url";
    }


    public function user(){
        return $this->belongsTo(User::class);
    }


    public function click(){
        return $this->hasMany(Click::class,"short_id");
    }



}
