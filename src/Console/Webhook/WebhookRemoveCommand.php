<?php

namespace Telegram\Bot\Laravel\Console\Webhook;

use Telegram\Bot\Bot;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Console\ConsoleBaseCommand;
use Throwable;

class WebhookRemoveCommand extends ConsoleBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:webhook:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the webhook from the Telegram Bot API';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->removeWebhook($this->bot());
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Remove Webhook.
     *
     *
     * @throws TelegramSDKException
     */
    protected function removeWebhook(Bot $bot): void
    {
        if ($this->confirm("Are you sure you want to remove the webhook for [{$bot->config('bot')}] bot?")) {
            $this->info('Removing webhook...');

            if ($bot->deleteWebhook()) {
                $this->info('Webhook removed successfully!');

                return;
            }

            $this->error('Webhook removal failed');
        }
    }
}
