<?php

use App\Domains\Booking\Enums\TravelClass;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('flight_number');
            $table->string('travel_class')->default(TravelClass::ECONOMY);
            $table->unsignedBigInteger('departure_airport_id');
            $table->foreign('departure_airport_id')->references('id')->on('airports');
            $table->unsignedBigInteger('destination_airport_id');
            $table->foreign('destination_airport_id')->references('id')->on('airports');
            $table->integer('default_seats')->default(159);
            $table->timestamp('open_until')->nullable();
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
        Schema::dropIfExists('travels');
    }
}
