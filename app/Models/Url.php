<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Url extends Model
{
    use HasFactory;

    protected $guarded = ["id"];
    public $timestamps = false;

    public function shorturl(): HasMany
    {
        return $this->hasMany(ShortUrl::class, "url_id");
    }

    public function clicks(): HasManyThrough
    {
        return $this->hasManyThrough(Click::class, ShortUrl::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, "url_id");
    }

}
