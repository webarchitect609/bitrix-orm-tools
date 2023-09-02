<?php

/** @noinspection PhpMissingReturnTypeInspection */

namespace WebArch\BitrixOrmTools\Iblock\Property;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\Field;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\SystemException;
use LogicException;
use ReflectionException;
use Symfony\Component\Finder\Finder;
use WebArch\BitrixCache\Cache;
use WebArch\BitrixOrmTools\Iblock\Property\Converter\PropertyToFieldConverter;
use const PATHINFO_FILENAME;

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

    private const CACHE_PATH = '/WebArch/BitrixOrmTools/Iblock/Property/DynamicSinglePropertiesTable';

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
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getTableName()
    {
        return self::TABLE_PREFIX . static::getIblockId();
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     * @return array<string, Field>
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getMap()
    {
        return Cache::create()
                    ->setPath(self::CACHE_PATH)
                    ->setKey(
                        sprintf(
                            'getMap_iblock-%d',
                            static::getIblockId()
                        )
                    )
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
        $subdir = 'Converter';
        $iterator = Finder::create()
                          ->name('*.php')
                          ->in(__DIR__ . DIRECTORY_SEPARATOR . $subdir)
                          ->files()
                          ->getIterator();
        $namespace = __NAMESPACE__ . '\\' . $subdir . '\\';
        $result = [];
        foreach ($iterator as $file) {
            $class = $namespace . pathinfo($file->getFilename(), PATHINFO_FILENAME);
            // @phpstan-ignore-next-line
            if (is_subclass_of($class, PropertyToFieldConverter::class)) {
                $result[] = new $class;
            }
        }

        return $result;
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
