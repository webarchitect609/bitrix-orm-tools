Changelog
=========

2.1.0
-----

### Добавлено:
- Новый тип поля `TimeField`

2.0.1
-----

### Исправлено:
- Исправлена ошибка в `DynamicSinglePropertiesTable`, связанная с кешированием и использованием статических полей

2.0.0
-----

### Добавлено:
- Возможность игнорировать неподдерживаемые типы свойств методом
`\WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable::ignoreUnsupportedPropertyType()`

### НАРУШЕНИЕ ОБРАТНОЙ СОВМЕСТИМОСТИ:
- PHP <= 7.1 больше не поддерживается
