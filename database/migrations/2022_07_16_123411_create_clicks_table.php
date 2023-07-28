<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->string("uid")->nullable(true);

            $table->unsignedBigInteger("shorturl_id");
            $table->foreign("shorturl_id")->on("short_urls")
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
                "Unknown"])->nullable(false);

            $table->enum("browser", ["Samsung Browser",
                "Edge",
                "Miui Browser",
                "Firefox",
                "Chrome",
                "Opera",
                "Nokia Browser",
                "Safari",
                "Internet Explorer",
                "Unknown"])->nullable(false);


            $table->string("user_agent");

            $table->timestamp("created_at")->default(now()->carbonize());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clicks');
    }
};
