<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VerifyMailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $users_ids;
    private $data;
    
    public function __construct(array $users_ids, array $data = [])
    {
        $this->users_ids = $users_ids;
        $this->data = $data;
    }

    public function getUsers()
    {
        return $this->users_ids;
    }

    public function getData()
    {
        return $this->data;
    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
