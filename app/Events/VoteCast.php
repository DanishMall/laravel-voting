<?php

namespace App\Events;
use App\Models\Candidate;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VoteCast implements ShouldBroadcast
{
    public $candidate;
    public $user;

    public function __construct(Candidate $candidate, User $user)
    {
        $this->candidate = $candidate;
        $this->user = $user;
    }

    public function broadcastOn(): array
    {
        return ['voting'];
    }
}
