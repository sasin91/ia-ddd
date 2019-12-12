<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('account_ledger_id');
            $table->foreign('account_ledger_id')->references('id')->on('account_ledgers')->onDelete('cascade');
            $table->morphs('stored_event');
            $table->nullableMorphs('causer');
            $table->decimal('amount');
            $table->decimal('exchange_rate');
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
        Schema::dropIfExists('account_movements');
    }
}
