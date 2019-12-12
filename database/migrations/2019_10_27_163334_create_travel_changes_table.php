<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_changes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('flight_number');
            $table->foreign('flight_number')->references('flight_number')->on('travels');
            $table->timestamp('departs_at');
            $table->json('modifications');
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
        Schema::dropIfExists('travel_changes');
    }
}
