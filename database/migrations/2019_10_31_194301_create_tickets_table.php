<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('outward_flight_number');
            $table->dateTimeTz('outward_departure_datetime');
            $table->dateTimeTz('outward_arrival_datetime');
            $table->string('home_flight_number')->nullable();
            $table->dateTimeTz('home_departure_datetime')->nullable();
            $table->dateTimeTz('home_arrival_datetime')->nullable();
            $table->string('travel_period');
            $table->string('travel_class');
            $table->unsignedBigInteger('price_id');
            $table->foreign('price_id')->references('id')->on('ticket_prices')->onDelete('cascade');
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
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
        Schema::dropIfExists('tickets');
    }
}
