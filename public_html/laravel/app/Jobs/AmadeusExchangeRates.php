<?php

namespace App\Jobs;

use App\Models\ExchangeRate;
use App\Models\Feed;
use App\Services\ConsoleColor;
use App\Services\ExchangeRatesAPI\Exception;
use App\Services\ExchangeRatesAPI\ExchangeRatesAPI;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AmadeusExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $feed_name='amadeus';
    protected $base_currency='USD';
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
     * @param ExchangeRatesAPI $exchange_rates_api
     * @param ConsoleColor $console
     *
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     * @return void
     */
    public function handle(ExchangeRatesAPI $exchange_rates_api,ConsoleColor $console)
    {
        //
        //
        try {
            $console->info('Grab rates for Amadeus feed from ExchangeRatesAPI.io');

            /** @var Feed $feed */
            $feed = Feed::where( 'name', $this->feed_name )->firstOrFail();
            if ( ! $feed->status ) {
                throw new \Exception( $feed->name.' feed is inactive' );
            }

            // check ExchangeRates
            if(!$feed->exchangeRates()->today()->exists()) {
                $console->warning( 'Exchange Rates is not exists for ' . Carbon::now()->toDateString() );

                $exchange_rates_api->setBaseCurrency( $this->base_currency );
                $response = $exchange_rates_api->fetch();
                if ( $response->getStatusCode() !== 200 ) {
                    throw new \Exception( 'Error. Status Code: ' . $response->getStatusCode() );
                }

                $rates_date = $response->getRatesDate();
                if ( empty( $rates_date ) ) {
                    throw new Exception( 'Rates Date is not available from API' );
                }
                $console->info( 'Rates for ' . $rates_date );
                foreach ( $response->getRates() as $currency => $rate ) {
                    $exchange_rate       = ExchangeRate::firstOrNew( [
                        'date'          => $rates_date,
                        'feed_id'       => $feed->id,
                        'base_currency' => $response->getBaseCurrency(),
                        'currency'      => $currency,
                    ] );
                    $exchange_rate->rate = $rate;
                    $exchange_rate->save();
                    $console->info( $response->getBaseCurrency() . '/' . $currency . ' => ' . $rate );
                }
                $console->warning( 'Exchange Rates has been updated.' );
            }
        }
        catch(\Exception $e){
            $console->error($e->getMessage());
        }
    }
}
