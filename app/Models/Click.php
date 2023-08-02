<?php

namespace App\Models;

use App\Http\Controllers\Api\AgentController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{

    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function shorturl() : BelongsTo
    {
        return $this->belongsTo(ShortUrl::class, "shorturl_id");
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function setCreatedAtAttribute()
    {
        $this->attributes['created_at'] = now()->timestamp;
    }

}
