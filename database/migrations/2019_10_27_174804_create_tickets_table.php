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
            $table->string('PNR')->unique();
            $table->string('buyer_email');
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->foreign('buyer_id')->references('id')->on('users');
            $table->boolean('express')->default(false);
            $table->integer('total_cost')->default(0);
            $table->timestamp('voided_at')->nullable();
            $table->timestamp('documents_sent_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('PNR');
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
