<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function url(): BelongsTo{
        return $this->belongsTo(Url::class, "url_id");
    }

    public function shortUrls() : HasMany {
        return $this->hasMany(ShortUrl::class, "batch_id");
    }
}
