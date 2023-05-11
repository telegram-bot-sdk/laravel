<?php

namespace Telegram\Bot\Laravel\Console\Webhook;

use Illuminate\Support\Str;
use Telegram\Bot\Bot;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Helpers\Util;
use Telegram\Bot\Laravel\Console\ConsoleBaseCommand;
use Throwable;

class WebhookSetupCommand extends ConsoleBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:webhook:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup webhook with Telegram Bot API';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->setupWebhook($this->bot());
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Setup Webhook.
     *
     *
     * @throws TelegramSDKException
     */
    protected function setupWebhook(Bot $bot): void
    {
        $this->comment("Setting webhook for [{$bot->config('bot')}] bot!");

        // Bot webhook config.
        $config = $bot->config('webhook', []);

        $secretToken = env('TELEGRAM_WEBHOOK_SECRET_TOKEN', $bot->config('token', ''));

        // Global webhook config merged with bot config with the latter taking precedence.
        $params = collect($bot->config('global.webhook'))
            ->except(['domain', 'path', 'controller', 'url'])
            ->merge($config)
            ->put('url', $this->webhookUrl($bot));

        if (filled($secretToken)) {
            $params->put('secret_token', Util::secretToken($secretToken));
        }

        if ($bot->setWebhook($params->all())) {
            $this->info('Success: Your webhook has been set!');

            return;
        }

        $this->error('Your webhook could not be set!');
    }

    protected function webhookUrl(Bot $bot): string
    {
        if (filled($bot->config('webhook.url'))) {
            return $bot->config('webhook.url');
        }

        return Str::replaceFirst('http:', 'https:', route('telegram.bot.webhook', [
            'bot' => $bot->config('bot'),
        ]));
    }
}
