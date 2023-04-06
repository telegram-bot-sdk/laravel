<?php

namespace Telegram\Bot\Laravel\Console\Webhook;

use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Laravel\Console\ConsoleBaseCommand;
use Telegram\Bot\Objects\WebhookInfo;

class WebhookInfoCommand extends ConsoleBaseCommand
{
    /**
     * Default number of max connections as per API Docs.
     */
    final public const DEFAULT_MAX_CONNECTIONS = 40;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'telegram:webhook:info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the webhook information for your bots from Telegram Bot API';

    /**
     * The table headers for the command.
     */
    protected array $headers = [
        'name' => 'Name',
        'username' => 'Username',
        'pending_update_count' => 'Pending Updates',
        'max_connections' => 'Max Connections',
        'has_custom_certificate' => 'Certificate',
        'last_error_date' => 'Last Error Date',
        'last_error_message' => 'Last Error Message',
        'allowed_updates' => 'Allowed Updates',
        'url' => 'Webhook URL',
    ];

    /**
     * The columns to display when using the "compact" flag.
     */
    protected array $compactColumns = [
        'name',
        'username',
        'has_custom_certificate',
        'pending_update_count',
        'max_connections',
        'url',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->displayWebhookInfo($this->getWebhooks());
        } catch (Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get webhooks info to display.
     */
    protected function getWebhooks(): array
    {
        $bots = collect($this->manager->config('bots'));

        if (null !== $bot = $this->argument('bot')) {
            $bots = $bots->only($bot);

            if ($bots->isEmpty()) {
                $this->error("Bot [{$bot}] not configured");

                exit;
            }
        }

        $rows = $bots->map(fn ($bot, $name): ?array => $this->getWebhookInfo($name))->filter()->all();

        if ($this->option('reverse')) {
            $rows = array_reverse($rows);
        }

        return $this->pluckColumns($rows);
    }

    /**
     * Compile the webhook info into a displayable format.
     *
     *
     * @throws TelegramSDKException
     */
    protected function getWebhookInfo(string $name): ?array
    {
        $bot = $this->manager->bot($name);

        try {
            $profile = $bot->getMe();
            $webhook = $bot->getWebhookInfo();
        } catch (TelegramSDKException $e) {
            $error = ($e->getCode() === 401) ? $e->getMessage().' - Token not properly configured' : $e->getMessage();

            $this->line('');
            $this->warn(sprintf('Skipped bot [%s] due to Error: %s', $name, $error));
            $this->line('');

            return null;
        }

        return [
            'name' => $name,
            'username' => $profile->username,
            'pending_update_count' => $webhook->get('pending_update_count', 0),
            'max_connections' => $webhook->get('max_connections', static::DEFAULT_MAX_CONNECTIONS),
            'has_custom_certificate' => $this->presentBool($webhook->has_custom_certificate),
            'last_error_date' => $this->presentLastErrorDate($webhook),
            'last_error_message' => $webhook->last_error_message,
            'allowed_updates' => implode(', ', $webhook->get('allowed_updates', [])),
            'url' => $webhook->url,
        ];
    }

    /**
     * Present last error date unix time to readable.
     */
    protected function presentLastErrorDate(WebhookInfo $webhook): ?Carbon
    {
        return $webhook->last_error_date !== 0
            ? Carbon::createFromTimestamp($webhook->last_error_date, config('app.timezone'))
            : null;
    }

    /**
     * Present boolean value as tick or cross.
     */
    protected function presentBool($value): string
    {
        return $value ? '<info>✔</info>' : '<comment>✖</comment>';
    }

    /**
     * Remove unnecessary columns from the webhook.
     */
    protected function pluckColumns(array $webhooks): array
    {
        return array_map(fn($webhook): array => Arr::only($webhook, $this->getColumns()), $webhooks);
    }

    /**
     * Display the webhook information on the console.
     *
     *
     * @throws \JsonException
     */
    protected function displayWebhookInfo(array $data): void
    {
        if ($this->option('json')) {
            $this->line(json_encode(array_values($data), JSON_THROW_ON_ERROR));

            return;
        }

        $this->table($this->getHeaders(), $data);
    }

    /**
     * Get the table headers for the visible columns.
     */
    protected function getHeaders(): array
    {
        return Arr::only($this->headers, $this->getColumns());
    }

    /**
     * Get the column names to show (lowercase table headers).
     */
    protected function getColumns(): array
    {
        $availableColumns = array_keys($this->headers);

        if ($this->option('compact')) {
            return array_intersect($availableColumns, $this->compactColumns);
        }

        $columns = $this->option('columns');
        if ($columns) {
            return array_intersect($availableColumns, $this->parseColumns($columns));
        }

        return $availableColumns;
    }

    /**
     * Parse the column list.
     */
    protected function parseColumns(array $columns): array
    {
        $results = [];

        foreach ($columns as $column) {
            if (Str::contains($column, ',')) {
                $results = array_merge($results, explode(',', (string) $column));
            } else {
                $results[] = $column;
            }
        }

        return array_map('strtolower', $results);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'columns',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Columns to include in the webhook info table',
            ],
            ['compact', 'c', InputOption::VALUE_NONE, 'Only show limited columns'],
            ['json', null, InputOption::VALUE_NONE, 'Output the webhook list as JSON'],
            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the webhook list'],
        ];
    }
}
