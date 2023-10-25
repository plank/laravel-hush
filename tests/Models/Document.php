<?php

namespace Plank\LaravelHush\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\LaravelHush\Concerns\HushesHandlers;
use Plank\LaravelHush\Tests\Database\Factories\DocumentFactory;
use Plank\LaravelHush\Tests\Observers\DocumentObserver;

class Document extends Model
{
    use HasFactory;
    use HushesHandlers;

    protected $guarded = [];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    protected static function newFactory()
    {
        return DocumentFactory::new();
    }

    // Add the document observer
    public static function boot()
    {
        parent::boot();

        static::observe(DocumentObserver::class);

        static::deleting(function () {
            throw new \Exception('deleting');
        });
    }
}
