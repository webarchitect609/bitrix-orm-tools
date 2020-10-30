<?php

namespace WebArch\BitrixOrmTools\SqlExpression\MySQL\Traits;

use Bitrix\Main\ORM\Fields\ScalarField;
use WebArch\BitrixOrmTools\SqlExpression\MySQL\Enum\Placeholder;

trait PlaceholderTypeTrait
{
    /**
     * Automatically guesses suitable placeholder and it's value.
     *
     * @param float|int|ScalarField|string $argument
     *
     * @return array<mixed> [string $placeholder, mixed $value]
     * @see \Bitrix\Main\DB\SqlExpression::execPlaceholders
     */
    protected function getPlaceholderAndValue($argument): array
    {
        if ($argument instanceof ScalarField) {
            return [Placeholder::COLUMN, $argument->getColumnName()];
        }
        if (is_int($argument)) {
            return [Placeholder::INTEGER, $argument];
        }
        if (is_float($argument) || is_numeric($argument)) {
            return [Placeholder::FLOAT, $argument];
        }

        return [Placeholder::STRING, $argument];
    }
}
