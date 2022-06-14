<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    use HasFactory;
    protected $guarded = ["id"];


    public function domain(){
        return $this->belongsTo(Domain::class);
    }

    public function getRouteKeyName()
    {
        return "url";
    }

    public static function findOrCreate($id)
    {
        $obj = static::find($id);
        return $obj ?: new static;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
