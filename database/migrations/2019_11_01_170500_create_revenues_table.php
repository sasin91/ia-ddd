<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('SET NULL');
            $table->string('customer_email');
            $table->integer('amount');
            $table->integer('earned_points');
            $table->decimal('exchange_rate');
            $table->string('currency_code');
            $table->string('description');
            $table->string('category');
            $table->string('billing_method');
            $table->string('reference')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->dateTime('paid_at')->nullable();
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
        Schema::dropIfExists('revenues');
    }
}
