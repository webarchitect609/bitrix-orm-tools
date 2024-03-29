<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\FloatField;
use WebArch\BitrixIblockPropertyType\Abstraction\IblockPropertyTypeBase;

class NumberConverter extends PropertyToFieldConverter
{
    /**
     * @inheritDoc
     */
    public function getPropertyType(): string
    {
        return IblockPropertyTypeBase::PROPERTY_TYPE_NUMBER;
    }

    /**
     * @inheritDoc
     */
    public function getUserType(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function createField(array $propertyFields): Field
    {
        return $this->doCreateField(FloatField::class, $propertyFields);
    }
}
