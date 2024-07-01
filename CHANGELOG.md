Changelog
=========

2.5.1
-----

### Добавлено

- `php: ^7.2 || ^8.0`

2.5.0
-----

### Добавлено

- описание таблицы `b_catalog_product_sets` для D7

2.4.0
-----

### Добавлено

- новые конверторы свойств инфоблока "Список" и "Привязка к элементам с автозаполнением"

2.3.0
-----

### Добавлено

- Поддержка множественных свойств инфоблоков при помощи `DynamicMultiplePropertiesTable`

2.2.1
-----

### Исправлено

- Исправлено значение константы `\WebArch\BitrixOrmTools\SqlExpression\MySQL\Spatial::MAX_DEC_DIGITS`, которая должна
  быть 2^31 - 1, хотя в документации MySQL указано 2^32 - 1

2.2.0
-----

### Добавлено

- Новый тип поля `GeometryField` с поддержкой работы в форматах Well-Known Binary, Well-Known Text, GeoJSON и
  Internal(MySQL internal geometry format);
- Хэлпер `\WebArch\BitrixOrmTools\SqlExpression\MySQL\SqlExpression` и его специфическая часть
  `\WebArch\BitrixOrmTools\SqlExpression\MySQL\Spatial` для работы со spatial функциями;
- Метод `PlaceholderTypeTrait::getPlaceholderAndValue()` для автоматического определения типа placeholder и его
  значения по любому из поддерживаемых в `\Bitrix\Main\DB\SqlExpression::execPlaceholders()` типов данных.

2.1.0
-----

### Добавлено

- Новый тип поля `TimeField`

2.0.1
-----

### Исправлено

- Исправлена ошибка в `DynamicSinglePropertiesTable`, связанная с кешированием и использованием статических полей

2.0.0
-----

### Добавлено

- Возможность игнорировать неподдерживаемые типы свойств методом
  `\WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable::ignoreUnsupportedPropertyType()`

### НАРУШЕНИЕ ОБРАТНОЙ СОВМЕСТИМОСТИ

- PHP <= 7.1 больше не поддерживается
