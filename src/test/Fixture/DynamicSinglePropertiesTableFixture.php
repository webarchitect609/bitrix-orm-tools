<?php

namespace WebArch\BitrixOrmTools\Test\Fixture;

use WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable;

class DynamicSinglePropertiesTableFixture extends DynamicSinglePropertiesTable
{
    /**
     * @inheritDoc
     */
    public static function getIblockId(): int
    {
        return 0;
    }
}

