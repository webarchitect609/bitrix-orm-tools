<?php

namespace WebArch\BitrixOrmTools\Iblock\Property\Converter;

use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\SystemException;
use WebArch\BitrixOrmTools\Exception\InvalidArgumentException;

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
     * @param array<string, mixed> $propertyFields
     *
     * @throws SystemException
     * @return Field
     */
    abstract public function createField(array $propertyFields): Field;

    /**
     * @param string               $fieldClass
     * @param array<string, mixed> $propertyFields
     *
     * @return Field
     */
    protected function doCreateField(string $fieldClass, array $propertyFields): Field
    {
        $this->assertFieldClass($fieldClass);

        return new $fieldClass(
            trim($propertyFields['CODE']),
            $this->getDefaultFieldParameters($propertyFields)
        );
    }

    /**
     * @param array<string, mixed> $propertyFields
     *
     * @return array<string, mixed>
     */
    protected function getDefaultFieldParameters(array $propertyFields): array
    {
        $parameters = [];

        if (isset($propertyFields['NAME']) && trim($propertyFields['NAME']) !== '') {
            $parameters['title'] = trim($propertyFields['NAME']);
        }

        if (isset($propertyFields['ID']) && $propertyFields['ID'] > 0) {
            $parameters['column_name'] = 'PROPERTY_' . $propertyFields['ID'];
        }

        return $parameters;
    }

    /**
     * @param string $fieldClass
     *
     * @return void
     */
    private function assertFieldClass(string $fieldClass): void
    {
        if (!class_exists($fieldClass)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Class does not exist: %s',
                    $fieldClass
                )
            );
        }
        if (!is_subclass_of($fieldClass, Field::class)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s must be subclass of %s',
                    $fieldClass,
                    Field::class
                )
            );
        }
    }
}
