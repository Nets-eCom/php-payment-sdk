<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Shared;

trait JsonDeserializeTrait
{
    /**
     * @return array<mixed>
     */
    protected static function jsonDeserialize(string $string): array
    {
        return json_decode($string, true, 512, \JSON_INVALID_UTF8_IGNORE);
    }

    /**
     * @return array<mixed>
     */
    protected static function jsonDeserializeToClassVars(string $string): array
    {
        return array_intersect_key(static::jsonDeserialize($string), get_class_vars(static::class));
    }
}
