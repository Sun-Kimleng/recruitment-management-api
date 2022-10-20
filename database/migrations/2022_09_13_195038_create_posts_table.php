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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('job_type');
            $table->unsignedBigInteger('posted_by');
            $table->unsignedBigInteger('job_title');
            $table->string('salary');
            $table->string('job_level');
            $table->string('experience');
            $table->string('status');
            $table->longText('description');
            $table->timestamps();

            $table->foreign('job_title')->references('id')->on('jobs');
            $table->foreign('posted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
