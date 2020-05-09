<?php

namespace Telegram\Bot\Laravel\Console\Command;

use Telegram\Bot\Commands\CommandHandler;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Console\ConsoleBaseCommand;

class CommandListCommand extends ConsoleBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:command:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all bot commands';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->displayCommands($this->getCommandsList());
    }

    /**
     * @param array $data
     */
    protected function displayCommands(array $data): void
    {
        $this->table(['Command', 'Description'], $data);
    }

    /**
     * Get Commands List.
     *
     * @throws TelegramSDKException
     *
     * @return array
     */
    protected function getCommandsList(): array
    {
        $handler = new CommandHandler($this->bot());

        return collect($handler->getCommands())
            ->map(
                fn ($command, $name) => [$name, $handler->getCommandBus()->resolveCommand($command)->getDescription()]
            )->all();
    }
}
