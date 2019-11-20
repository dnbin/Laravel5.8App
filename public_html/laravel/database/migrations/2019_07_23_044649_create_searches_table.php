<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('city');
            $table->integer('booking_dest_id')->nullable();
            $table->date('check_in_date');
            $table->unsignedSmallInteger('nights');
            $table->unsignedTinyInteger('hotel_class')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->unsignedDecimal('max_budget')->nullable();
            $table->enum('max_budget_currency',['USD','EUR','GBP','CAD'])->nullable();
            $table->unsignedTinyInteger('number_of_adults')->default(2);
            $table->json('children')->nullable();
	        $table->string('ip')->nullable();
	        $table->string('referrer')->nullable();
	        $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();

	        $table->foreign('user_id')
	              ->references('id')
	              ->on('users')
	              ->onDelete('cascade')
	              ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('searches');
    }
}
