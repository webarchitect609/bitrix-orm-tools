Битрикс ORM инструменты
=======================
[![Travis Build Status](https://travis-ci.org/webarchitect609/bitrix-orm-tools.svg?branch=master)](https://travis-ci.org/webarchitect609/bitrix-orm-tools)
[![Latest version](https://img.shields.io/github/v/tag/webarchitect609/bitrix-orm-tools?sort=semver)](https://github.com/webarchitect609/bitrix-orm-tools/releases)
[![Downloads](https://img.shields.io/packagist/dt/webarchitect609/bitrix-orm-tools)](https://packagist.org/packages/webarchitect609/bitrix-orm-tools)
[![PHP version](https://img.shields.io/packagist/php-v/webarchitect609/bitrix-orm-tools)](https://www.php.net/supported-versions.php)
[![License](https://img.shields.io/github/license/webarchitect609/bitrix-orm-tools)](LICENSE.md)
[![More stuff from me](https://img.shields.io/badge/packagist-webarchitect609-blueviolet)](https://packagist.org/packages/webarchitect609/)

**Пожалуйста, будьте осторожны:** это пока нестабильная версия без покрытия Unit-тестами!

Вспомогательные инструменты для работы с Битрикс D7 ORM. 

Возможности
-----------
- Генерация описания полей таблицы хранения немножественных свойств элемента инфоблока.

Установка
---------
1. Установить через [composer](https://getcomposer.org/):

    ```bash
    composer require webarchitect609/bitrix-orm-tools
    ```
2. Добавить подключение [автозагрузчика](https://getcomposer.org/doc/01-basic-usage.md#autoloading) composer в самое
начало [файла init.php](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2916&LESSON_PATH=3913.4776.2916)
    
    ```php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/../../vendor/autoload.php';
    ```

Использование
-------------
Чтобы воспользоваться функциональностью генерации описания немножественных свойств элемента инфоблока:
- отнаследоваться от `\WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable` и объявить метод
    `getIblockId()`;
- при объявлении `Bitrix\Main\Entity\ReferenceField` (или `Bitrix\Main\ORM\Fields\Relations\Reference`)
    воспользоваться созданным классом;
    
```php
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Query\Join;
use WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable;

class NewsSinglePropTable extends DynamicSinglePropertiesTable
{
    public static function getIblockId(): int
    {
        return 123;
    }
}

ElementTable::query()
            ->registerRuntimeField(
                new ReferenceField(
                    'NEWS_PROP',
                    NewsSinglePropTable::class,
                    Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')
                )
            );
```

Известные особенности
---------------------

### Необходимость описывать конверторы для всех свойств
Если используются пользовательские свойства элемента инфоблока, может возникать ошибка

```
Unsupported property type `S` with user type `Vendor\Package\NutritionValueProperty`
```

Можно дописать необходимый конвертор и вернуть его, переопределив статический метод
`\WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable::getConverterList()` и добавив к уже
существующему списку конверторов.

Другой вариант - включить игнорирование таких ошибок при помощи метода
`\WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable::ignoreUnsupportedPropertyType()`

Лицензия и информация об авторе
--------------------------------

[BSD-3-Clause](LICENSE.md)
