<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->char('iata_code',3)->unique();
            $table->string('name');
            $table->unsignedInteger('country_id')->nullable();
            $table->decimal('lat',10,8)->nullable();
            $table->decimal('lng',11,8)->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')
                  ->onDelete('set null')
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
        Schema::dropIfExists('cities');
    }
}
