<?php

namespace WebArch\BitrixOrmTools\Test\Field;

use PHPUnit\Framework\TestCase;
use WebArch\BitrixOrmTools\Field\ExpressionField;
use WebArch\BitrixTaxidermist\Taxidermist;

class ExpressionFieldTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        (new Taxidermist())->taxidermizeAll();
    }

    /**
     * @return void
     */
    public function testConfigureDataType()
    {
        $dataType = 'arbitraryType';
        $expressionField = (new ExpressionField('FOO', 'expr'))->configureDataType($dataType);
        $this->assertEquals($dataType, $expressionField->getDataType());
    }
}
