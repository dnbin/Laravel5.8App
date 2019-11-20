<?php

namespace App\Console\Commands;

use App\Jobs\SearchSendEntries;
use App\Services\ConsoleColor;
use App\User;
use Illuminate\Console\Command;

class SendEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searches:send_entries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send entries to customers emails based on search';

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
	    foreach(User::all() as $user) {
	        $console->info('User: '.$user->name);
	        foreach($user->searches as $search){
                SearchSendEntries::dispatch( $search );
            }
	    }
    }
}
