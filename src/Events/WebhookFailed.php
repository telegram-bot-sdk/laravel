<?php

namespace Telegram\Bot\Laravel\Events;

use Telegram\Bot\Objects\Update;
use Throwable;

/**
 * Class WebhookFailed
 */
class WebhookFailed
{
    final public const NAME = 'webhook.failed';

    /**
     * Create a new event instance.
     */
    public function __construct(public string $botname, public Update $update, public Throwable $exception)
    {
    }
}
