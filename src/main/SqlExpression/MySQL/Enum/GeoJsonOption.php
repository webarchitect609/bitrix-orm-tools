<?php

namespace WebArch\BitrixOrmTools\SqlExpression\MySQL\Enum;

/**
 * Class GeoJsonOption
 * @package WebArch\BitrixOrmTools\SqlExpression\MySQL\Enum
 * @link https://dev.mysql.com/doc/refman/5.7/en/spatial-geojson-functions.html#function_st-asgeojson
 */
class GeoJsonOption
{
    const NO_OPTION = 0;

    const BOUNDING_BOX = 1;

    const SHORT_FORMAT = 2;

    const LONG_FORMAT = 4;
}
