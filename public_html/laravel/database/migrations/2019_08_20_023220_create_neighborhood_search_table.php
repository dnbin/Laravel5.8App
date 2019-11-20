<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNeighborhoodSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('neighborhood_search', function (Blueprint $table) {
            $table->unsignedBigInteger('neighborhood_id');
            $table->unsignedBigInteger('search_id');

            $table->foreign('neighborhood_id')->references('id')->on('neighborhoods')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('search_id')->references('id')->on('searches')
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
        Schema::dropIfExists('neighborhood_search');
    }
}
