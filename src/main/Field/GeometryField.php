<?php

namespace WebArch\BitrixOrmTools\Field;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\SqlExpression as BxSqlExpression;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\ScalarField;
use Bitrix\Main\SystemException;
use WebArch\BitrixOrmTools\Enum\GeometryFormat;
use WebArch\BitrixOrmTools\Exception\InvalidArgumentException;
use WebArch\BitrixOrmTools\Exception\LogicException;
use WebArch\BitrixOrmTools\SqlExpression\MySQL\Enum\GeoJsonOption;
use WebArch\BitrixOrmTools\SqlExpression\MySQL\Spatial;
use WebArch\BitrixOrmTools\SqlExpression\MySQL\SqlExpression;

class GeometryField extends ScalarField
{
    const PARAM_GEO_JSON_MAX_DEC_DIGITS = 'geo_json_max_dec_digits';

    const PARAM_GEO_JSON_OPTIONS = 'geo_json_options';

    /**
     * @var string
     * @see GeometryFormat
     */
    private $inputFormat = GeometryFormat::WKT;

    /**
     * @var string
     * @see GeometryFormat
     */
    private $outputFormat = GeometryFormat::WKT;

    public function __construct($name, $parameters = [])
    {
        parent::__construct($name, $parameters);
        $this->addFetchDataModifier([$this, 'convertValueFromDb']);
        $this->addSaveDataModifier([$this, 'convertValueToDb']);
    }

    /**
     * @param string $name
     * @param int $maxDecDigits
     * @param int $options
     * @param array<string, mixed> $parameters
     *
     * @throws ArgumentException
     * @throws SystemException
     * @return ExpressionField
     */
    public function asGeoJson(
        string $name,
        $maxDecDigits = Spatial::MAX_DEC_DIGITS,
        $options = GeoJsonOption::NO_OPTION,
        array $parameters = []
    ): ExpressionField {
        return new ExpressionField(
            $name,
            (string)SqlExpression::create()
                                 ->spatial()
                                 ->ST_AsGeoJSON($this, $maxDecDigits, $options),
            null,
            $parameters
        );
    }

    /**
     * @param string $name
     * @param array<string, mixed> $parameters
     *
     * @throws ArgumentException
     * @throws SystemException
     * @return ExpressionField
     */
    public function asWkt(string $name, array $parameters = []): ExpressionField
    {
        return new ExpressionField(
            $name,
            (string)SqlExpression::create()
                                 ->spatial()
                                 ->ST_AsWKT($this),
            null,
            $parameters
        );
    }

    /**
     * @param string $name
     * @param array<string, mixed> $parameters
     *
     * @throws ArgumentException
     * @throws SystemException
     * @return ExpressionField
     */
    public function asWkb(string $name, array $parameters = []): ExpressionField
    {
        return new ExpressionField(
            $name,
            (string)SqlExpression::create()
                                 ->spatial()
                                 ->ST_AsWKB($this),
            null,
            $parameters
        );
    }

    /**
     * @param string $name
     * @param array<string, mixed> $parameters Если $outputFormat === GeometryFormat::GEO_JSON, то в $parameters
     *     принимаются дополнительные настройки self::PARAM_GEO_JSON_MAX_DEC_DIGITS и self::PARAM_GEO_JSON_OPTIONS (см.
     *     asGeoJson() )
     *
     * @throws ArgumentException
     * @throws SystemException
     * @return ExpressionField|GeometryField Возвращает $this, игнорируя аргументы $name и $parameters, если выходной
     *     формат GeometryFormat::INTERNAL.
     */
    public function asOutputFormat(string $name, array $parameters = [])
    {
        if (GeometryFormat::WKB === $this->getOutputFormat()) {
            return $this->asWkb($name, $parameters);
        }
        if (GeometryFormat::WKT === $this->getOutputFormat()) {
            return $this->asWkt($name, $parameters);
        }
        if (GeometryFormat::GEO_JSON === $this->getOutputFormat()) {
            $maxDecDigits = Spatial::MAX_DEC_DIGITS;
            if (array_key_exists(self::PARAM_GEO_JSON_MAX_DEC_DIGITS, $parameters)) {
                $maxDecDigits = $parameters[self::PARAM_GEO_JSON_MAX_DEC_DIGITS];
            }
            $options = GeoJsonOption::NO_OPTION;
            if (array_key_exists(self::PARAM_GEO_JSON_OPTIONS, $parameters)) {
                $options = $parameters[self::PARAM_GEO_JSON_OPTIONS];
            }

            return $this->asGeoJson($name, $maxDecDigits, $options, $parameters);
        }
        if (GeometryFormat::INTERNAL === $this->getOutputFormat()) {
            return $this;
        }

        throw new LogicException(
            sprintf(
                'Unsupported output format "%s" is set for the field "%s".',
                $this->getOutputFormat(),
                $this->getName()
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function cast($value)
    {
        if (!is_string($value) || '' === trim($value)) {
            return null;
        }

        return $value;
    }

    /**
     * @inheritDoc
     * @internal Impossible to convert anything, because the value is in a complex format.
     * @see asWkb
     * @see asWkt
     * @see asGeoJson
     * @see asOutputFormat
     */
    public function convertValueFromDb($value)
    {
        return $this->cast($value);
    }

    /**
     * @inheritDoc
     * @throws ArgumentException
     * @return BxSqlExpression|mixed|string
     */
    public function convertValueToDb($value)
    {
        switch ($this->getInputFormat()) {
            case GeometryFormat::INTERNAL:
                return $value;
            case GeometryFormat::WKB:
                return SqlExpression::create()->spatial()->ST_GeomFromWKB($value);
            case GeometryFormat::WKT:
                return SqlExpression::create()->spatial()->ST_GeomFromText($value);
            case GeometryFormat::GEO_JSON:
                return SqlExpression::create()->spatial()->ST_GeomFromGeoJSON($value);
        }
        throw new LogicException(
            sprintf(
                'Unsupported input format "%s" is set for the field "%s".',
                $this->getInputFormat(),
                $this->getName()
            )
        );
    }

    /**
     * @return string
     */
    public function getInputFormat(): string
    {
        return $this->inputFormat;
    }

    /**
     * @param string $inputFormat
     *
     * @return $this
     */
    public function configureInputFormat(string $inputFormat)
    {
        if (!GeometryFormat::isSupported($inputFormat)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unsupported input format "%s". Expect one of these values: %s',
                    $inputFormat,
                    implode(', ', GeometryFormat::getSupported())
                )
            );
        }
        $this->inputFormat = $inputFormat;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    /**
     * @param string $outputFormat
     *
     * @return $this
     */
    public function configureOutputFormat(string $outputFormat)
    {
        if (!GeometryFormat::isSupported($outputFormat)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unsupported output format "%s". Expect one of these values: %s',
                    $outputFormat,
                    implode(', ', GeometryFormat::getSupported())
                )
            );
        }
        $this->outputFormat = $outputFormat;

        return $this;
    }
}
