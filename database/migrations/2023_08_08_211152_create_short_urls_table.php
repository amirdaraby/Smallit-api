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
        Schema::create('short_urls', function (Blueprint $table) {

            $table->id()->autoIncrement()->startingValue(100000);
            $table->string("short_url");

            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->on("users")->references("id")
                ->onDelete("cascade");

            $table->unsignedBigInteger("url_id");
            $table->foreign("url_id")->on("urls")->references("id")
                ->onDelete("cascade");

            $table->unsignedBigInteger("batch_id");
            $table->foreign("batch_id")->on("batches")->references("id")
                ->onDelete("cascade");

            $table->unique("short_url");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
