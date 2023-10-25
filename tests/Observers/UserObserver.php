<?php

namespace Plank\LaravelHush\Tests\Observers;

use Plank\LaravelHush\Tests\Models\User;

class UserObserver
{
    public function saving(User $user)
    {
        $user->name .= ' – handled saving in observer';
    }

    public function deleting()
    {
        throw new \Exception('deleting');
    }
}
