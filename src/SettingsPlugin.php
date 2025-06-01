<?php

declare(strict_types=1);

namespace Atendwa\Settings;

use Atendwa\Filakit\Concerns\UsesPluginSetup;
use Atendwa\Filakit\Panel;
use Atendwa\Settings\Filament\Resources\SettingResource;
use Filament\Contracts\Plugin;

class SettingsPlugin implements Plugin
{
    use UsesPluginSetup;

    public function register(Panel|\Filament\Panel $panel): void
    {
        $panel->resources([SettingResource::class]);
    }
}
