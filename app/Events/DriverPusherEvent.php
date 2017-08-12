<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DriverPusherEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Only (!) Public members will be serialized to JSON and sent to Pusher
    **/
    public $message, $id, $drivers, $index, $order_id, $userLat, $userLong;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $id, $index, $drivers, $order_id, $userLat, $userLong)
    {
        $this->message = $message;
        $this->id = $id;
        $this->index = $index;
        $this->drivers = $drivers;
        $this->order_id = $order_id;
        $this->userLat = $userLat;
        $this->userLong = $userLong;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['channel-name-' . $this->id];
    }
}
