<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShortUrlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            UrlSeeder::class,
        ]);

        DB::table('short_urls')->insert([
            "user_id"   => 1,
            "url_id"    => 1,
            "short_url" => "test"
        ]);

    }
}
