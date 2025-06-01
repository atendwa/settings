<?php

declare(strict_types=1);

use Atendwa\Settings\Facades\Settings;
use Atendwa\Settings\Models\Setting;
use Illuminate\Support\Collection;

if (! function_exists('setting')) {
    /**
     * @throws Throwable
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Settings::show($key) ?? $default;
    }
}

if (! function_exists('settings')) {
    /**
     * @param  array<string>  $keys
     *
     * @return Collection<int, non-empty-array<string, mixed>>
     *
     * @throws Throwable
     */
    function settings(array $keys): Collection
    {
        return Settings::fromKeys($keys);
    }
}

if (! function_exists('settingGroup')) {
    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     *
     * @throws Throwable
     */
    function settingGroup(string $group): Collection
    {
        return Settings::group($group);
    }
}

if (! function_exists('createSetting')) {
    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    function createSetting(array $attributes): Setting
    {
        return Settings::create($attributes);
    }
}

if (! function_exists('updateSetting')) {
    /**
     * @throws Throwable
     */
    function updateSetting(string $key, mixed $value): Setting
    {
        return Settings::update($key, $value);
    }
}

if (! function_exists('getSetting')) {
    /**
     * @throws Throwable
     */
    function getSetting(string $key): Setting
    {
        return Settings::get($key);
    }
}
