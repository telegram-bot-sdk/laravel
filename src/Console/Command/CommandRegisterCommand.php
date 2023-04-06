<?php

namespace Telegram\Bot\Laravel\Console\Command;

use Illuminate\Support\Str;
use Telegram\Bot\Commands\CommandHandler;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Console\ConsoleBaseCommand;
use Telegram\Bot\Objects\BotCommand;

class CommandRegisterCommand extends ConsoleBaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:command:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register bot commands on Telegram';

    /**
     * Execute the console command.
     *
     * @throws TelegramSDKException
     */
    public function handle(): void
    {
        try {
            $this->registerCommands();
        } catch (TelegramSDKException $e) {
            $this->error($e->getMessage());

            return;
        }

        $bot = $this->bot()->config('bot');

        $this->info("[{$bot}] Bot: Commands Registered Successfully!");
    }

    /**
     * @throws TelegramSDKException
     */
    protected function registerCommands(): void
    {
        $handler = new CommandHandler($this->bot());
        $botCommands = $handler->getCommands();

        $commands = collect($botCommands)->map(static function ($command, $name) use ($handler): BotCommand {
            $command = $handler->getCommandBus()->resolveCommand($command);

            return BotCommand::make([
                // Can contain only lowercase English letters, digits and underscores.
                'command' => Str::lower($name),
                'description' => $command->getDescription(),
            ]);
        })->values()->all();

        $this->bot()->setMyCommands(['commands' => $commands]);
    }
}
