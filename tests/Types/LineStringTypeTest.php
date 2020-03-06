<?php declare(strict_types=1);

namespace Orzford\Limoncello\Test\Spatial\Types;

use Brick\Geo\Exception\CoordinateSystemException;
use Brick\Geo\Exception\GeometryIOException;
use Brick\Geo\Exception\InvalidGeometryException;
use Brick\Geo\Exception\UnexpectedGeometryException;
use Brick\Geo\LineString;
use Brick\Geo\Proxy\PointProxy;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Orzford\Limoncello\Spatial\Types\LineStringType;
use Orzford\Limoncello\Test\Spatial\TestCase;

/**
 * @package App
 */
class LineStringTypeTest extends TestCase
{
    /**
     * @inheritDoc
     * @throws DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (Type::hasType(LineStringType::NAME) === false)
            Type::addType(LineStringType::NAME, LineStringType::class);
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     */
    public function testLineStringFromWKB(): void
    {
        $lineString = LineString::fromBinary(hex2bin('0102000000030000000000000000003e40000000000000244000000000000024400000000000003e4000000000000044400000000000004440'), 4326);

        $this->assertInstanceOf(LineString::class, $lineString);
        $this->assertEquals('LINESTRING (30 10, 10 30, 40 40)', $lineString->asText());
        $this->assertEquals(4326, $lineString->SRID());
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     */
    public function testWKTToLineString(): void
    {
        $lineString = LineString::fromText('LINESTRING(30 10,10 30,40 40)', 4326);

        $this->assertEquals(4326, $lineString->SRID());
        $this->assertEquals(3, $lineString->count());
        $this->assertEquals('0102000000030000000000000000003e40000000000000244000000000000024400000000000003e4000000000000044400000000000004440', bin2hex($lineString->asBinary()));
    }

    /**
     * @throws DBALException
     */
    public function testLineStringTypePHPValueConversion()
    {
        $type = Type::getType(LineStringType::NAME);

        $platform = $this->createConnection()->getDatabasePlatform();

        $wkb = hex2bin('0102000000030000000000000000003e40000000000000244000000000000024400000000000003e4000000000000044400000000000004440');

        /** @var PointProxy $phpValue */
        $phpValue = $type->convertToPHPValue($wkb, $platform);

        $this->assertInstanceOf(PointProxy::class, $phpValue);
        $this->assertEquals(0, $phpValue->SRID());
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     */
    public function testLineStringTypeToDatabaseValueConversion()
    {
        $type = Type::getType(LineStringType::NAME);

        $platform = $this->createConnection()->getDatabasePlatform();

        $point = LineString::fromText('LINESTRING(30 10,10 30,40 40)', 4326);

        $databaseValue = $type->convertToDatabaseValue($point, $platform);

        $this->assertNotNull($databaseValue);
        $this->assertEquals('0102000000030000000000000000003e40000000000000244000000000000024400000000000003e4000000000000044400000000000004440', bin2hex($databaseValue));
    }
}
