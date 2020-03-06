<?php declare(strict_types=1);

namespace Orzford\Limoncello\Spatial\Packages;

use Limoncello\Contracts\Provider\ProvidesContainerConfiguratorsInterface;

/**
 * @package App
 */
class SpatialProvider implements ProvidesContainerConfiguratorsInterface
{
    /**
     * @inheritDoc
     */
    public static function getContainerConfigurators(): array
    {
        return [
            SpatialContainerConfigurator::class,
        ];
    }
}
