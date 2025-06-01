<?php

declare(strict_types=1);

namespace Atendwa\Settings\Console\Commands;

use Atendwa\Settings\Facades\Settings;
use Illuminate\Console\Command;
use Throwable;

class ShowSettingGroup extends Command
{
    protected $signature = 'setting:group {group}';

    protected $description = 'Display the values of settings by their a group name';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $rows = Settings::group($this->argument('group'))->collapse();

        $this->table(['Key', 'Value'], $rows->map(fn ($value, $key): array => [$key, json_encode($value)])->values()->all());
    }
}
