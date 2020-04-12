<?php

namespace IHelpShopping\Enum;

abstract class Enum
{
    /**
     * Returns array where values are all constants
     * @throws \ReflectionException
     *
     * @return array
     */
    public static function getValues(): array
    {
        static $values;
        if (!$values) {
            $reflection = new \ReflectionClass(get_called_class());
            $values = array_values($reflection->getConstants());
        }

        return $values;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public static function hasValue($value): bool
    {
        return in_array($value, static::getValues(), true);
    }

    /**
     * @return array|string[]
     */
    public static function all(): array
    {
        return static::getValues();
    }
}
