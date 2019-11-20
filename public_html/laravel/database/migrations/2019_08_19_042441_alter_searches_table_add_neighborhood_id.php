<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSearchesTableAddNeighborhoodId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('searches', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('neighborhood_id')->after('city_id')->nullable();
            $table->foreign('neighborhood_id')->references('id')->on('neighborhoods')
                  ->onDelete('restrict')
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
        Schema::table('searches', function (Blueprint $table) {
            //
            $table->dropColumn('neighborhood_id');
        });
    }
}
