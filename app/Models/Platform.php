<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;
    protected $guarded = ["id"];

    public function click(){
        return $this->hasMany(Click::class);
    }

}
