<?php

namespace Telegram\Bot\Laravel;

use Telegram\Bot\Api;
use Telegram\Bot\Bot;
use Telegram\Bot\BotManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Class TelegramServiceProvider.
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/telegram.php', 'telegram');

        $this->registerBindings();
    }

    /**
     * Boot the service provider.
     *
     * @return void
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
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/telegram.php');
            });
        }
    }

    /**
     * Setup the resource publishing groups.
     */
    protected function offerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
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
        $this->app->bind(
            BotManager::class,
            fn($app) => (new BotManager(config('telegram')))->setContainer($app)
        );
        $this->app->alias(BotManager::class, 'telegram');

        $this->app->bind(Bot::class, fn($app) => $app[BotManager::class]->bot());
        $this->app->alias(Bot::class, 'telegram.bot');

        $this->app->bind(Api::class, fn($app) => $app[Bot::class]->getApi());
        $this->app->alias(Api::class, 'telegram.api');
    }

    /**
     * Register the Artisan commands.
     */
    protected function registerCommands(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
            Console\Command\CommandListCommand::class,
            Console\Command\CommandMakeCommand::class,
            Console\Command\CommandRegisterCommand::class,
            Console\Webhook\WebhookInfoCommand::class,
            Console\Webhook\WebhookRemoveCommand::class,
            Console\Webhook\WebhookSetupCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [BotManager::class, Bot::class, Api::class, 'telegram', 'telegram.bot', 'telegram.api'];
    }
}
