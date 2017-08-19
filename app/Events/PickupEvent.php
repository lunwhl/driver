<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PickupEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Only (!) Public members will be serialized to JSON and sent to Pusher
    **/
    public $message, $address, $delivery_id, $driver_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $address, $delivery_id, $driver_id)
    {
        $this->message = $message;
        $this->address = $address;
        $this->$delivery_id = $delivery_id;
        $this->$driver_id = $driver_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['channel-pickup-' . $this->driver_id];
    }
}
