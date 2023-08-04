<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string("name", 255)->nullable(true);

            $table->enum("status", [
                "queue",
                "success",
                "failed"
            ])->default("queue");

            $table->unsignedBigInteger("user_id")->nullable(false);
            $table->foreign("user_id")->on("users")
                ->references("id")->onDelete("cascade");

            $table->unsignedBigInteger("url_id")->nullable(false);
            $table->foreign("url_id")->on("urls")
                ->references("id")->onDelete("cascade");

            $table->mediumInteger("amount")->nullable(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
