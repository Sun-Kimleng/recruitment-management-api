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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            
            //overview
            $table->string('name');
            $table->string('workplace')->nullable();
            $table->string('city');
            $table->string('school');
            $table->string('job_status');
            $table->string('interested_job');
            $table->string('job_level');
            $table->longText('description');

            //Appearance
            $table->string('gender');
            $table->string('birthday');
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            
            //Contact
            $table->string('phone');
            $table->string('email');
            $table->string('address');

            //Education
            $table->json('educations')->nullable();
            
            //Skills
            $table->json('skills')->nullable();

            //Experiences
            $table->json('experiences')->nullable();

            //Languages
            $table->json('languages')->nullable();
            
            //CV FIle
            $table->string('cv')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates');
    }
};
