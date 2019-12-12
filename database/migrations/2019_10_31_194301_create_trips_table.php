<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('outward_travel');
            $table->foreign('outward_travel')->references('flight_number')->on('travels');
            $table->dateTimeTz('outward_departure_datetime');
            $table->dateTimeTz('outward_arrival_datetime');
            $table->string('home_travel')->nullable();
            $table->foreign('home_travel')->references('flight_number')->on('travels');
            $table->dateTimeTz('home_departure_datetime')->nullable();
            $table->dateTimeTz('home_arrival_datetime')->nullable();
            $table->string('type');
            $table->string('travel_class');
            $table->unsignedBigInteger('price_id');
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
            $table->string('PNR');
            $table->foreign('PNR')->references('PNR')->on('tickets')->onDelete('cascade');
            $table->unsignedBigInteger('passenger_id');
            $table->foreign('passenger_id')->references('id')->on('passengers')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('trips');
    }
}
