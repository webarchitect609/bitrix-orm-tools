<?php

namespace WebArch\BitrixOrmTools\Enum;

use ReflectionClass;

class GeometryFormat
{
    const INTERNAL = 'Internal';

    const WKB = 'Well-Known Binary';

    const WKT = 'Well-Known Text';

    const GEO_JSON = 'GeoJSON';

    /**
     * Checks whether the $format is supported.
     *
     * @param string $format
     *
     * @return bool
     */
    public static function isSupported(string $format): bool
    {
        return in_array($format, self::getConstants());
    }

    /**
     * Returns all supported formats.
     *
     * @return array<string>
     */
    public static function getSupported(): array
    {
        return array_values(static::getConstants());
    }

    /**
     * Returns all supported formats, which can be read and write by human(i.e. non-binary).
     *
     * @return string[]
     */
    public static function getSupportedTextLike(): array
    {
        return [
            self::WKT,
            self::GEO_JSON,
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function getConstants(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
