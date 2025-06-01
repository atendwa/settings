<?php

declare(strict_types=1);

namespace Atendwa\Settings\Console\Commands;

use Atendwa\Settings\Facades\Settings;
use Illuminate\Console\Command;
use Throwable;

class ShowSetting extends Command
{
    protected $signature = 'setting:show {key}';

    protected $description = 'Display the value(s) of one or more settings by key (comma-separated)';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        /** @var string $key */
        $key = $this->argument('key');
        $rows = Settings::fromKeys(explode(',', asString($key)))->collapse();

        $this->table(['Key', 'Value'], $rows->map(fn ($value, $key): array => [$key, json_encode($value)])->values()->all());
    }
}
