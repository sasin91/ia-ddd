<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelCancelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_cancels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('flight_number');
            $table->timestamp('departs_at');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['flight_number', 'departs_at']);
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
        Schema::dropIfExists('travel_cancels');
    }
}
