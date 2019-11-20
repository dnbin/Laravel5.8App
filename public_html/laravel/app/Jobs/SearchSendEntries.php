<?php

namespace App\Jobs;

use App\Models\Entry;
use App\Models\Search;
use App\Models\SearchSnapshot;
use App\Notifications\SendEntriesByMail;
use App\Services\ConsoleColor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SearchSendEntries implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Search $search ) {
        //
        $this->search = $search;
    }

    /**
     * Execute the job.
     *
     * @param ConsoleColor $console
     *
     * @return void
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function handle( ConsoleColor $console ) {
        try {
            //
            /** @var Search $search */

            // disable update_frequency functionality
            /*
            if ( ! empty( $this->search->hotel_offers_sent_at ) &&
                 (
                     Carbon::now()->diffInHours( $this->search->hotel_offers_sent_at ) < $this->search->update_frequency &&
                     Carbon::now()->isToday() &&
                     $this->search->hotel_offers_sent_at->isToday()
                 )
            ) {
                $console->warning( 'Update frequency: ' . $this->search->update_frequency );
                $console->warning( 'Hotel Search sent at: ' . $this->search->hotel_offers_sent_at->toDateTimeString() );
                $console->warning( 'Now: ' . Carbon::now()->toDateTimeString() );
                $console->warning( 'Diff in hours: ' . Carbon::now()->diffInHours( $this->search->hotel_offers_sent_at ) );
                throw new \Exception('Skip search mail sending. Wait when update_frequency interval elapsed');
            }
            */
            /** @var Search $search */
            //$console->info( 'Process search #' . $this->search->id . ' Update frequency: ' . $this->search->update_frequency . ' Last mail sent: ' . ( $this->search->hotel_offers_sent_at ? $this->search->hotel_offers_sent_at->toDateTimeString() : 'n/a' ) . ' Current Time: ' . Carbon::now()->toDateTimeString() );
            $console->info( 'Process search #' . $this->search->id . ' Last mail sent: ' . ( $this->search->hotel_offers_sent_at ? $this->search->hotel_offers_sent_at->toDateTimeString() : 'n/a' ) . ' Current Time: ' . Carbon::now()->toDateTimeString() );
            $entries = $this->search->entries()->wherePivot( 'sent_at', null )->get();
            $console->warning( 'Found ' . $entries->count() . ' to send..' );

            $this->search->hotel_offers_sent_at = Carbon::now();
            $this->search->saveQuietly();
            if ( $entries->isNotEmpty() ) {
                $this->search->user->notify( new SendEntriesByMail( $this->search, $entries ) );
                $console->warning( 'Mail has been sent' );
                foreach ( $entries as $entry ) {
                    /** @var Entry $entry */
                    $this->search->entries()->updateExistingPivot( $entry->id,
                        [
                            'sent_at'    => now(),
                            'sent_snapshot'=>$entry->makeHidden('pivot')
                        ]
                    );
                }

                // save snapshot of entries
                $snapshot=new SearchSnapshot();
                $snapshot->search_id=$this->search->id;
                $snapshot->snapshot=$entries;
                $snapshot->save();
                $console->warning('Snapshot has been saved.');

            }
        } catch ( \Exception $e ) {
            $console->error( $e->getMessage() );
        }
    }
}
