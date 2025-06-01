<?php

declare(strict_types=1);

namespace Atendwa\Settings\Providers;

use Atendwa\Settings\Console\Commands\CreateSetting;
use Atendwa\Settings\Console\Commands\InstallSettings;
use Atendwa\Settings\Console\Commands\ShowSetting;
use Atendwa\Settings\Console\Commands\ShowSettingGroup;
use Atendwa\Settings\Console\Commands\UpdateSetting;
use Atendwa\Settings\Models\Setting;
use Atendwa\Settings\Policies\SettingPolicy;
use Atendwa\Settings\Settings;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/settings.php', 'settings');

        $this->app->singleton('atendwa-settings', fn (): Settings => new Settings());

        Gate::policy(Setting::class, SettingPolicy::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateSetting::class, ShowSetting::class, InstallSettings::class,
                ShowSettingGroup::class, UpdateSetting::class,
            ]);

            $this->publishes(
                [
                    __DIR__ . '/../../database/migrations' => database_path('migrations'),
                ],
                'migrations'
            );

            $this->publishes(
                [
                    __DIR__ . '/../../config/settings.php' => config_path('settings.php'),
                ],
                'config'
            );
        }
    }
}
