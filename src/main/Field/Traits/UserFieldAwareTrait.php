<?php

namespace WebArch\BitrixOrmTools\Field\Traits;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ORM\Entity;
use CUserTypeEntity;
use WebArch\BitrixCache\Cache;

/**
 * Trait UserFieldAwareTrait
 *
 * Позволяет автоматически получить все данные о пользовательском поле, если текущее поле таблицы используется в
 * HL-блоке.
 *
 * @package WebArch\BitrixOrmTools\Field\Traits
 */
trait UserFieldAwareTrait
{
    /**
     * @var null|array<string, mixed> Если поле относится к HL-блоку, здесь будут параметры пользовательского поля.
     */
    protected $userField;

    /**
     * @inheritDoc
     */
    public function postInitialize()
    {
        $tableName = $this->getEntity()->getDBTableName();
        $this->userField = Cache::create()
                                ->setPathByClass(static::class)
                                ->setTTL(14400)
                                ->setKey(
                                    sprintf(
                                        'postInitialize_%s',
                                        md5($tableName . $this->getName())
                                    )
                                )
                                ->callback(
                                    function () use ($tableName) {
                                        $hlbRow = HighloadBlockTable::query()
                                                                    ->setSelect(['ID'])
                                                                    ->setFilter(['=TABLE_NAME' => $tableName])
                                                                    ->setLimit(1)
                                                                    ->exec()
                                                                    ->fetch();
                                        if (false == $hlbRow) {
                                            return null;
                                        }

                                        $filter = [
                                            'ENTITY_ID'  => 'HLBLOCK_' . $hlbRow['ID'],
                                            'FIELD_NAME' => $this->getName(),
                                        ];
                                        $userField = CUserTypeEntity::GetList([], $filter)
                                                                    ->Fetch();
                                        if (is_array($userField)) {
                                            return $userField;
                                        }

                                        return null;
                                    }
                                );

        return null;
    }

    /**
     * @return Entity
     */
    abstract public function getEntity();

    /**
     * @return string
     */
    abstract public function getName();
}
