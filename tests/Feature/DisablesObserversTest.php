<?php

use Plank\LaravelHush\Tests\Models\Document;
use Plank\LaravelHush\Tests\Observers\DocumentObserver;

it('throws an exception when creating a document with observers enabled', function () {
    Document::factory()->create();
})->throws(Exception::class, 'saving');

it('does not run or throw an exception when creating a document with the observer disabled', function () {
    Document::withoutObserver(DocumentObserver::class, function () {
        Document::factory()->create();
    });

    expect(Document::query()->count())->toBe(1);
});

it('restores the observers handlers once the closure has completed executions', function () {
    Document::withoutObserver(DocumentObserver::class, function () {
        Document::factory()->create();
    });

    expect(Document::query()->count())->toBe(1);

    expect(fn () => Document::factory()->create())->toThrow(Exception::class, 'saving');
});