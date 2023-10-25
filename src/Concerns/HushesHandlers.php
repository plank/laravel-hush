<?php

namespace Plank\LaravelHush\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use ReflectionFunction;

/**
 * @mixin Model
 */
trait HushesHandlers
{
    /**
     * Disable a specific Model Observer during the execution of a callback. You can also
     * pass in a class name where observers have been registered on the model statically.
     *
     * @param  class-string  $class
     * @return mixed
     */
    public static function withoutObserver(string $class, Closure $callback)
    {
        return static::withoutObservers([$class], $callback);
    }

    /**
     * Disable an array of Model Observers during the execution of a callback. You can also
     * pass in a class name where observers have been registered on the model statically.
     *
     * @param  array<class-string>  $classes
     * @return mixed
     */
    public static function withoutObservers(array $classes, Closure $callback)
    {
        /** @var Model $instance */
        $instance = new static;

        /** @var Dispatcher $dispatcher */
        $dispatcher = static::getEventDispatcher();
        $listeners = $dispatcher->getRawListeners();

        $disabled = [];

        foreach ($instance->getObservableEvents() as $event) {
            $dispatchedEvent = 'eloquent.'.$event.': '.static::class;
            $handlers = $listeners[$dispatchedEvent] ?? [];

            if (empty($handlers)) {
                continue;
            }

            $disabled[$dispatchedEvent] = $handlers;
            $dispatcher->forget($dispatchedEvent);

            $filtered = static::filteredHandlers($handlers, $classes);

            foreach ($filtered as $handler) {
                $dispatcher->listen($dispatchedEvent, $handler);
            }
        }

        $result = $callback();

        foreach ($disabled as $dispatchedEvent => $handlers) {
            $dispatcher->forget($dispatchedEvent);

            foreach ($handlers as $handler) {
                $dispatcher->listen($dispatchedEvent, $handler);
            }
        }

        return $result;
    }

    /**
     * Mute a specific observable model event. You can also pass in class names of specific
     * observers or a classes where handlers have been registered on the model statically.
     *
     * @param  array<class-string>  $classes
     * @return mixed
     */
    public static function withoutHandler(string $event, Closure $callback, array $classes = [])
    {
        static::withoutHandlers([$event], $callback, $classes);
    }

    /**
     * Mute specific observable model events. You can also pass in class names of specific
     * observers or a classes where handlers have been registered on the model statically.
     *
     * @param  array<string>  $events
     * @param  array<class-string>  $classes
     * @return mixed
     */
    public static function withoutHandlers(array $events, Closure $callback, array $classes = [])
    {
        // Ensure the model is booted and initialized, so its events have been registered already
        new static;

        /** @var Dispatcher $dispatcher */
        $dispatcher = static::getEventDispatcher();
        $listeners = $dispatcher->getRawListeners();

        $disabled = [];

        foreach ($events as $event) {
            $dispatchedEvent = 'eloquent.'.$event.': '.static::class;
            $handlers = $listeners[$dispatchedEvent] ?? [];

            if (empty($handlers)) {
                continue;
            }

            $disabled[$dispatchedEvent] = $handlers;
            $dispatcher->forget($dispatchedEvent);

            $filtered = $classes ? static::filteredHandlers($handlers, $classes) : [];

            foreach ($filtered as $handler) {
                $dispatcher->listen($dispatchedEvent, $handler);
            }
        }

        $result = $callback();

        foreach ($disabled as $dispatchedEvent => $handlers) {
            $dispatcher->forget($dispatchedEvent);

            foreach ($handlers as $handler) {
                $dispatcher->listen($dispatchedEvent, $handler);
            }
        }

        return $result;
    }

    protected static function filteredHandlers(array $handlers, array $classes)
    {
        return array_filter($handlers, function ($handler) use ($classes) {
            if (is_string($handler)) {
                return ! str($handler)->contains($classes);
            }

            if (is_callable($handler)) {
                $refl = new ReflectionFunction($handler);
                $class = $refl->getClosureScopeClass();

                return $class === null || ! str($class->getName())->contains($classes);
            }

            return true;
        });
    }
}
