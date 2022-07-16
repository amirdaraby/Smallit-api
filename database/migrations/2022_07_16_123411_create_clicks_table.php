<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->string("uid")->nullable(false)->unique();

            $table->unsignedBigInteger("shorturl_id");
            $table->foreign("shorturl_id")->on("short_urls")
                ->references("id")
                ->onDelete("cascade");

            $table->unsignedBigInteger("browser_id");
            $table->foreign("browser_id")->on("browsers")
                ->references("id")
                ->onDelete("cascade");

            $table->unsignedBigInteger("platform_id");
            $table->foreign("platform_id")->on("platforms")
                ->references("id")
                ->onDelete("cascade");

            $table->string("useragent");


            $table->timestamps();
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
