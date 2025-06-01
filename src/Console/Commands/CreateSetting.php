<?php

declare(strict_types=1);

namespace Atendwa\Settings\Console\Commands;

use Atendwa\Settings\Concerns\HasConsoleSettingsInputFields;
use Atendwa\Settings\Facades\Settings;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;
use Throwable;

class CreateSetting extends Command
{
    use HasConsoleSettingsInputFields;

    protected $signature = 'setting:create';

    protected $description = 'Create a new setting';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $attributes = [];

        $attributes['name'] = text('Setting Name', required: true, validate: ['name' => 'required|max:255|string']);
        $attributes['group'] = text('Setting Group', required: true, validate: ['group' => 'required|max:255|string']);

        $attributes['type'] = (string) select(
            'Type',
            options: ['array', 'boolean', 'color', 'date', 'numeric', 'string', 'time'],
            default: 'string'
        );

        $attributes['value'] = $this->getValueInput($attributes['type']);

        $attributes['is_encrypted'] = $this->confirm('Is this setting encrypted?');

        $setting = Settings::create($attributes);

        $this->info('Setting created successfully.');

        $data = $setting->getAttribute('data');

        if (blank($data)) {
            $data = 'null';
        }

        if ($setting->type() === 'array') {
            $data = json_encode($data);
        }

        table(headers: ['key', 'group', 'name', 'type', 'value', 'is_encrypted'], rows: [
            [
                $setting->string('key'),
                $setting->string('group'),
                $setting->string('name'),
                $setting->string('type'),
                asString($data),
                $setting->string('is_encrypted') ? 'true' : 'false',
            ],
        ]);
    }
}
