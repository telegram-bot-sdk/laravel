<?php

namespace Telegram\Bot\Laravel\Events;

use Telegram\Bot\Objects\ResponseObject;
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
    public function __construct(public string $botname, public ResponseObject $update, public Throwable $exception)
    {
    }
}
