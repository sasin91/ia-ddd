<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('flight_number');
            $table->foreign('flight_number')->references('flight_number')->on('travels');
            $table->timestamp('departs_at');
            $table->integer('remaining');
            $table->integer('available');
            $table->integer('occupied');
            $table->integer('by_us');
            $table->integer('by_others');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['departs_at', 'flight_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seats');
    }
}
