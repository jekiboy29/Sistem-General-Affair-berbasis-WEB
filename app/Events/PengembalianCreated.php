<?php

namespace App\Events;

use App\Models\Peminjaman;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PengembalianCreated implements ShouldBroadcast
{
    use SerializesModels;

    public $peminjaman;

    public function __construct(Peminjaman $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function broadcastOn()
    {
        return new Channel('pengembalian-channel');
    }

    public function broadcastAs()
    {
        return 'pengembalian.created';
    }
}
