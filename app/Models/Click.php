<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Click extends Model
{
//    use HasFactory;
    protected $guarded = ['id'];


    public function shorturl (){
        $this->belongsTo(ShortUrl::class,"short_id");
    }




}
