<?php declare(strict_types=1);

namespace Orzford\Limoncello\Test\Spatial\Types;

use Brick\Geo\Exception\CoordinateSystemException;
use Brick\Geo\Exception\GeometryIOException;
use Brick\Geo\Exception\InvalidGeometryException;
use Brick\Geo\Exception\UnexpectedGeometryException;
use Brick\Geo\Point;
use Brick\Geo\Proxy\PointProxy;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Orzford\Limoncello\Spatial\Types\PointType;
use Orzford\Limoncello\Test\Spatial\TestCase;

/**
 * @package App
 */
class PointTypeTest extends TestCase
{
    /**
     * @inheritDoc
     * @throws DBALException
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (Type::hasType(PointType::NAME) === false)
            Type::addType(PointType::NAME, PointType::class);
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     */
    public function testPointFromWKB(): void
    {
        $point = Point::fromBinary(hex2bin('0101000000363b527de78b5c40a5d8d138d4433640'), 4326);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals('POINT (114.186004 22.264957)', $point->asText());
        $this->assertEquals(114.186004, $point->x());
        $this->assertEquals(22.264957, $point->y());
        $this->assertEquals(4326, $point->SRID());
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException
     */
    public function testWKTToPoint(): void
    {
        $point = Point::fromText('POINT (114.186004 22.264957)', 4326);

        $this->assertEquals(114.186004, $point->x());
        $this->assertEquals(22.264957, $point->y());
        $this->assertEquals(4326, $point->SRID());
        $this->assertEquals('0101000000363b527de78b5c40a5d8d138d4433640', bin2hex($point->asBinary()));
    }

    /**
     * @throws DBALException
     */
    public function testPointTypePHPValueConversion()
    {
        $type = Type::getType(PointType::NAME);

        $platform = $this->createConnection()->getDatabasePlatform();

        $wkb = hex2bin('0101000000363b527de78b5c40a5d8d138d4433640');

        /** @var PointProxy $phpValue */
        $phpValue = $type->convertToPHPValue($wkb, $platform);

        $this->assertInstanceOf(PointProxy::class, $phpValue);
        $this->assertEquals(114.186004, $phpValue->x());
        $this->assertEquals(22.264957, $phpValue->y());
        $this->assertEquals(0, $phpValue->SRID());
    }

    /**
     * @throws DBALException
     * @throws CoordinateSystemException
     * @throws GeometryIOException
     * @throws InvalidGeometryException
     * @throws UnexpectedGeometryException*
     */
    public function testPointTypeToDatabaseValueConversion()
    {
        $type = Type::getType(PointType::NAME);

        $platform = $this->createConnection()->getDatabasePlatform();

        $point = Point::fromText('POINT (114.186004 22.264957)', 4326);

        $databaseValue = $type->convertToDatabaseValue($point, $platform);

        $this->assertNotNull($databaseValue);
        $this->assertEquals('0101000000363b527de78b5c40a5d8d138d4433640', bin2hex($databaseValue));
    }
}
