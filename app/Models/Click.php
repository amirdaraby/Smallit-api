<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function shorturl (){
        return $this->belongsTo(ShortUrl::class,"short_id");
    }

    public function browser(){
        return $this->belongsTo(Browser::class, "browser_id");
    }
    public function platform(){
        return $this->belongsTo(Platform::class , "platform_id");

    }

}
