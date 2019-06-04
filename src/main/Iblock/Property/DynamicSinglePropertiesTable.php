<?php

namespace WebArch\BitrixOrmTools\Iblock\Property;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\SystemException;
use Exception;
use LogicException;
use WebArch\BitrixCache\BitrixCache;
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
    const TABLE_PREFIX = 'b_iblock_element_prop_s';

    /**
     * @var string
     */
    protected static $tableName;

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
     * @throws Exception
     */
    public static function getMap()
    {
        return (new BitrixCache())->withIblockTag(static::getIblockId())
                                  ->resultOf(
                                      function () {
                                          return static::doGetMap();
                                      }
                                  );
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @return array
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
            $code = $propertyFields['CODE'];
            $map[$code] = static::findConverter($propertyFields)->createField($propertyFields);
        }

        return $map;
    }

    /**
     * @return array|PropertyToFieldConverter[]
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
     * @param array $propertyFields
     *
     * @return PropertyToFieldConverter
     */
    protected static function findConverter(array $propertyFields): PropertyToFieldConverter
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

        throw new LogicException(
            sprintf(
                'Unsupported property type `%s` with user type `%s`',
                $type,
                $userType
            )
        );
    }
}
