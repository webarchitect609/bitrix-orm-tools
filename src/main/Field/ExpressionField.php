<?php

namespace WebArch\BitrixOrmTools\Field;

use Bitrix\Main\ORM\Fields\ExpressionField as BxExpressionField;

/**
 * Class ExpressionField
 *
 * Этот тип поля позволяет задать тип данных, что невозможно в стандартном ExpressionField в Битриксе.
 *
 * @package WebArch\BitrixOrmTools\Field
 */
class ExpressionField extends BxExpressionField
{
    /**
     * @param string $dataType
     *
     * @return $this
     */
    public function configureDataType(string $dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }
}
