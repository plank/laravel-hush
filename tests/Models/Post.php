<?php

namespace Plank\LaravelHush\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\LaravelHush\Concerns\HushesHandlers;
use Plank\LaravelHush\Tests\Database\Factories\PostFactory;
use Plank\LaravelHush\Tests\Models\Concerns\HasEventHandlersInTrait;

class Post extends Model
{
    use HasEventHandlersInTrait;
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
        return PostFactory::new();
    }
}
