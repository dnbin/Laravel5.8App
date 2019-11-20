<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            //name,alpha-2,alpha-3,country-code,iso_3166-2,region,sub-region,intermediate-region,region-code,sub-region-code,intermediate-region-code
            //Afghanistan,AF,AFG,004,ISO 3166-2:AF,Asia,Southern Asia,"",142,034,""

            $table->increments('id');
            $table->string('name')->unique();
            $table->char('alpha_2',2);
            $table->char('alpha_3',3);
            $table->string('country_code');
            $table->string('iso_3166_2');
            $table->string('region');
            $table->string('intermediate_region')->nullable();
            $table->string('region_code')->nullable();
            $table->string('sub_region_code')->nullable();
            $table->string('intermediate_region_code')->nullable();
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
        Schema::dropIfExists('countries');
    }
}
