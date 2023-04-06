<?php

namespace Telegram\Bot\Laravel\Exceptions;

use Throwable;
use Telegram\Bot\Exceptions\TelegramSDKException;

class WebhookException extends TelegramSDKException
{
    public static function failedToListenToUpdate($message, Throwable $e): self
    {
        return new static($message, 0, $e);
    }
}
