<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceSeasonDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_season_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('season_id')->index();
            $table->foreign('season_id')->references('id')->on('price_seasons');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->timestamps();

            $table->index(['season_id', 'starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_season_dates');
    }
}
