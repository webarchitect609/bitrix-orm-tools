<?php

namespace WebArch\BitrixOrmTools\Iblock\Property;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\SystemException;
use LogicException;
use WebArch\BitrixCache\Cache;
use WebArch\BitrixOrmTools\Iblock\Property\Converter\DirectoryConverter;
use WebArch\BitrixOrmTools\Iblock\Property\Converter\NumberConverter;
use WebArch\BitrixOrmTools\Iblock\Property\Converter\PropertyToFieldConverter;
use WebArch\BitrixOrmTools\Iblock\Property\Converter\SkuConverter;
use WebArch\BitrixOrmTools\Iblock\Property\Converter\StringConverter;
use WebArch\BitrixOrmTools\Iblock\Property\Converter\YesNoTypeConverter;

/**
 * Class DynamicSinglePropertiesTable
 *
 * Динамически генерируемое описание таблицы немножественных свойств элемента инфоблока.
 *
 * @package WebArch\BitrixOrmTools
 */
abstract class DynamicSinglePropertiesTable extends DataManager
{
    protected const TABLE_PREFIX = 'b_iblock_element_prop_s';

    /**
     * @var null|string
     */
    protected static $tableName;

    /**
     * @var array<string, bool>
     */
    private static $ignoreUnsupportedPropertyTypeFlags = [];

    /**
     * Возвращает id инфоблока.
     *
     * @return int
     */
    abstract public static function getIblockId(): int;

    /**
     * @inheritDoc
     */
    public static function getTableName()
    {
        if (is_null(static::$tableName)) {
            static::$tableName = self::TABLE_PREFIX . static::getIblockId();
        }

        return static::$tableName;
    }

    /**
     * @inheritDoc
     * @return array<string, Field>
     */
    public static function getMap()
    {
        return Cache::create()
                    ->setPath('/WebArch/BitrixOrmTools/Iblock/Property/DynamicSinglePropertiesTable')
                    ->callback(
                        function () {
                            return static::doGetMap();
                        }
                    );
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @return array<string, Field>
     */
    protected static function doGetMap(): array
    {
        $dbPropList = PropertyTable::query()
                                   ->setSelect(['*'])
                                   ->setFilter(['MULTIPLE' => 'N', 'IBLOCK_ID' => static::getIblockId()])
                                   ->exec();

        $map = [
            'IBLOCK_ELEMENT_ID' => new IntegerField(
                'IBLOCK_ELEMENT_ID',
                [
                    'primary'      => true,
                    'autocomplete' => true,
                    'title'        => 'ID элемента',
                ]
            ),
        ];
        while ($propertyFields = $dbPropList->fetch()) {
            /** @var array<string, mixed> $propertyFields */
            $code = trim($propertyFields['CODE']);
            $converter = static::findConverter($propertyFields);
            if ($converter instanceof PropertyToFieldConverter) {
                $map[$code] = $converter->createField($propertyFields);
            }
        }

        return $map;
    }

    /**
     * @return array<PropertyToFieldConverter>
     */
    protected static function getConverterList(): array
    {
        return [
            new SkuConverter(),
            new DirectoryConverter(),
            new NumberConverter(),
            new StringConverter(),
            new YesNoTypeConverter(),
        ];
    }

    /**
     * @param array<string, mixed> $propertyFields
     *
     * @return null|PropertyToFieldConverter
     */
    protected static function findConverter(array $propertyFields): ?PropertyToFieldConverter
    {
        $type = trim($propertyFields['PROPERTY_TYPE']);
        $userType = trim($propertyFields['USER_TYPE']);

        foreach (static::getConverterList() as $converter) {
            if (
                $converter->getPropertyType() === $type
                && $converter->getUserType() === $userType
            ) {
                return $converter;
            }
        }

        if (!self::isUnsupportedPropertyTypeIgnored(static::class)) {
            throw new LogicException(
                sprintf(
                    'Unsupported property type `%s` with user type `%s`',
                    $type,
                    $userType
                )
            );
        }

        return null;
    }

    /**
     * Проверяет, игнорируются ли ошибки неподдерживаемых свойств классом.
     *
     * @param string $class
     *
     * @return bool
     */
    public static function isUnsupportedPropertyTypeIgnored(string $class): bool
    {
        return array_key_exists(
            self::assertPropsTableClass($class),
            self::$ignoreUnsupportedPropertyTypeFlags
        );
    }

    /**
     * Игнорировать ошибки неподдерживаемых свойств в классе $class.
     *
     * @param string $class
     */
    public static function ignoreUnsupportedPropertyType(string $class): void
    {
        if (!self::isUnsupportedPropertyTypeIgnored($class)) {
            self::$ignoreUnsupportedPropertyTypeFlags[$class] = true;
        }
    }

    /**
     * Вернуть выброс ошибок неподдерживаемых свойств в классе $class.
     *
     * @param string $class
     */
    public static function alertUnsupportedPropertyType(string $class): void
    {
        if (self::isUnsupportedPropertyTypeIgnored($class)) {
            unset(self::$ignoreUnsupportedPropertyTypeFlags[$class]);
        }
    }

    /**
     * Очистить список классов, для которых игнорируются ошибки неподдерживаемых свойств.
     *
     * @return void
     */
    public static function clearIgnoreUnsupportedPropertyType(): void
    {
        self::$ignoreUnsupportedPropertyTypeFlags = [];
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private static function assertPropsTableClass(string $class): string
    {
        $class = trim($class);

        if (!is_subclass_of($class, self::class)) {
            throw new LogicException(
                sprintf(
                    'Expect subclass of %s, but got %s',
                    self::class,
                    $class
                )
            );
        }

        return $class;
    }
}
