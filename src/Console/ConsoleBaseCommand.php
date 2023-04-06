<?php

namespace Telegram\Bot\Laravel\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Telegram\Bot\Bot;
use Telegram\Bot\BotManager;
use Telegram\Bot\Exceptions\TelegramSDKException;

class ConsoleBaseCommand extends Command
{
    public function __construct(protected BotManager $manager)
    {
        parent::__construct();
    }

    /**
     * @throws TelegramSDKException
     */
    public function bot(): Bot
    {
        return $this->manager->bot($this->argument('bot'));
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['bot', InputArgument::OPTIONAL, 'The bot name defined in config'],
        ];
    }
}
