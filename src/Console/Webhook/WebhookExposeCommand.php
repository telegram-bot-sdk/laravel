<?php

namespace Telegram\Bot\Laravel\Console\Webhook;

use Exception;
use File;
use Symfony\Component\Process\Process;
use Telegram\Bot\Bot;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Console\ConsoleBaseCommand;
use Throwable;

class WebhookExposeCommand extends ConsoleBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:webhook:expose';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expose your webhook online';

    protected string $exposeUrl;
    protected string $initialBuffer = '';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->exposeWebhook($this->bot());
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param Bot $bot
     *
     * @throws TelegramSDKException
     * @throws Exception
     */
    protected function exposeWebhook(Bot $bot): void
    {
        $this->info("Exposing webhook for [{$bot->config('bot')}] bot!");
        $this->newLine();

        $process = $this->exposeProcess();
        $process->start();

        $this->info("Registering with the Expose Server");
        $process->waitUntil(function ($type, $output) {
            return $this->detectOnlineUrl($output);
        });

        $this->registerWebhook($bot);

        //Now we have everything registered. Remove the timeout.
        $process->setTimeout(0);
        $process->wait(function ($type, $buffer) {
            $this->processOutput($type, $buffer);
        });
    }

    /**
     * @throws Exception
     * @return Process
     */
    protected function exposeProcess()
    {
        return (new Process(
            [
                $this->exposeBinary(),
                '--quiet',
                '--no-interaction',
            ]
        ))
            ->setWorkingDirectory(base_path())
            ->setTimeout(10);
    }

    /**
     * @throws Exception
     * @return string
     */
    protected function exposeBinary()
    {
        $binary = base_path('vendor/bin/expose');

        if (File::exists($binary)) {
            return $binary;
        }

        throw new Exception('Unable to find the Expose binary');
    }

    protected function detectOnlineUrl($output): bool
    {
        $this->initialBuffer .= $output;

        if (preg_match('/---(.*?Expose-URL:\s+(.+))/is', $this->initialBuffer, $matches)) {
            $this->exposeUrl = trim($matches[2]);
            $this->info('Your access URLs are:');
            $this->comment($matches[1]);
            return true;
        }

        return false;
    }

    /**
     * @param Bot $bot
     *
     * @throws TelegramSDKException
     */
    protected function registerWebhook(Bot $bot)
    {
        $url = "{$this->exposeUrl}/{$bot->getToken()}/{$bot->config('bot')}";

        $this->info("Registering your webhook with Telegram.");
        if ($bot->setWebhook(compact('url'))) {
            $this->info("Success: Telegram is now sending updates to:");
            $this->comment("$url");
            return;
        }

        $this->error('Your webhook could not be registered!');
    }

    protected function processOutput($type, $buffer)
    {
        if ($type === Process::ERR) {
            $this->error($buffer);
            return;
        }

        $this->line($buffer);
    }
}
