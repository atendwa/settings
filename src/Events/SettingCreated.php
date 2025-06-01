<?php

declare(strict_types=1);

namespace Atendwa\Settings\Events;

use Atendwa\Settings\Models\Setting;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SettingCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Setting $setting) {}
}
