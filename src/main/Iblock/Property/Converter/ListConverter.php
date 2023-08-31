<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;
use WebArch\BitrixIblockPropertyType\Abstraction\IblockPropertyTypeBase;

class ListConverter extends PropertyToFieldConverter
{
    /**
     * @inheritDoc
     */
    public function getPropertyType(): string
    {
        return IblockPropertyTypeBase::PROPERTY_TYPE_LIST;
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
        return $this->doCreateField(IntegerField::class, $propertyFields);
    }
}
