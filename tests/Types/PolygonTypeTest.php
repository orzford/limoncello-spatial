<?php declare(strict_types=1);

namespace Orzford\Limoncello\Test\Spatial\Types;

use Brick\Geo\Exception\CoordinateSystemException;
use Brick\Geo\Exception\GeometryIOException;
use Brick\Geo\Exception\InvalidGeometryException;
use Brick\Geo\Exception\UnexpectedGeometryException;
use Brick\Geo\Point;
use Brick\Geo\Polygon;
use Brick\Geo\Proxy\PointProxy;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Orzford\Limoncello\Spatial\Types\PolygonType;
use Orzford\Limoncello\Test\Spatial\TestCase;

/**
 * @package App
 */
class PolygonTypeTest extends TestCase
{
    /**
     * @inheritDoc
     * @throws DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (Type::hasType(PolygonType::NAME) === false)
            Type::addType(PolygonType::NAME, PolygonType::class);
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     */
    public function testPolygonFromWKB(): void
    {
        $polygon = Polygon::fromBinary(hex2bin('010300000001000000050000000000000000003e4000000000000024400000000000004440000000000000444000000000000034400000000000004440000000000000244000000000000034400000000000003e400000000000002440'), 4326);

        $this->assertInstanceOf(Polygon::class, $polygon);
        $this->assertEquals('POLYGON((30 10,40 40,20 40,10 20,30 10))', $polygon->asText());
        $this->assertEquals(4326, $polygon->SRID());
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     */
    public function testWKTToPolygon(): void
    {
        $polygon = Polygon::fromText('POLYGON((30 10,40 40,20 40,10 20,30 10))', 4326);

        $this->assertEquals(4326, $polygon->SRID());
        $this->assertEquals('010300000001000000050000000000000000003e4000000000000024400000000000004440000000000000444000000000000034400000000000004440000000000000244000000000000034400000000000003e400000000000002440', bin2hex($polygon->asBinary()));
    }

    /**
     * @throws DBALException
     */
    public function testPolygonTypePHPValueConversion()
    {
        $type = Type::getType(PolygonType::NAME);

        $platform = $this->createConnection()->getDatabasePlatform();

        $wkb = hex2bin('010300000001000000050000000000000000003e4000000000000024400000000000004440000000000000444000000000000034400000000000004440000000000000244000000000000034400000000000003e400000000000002440');

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
     * @throws UnexpectedGeometryException*
     */
    public function testPolygonTypeToDatabaseValueConversion()
    {
        $type = Type::getType(PolygonType::NAME);

        $platform = $this->createConnection()->getDatabasePlatform();

        $polygon = Point::fromText('POLYGON((30 10,40 40,20 40,10 20,30 10))', 4326);

        $databaseValue = $type->convertToDatabaseValue($polygon, $platform);

        $this->assertNotNull($databaseValue);
        $this->assertEquals('010300000001000000050000000000000000003e4000000000000024400000000000004440000000000000444000000000000034400000000000004440000000000000244000000000000034400000000000003e400000000000002440', bin2hex($databaseValue));
    }
}
