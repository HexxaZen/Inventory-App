<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Bahan;

class StokMenipisEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bahan;

    public function __construct(Bahan $bahan)
    {
        $this->bahan = $bahan;
    }

    public function broadcastOn()
    {
        return new Channel('stok-menipis');
    }

    public function broadcastAs()
    {
        return 'stokMenipis';
    }
}
