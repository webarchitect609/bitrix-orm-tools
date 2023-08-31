<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\TextField;
use WebArch\BitrixIblockPropertyType\Abstraction\IblockPropertyTypeBase;

class DirectoryConverter extends PropertyToFieldConverter
{
    /**
     * @inheritDoc
     */
    public function getPropertyType(): string
    {
        return IblockPropertyTypeBase::PROPERTY_TYPE_STRING;
    }

    /**
     * @inheritDoc
     */
    public function getUserType(): string
    {
        return 'directory';
    }

    /**
     * @inheritDoc
     */
    public function createField(array $propertyFields): Field
    {
        return $this->doCreateField(TextField::class, $propertyFields);
    }
}
