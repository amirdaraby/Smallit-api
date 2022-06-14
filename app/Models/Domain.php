<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $guarded = ["id"];


    public function getRouteKeyName()
    {
        return "url";
    }
    public static function findOrCreate($url)
    {
        $obj = static::find($url);
        return $obj ?: new static;
    }
    public function shorturl(){
        return $this->hasMany(ShortUrl::class);
    }


}
