<?php

/** @noinspection PhpMissingReturnTypeInspection */

namespace WebArch\BitrixOrmTools\Iblock\Property;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;

abstract class DynamicMultiplePropertiesTable extends DataManager
{
    protected const TABLE_PREFIX = 'b_iblock_element_prop_m';

    /**
     * Возвращает id инфоблока.
     *
     * @return int
     */
    abstract public static function getIblockId(): int;

    /**
     * @inheritDoc
     */
    public static function getTableName()
    {
        return self::TABLE_PREFIX . static::getIblockId();
    }

    /**
     * @inheritDoc
     * @return array<string, Field>
     */
    public static function getMap()
    {
        return [
            'ID'                 => (new IntegerField('ID'))->configurePrimary(true)
                                                            ->configureAutocomplete(true)
                                                            ->configureRequired(false),
            'IBLOCK_ELEMENT_ID'  => (new IntegerField('IBLOCK_ELEMENT_ID'))->configureRequired(true),
            'IBLOCK_PROPERTY_ID' => (new IntegerField('IBLOCK_PROPERTY_ID'))->configureRequired(true),
            'VALUE'              => (new TextField('VALUE'))->configureRequired(true),
            'VALUE_ENUM'         => (new IntegerField('VALUE_ENUM')),
            'VALUE_NUM'          => (new IntegerField('VALUE_NUM')),
            'DESCRIPTION'        => (new StringField('DESCRIPTION'))->configureSize(255),
        ];
    }
}
