<?php

declare(strict_types=1);

namespace Atendwa\Settings\Concerns;

use Atendwa\Settings\Facades\Settings;
use Carbon\Carbon;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

trait HasConsoleSettingsInputFields
{
    protected function getValueInput(string $type): string|bool
    {
        return match ($type) {
            'boolean' => confirm('Is this setting true?'),
            default => text(
                'Value',
                $this->getPlaceholder($type),
                required: true,
                validate: Settings::valueRules($type, true),
                hint: $this->getHint($type),
                transform: function (string $value) use ($type) {
                    if ($type === 'numeric') {
                        return (float) $value;
                    }

                    return match ($value) {
                        'array' => str($value)->explode(',')->map(fn (string $value): string => trim($value))->filter()->toJson(),
                        'date' => Carbon::createFromTimestamp(Carbon::parse($value)->getTimestamp())->format('Y-m-d'),
                        'time' => Carbon::createFromTimestamp(Carbon::parse($value)->getTimestamp())->format('H:i:s'),
                        default => $value,
                    };
                }
            ),
        };
    }

    private function getPlaceholder(string $type): string
    {
        $now = now();

        return match ($type) {
            'date' => $now->format('d-m-Y'),
            'time' => $now->format('H:i'),
            'color' => fake()->hexColor(),
            'array' => '[foo,bar,baz]',
            'numeric' => '123',
            default => '',
        };
    }

    private function getHint(string $type): string
    {
        return match ($type) {
            'array' => 'Wrapped in square brackets',
            'color' => 'Hex code (e.g., #ffffff)',
            'time' => 'Format: HH:MM (24-hour)',
            'date' => 'Format: DD-MM-YYYY',
            'bool', 'boolean' => '1 or 0',
            default => '',
        };
    }
}
