<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\SystemException;

abstract class PropertyToFieldConverter
{
    /**
     * Возвращает тип свойства из стандартных типов Битрикс.
     *
     * @return string
     */
    abstract public function getPropertyType(): string;

    /**
     * Возвращает тип пользовательского свойства элементов информационных блоков.
     *
     * @return string
     */
    abstract public function getUserType(): string;

    /**
     * Возвращает объект описания поля таблицы.
     *
     * @param array $propertyFields
     *
     * @throws SystemException
     * @return Field
     */
    abstract public function createField(array $propertyFields): Field;

    /**
     * @param array $propertyFields
     *
     * @return array
     */
    protected function getDefaultFieldParameters(array $propertyFields): array
    {
        $parameters = [];

        if (isset($propertyFields['NAME']) && trim($propertyFields['NAME']) != '') {
            $parameters['title'] = trim($propertyFields['NAME']);
        }

        if (isset($propertyFields['ID']) && $propertyFields['ID'] > 0) {
            $parameters['column_name'] = 'PROPERTY_' . $propertyFields['ID'];
        }

        return $parameters;
    }

}
