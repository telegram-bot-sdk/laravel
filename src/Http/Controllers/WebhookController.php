<?php

namespace Telegram\Bot\Laravel\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Telegram\Bot\BotManager;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Events\WebhookFailed;
use Telegram\Bot\Laravel\Exceptions\WebhookException;

/**
 * Class WebhookController
 */
class WebhookController extends Controller
{
    /**
     * Listen to incoming update.
     *
     * @param  BotManager  $manager
     * @param  string      $token
     * @param  string      $bot
     *
     * @throws WebhookException|TelegramSDKException
     * @return mixed
     */
    public function __invoke(BotManager $manager, string $token, string $bot)
    {
        App::terminating(static function () use ($manager, $bot) {
          try {
              $manager->bot($bot)->listen(true);
          } catch (\Throwable $e) {
              $telegram = $manager->bot($bot);

              $telegram->getEventFactory()->dispatch(
                WebhookFailed::NAME,
                new WebhookFailed($bot, $telegram->getWebhookUpdate(), $e)
              );

              if (!$telegram->getEventFactory()->hasListeners(WebhookFailed::NAME)) {
                  throw WebhookException::failedToListenToUpdate($e->getMessage(), $e);
              }
          }
        });

        return response()->noContent();
    }
}
