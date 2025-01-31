<?php

namespace Plank\LaravelHush\Tests\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Plank\LaravelHush\Concerns\HushesHandlers;
use Plank\LaravelHush\Tests\Database\Factories\UserFactory;
use Plank\LaravelHush\Tests\Observers\UserObserver;

class User extends Authenticatable
{
    use HasFactory;
    use HushesHandlers;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function () {
            throw new Exception('creating');
        });

        static::saving(function () {
            throw new Exception('saving');
        });

        static::observe(UserObserver::class);
    }
}
