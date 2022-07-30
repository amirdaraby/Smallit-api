<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([UserSeeder::class]);
-
        DB::table("personal_access_tokens")->insert([
            "id"             => 1,
            "tokenable_type" => "App\Models\User",
            "tokenable_id"   => 1,
            "name"           => 'LaravelSanctumAuth',
            "token"          => 'token'

        ]);

    }
}
