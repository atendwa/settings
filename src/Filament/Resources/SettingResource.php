<?php

declare(strict_types=1);

namespace Atendwa\Settings\Filament\Resources;

use Atendwa\Filakit\Concerns\CustomizesFilamentResource;
use Atendwa\Filakit\Resource;
use Atendwa\Settings\Filament\Resources\SettingResource\Pages\EditSetting;
use Atendwa\Settings\Filament\Resources\SettingResource\Pages\ListSettings;
use Atendwa\Settings\Filament\Resources\SettingResource\Pages\ViewSetting;
use Atendwa\Settings\Models\Setting;
use Filament\Clusters\Cluster;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\PageRegistration;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;

class SettingResource extends Resource
{
    use CustomizesFilamentResource;

    public static ?string $pluginName = 'atendwa-settings';

    protected static ?string $model = Setting::class;

    public static function getCluster(): ?string
    {
        try {
            $cluster = app(asString(config('settings.resource.cluster')));

            return match ($cluster instanceof Cluster) {
                true => $cluster::class,
                false => null,
            };
        } catch (Throwable) {
            return null;
        }
    }

    public static function getNavigationSort(): ?int
    {
        return asInteger(config('settings.resource.sort'));
    }

    public static function getNavigationGroup(): ?string
    {
        $group = config('settings.resource.group');

        return match (is_string($group)) {
            true => $group,
            false => null,
        };
    }

    public static function getNavigationIcon(): string
    {
        $icon = config('settings.resource.icon');

        return match (is_string($icon)) {
            true => $icon,
            false => 'heroicon-o-rectangle-stack',
        };
    }

    public static function getActiveNavigationIcon(): string
    {
        $icon = config('settings.resource.active_icon');

        return match (is_string($icon)) {
            true => $icon,
            false => self::getNavigationIcon(),
        };
    }

    public static function getRecordTitleAttribute(): ?string
    {
        $title = config('settings.resource.record_title_attribute');

        return match (is_string($title)) {
            true => $title,
            false => null,
        };
    }

    /**
     * @throws Throwable
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            textInput('group')->headline()->visibleOn('view'),
            textInput('key')->visibleOn('view'),
            textInput('name')->headline()->visibleOn('view'),
            textInput('type')->headline()->visibleOn('view'),
            Setting::getInput($form),
            Toggle::make('is_encrypted')->required(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public static function table(Table $table): Table
    {
        self::$customTable = $table->columns([
            column('group')->headline(),
            column('name')->headline(),
            column('key')->copyable()->visible(app()->isLocal())->badge()->color('gray'),
            Setting::getColumn(TextColumn::make('data')),
            IconColumn::make('is_encrypted')->boolean()->label('Encrypted'),
        ]);

        return self::customTable();
    }

    /**
     * @return PageRegistration[]
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'view' => ViewSetting::route('/view/{record}'),
            'update' => EditSetting::route('/update/{record}'),
        ];
    }
}
