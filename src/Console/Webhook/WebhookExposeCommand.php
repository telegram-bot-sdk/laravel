<?php

namespace Telegram\Bot\Laravel\Console\Webhook;

use Exception;
use File;
use Symfony\Component\Process\Process;
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

    protected string $exposeUrl = '';
    protected string $initialBuffer = '';
    protected Process $process;
    protected bool $shouldRestart = false;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->exposeWebhook();
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @throws TelegramSDKException
     * @throws Exception
     * @return int|mixed
     */
    protected function exposeWebhook()
    {
        $this->info("Exposing webhook for [{$this->bot()->config('bot')}] bot!");
        $this->newLine();
        $this->info("Attempting to register with the Expose Server");

        $this->process = $this->exposeProcess();
        $this->process->start();
        $this->process->waitUntil(function ($type, $output) {
            return $this->determineStartupStatus($output);
        });

        if ($this->shouldRestart) {
            return $this->restartProcess();
        }

        $this->registerWebhook();

        return $this->showRequests();
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
            ->setTimeout(20);
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

    /**
     * @param $output
     *
     * @throws Exception
     * @return bool
     */
    protected function determineStartupStatus($output)
    {
        $this->initialBuffer .= $output;

        return ($this->exposeTokenError() || $this->detectOnlineUrl());
    }

    /**
     * @throws Exception
     * @return bool
     */
    protected function exposeTokenError()
    {
        $this->shouldRestart = false;

        if (preg_match('/Authentication failed/i', $this->initialBuffer)) {
            $this->error("It appears you have not set your Expose token yet or it is invalid!");
            $this->comment("Visit https://beyondco.de/login to register and generate your Expose token.");
            $this->comment("This step only ever needs to be done ONCE.");
            $this->comment("Once you have your token you can continue!");

            $this->tokenSettingProcess($this->ask("Please enter/paste your Expose authentication token"))
                ->run(function ($type, $buffer) {
                    return $this->processOutput($type, $buffer);
                });

            return $this->shouldRestart = true;
        }

        return false;
    }

    /**
     * @param string $token
     *
     * @throws Exception
     * @return Process
     */
    private function tokenSettingProcess(string $token)
    {
        return (new Process(
            [
                $this->exposeBinary(),
                'token',
                $token,
            ]
        ));
    }

    /**
     * @param $type
     * @param $buffer
     */
    protected function processOutput($type, $buffer)
    {
        if ($type === Process::ERR) {
            $this->error($buffer);
            return;
        }

        $this->line($buffer);
    }

    /**
     * @return bool
     */
    protected function detectOnlineUrl(): bool
    {
        if (preg_match('/---\n(.*?Expose-URL:\s+(.+?)\n)/is', $this->initialBuffer, $matches)) {
            $this->exposeUrl = trim($matches[2]);
            $this->newLine();
            $this->info('Success! Your access URLs are:');
            $this->comment($matches[1]);
            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     * @return int|mixed
     */
    protected function restartProcess()
    {
        $this->process->stop();
        $this->initialBuffer = '';
        $this->alert('Restarting the command.');
        return $this->exposeWebhook();
    }

    /**
     * @throws TelegramSDKException
     */
    protected function registerWebhook()
    {
        $url = "{$this->exposeUrl}/{$this->bot()->getToken()}/{$this->bot()->config('bot')}";

        $this->info("Attempting to register your webhook with Telegram.");
        if ($this->bot()->setWebhook(compact('url'))) {
            $this->newLine();
            $this->info("Success! Telegram is now sending updates to:");
            $this->comment("$url");
            return;
        }

        $this->error('Your webhook could not be registered!');
    }

    /**
     * @return int
     */
    protected function showRequests()
    {
        //Now we have everything registered.
        //Remove the timeout and show inbound connections as they occur.
        return $this->process
            ->setTimeout(0)
            ->wait(function ($type, $buffer) {
                $this->processOutput($type, $buffer);
            });
    }
}
