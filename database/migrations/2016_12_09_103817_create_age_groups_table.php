<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAgeGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('age_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('from');
            $table->integer('to');
            $table->integer('luggage_limit')->default(25);
            $table->integer('passenger_limit')->default(9);
            $table->boolean('passport_required')->default(true);
            $table->string('icon')->nullable();
            $table->string('photo')->nullable();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('age_groups');
    }
}
