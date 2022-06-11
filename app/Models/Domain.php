<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function shorturl(){
        return $this->hasMany(ShortUrl::class);
    }

}
