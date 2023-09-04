<?php

namespace WebArch\BitrixOrmTools\Catalog\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;

class ProductSetsTable extends DataManager
{
    /**
     * @inheritDoc
     */
    public static function getTableName()
    {
        return 'b_catalog_product_sets';
    }

    /**
     * @inheritDoc
     */
    public static function getMap()
    {
        return [
            'ID'               => (new IntegerField('ID'))->configurePrimary(true)
                                                          ->configureAutocomplete(true)
                                                          ->configureRequired(false),
            'TYPE'             => (new IntegerField('TYPE'))->configureRequired(true),
            'SET_ID'           => (new IntegerField('SET_ID'))->configureRequired(true),
            'ACTIVE'           => (new StringField('ACTIVE'))->configureRequired(true),
            'OWNER_ID'         => (new IntegerField('OWNER_ID'))->configureRequired(true),
            'ITEM_ID'          => (new IntegerField('ITEM_ID'))->configureRequired(true),
            'QUANTITY'         => (new FloatField('QUANTITY'))->configureRequired(false),
            'MEASURE'          => (new IntegerField('MEASURE'))->configureRequired(false),
            'DISCOUNT_PERCENT' => (new FloatField('DISCOUNT_PERCENT'))->configureRequired(false),
            'SORT'             => (new IntegerField('SORT'))->configureRequired(true)
                                                            ->configureDefaultValue(100),
            'CREATED_BY'       => (new IntegerField('CREATED_BY'))->configureRequired(false),
            'DATE_CREATE'      => (new DateField('DATE_CREATE'))->configureRequired(false),
            'MODIFIED_BY'      => (new IntegerField('MODIFIED_BY'))->configureRequired(false),
            'TIMESTAMP_X'      => (new DateField('TIMESTAMP_X'))->configureRequired(false),
            'XML_ID'           => (new StringField('XML_ID'))->configureRequired(false)
                                                             ->configureSize(255),
        ];
    }
}
