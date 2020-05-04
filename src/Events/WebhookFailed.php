<?php

namespace Telegram\Bot\Laravel\Events;

use Telegram\Bot\Objects\Update;
use Throwable;

class WebhookFailed
{
    public const NAME = 'webhook.failed';
    public string $botname;
    public Update $update;
    public Throwable $exception;

    /**
     * Create a new event instance.
     *
     * @param string    $botname
     * @param Update    $update
     * @param Throwable $exception
     */
    public function __construct(string $botname, Update $update, Throwable $exception)
    {
        $this->botname = $botname;
        $this->update = $update;
        $this->exception = $exception;
    }
}
