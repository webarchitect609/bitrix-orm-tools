<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\Field;

class YesNoTypeConverter extends PropertyToFieldConverter
{
    /**
     * @inheritDoc
     */
    public function getPropertyType(): string
    {
        return 'N';
    }

    /**
     * @inheritDoc
     */
    public function getUserType(): string
    {
        return 'WebArch\BitrixIblockPropertyType\YesNoType';
    }

    /**
     * @inheritDoc
     */
    public function createField(array $propertyFields): Field
    {
        return new BooleanField(
            trim($propertyFields['CODE']),
            self::getDefaultFieldParameters($propertyFields)
        );
    }
}
