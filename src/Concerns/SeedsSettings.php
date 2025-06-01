<?php

declare(strict_types=1);

namespace Atendwa\Settings\Concerns;

use Atendwa\Settings\Models\Setting;
use Illuminate\Support\Collection;

trait SeedsSettings
{
    /**
     * @param  Collection<int, mixed>  $settings
     */
    protected function seedSettings(Collection $settings): void
    {
        $settings = $settings->map(function ($setting): ?array {
            if (! is_array($setting)) {
                return null;
            }

            $encryptedValue = $setting['encrypted_value'] ?? null;

            if (filled($encryptedValue)) {
                $encryptedValue = encrypt($encryptedValue);
            }

            return [
                'key' => asString($setting['group']) . ':' . asString($setting['name']),
                'is_encrypted' => filled($encryptedValue),
                'type' => $setting['type'] ?? 'string',
                'encrypted_value' => $encryptedValue,
                'value' => $setting['value'] ?? null,
                'group' => $setting['group'],
                'name' => $setting['name'],
            ];
        });

        Setting::query()->insert($settings->filter()->all());
    }
}
