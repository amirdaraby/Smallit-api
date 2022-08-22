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
        Schema::create('user_jobs', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')->on('users')
                ->references('id')->onDelete('cascade');

            $table->string('url_id');

            $table->integer('count');

            $table->enum('status',[
                'queue',
                'created',
                'failed'
            ]);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_jobs');
    }
};