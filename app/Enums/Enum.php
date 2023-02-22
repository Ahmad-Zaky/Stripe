<?php

namespace App\Enums;

Abstract class Enum implements IEnum
{
    public static function toArray()
    {
        $reflectionClass = new \ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }

    public static function implode($separator = ",")
    {
        $constants = static::toArray();

        return implode($separator, $constants);
    }
}