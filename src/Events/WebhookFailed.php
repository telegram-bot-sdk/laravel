<?php

namespace Telegram\Bot\Laravel\Events;

use Telegram\Bot\Objects\Update;
use Throwable;

/**
 * Class WebhookFailed
 */
class WebhookFailed
{
    public const NAME = 'webhook.failed';

    public string $botname;

    public Update $update;

    public Throwable $exception;

    /**
     * Create a new event instance.
     */
    public function __construct(string $botname, Update $update, Throwable $exception)
    {
        $this->botname = $botname;
        $this->update = $update;
        $this->exception = $exception;
    }
}
