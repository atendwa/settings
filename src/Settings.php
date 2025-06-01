<?php

declare(strict_types=1);

namespace Atendwa\Settings;

use Atendwa\Settings\Models\Setting;
use Atendwa\Support\Rules\JsonObjectOrArray;
use Illuminate\Support\Collection;
use Throwable;
use Validator;

class Settings
{
    /**
     * @throws Throwable
     */
    public function show(string $key): mixed
    {
        if (config('settings.block')) {
            return null;
        }

        return $this->get($key)->getAttribute('data');
    }

    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     *
     * @throws Throwable
     */
    public function group(string $group): Collection
    {
        $builder = Setting::query()->where('group', $group);

        throw_if($builder->doesntExist(), 'Setting group:' . $group . ' not found!');

        return $builder->get()->map(fn (Setting $setting) => [
            $setting->string('key') => $setting->getAttribute('data'),
        ]);
    }

    /**
     * @param  array<string>  $keys
     *
     * @return Collection<int, non-empty-array<string, mixed>>
     *
     * @throws Throwable
     */
    public function fromKeys(array $keys): Collection
    {
        $keys = array_unique($keys);

        $column = 'key';

        $settings = Setting::query()->whereIn($column, $keys)->get();

        $missing = collect($keys)->diff(arrayOfStrings($settings->pluck($column)->all()));

        throw_if($missing->isNotEmpty(), 'Settings:' . json_encode($missing) . ' not found');

        return $settings->map(fn (Setting $setting) => [
            $setting->string($column) => $setting->getAttribute('data'),
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @throws Throwable
     */
    public function create(array $attributes): Setting
    {
        $validator = Validator::make($attributes, [
            'value' => $this->valueRules(asString($attributes['type']), true)['value'],
            'type' => 'required|string|in:array,boolean,color,date,numeric,string,time',
            'group' => 'required|max:255|string',
            'is_encrypted' => 'required|boolean',
            'name' => 'required|max:255|string',
        ]);

        throw_if($validator->fails(), validatorErrorString($validator));

        return Setting::query()->create(asMixedArray($validator->validated()));
    }

    /**
     * @return array<string, mixed>
     */
    public function valueRules(string $type, bool $creating = false): array
    {
        $rules = [
            'date' => ['required', 'regex:/^(0[1-9]|[12][0-9]|3[01])-(0[1-9]|1[0-2])-\d{4}$/', 'date'],
            'time' => ['required', 'regex:/^(?:[0-2]\d|2[0-9]):[0-5]\d$/'],
            'color' => ['required', 'string', 'size:7', 'starts_with:#'],
            'string' => ['required', 'string', 'max:255'],
            'boolean' => ['required', 'boolean'],
            'numeric' => ['required', 'numeric'],
            'integer' => ['required', 'numeric'],
            'float' => ['required', 'numeric'],
            'bool' => ['required', 'boolean'],
        ];

        $rules = match ($creating) {
            true => array_merge($rules, ['array' => ['required', new JsonObjectOrArray()]]),
            false => array_merge($rules, ['array' => ['required', 'array']]),
        };

        return ['value' => $rules[$type]];
    }

    /**
     * @throws Throwable
     */
    public function update(
        string $key,
        mixed $value,
        ?Setting $setting = null
    ): Setting {
        if (blank($setting)) {
            $setting = $this->get($key);
        }

        $key = [true => 'encrypted_value', false => 'value'][$setting->getAttribute('is_encrypted')];

        $validator = Validator::make([$key => $value], [$key => $this->valueRules($setting->type())['value']]);

        throw_if($validator->fails(), validatorErrorString($validator));

        if (is_array($value) && $setting->getAttribute('is_encrypted')) {
            $value = json_encode($value);
        }

        $setting->update([$key => $value]);

        return $setting->refresh();
    }

    /**
     * @throws Throwable
     */
    public function get(string $key): Setting
    {
        $setting = Setting::query()->firstWhere('key', $key);

        throw_if(! $setting, 'Setting: ' . $key . ' not Found');

        return $setting;
    }
}
