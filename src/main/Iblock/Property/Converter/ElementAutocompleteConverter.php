<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\SystemException;
use WebArch\BitrixIblockPropertyType\Abstraction\IblockPropertyTypeBase;

class ElementAutocompleteConverter extends PropertyToFieldConverter
{
    /**
     * @inheritDoc
     */
    public function getPropertyType(): string
    {
        return IblockPropertyTypeBase::PROPERTY_TYPE_ELEMENT_LINK;
    }

    /**
     * @inheritDoc
     */
    public function getUserType(): string
    {
        return 'EAutocomplete';
    }

    /**
     * @inheritDoc
     */
    public function createField(array $propertyFields): Field
    {
        return $this->doCreateField(IntegerField::class, $propertyFields);
    }
}
