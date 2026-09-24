<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\User;

class RoleAssigned
{
    use Dispatchable, SerializesModels;

    public $user;
    public $roleName;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string $roleName
     */
    public function __construct(User $user, string $roleName)
    {
        $this->user = $user;
        $this->roleName = $roleName;
    }
}
