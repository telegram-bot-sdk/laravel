<?php

namespace Telegram\Bot\Laravel;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Laravel\Artisan\WebhookCommand;

/**
 * Class TelegramServiceProvider.
 */
class TelegramServiceProvider extends ServiceProvider implements DeferrableProvider
{

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $source = __DIR__ . '/../config/telegram.php';

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('telegram.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('telegram');
        }

        $this->mergeConfigFrom($source, 'telegram');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerManager();
        $this->registerBindings();
        $this->commands('telegram.bot.commands.webhook');
    }

    /**
     * Register the manager class.
     *
     * @return void
     */
    protected function registerManager(): void
    {
        $this->app->bind(
            'telegram',
            fn($app) => (new BotsManager((array)$app['config']['telegram']))->setContainer($app)
        );

        $this->app->alias('telegram', BotsManager::class);
    }

    /**
     * Register the bindings.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->bind('telegram.bot', fn($app) => $app['telegram']->bot());
        $this->app->alias('telegram.bot', Api::class);
        $this->app->bind('telegram.bot.commands.webhook', WebhookCommand::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['telegram', 'telegram.bot', BotsManager::class, Api::class];
    }
}
