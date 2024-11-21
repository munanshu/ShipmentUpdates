<?php

namespace App\Events;

use App\Models\Shipments;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ShipmentDelayedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $shipment;

    public function __construct(Shipments $shipment)
    {
        $this->shipment = $shipment;
    }

    public function broadcastOn()
    {
        return new Channel('shipment-status'); 
    }

    public function broadcastWith()
    {
        Log::info('Delayed Shipment data:', ['data' => $this->shipment]);
        return [
            'shipment_id' => $this->shipment->shipment_id,
            'status' => 'Delayed', 
        ];
    }
}