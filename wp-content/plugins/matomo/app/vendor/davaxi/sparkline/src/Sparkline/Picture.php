<?php

namespace Davaxi\Sparkline;

/**
 * Class PictureTrait.
 */
class Picture
{
    const DOT_RADIUS_TO_WIDTH = 2;
    /**
     * @var resource
     */
    protected $resource;
    /**
     * @var int
     */
    protected $height;
    /**
     * @var int
     */
    protected $width;
    /**
     * Picture constructor.
     * @param int $width
     * @param int $height
     */
    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->resource = imagecreatetruecolor($width, $height);
    }
    /**
     * @param array $setColor
     *
     * @return int
     */
    protected function getBackground(array $setColor = []) : int
    {
        if ($setColor) {
            return imagecolorallocate($this->resource, $setColor[0], $setColor[1], $setColor[2]);
        }
        return imagecolorallocatealpha($this->resource, 0, 0, 0, 127);
    }
    /**
     * @param array $lineColor
     *
     * @return int
     */
    public function getLineColor(array $lineColor) : int
    {
        return imagecolorallocate($this->resource, $lineColor[0], $lineColor[1], $lineColor[2]);
    }
    /**
     * @param array $backgroundColor
     */
    public function applyBackground(array $backgroundColor)
    {
        imagesavealpha($this->resource, \true);
        imagefill($this->resource, 0, 0, $this->getBackground($backgroundColor));
    }
    /**
     * @param int $lineThickness
     */
    public function applyThickness(int $lineThickness)
    {
        imagesetthickness($this->resource, $lineThickness);
    }
    /**
     * @param array $polygon
     * @param array $fillColor
     * @param int $count
     */
    public function applyPolygon(array $polygon, array $fillColor, int $count)
    {
        if (!$fillColor) {
            return;
        }
        $fillColor = imagecolorallocate($this->resource, $fillColor[0], $fillColor[1], $fillColor[2]);
        if (version_compare(\PHP_VERSION, '8.1.0') === -1) {
            imagefilledpolygon($this->resource, $polygon, $count + 2, $fillColor);
        } else {
            imagefilledpolygon($this->resource, $polygon, $fillColor);
        }
    }
    /**
     * @param array $line
     * @param array $lineColor
     */
    public function applyLine(array $line, array $lineColor)
    {
        $lineColor = $this->getLineColor($lineColor);
        foreach ($line as $coordinates) {
            list($pictureX1, $pictureY1, $pictureX2, $pictureY2) = $coordinates;
            imageline($this->resource, $pictureX1, $pictureY1, $pictureX2, $pictureY2, $lineColor);
        }
    }
    /**
     * @param int $positionX
     * @param int $positionY
     * @param float $radius
     * @param array $color
     */
    public function applyDot(int $positionX, int $positionY, float $radius, array $color)
    {
        if (!$color || !$radius) {
            return;
        }
        $minimumColor = imagecolorallocate($this->resource, $color[0], $color[1], $color[2]);
        $dotDiameter = (int) round($radius * static::DOT_RADIUS_TO_WIDTH);
        imagefilledellipse($this->resource, $positionX, $positionY, $dotDiameter, $dotDiameter, $minimumColor);
    }
    /**
     * @param int $width
     * @param int $height
     *
     * @return resource
     */
    public function generate(int $width, int $height)
    {
        $sparkline = imagecreatetruecolor($width, $height);
        imagealphablending($sparkline, \false);
        imagecopyresampled($sparkline, $this->resource, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        imagesavealpha($sparkline, \true);
        imagedestroy($this->resource);
        return $sparkline;
    }
}
