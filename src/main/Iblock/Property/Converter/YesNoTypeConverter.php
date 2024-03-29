<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\Field;
use WebArch\BitrixIblockPropertyType\Abstraction\IblockPropertyTypeBase;
use WebArch\BitrixIblockPropertyType\YesNoType;

class YesNoTypeConverter extends PropertyToFieldConverter
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
        return YesNoType::class;
    }

    /**
     * @inheritDoc
     */
    public function createField(array $propertyFields): Field
    {
        return $this->doCreateField(BooleanField::class, $propertyFields);
    }
}
