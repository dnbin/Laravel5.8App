<?php

namespace App\Notifications;

use App\Models\Entry;
use App\Models\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

class SendEntriesByMail extends Notification {
    use Queueable;

    protected $search;
    protected $entries;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( Search $search, Collection $entries ) {
        //
        $this->search  = $search;
        $this->entries = $entries;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via( $notifiable ) {
        return [ 'mail' ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail( $notifiable ) {
        /*
        $mail_message = ( new MailMessage )
            ->subject( 'Hotel search #'.$this->search->id.'. Found ' . $this->entries->count() . ' hotels: ' )
            ->greeting( 'Hello, ' . $notifiable->name )
            ->line( 'Here is a list of filtered hotels for your search.' );
        foreach ( $this->entries as $index => $entry ) {
             // @var Entry $entry
            $mail_message->line( ( $index + 1 ) . '. ' . $entry->title . ' | Price: ' . $entry->price . ' ' . $entry->currency );
        }
        $mail_message->action( 'View search filtered hotels on the website', route( 'searches.search.view', [ 'id' => $this->search->id ] ) )
                     ->line( 'Thank you for using our application!' );

        return $mail_message;
        */

        return (new MailMessage)
            ->subject('Hotel search #'.$this->search->id.'. Found ' . $this->entries->count() . ' new hotel offers.')
            ->view(
            'emails.hotel_offers', ['search' => $this->search,'entries'=>$this->entries,'notifiable'=>$notifiable]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray( $notifiable ) {
        return [
            //
        ];
    }
}
