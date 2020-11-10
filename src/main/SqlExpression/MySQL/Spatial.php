<?php

namespace WebArch\BitrixOrmTools\SqlExpression\MySQL;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\SqlExpression as BxSqlExpression;
use Bitrix\Main\ORM\Fields\ScalarField;
use WebArch\BitrixOrmTools\SqlExpression\MySQL\Enum\GeoJsonOption;
use WebArch\BitrixOrmTools\SqlExpression\MySQL\Traits\PlaceholderTypeTrait;

class Spatial
{
    use PlaceholderTypeTrait;

    /**
     * Documentation reads: "(2^32) - 1", but in real it is (2^31) - 1
     */
    public const MAX_DEC_DIGITS = 2147483647;

    /**
     * Converts a value in internal geometry format to its WKT representation and returns the string result.
     *
     * @param ScalarField|string $geometry Geometry in MySQL internal geometry format.
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-format-conversion-functions.html#function_st-astext
     */
    public function ST_AsText($geometry): BxSqlExpression
    {
        [$placeholder, $value] = $this->getPlaceholderAndValue($geometry);

        return new BxSqlExpression('ST_AsText(' . $placeholder . ')', $value);
    }

    /**
     * Converts a value in internal geometry format to its WKT representation and returns the string result.
     *
     * @param ScalarField|string $geometry Geometry in MySQL internal geometry format.
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-format-conversion-functions.html#function_st-astext
     */
    public function ST_AsWKT($geometry): BxSqlExpression
    {
        return $this->ST_AsText($geometry);
    }

    /**
     * Converts a value in internal geometry format to its WKB representation and returns the binary result.
     *
     * @param ScalarField|string $geometry Geometry in MySQL internal geometry format.
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-format-conversion-functions.html#function_st-asbinary
     */
    public function ST_AsBinary($geometry): BxSqlExpression
    {
        [$placeholder, $value] = $this->getPlaceholderAndValue($geometry);

        return new BxSqlExpression('ST_AsBinary(' . $placeholder . ')', $value);
    }

    /**
     * Converts a value in internal geometry format to its WKB representation and returns the binary result.
     *
     * @param ScalarField|string $geometry Geometry in MySQL internal geometry format.
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-format-conversion-functions.html#function_st-asbinary
     */
    public function ST_AsWKB($geometry): BxSqlExpression
    {
        return $this->ST_AsBinary($geometry);
    }

    /**
     * Generates a GeoJSON object from the geometry g. The object string has the connection character set and collation.
     *
     * @param ScalarField|string $geometry
     * @param int|ScalarField $maxDecDigits
     * @param int|ScalarField $options
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/spatial-geojson-functions.html#function_st-asgeojson
     */
    public function ST_AsGeoJSON(
        $geometry,
        $maxDecDigits = self::MAX_DEC_DIGITS,
        $options = GeoJsonOption::NO_OPTION
    ): BxSqlExpression {
        [$geometryPlaceholder, $geometryValue] = $this->getPlaceholderAndValue($geometry);
        [$maxDecDigitsPlaceholder, $maxDecDigitsValue] = $this->getPlaceholderAndValue($maxDecDigits);
        [$optionsPlaceholder, $optionsValue] = $this->getPlaceholderAndValue($options);

        return new BxSqlExpression(
            sprintf(
                'ST_AsGeoJSON(%s, %s, %s)',
                $geometryPlaceholder,
                $maxDecDigitsPlaceholder,
                $optionsPlaceholder
            ),
            $geometryValue,
            $maxDecDigitsValue,
            $optionsValue
        );
    }

    /**
     * Returns a geometry that represents the point set union of the geometry values g1 and g2. If any argument is
     * NULL, the return value is NULL.
     *
     * @param ScalarField|string $geometryA
     * @param ScalarField|string $geometryB
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/spatial-operator-functions.html#function_st-union
     */
    public function ST_Union($geometryA, $geometryB): BxSqlExpression
    {
        [$geometryAPlaceholder, $geometryAValue] = $this->getPlaceholderAndValue($geometryA);
        [$geometryBPlaceholder, $geometryBValue] = $this->getPlaceholderAndValue($geometryB);

        return new BxSqlExpression(
            sprintf(
                'ST_Union(%s, %s)',
                $geometryAPlaceholder,
                $geometryBPlaceholder
            ),
            $geometryAValue,
            $geometryBValue
        );
    }

    /**
     * Simplifies a geometry using the Douglas-Peucker algorithm and returns a simplified value of the same type.
     *
     * ATTENTION: According to Boost.Geometry, geometries might become invalid as a result of the simplification
     * process, and the process might create self-intersections. To check the validity of the result, pass it to
     * ST_IsValid().
     *
     * @param ScalarField|string $geometry
     * @param float|ScalarField $maxDistance
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/spatial-convenience-functions.html#function_st-simplify
     */
    public function ST_Simplify($geometry, $maxDistance): BxSqlExpression
    {
        [$geometryPlaceholder, $geometryValue] = $this->getPlaceholderAndValue($geometry);
        [$maxDistancePlaceholder, $maxDistanceValue] = $this->getPlaceholderAndValue($maxDistance);

        return new BxSqlExpression(
            sprintf(
                'ST_Simplify(%s, %s)',
                $geometryPlaceholder,
                $maxDistancePlaceholder
            ),
            $geometryValue,
            $maxDistanceValue
        );
    }

    /**
     * Returns 1 if the argument is syntactically well-formed and is geometrically valid, 0 if the argument is not
     * syntactically well-formed or is not geometrically valid. If the argument is NULL, the return value is NULL.
     * Geometry validity is defined by the OGC specification.
     *
     * @param ScalarField|string $geometry
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/spatial-convenience-functions.html#function_st-isvalid
     */
    public function ST_IsValid($geometry): BxSqlExpression
    {
        [$placeholder, $value] = $this->getPlaceholderAndValue($geometry);

        return new BxSqlExpression('ST_IsValid(' . $placeholder . ')', $value);
    }

    /**
     * Parses a string $geoJson representing a GeoJSON object and returns a geometry.
     *
     * @param ScalarField|string $geoJson
     * @param null|int|ScalarField $options
     * @param null|int|ScalarField $srId
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/spatial-geojson-functions.html#function_st-geomfromgeojson
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function ST_GeomFromGeoJSON($geoJson, $options = null, $srId = null): BxSqlExpression
    {
        [$geoJsonPlaceholder, $geoJsonValue] = $this->getPlaceholderAndValue($geoJson);

        return new BxSqlExpression(
            sprintf(
                'ST_GeomFromGeoJSON(%s)',
                $geoJsonPlaceholder
            ),
            $geoJsonValue
        );
    }

    /**
     * Constructs a geometry value of any type using its WKT representation and SRID.
     *
     * @param ScalarField|string $wkt
     * @param null|int|ScalarField $srId
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-wkt-functions.html#function_st-geomfromtext
     */
    public function ST_GeomFromText($wkt, $srId = null): BxSqlExpression
    {
        [$wktPlaceholder, $wktValue] = $this->getPlaceholderAndValue($wkt);

        if (!is_null($srId)) {
            [$srIdPlaceholder, $srIdValue] = $this->getPlaceholderAndValue($srId);

            return new BxSqlExpression(
                sprintf(
                    'ST_GeomFromText(%s, %s)',
                    $wktPlaceholder,
                    $srIdPlaceholder
                ),
                $wktValue,
                $srIdValue
            );
        }

        return new BxSqlExpression(
            sprintf(
                'ST_GeomFromText(%s)',
                $wktPlaceholder
            ),
            $wktValue
        );
    }

    /**
     * Constructs a geometry value of any type using its WKT representation and SRID.
     *
     * @param ScalarField|string $wkt
     * @param null|int|ScalarField $srId
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-wkt-functions.html#function_st-geomfromtext
     */
    public function ST_GeometryFromText($wkt, $srId = null): BxSqlExpression
    {
        return $this->ST_GeomFromText($wkt, $srId);
    }

    /**
     * Constructs a geometry value of any type using its WKB representation and SRID.
     *
     * @param ScalarField|string $wkb
     * @param null|int|ScalarField $srId
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-wkb-functions.html#function_st-geomfromwkb
     */
    public function ST_GeomFromWKB($wkb, $srId = null): BxSqlExpression
    {
        [$wkbPlaceholder, $wkbValue] = $this->getPlaceholderAndValue($wkb);

        if (!is_null($srId)) {
            [$srIdPlaceholder, $srIdValue] = $this->getPlaceholderAndValue($srId);

            return new BxSqlExpression(
                sprintf(
                    'ST_GeomFromWKB(%s, %s)',
                    $wkbPlaceholder,
                    $srIdPlaceholder
                ),
                $wkbValue,
                $srIdValue
            );
        }

        return new BxSqlExpression(
            sprintf(
                'ST_GeomFromWKB(%s)',
                $wkbPlaceholder
            ),
            $wkbValue
        );
    }

    /**
     * Constructs a geometry value of any type using its WKB representation and SRID.
     *
     * @param ScalarField|string $wkb
     * @param null|int|ScalarField $srId
     *
     * @throws ArgumentException
     * @return BxSqlExpression
     *
     * @link https://dev.mysql.com/doc/refman/5.7/en/gis-wkb-functions.html#function_st-geomfromwkb
     */
    public function ST_GeometryFromWKB($wkb, $srId = null): BxSqlExpression
    {
        return $this->ST_GeomFromWKB($wkb, $srId);
    }
}
