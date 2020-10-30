<?php

namespace WebArch\BitrixOrmTools\SqlExpression\MySQL;

/**
 * Class SqlExpression
 *
 * Sql expression helps to build
 *
 * @package WebArch\BitrixOrmTools\SqlExpression
 */
class SqlExpression
{
    /**
     * @var null|Spatial
     */
    private $spatial;

    /**
     * @return SqlExpression
     */
    public static function create()
    {
        return new self;
    }

    /**
     * @return Spatial
     */
    public function spatial(): Spatial
    {
        if (is_null($this->spatial)) {
            $this->spatial = new Spatial();
        }

        return $this->spatial;
    }
}
