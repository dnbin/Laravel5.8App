<?php

namespace App\Jobs;

use App\Events\FeedImported;
use App\Models\Entry;
use App\Models\Feed;
use App\Services\ConsoleColor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Prewk\XmlStringStreamer;

class ExpediaFeedImportJob implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $console;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct() {
		//
		$this->console = new ConsoleColor();
	}

	/**
	 * Execute the job.
	 *
	 * @param Repository $cache
	 *
	 * @return void
	 * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 * @throws \Exception
	 */
	public function handle( Repository $cache ) {
		//
		try {
			$this->console->info( 'Import Expedia feed' );
			/** @var Feed $feed */
			$feed = Feed::where( 'name', 'expedia' )->firstOrFail();
			if ( ! $feed->status ) {
				throw new \Exception( 'Expedia feed is inactive' );
			}
			$this->console->info( 'Find filename: ' . $feed->feed_path );

			$disk = Storage::disk( 'local' );

			if ( ! $disk->exists( $feed->feed_path ) ) {
				throw new \Exception( 'Feed file is not found: ' . $disk->path( $feed->feed_path ) );
			}
			$this->console->info( 'File ' . $feed->feed_path . ' found. Size is: ' . $disk->size( $feed->feed_path ) );
			// check if file was already processed (cache)
			$cache_key = 'feed_' . $feed->id . '_' . $disk->size( $feed->feed_path ) . '_' . $disk->lastModified( $feed->feed_path );
			if ( $cache->has( $cache_key ) ) {
				throw new \Exception( 'Feed file has been already processed.' );
			}

			// delete all entries for that feed. Process new ones.
			$total = $feed->entries()->delete();
			$this->console->warning( 'Existing entries has been deleted: ' . $total );

			$streamer     = XmlStringStreamer::createStringWalkerParser( $disk->path( $feed->feed_path ) );
			$current_item = 0;
			$children     = [];
			//foreach ( $xml as $item ) {
			while ( $node = $streamer->getNode() ) {
				try {
					$current_item ++;
					$item = simplexml_load_string( $this->utf8_for_xml( $node ) );
					/** @var $item \SimpleXMLElement */
					//$attributes = $item->attributes();
					if ( empty( $item->id ) ) {
						throw new \Exception( 'Id is not found' );
					}
					if ( empty( $item->title ) ) {
						throw new \Exception( 'Title is not found' );
					}

					//dd($attributes);
					/** @var Entry $entry */
					$entry        = Entry::firstOrNew( [ 'feed_id' => $feed->id, 'feed_entry_id' => (int) $item->id ] );
					$entry->title = (string) $item->title;

					if ( ! empty( $item->description ) ) {
						$entry->description = (string) $item->description;
					}
					if ( ! empty( $item->last_updated ) ) {
						$entry->last_updated_at = Carbon::parse( $item->last_updated );
					}

					if ( ! empty( $item->travel_type ) ) {
						$entry->travel_type = (string) $item->travel_type;
					}
					if ( ! empty( $item->street_address ) ) {
						$entry->street_address = (string) $item->street_address;
					}
					if ( ! empty( $item->city ) ) {
						$entry->city = (string) $item->city;
					}
					if ( ! empty( $item->province_state ) ) {
						$entry->province_state = (string) $item->province_state;
					}
					if ( ! empty( $item->zip_code ) ) {
						$entry->zip_code = (string) $item->zip_code;
					}
					if ( ! empty( $item->country ) ) {
						$entry->country = (string) $item->country;
					}
					if ( ! empty( $item->phone_number ) ) {
						$entry->phone_number = (string) $item->phone_number;
					}
					if ( ! empty( $item->latitude ) ) {
						$entry->latitude = (float) $item->latitude;
					}
					if ( ! empty( $item->longitude ) ) {
						$entry->longitude = (string) $item->longitude;
					}
					if ( ! empty( $item->price ) ) {
						$data            = explode( ' ', (string) $item->price );
						$entry->price    = $data[0];
						$entry->currency = $data[1];
					}
					if ( ! empty( $item->link ) ) {
						$entry->link = (string) $item->link;
					}
					if ( ! empty( $item->image_link ) ) {
						$entry->image_link = (string) $item->image_link;
					}
					if ( ! empty( $item->custom_label_0 ) ) {
						$entry->custom_label_0 = (string) $item->custom_label_0;
					}
					if ( ! empty( $item->custom_label_1 ) ) {
						$entry->custom_label_1 = (string) $item->custom_label_1;
					}
					if ( ! empty( $item->star_rating ) ) {
						$entry->star_rating = (float) $item->star_rating;
					}
					if ( ! empty( $item->bedrooms ) ) {
						$entry->bedrooms = (float) $item->bedrooms;
					}
					if ( ! empty( $item->baths ) ) {
						$entry->baths = (float) $item->baths;
					}
					$entry->save();
					/*
					foreach ($item->children() as $child)
					{
						if(!in_array($child->getName(),$children)){
							$children[]=$child->getName();
						}
					}
					if($current_item%100===0){
						dump($children);
					}
		*/
					if ( $current_item % 1000 === 0 ) {
						$this->console->info( 'Entry #' . $entry->id . ' has been added.' );
					}
				} catch ( \Exception $e ) {
					$this->console->error( $e->getMessage() );
				}
			}
			$this->console->warning( 'Import has been done.' );
			$cache->put( $cache_key, true, Carbon::now()->endOfDay() ); // cache until end of the day
			//
			event( new FeedImported( $feed ) );
		} catch ( \Exception $e ) {
			$this->console->error( $e->getMessage() );
		}
	}

	protected function utf8_for_xml( $string ) {
		return preg_replace( '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $string );
	}

}
