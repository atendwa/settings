<?php

declare(strict_types=1);

namespace Atendwa\Settings\Attributes;

use Attribute;
use Illuminate\Contracts\Container\ContextualAttribute;
use Throwable;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Setting implements ContextualAttribute
{
    public function __construct(public string $key, public mixed $default = null) {}

    /**
     * @throws Throwable
     */
    public static function resolve(self $attribute): mixed
    {
        return setting($attribute->key) ?? $attribute->default;
    }
}
