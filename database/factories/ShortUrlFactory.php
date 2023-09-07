<?php

namespace Database\Factories;

use App\Models\ShortUrl;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    public function definition(): array
    {
        return [
            "short_url" => Str::random(10)
        ];
    }
}
