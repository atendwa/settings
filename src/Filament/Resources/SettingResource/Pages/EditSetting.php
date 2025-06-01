<?php

declare(strict_types=1);

namespace Atendwa\Settings\Filament\Resources\SettingResource\Pages;

use Atendwa\Filakit\Pages\EditRecord;
use Atendwa\Settings\Filament\Resources\SettingResource;

class EditSetting extends EditRecord {
    protected static string $resource = SettingResource::class;
}
