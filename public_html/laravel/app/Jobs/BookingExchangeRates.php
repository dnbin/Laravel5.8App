<?php

namespace App\Jobs;

use App\Models\Feed;
use App\Services\ConsoleColor;
use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\RapidApiBooking\RapidApiBooking;

class BookingExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * @param ConsoleColor $console
     *
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     * @return void
     */
    public function handle(ConsoleColor $console,RapidApiBooking $booking)
    {
        //
        try {
            $console->info( 'Check Booking Exchange Rates' );
            /** @var Feed $feed */
            $feed = Feed::where( 'name', 'booking' )->firstOrFail();
            if ( ! $feed->status ) {
                throw new \Exception( $feed->name.' feed is inactive' );
            }

            // check ExchangeRates
            if(!$feed->exchangeRates()->today()->exists()){
                $console->warning('Exchange Rates is not exists for '.Carbon::now()->toDateString());

                $parameters=[
                    'base_currency'=>'USD',
                    'languagecode'=>'en-us'
                ];
                $response=$booking->getCurrencyExchangeRates($parameters);
                if(!empty($response)){
                    $base_currency_date=Carbon::parse($response->base_currency_date);
                    //if($base_currency_date->isToday()) {
                        //$feed->exchangeRates()->where('base_currency','USD')->delete();
                        foreach ( $response->exchange_rates as $rate ) {
                            $exchange_rate=ExchangeRate::firstOrNew( [
                                'date'          => $base_currency_date->toDateString(),
                                'feed_id'       => $feed->id,
                                'base_currency' => $response->base_currency,
                                'currency'      => $rate->currency,
                            ] );
                            $exchange_rate->rate=$rate->exchange_rate_buy;
                            $exchange_rate->save();
                        }
                        $console->warning( 'Exchange Rates has been updated.' );
                    /*
                        }
                        else{
                            $console->warning('Base Currency Date '.$base_currency_date->toDateString().' is NOT today.');
                        }
                    */
                }

            }
            else{
                $console->warning('Today Exchange Rates already exists');
            }
        }
        catch(\Exception $e){
            $console->error($e->getMessage());
        }
    }
}
