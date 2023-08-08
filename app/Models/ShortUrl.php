<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortUrl extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public $timestamps = false;

    public function url(): BelongsTo
    {

        return $this->belongsTo(Url::class, "url_id");
    }

    public function getRouteKeyName(): string
    {
        return "short_url";
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class, "shorturl_id");
    }

    public function batch(): BelongsTo {
        return $this->belongsTo(Batch::class, "batch_id");
    }

}
