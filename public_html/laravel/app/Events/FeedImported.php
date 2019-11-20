<?php

namespace App\Events;

use App\Models\Feed;
use App\Services\ConsoleColor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeedImported
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $feed;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Feed $feed)
    {
        //
	    $console=new ConsoleColor();
	    $console->info('Event FeedImported fires');
	    $this->feed=$feed;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
