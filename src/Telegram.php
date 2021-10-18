<?php

namespace Telegram\Bot\Laravel;

class Telegram
{
    /**
     * Indicates if Telegram Bot SDK Laravel routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = true;

    /**
     * Configure Telegram Bot SDK Laravel to not register its routes.
     *
     * @return static
     */
    public static function ignoreRoutes()
    {
        static::$registersRoutes = false;

        return new static;
    }
}
