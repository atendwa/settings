<?php

declare(strict_types=1);

namespace Atendwa\Settings\Attributes;

use Attribute;
use Illuminate\Contracts\Container\ContextualAttribute;
use Illuminate\Support\Collection;
use Throwable;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class SettingGroup implements ContextualAttribute
{
    public function __construct(private string $group) {}

    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     *
     * @throws Throwable
     */
    public static function resolve(self $attribute): Collection
    {
        return settingGroup($attribute->group);
    }
}
