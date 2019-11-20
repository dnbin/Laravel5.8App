<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('feed_id')->nullable();
            $table->char('base_currency',3);
            $table->char('currency',3);
            $table->unsignedDecimal('rate',18,7);
            $table->timestamps();
            $table->foreign('feed_id')->references('id')->on('feeds')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->unique(['date','feed_id','base_currency','currency']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_rates');

    }
}
