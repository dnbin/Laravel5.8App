<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntrySearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('entry_search', function (Blueprint $table) {
	        $table->unsignedBigInteger('entry_id');
	        $table->unsignedBigInteger('search_id');
	        $table->unsignedTinyInteger('is_sent')->default(0);
	        $table->timestamps();

	        $table->foreign('entry_id')->references('id')->on('entries')
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
        Schema::dropIfExists('entry_search');
    }
}
