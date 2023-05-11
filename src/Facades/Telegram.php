<?php

namespace Telegram\Bot\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Telegram\Bot\BotManager;
use Telegram\Bot\Testing\BotFake;

/**
 * @see \Telegram\Bot\BotManager
 * @see \Telegram\Bot\Bot
 * @see \Telegram\Bot\Api
 */
class Telegram extends Facade
{
    /**
     * Replace the bound instance with a fake.
     */
    public static function fake(array $responses = []): BotFake
    {
        return tap(new BotFake($responses), static fn ($fake) => static::swap($fake));
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return BotManager::class;
    }
}
