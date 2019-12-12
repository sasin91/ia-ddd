<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('creator_id')->nullable()->index();
            $table->foreign('creator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

            $table->string('phone')->nullable();
            $table->string('title')->nullable();
            $table->string('name');
            $table->string('gender');
            $table->date('birthdate')->nullable();

            $table->string('age_group');
            $table->foreign('age_group')->references('name')->on('age_groups');

            $table->string('nationality')->nullable();
            $table->string('citizenship')->nullable();

            $table->string('passport')->nullable();
            $table->date('passport_expires_at')->nullable();
            $table->string('visa')->nullable();
            $table->date('visa_expires_at')->nullable();
            $table->string('visa_country')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passengers');
    }
}
