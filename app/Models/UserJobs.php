<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserJobs extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function url(){
        return $this->belongsTo(Url::class,'url_id');
    }

}
