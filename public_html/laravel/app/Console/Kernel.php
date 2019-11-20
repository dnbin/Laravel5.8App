<?php

namespace App\Console;

use App\Jobs\AmadeusExchangeRates;
use App\Jobs\AmadeusHotelSearchJob;
use App\Jobs\BookingExchangeRates;
use App\Jobs\BookingHotelSearch;
use App\Jobs\ExpediaFeedImportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule( Schedule $schedule ) {

        $schedule->job( new ExpediaFeedImportJob() )
                 ->dailyAt( '06:00' )
                 ->withoutOverlapping()
                 ->runInBackground();


        $schedule->command( 'searches:send_entries' )
                 //->hourlyAt(30)
                 ->cron('30 */3 * * *')
                 ->withoutOverlapping( 60 )
                 ->runInBackground();

//        $schedule->job( new AmadeusHotelSearchJob() )
//                 ->hourly()
//                 ->cron('10 */3 * * *')
//                 ->withoutOverlapping()
//                 ->runInBackground();


        $schedule->job( new BookingHotelSearch() )
                 //->hourly()
                 ->cron('10 */3 * * *')
                 ->withoutOverlapping()
                 ->runInBackground();

        $schedule->job(new AmadeusExchangeRates())->hourly()->withoutOverlapping();
        $schedule->job(new BookingExchangeRates())->cron('10 */4 * * *')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load( __DIR__ . '/Commands' );

        require base_path( 'routes/console.php' );
    }
}
