<?php declare (strict_types=1);

namespace Orzford\Limoncello\Spatial\Types;

use Brick\Geo\Proxy\PointProxy;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * @package App
 */
class PointType extends \Brick\Geo\Doctrine\Types\PointType
{
    /**
     * Type name
     */
    const NAME = 'limoncelloPoint';

    /**
     * @inheritDoc
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        /** @var PointProxy $phpValue */
        $phpValue = parent::convertToPHPValue($value, $platform);

        return $phpValue->asText();
    }


}
