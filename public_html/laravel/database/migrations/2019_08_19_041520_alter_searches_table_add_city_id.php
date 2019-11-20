<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSearchesTableAddCityId extends Migration
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
            $table->unsignedInteger('city_id')->after('city')->nullable();
            $table->foreign('city_id')->references('id')->on('cities')
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
            $table->dropColumn('city_id');
        });
    }
}
