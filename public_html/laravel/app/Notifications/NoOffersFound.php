<?php

namespace App\Notifications;

use App\Models\Entry;
use App\Models\Search;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NoOffersFound extends Notification {
    use Queueable;

    protected $search;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( Search $search ) {
        //
        $this->search  = $search;
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


        $mail_message = ( new MailMessage )
            ->subject( 'Hotel search #'.$this->search->id.'. No hotels found ' )
            ->greeting( 'Hello, ' . $notifiable->name )
            ->line( 'There is no match for your search at this time.' )
            ->line('But we will keep searching for matching offers.');

        $feed_id=1;//expedia
        $avgprice=DB::table('entries')->selectRaw('AVG(price) as amount,currency')
                    ->where('feed_id',$feed_id)
                    ->where('city','like',rawurldecode($this->search->city->name));
        if($this->search->hotel_class> 0 && $this->search->hotel_class<=5){
            $avgprice=$avgprice->where('star_rating','>=',$this->search->hotel_class);
        }

        $avgprice=$avgprice->groupBy('currency')->get();// get avg price based on expedia feed
        if(!empty($avgprice)){
            $mail_message->line('The average room rate for your desired hotels is '.round($avgprice->first()->amount,0).' '.$avgprice->first()->currency);
        }

        $mail_message->line('You may want to adjust your budget close to the average price, change your dates or other conditions.');
        //$mail_message->action( 'View latest search filtered hotels on the website', route( 'searches.search.view', [ 'id' => $this->search->id ] ) );

        return $mail_message;

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
