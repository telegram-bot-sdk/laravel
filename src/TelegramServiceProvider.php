<?php

namespace Telegram\Bot\Laravel;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Api;
use Telegram\Bot\Bot;
use Telegram\Bot\BotManager;
use Telegram\Bot\Laravel\Console\Command\CommandListCommand;
use Telegram\Bot\Laravel\Console\Command\CommandMakeCommand;
use Telegram\Bot\Laravel\Console\Command\CommandRegisterCommand;
use Telegram\Bot\Laravel\Console\InstallCommand;
use Telegram\Bot\Laravel\Console\Webhook\WebhookInfoCommand;
use Telegram\Bot\Laravel\Console\Webhook\WebhookRemoveCommand;
use Telegram\Bot\Laravel\Console\Webhook\WebhookSetupCommand;

/**
 * Class TelegramServiceProvider.
 */
class TelegramServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/telegram.php', 'telegram');

        $this->registerBindings();
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->offerPublishing();
        $this->registerRoutes();
        $this->registerCommands();
    }

    /**
     * Register the routes.
     */
    protected function registerRoutes(): void
    {
        if (Telegram::$registersRoutes) {
            Route::group([
                'domain' => config('telegram.webhook.domain', null),
                'prefix' => config('telegram.webhook.path'),
            ], function (): void {
                $this->loadRoutesFrom(__DIR__.'/../routes/telegram.php');
            });
        }
    }

    /**
     * Setup the resource publishing groups.
     */
    protected function offerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/telegram.php' => config_path('telegram.php'),
        ], 'telegram-config');

        $this->publishes([
            __DIR__.'/../routes/telegram.php' => base_path('routes/telegram.php'),
        ], 'telegram-routes');
    }

    /**
     * Register bindings in the container.
     */
    protected function registerBindings(): void
    {
        $this->app->singleton(
            BotManager::class,
            fn ($app): BotManager => (new BotManager(config('telegram')))->setContainer($app)
        );
        $this->app->alias(BotManager::class, 'telegram');

        $this->app->bind(Bot::class, fn ($app) => $app[BotManager::class]->bot());
        $this->app->alias(Bot::class, 'telegram.bot');

        $this->app->bind(Api::class, fn ($app) => $app[Bot::class]->getApi());
        $this->app->alias(Api::class, 'telegram.api');
    }

    /**
     * Register the Artisan commands.
     */
    protected function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
            CommandListCommand::class,
            CommandMakeCommand::class,
            CommandRegisterCommand::class,
            WebhookInfoCommand::class,
            WebhookRemoveCommand::class,
            WebhookSetupCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [BotManager::class, Bot::class, Api::class, 'telegram', 'telegram.bot', 'telegram.api'];
    }
}
