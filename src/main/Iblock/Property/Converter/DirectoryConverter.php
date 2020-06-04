<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\TextField;

class DirectoryConverter extends PropertyToFieldConverter
{
    /**
     * @inheritDoc
     */
    public function getPropertyType(): string
    {
        return 'S';
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
        return new TextField(
            trim($propertyFields['CODE']),
            self::getDefaultFieldParameters($propertyFields)
        );
    }
}
