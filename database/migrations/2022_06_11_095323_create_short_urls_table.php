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
        Schema::create('short_urls', function (Blueprint $table) {
            $table->id();
            $table->string("url");
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->on("users")->references("id")
                ->onDelete("cascade");
            $table->unsignedBigInteger("domain_id");
            $table->foreign("domain_id")->on("domains")->references("id")
                ->onDelete("cascade");
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
        Schema::dropIfExists('short_urls');
    }
};
