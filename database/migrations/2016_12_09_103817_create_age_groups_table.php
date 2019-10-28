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
            $table->string('name')->nullable();
            $table->integer('from')->nullable();
            $table->integer('to')->nullable();
            $table->integer('luggage_limit')->default(25);
            $table->boolean('passport_required')->default(true);
            $table->string('icon')->nullable();
            $table->string('photo')->nullable();
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
