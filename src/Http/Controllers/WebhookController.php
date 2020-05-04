<?php

namespace Telegram\Bot\Laravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Telegram\Bot\BotManager;
use Telegram\Bot\Laravel\Events\TelegramWebhookFailed;
use Telegram\Bot\Laravel\Exceptions\WebhookException;

class WebhookController extends Controller
{
    /**
     * Listen to incoming update.
     *
     * @param BotManager $manager
     * @param string     $token
     * @param string     $bot
     *
     * @return mixed
     */
    public function __invoke(BotManager $manager, string $token, string $bot)
    {
        App::terminating(static function () use ($manager, $bot) {
            try {
                $manager->bot($bot)->listen(true);
            } catch (\Throwable $e) {
                $event = new TelegramWebhookFailed($bot, $manager->bot($bot)->getWebhookUpdate(), $e);

                $manager->bot($bot)->getEventFactory()->dispatch(TelegramWebhookFailed::NAME, $event);

                event($event);

                throw WebhookException::failedToListenToUpdate($e->getMessage(), $e);
            }
        });

        return response()->noContent();
    }
}
