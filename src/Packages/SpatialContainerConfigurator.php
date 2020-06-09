<?php declare(strict_types=1);

namespace Orzford\Limoncello\Spatial\Packages;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Container\ContainerInterface as LimoncelloContainerInterface;
use Limoncello\Flute\Package\FluteContainerConfigurator;
use Orzford\Limoncello\Spatial\Types\LineStringType;
use Orzford\Limoncello\Spatial\Types\PointType;
use Orzford\Limoncello\Spatial\Types\PolygonType;

/**
 * @package App
 */
class SpatialContainerConfigurator extends FluteContainerConfigurator
{
    /**
     * @inheritDoc
     */
    public static function configureContainer(LimoncelloContainerInterface $container): void
    {
        parent::configureContainer($container);

        Type::hasType(LineStringType::NAME) === true ?: Type::addType(LineStringType::NAME, LineStringType::class);
        Type::hasType(PointType::NAME) === true ?: Type::addType(PointType::NAME, PointType::class);
        Type::hasType(PolygonType::NAME) === true ?: Type::addType(PolygonType::NAME, PolygonType::class);
    }
}
