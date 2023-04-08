<?php

return [
    'facade' => Telegram\Bot\Laravel\Facades\Telegram::class,

    'classes' => [
        \Telegram\Bot\BotManager::class,
        \Telegram\Bot\Api::class => [
            'setContainer',
            'getWebhookUpdates',
        ],
        \Telegram\Bot\Commands\CommandBus::class => [
            'getTelegram',
            'setTelegram',
        ],
    ],

    'excludedMethods' => [],
];
