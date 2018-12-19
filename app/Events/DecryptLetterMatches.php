<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DecryptLetterMatches implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $letterMatches;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($letterMatches)
    {
        $this->letterMatches = $letterMatches;
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            'letterMatches' => $this->letterMatches
        ];
    }

    public function broadcastAs()
    {
        return 'decodingLetterMatches';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('decoding');
    }
}
