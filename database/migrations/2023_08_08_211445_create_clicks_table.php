<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clicks', function (Blueprint $table) {
                $table->id();
                $table->string("uid")->nullable(true);

                $table->unsignedBigInteger("short_url_id");
                $table->foreign("short_url_id")->on("short_urls")
                    ->references("id")
                    ->onDelete("cascade");


                $table->enum("platform", ["Windows",
                    "Android",
                    "Mac OS",
                    "Linux",
                    "iPhone",
                    "iPod",
                    "iPad",
                    "BlackBerry",
                    "Windows Phone",
                    "Mobile",
                    ])->nullable(true);

                $table->enum("browser", ["Samsung Browser",
                    "Edge",
                    "Miui Browser",
                    "Firefox",
                    "Chrome",
                    "Opera",
                    "Nokia Browser",
                    "Safari",
                    "Internet Explorer",
                    ])->nullable(true);


                $table->string("user_agent")->nullable(false);

                $table->timestamp("created_at")->default(now()->carbonize());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};
