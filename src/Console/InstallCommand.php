<?php

namespace Telegram\Bot\Laravel\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:install {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Telegram Bot SDK scaffolding';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        // Publish config
        $this->callSilent('vendor:publish', ['--tag' => 'telegram-config', '--force' => true]);
        $this->installScaffolding();
    }

    /**
     * Install the Telegram Bot SDK Scaffolding into the application.
     */
    protected function installScaffolding(): void
    {
        // Directories
        (new Filesystem)->ensureDirectoryExists(app_path('Listeners'));
        (new Filesystem)->ensureDirectoryExists(app_path('Telegram/Commands'));

        // Scaffolding files.
        copy(__DIR__.'/../../stubs/app/Listeners/ProcessInboundPhoto.php',
            app_path('Listeners/ProcessInboundPhoto.php'));
        copy(__DIR__.'/../../stubs/app/Telegram/Commands/Start.php', app_path('Telegram/Commands/Start.php'));

        $this->replaceInFile(
            "// 'start' => App\Telegram\Commands\Start::class",
            "'start' => App\Telegram\Commands\Start::class",
            config_path('telegram.php')
        );

        $this->replaceInFile(
            "'message'                  => [],",
            "'message'                  => [],\n\n\t\t\t\t'message.photo' => [
            \t\t\App\Listeners\ProcessInboundPhoto::class,\n\t\t\t\t],\n",
            config_path('telegram.php')
        );

        $this->setBotTokenVariableInEnvironmentFile();

        $this->line('');
        $this->info('Telegram Bot SDK scaffolding installed successfully.');
        $this->comment('Please add your Telegram Bot API Token & Hostname for Webhook in your ".env" file.');
        $this->comment('Once configured, run "php artisan telegram:webhook:setup"');
    }

    /**
     * Set the bot token var in the environment file.
     */
    protected function setBotTokenVariableInEnvironmentFile(): void
    {
        if (env('TELEGRAM_BOT_TOKEN') !== null) {
            return;
        }

        (new Filesystem)->append(base_path('.env'), "\nTELEGRAM_BOT_TOKEN=\nTELEGRAM_WEBHOOK_DOMAIN=");
        (new Filesystem)->append(base_path('.env.example'), "\nTELEGRAM_BOT_TOKEN=");
    }

    /**
     * Replace a given string within a given file.
     */
    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
