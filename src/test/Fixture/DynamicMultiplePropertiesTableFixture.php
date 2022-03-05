<?php

namespace WebArch\BitrixOrmTools\Test\Fixture;

use WebArch\BitrixOrmTools\Iblock\Property\DynamicMultiplePropertiesTable;

class DynamicMultiplePropertiesTableFixture extends DynamicMultiplePropertiesTable
{
    /**
     * @inheritDoc
     */
    public static function getIblockId(): int
    {
        return 0;
    }
}
