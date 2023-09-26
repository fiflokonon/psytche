<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use function Symfony\Component\Translation\t;

class PaymentCallbackEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $success;
    public $call_id;
    /**
     * Create a new event instance.
     */
    public function __construct(string $call_id, bool $success)
    {
        $this->call_id = $call_id;
        $this->success = $success;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("payment-$this->call_id"),
        ];
    }

    public function broadcastAs()
    {
        return 'PaymentCallbackEvent';
    }

    public function broadcastWith()
    {
        return ['success' => true];
    }
}
