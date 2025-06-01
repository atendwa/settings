<?php

declare(strict_types=1);

namespace Atendwa\Settings\Console\Commands;

use Atendwa\Settings\Concerns\HasConsoleSettingsInputFields;
use Atendwa\Settings\Facades\Settings;
use Illuminate\Console\Command;
use Throwable;

class UpdateSetting extends Command
{
    use HasConsoleSettingsInputFields;

    protected $signature = 'setting:update {key} {value?}';

    protected $description = 'Update a setting';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $key = asString($this->argument('key'));
        $value = $this->argument('value');
        $setting = null;

        if (blank($value)) {
            $setting = Settings::get($key);

            $value = $this->getValueInput($setting->type());
        }

        Settings::update($key, $value, $setting);

        $this->info('Setting: ' . $key . ' updated!');
    }
}
