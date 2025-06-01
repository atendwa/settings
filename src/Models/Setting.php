<?php

declare(strict_types=1);

namespace Atendwa\Settings\Models;

use Atendwa\Settings\Events\SettingCreated;
use Atendwa\Settings\Events\SettingUpdated;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Throwable;

class Setting extends Model
{
    protected $guarded = ['id'];

    /**
     * @throws Throwable
     */
    public function isFalse(): bool
    {
        return ! $this->isTrue();
    }

    /**
     * @throws Throwable
     */
    public function isTrue(): bool
    {
        throw_if(! in_array($this->type(), ['bool', 'boolean']), 'Type is not boolean');

        return str($this->data)->toBoolean();
    }

    public static function getColumn(TextColumn $textColumn): TextColumn
    {
        return $textColumn
            ->wrap()->badge(fn (Setting $setting): bool => $setting->useBadge())
            ->formatStateUsing(function (Setting $setting) {
                $data = $setting->getAttribute('data');

                if ($setting->type() === 'array' && is_array($data)) {
                    return collect($data)->implode(', ');
                }

                $data = asString($data);

                return match ($setting->type()) {
                    'bool', 'boolean' => [true => 'True', false => 'False'][$setting->isTrue()],
                    'date' => Carbon::parse($data)->toFormattedDayDateString(),
                    'time' => Carbon::parse($data)->toTimeString(),
                    default => $data,
                };
            })->color(function (Setting $setting): ?string {
                return match ($setting->getAttribute('type')) {
                    'bool', 'boolean' => [true => 'success', false => 'danger'][$setting->data],
                    'array' => 'gray',
                    default => null,
                };
            });
    }

    /**
     * @throws Throwable
     */
    public static function getInput(Form $form): Component
    {
        $model = $form->getModelInstance();

        throw_if(! $model instanceof Setting, 'Model is not an instance of Setting');

        $input = match ($model->getAttribute('type')) {
            'array' => TagsInput::make('value')->color('gray')->formatStateUsing(fn () => $model->getAttribute('data')),
            'time' => TimePicker::make('value')->rules('date_format:H:i:s'),
            default => TextInput::make('value')->maxValue(255)->string(),
            'int', 'integer' => TextInput::make('value')->integer(),
            'date' => DatePicker::make('value')->date(),
            'bool', 'boolean' => Toggle::make('value'),
            'color' => ColorPicker::make('value'),
        };

        return $input->required();
    }

    public function useBadge(): bool
    {
        return str($this->type())->is(['bool', 'boolean']);
    }

    public function string(string $attribute): string
    {
        return asString($this->getAttribute($attribute));
    }

    public function type(): string
    {
        return $this->string('type');
    }

    protected static function booted(): void
    {
        parent::created(fn (Setting $setting) => event(new SettingCreated($setting)));
        parent::updated(fn (Setting $setting) => event(new SettingUpdated($setting)));

        parent::creating(fn (Setting $setting) => $setting->determineColumn());
        parent::updating(fn (Setting $setting) => $setting->determineColumn());
    }

    /**
     * @return Attribute<string, mixed>
     */
    protected function data(): Attribute
    {
        $data = asString($this->getAttribute('value') ?? $this->getAttribute('encrypted_value'));

        $data = match ($this->type()) {
            'int', 'integer' => [true => floatval($data), false => $data][is_numeric($data)],
            'array' => json_decode($data, true),
            'bool', 'boolean' => boolval($data),
            default => $data,
        };

        return Attribute::make(get: fn (): mixed => $data);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'encrypted_value' => 'encrypted',
            'is_encrypted' => 'boolean',
            'group' => 'string',
            'name' => 'string',
            'type' => 'string',
            'key' => 'string',
        ];
    }

    private function determineColumn(): void
    {
        $this->setAttribute('key', $this->string('group') . ':' . $this->string('name'));

        $data = $this->getAttribute('value') ?? $this->getAttribute('encrypted_value');
        $isEncrypted = $this->getAttribute('is_encrypted') ?? false;

        if ($this->type() === 'array') {
            $data = json_encode($data);
        }

        $value = [true => $data, false => null];

        $this->setAttribute('encrypted_value', $value[$isEncrypted]);
        $this->setAttribute('value', $value[! $isEncrypted]);
        $this->setAttribute('is_encrypted', $isEncrypted);
    }
}
