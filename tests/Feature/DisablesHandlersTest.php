<?php

use Plank\LaravelHush\Tests\Models\Concerns\HasEventHandlersInTrait;
use Plank\LaravelHush\Tests\Models\Post;
use Plank\LaravelHush\Tests\Models\User;
use Plank\LaravelHush\Tests\Observers\UserObserver;

it('throws an exception when creating a document with observers enabled', function () {
    User::factory()->create();
})->throws(Exception::class, 'saving');

it('can disable handlers for a specific event and doesnt throw a saving exception as a result', function () {
    User::withoutHandler('saving', function () {
        User::factory()->create();
    });
})->throws(Exception::class, 'creating');

it('can disable handlers for a specific event and classes and doesnt throw a saving exception as a result', function () {
    User::withoutHandler('saving', function () {
        User::factory()->create();
    }, [User::class]);
})->throws(Exception::class, 'creating');

it('can disable observer handlers for a specific event and doesnt throw a deleting exception as a result', function () {
    $user = User::withoutEvents(function () {
        return User::factory()->create();
    });

    expect($user)->not->toBeNull();

    User::withoutHandler('deleting', fn () => $user->delete(), [UserObserver::class]);

    expect(User::query()->count())->toBe(0);
});

it('can disable handlers for a specific event and trait and doesnt throw a deleting exception as a result', function () {
    $post = Post::withoutEvents(function () {
        return Post::factory()->create();
    });

    expect($post)->not->toBeNull();

    Post::withoutHandler('deleting', fn () => $post->delete(), [HasEventHandlersInTrait::class]);

    expect(Post::query()->count())->toBe(0);
});

it('throws a deleting exception when a traits handler is not disabled', function () {
    $post = Post::withoutEvents(function () {
        return Post::factory()->create();
    });

    expect($post)->not->toBeNull();

    $post->delete();

    expect(Post::query()->count())->toBe(0);
})->throws('deleting in trait');

it('restores the handlers once the closure has completed executions', function () {
    User::withoutHandlers(['saving', 'creating'], function () {
        User::factory()->create();
    });

    expect(User::query()->count())->toBe(1);

    expect(fn () => User::factory()->create())->toThrow(Exception::class, 'saving');
});

it('disabling handlers for events with nothing registered does not throw an error', function () {
    $user = User::withoutHandlers(['saving', 'creating', 'saved', 'created'], function () {
        return User::factory()->create();
    });

    expect($user)->not->toBeNull();
});
