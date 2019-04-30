Утилиты для работы с так называемым Битрикс ORM

Пока нестабильная версия - будьте внимательны!

Как использовать:
-----------------

1 Установить через composer 

`composer require webarchitect609/bitrix-orm-tools`

2 Чтобы воспользоваться функциональностью генерации описания немножественных свойств элемента инфоблока:
  - отнаследоваться от `\WebArch\BitrixOrmTools\Iblock\Property\DynamicSinglePropertiesTable` и объявить метод
    `getIblockId()`;
    
```php
class NewsSinglePropTable extends DynamicSinglePropertiesTable
{
    public static function getIblockId(): int
    {
        return 123;
    }

}
```

  - при объявлении `Bitrix\Main\Entity\ReferenceField` (или `Bitrix\Main\ORM\Fields\Relations\Reference`)
  воспользоваться созданным классом;
  
```php
new ReferenceField(
    'NEWS_PROP',
    NewsSinglePropTable::class,
    [
        '=this.ID' => 'ref.IBLOCK_ELEMENT_ID',
    ],
    ['join_type' => 'INNER']
)
```

