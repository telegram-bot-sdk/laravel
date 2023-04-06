<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Laravel\Http\Middleware\ValidateWebhook;

Route::group(['middleware' => ValidateWebhook::class], function () {

    Route::post('/{token}/{bot}', config('telegram.webhook.controller'))->name('telegram.bot.webhook');

//    # Longpolling method (manual).
//    Route::get('/{token}/updates/{bot?}', function ($bot = 'default') {
//         # This method will fetch updates,
//         # fire relevant events and,
//         # confirm we've received the updates with Telegram.
//
//         $updates = Telegram::bot($bot)->listen();
//
//         # You can do something with the fetched array of update objects.
//
//         # NOTE: You won't be able to fetch updates if a webhook is set.
//         # Remove webhook before using this method.
//    })->name('telegram.bot.updates');
});
