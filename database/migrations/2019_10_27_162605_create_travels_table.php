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
            $table->string('flight_number')->unique();
            $table->string('travel_class')->default(TravelClass::ECONOMY);
            $table->string('departure_airport');
            $table->foreign('departure_airport')->references('IATA')->on('airports');
            $table->string('destination_airport');
            $table->foreign('destination_airport')->references('IATA')->on('airports');
            $table->integer('default_seats')->default(159);
            $table->timestamp('open_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('flight_number');
            $table->index(['departure_airport', 'destination_airport']);
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
