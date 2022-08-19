<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    protected $fillable = ["short_url" , "url_id" ,"user_id"];
    public $timestamps = true;

    public function url()
    {

        return $this->belongsTo(Url::class, "url_id");
    }

    public function getRouteKeyName()
    {
        return "short_url";
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function clicks()
    {
        return $this->hasMany(Click::class, "shorturl_id");
    }


}
