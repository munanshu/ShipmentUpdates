<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ShipmentUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $shipment;

    // Constructor to pass data (e.g., shipment data)
    public function __construct($shipment)
    {
        Log::info('Recieved data:', ['data' => $this->shipment]);
        $this->shipment = $shipment;
    }

    // The channel the event will broadcast on
    public function broadcastOn()
    {
        return new Channel('shipmentupdates');
    }

    // Optionally, you can define what data you want to broadcast
    public function broadcastWith()
    {
        Log::info('Broadcasting data:', ['data' => $this->shipment]);
        return [
            'shipment' => $this->shipment,
        ];
    }
}
