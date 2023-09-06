<?php

namespace Database\Factories;

use App\Traits\UserAgent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Click>
 */
class ClickFactory extends Factory
{

    use UserAgent;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userAgent = $this->faker->userAgent;
        return [
            "uid" => $this->faker->uuid(),
            "platform" => $this->getOs($userAgent),
            "browser" => $this->getBrowser($userAgent),
            "user_agent" => $userAgent,
        ];
    }
}
