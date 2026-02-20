<?php

declare (strict_types=1);
namespace WordPress\AiClient\Files\Enums;

use WordPress\AiClient\Common\AbstractEnum;
/**
 * Represents the type of file storage.
 *
 * @method static self square() Returns the square orientation
 * @method static self landscape() Returns the landscape orientation.
 * @method static self portrait() Returns the portrait orientation.
 * @method bool isSquare() Checks if this is an square orientation
 * @method bool isLandscape() Checks if this is a landscape orientation.
 * @method bool isPortrait() Checks if this is a portrait orientation.
 *
 * @since 0.1.0
 */
class MediaOrientationEnum extends AbstractEnum
{
    /**
     * Square orientation.
     *
     * @var string
     */
    public const SQUARE = 'square';
    /**
     * Landscape orientation.
     *
     * @var string
     */
    public const LANDSCAPE = 'landscape';
    /**
     * Portrait orientation.
     *
     * @var string
     */
    public const PORTRAIT = 'portrait';
}
