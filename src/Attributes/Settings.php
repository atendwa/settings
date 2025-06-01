<?php

declare(strict_types=1);

namespace Atendwa\Settings\Attributes;

use Attribute;
use Illuminate\Contracts\Container\ContextualAttribute;
use Illuminate\Support\Collection;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Settings implements ContextualAttribute
{
    /**
     * @param  array<string>  $keys
     */
    public function __construct(private array $keys) {}

    /**
     * @return Collection<int, non-empty-array<string, mixed>>
     */
    public static function resolve(self $attribute): Collection
    {
        return settings($attribute->keys);
    }
}
