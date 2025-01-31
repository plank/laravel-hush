<?php

namespace Plank\LaravelHush\Tests\Models\Concerns;

trait HasEventHandlersInTrait
{
    public static function bootHasEventHandlersInTrait(): void
    {
        static::deleting(function () {
            throw new \Exception('deleting in trait');
        });
    }
}
