<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedSearchTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'feed_search', function ( Blueprint $table ) {

            $table->unsignedBigInteger( 'feed_id' );
            $table->unsignedBigInteger( 'search_id' );

            $table->foreign( 'feed_id' )->references( 'id' )->on( 'feeds' )
                  ->onDelete( 'cascade' )
                  ->onUpdate( 'cascade' );
            $table->foreign( 'search_id' )->references( 'id' )->on( 'searches' )
                  ->onDelete( 'cascade' )
                  ->onUpdate( 'cascade' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'feed_search' );
    }
}
