<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('feed_id');
            $table->unsignedBigInteger('feed_entry_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('travel_type')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('province_state')->nullable();
            $table->unsignedInteger('zip_code')->nullable();
            $table->string('country')->nullable();
            $table->string('phone_number')->nullable();
            $table->decimal('latitude',8,5)->nullable();
	        $table->decimal('longitude',8,5)->nullable();
	        $table->unsignedDecimal('price')->nullable();
	        $table->string('currency')->nullable();
	        $table->text('link')->nullable();
	        $table->text('image_link')->nullable();
	        $table->unsignedDecimal('star_rating')->nullable();
	        $table->text('custom_label_0')->nullable();
	        $table->text('custom_label_1')->nullable();
	        $table->unsignedTinyInteger('bedrooms')->nullable();
	        $table->unsignedTinyInteger('baths')->nullable();
	        $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['feed_id','feed_entry_id']);
	        $table->foreign('feed_id')
	              ->references('id')
	              ->on('feeds')
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
        Schema::dropIfExists('entries');
    }
}
