<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;

class SkuConverter extends PropertyToFieldConverter
{
    /**
     * @inheritDoc
     */
    public function getPropertyType(): string
    {
        return 'E';
    }

    /**
     * @inheritDoc
     */
    public function getUserType(): string
    {
        return 'SKU';
    }

    /**
     * @inheritDoc
     */
    public function createField(array $propertyFields): Field
    {
        return new IntegerField(
            trim($propertyFields['CODE']),
            self::getDefaultFieldParameters($propertyFields)
        );
    }

}
