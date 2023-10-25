<?php

namespace Plank\LaravelHush\Tests\Observers;

class DocumentObserver
{
    public function saving()
    {
        throw new \Exception('saving');
    }
}
