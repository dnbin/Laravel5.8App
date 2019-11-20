<?php

namespace App\Console\Commands;

use App\Models\Entry;
use App\Models\Feed;
use App\Services\ConsoleColor;
use Illuminate\Console\Command;

class RegularRoomRateSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regular:rate:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regular Room Rate Search';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ConsoleColor $console)
    {
        //
        /** @var Feed $expedia_feed */
        $expedia_feed=Feed::where('name','expedia')->firstOrFail();

        $entries=Entry::with('feed')->where('feed_id','<>',$expedia_feed->id)->get();
        $console->info('Found '.$entries->count().' entries to check.');
        foreach($entries as $entry){
            $console->info('Feed: '.$entry->feed->name.' Hotel: '.$entry->title.' Price: '.$entry->price.' '.$entry->currency );
            // search expedia hotel by title
            $expedia_hotel=$expedia_feed->entries()->where('title','like',$entry->title.'%')->where('country',$entry->country)->first();
            if(!empty($expedia_hotel)){
                $console->warning('Expedia matched hotel is found. Hotel: '.$expedia_hotel->title.' Price: '.$expedia_hotel->price.' '.$expedia_hotel->currency );
                $entry->regular_room_rate_amount=$expedia_hotel->price;
                $entry->regular_room_rate_currency=$expedia_hotel->currency;
                $entry->save();
            }
            else{
                $console->error('Expedia matched hotel is not found');
            }
        }
    }
}
