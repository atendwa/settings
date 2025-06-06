<?php

declare(strict_types=1);

namespace Atendwa\Settings\Filament\Resources\SettingResource\Pages;

use Atendwa\Filakit\Pages\ListRecords;
use Atendwa\Settings\Filament\Resources\SettingResource;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;
}
