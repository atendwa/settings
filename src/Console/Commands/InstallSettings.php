<?php

declare(strict_types=1);

namespace Atendwa\Settings\Console\Commands;

use Atendwa\Settings\Filament\Resources\SettingResource;
use Atendwa\Settings\Providers\SettingsServiceProvider;
use Atendwa\Support\Command;

class InstallSettings extends Command
{
    protected $signature = 'settings:install';

    protected $description = 'Install the settings package';

    protected string $provider = SettingsServiceProvider::class;

    protected array $resources = [SettingResource::class];
}
