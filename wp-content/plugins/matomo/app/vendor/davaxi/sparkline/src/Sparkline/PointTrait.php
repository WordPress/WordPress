<?php

namespace Davaxi\Sparkline;

use InvalidArgumentException;
/**
 * Trait PointTrait.
 */
trait PointTrait
{
    /**
     * @var array
     */
    protected $points = [];
    /**
     * @param int|string $index
     * @param float $dotRadius
     * @param string $colorHex
     * @param int $seriesIndex
     */
    public function addPoint($index, float $dotRadius, string $colorHex, int $seriesIndex = 0)
    {
        $mapping = $this->getPointIndexMapping($seriesIndex);
        if (array_key_exists($index, $mapping)) {
            $index = $mapping[$index];
            if ($index < 0) {
                return;
            }
        }
        if (!is_numeric($index)) {
            throw new InvalidArgumentException('Invalid index : ' . $index);
        }
        $this->checkPointIndex($index, $seriesIndex);
        $this->points[] = ['series' => $seriesIndex, 'index' => $index, 'radius' => $dotRadius, 'color' => $this->colorHexToRGB($colorHex)];
    }
    /**
     * @param int $seriesIndex
     * @return array
     */
    protected function getPointIndexMapping(int $seriesIndex = 0) : array
    {
        $count = $this->getCount($seriesIndex);
        list($minIndex, $min, $maxIndex, $max) = $this->getExtremeValues($seriesIndex);
        $mapping = [];
        $mapping['first'] = $count > 1 ? 0 : -1;
        $mapping['last'] = $count > 1 ? $count - 1 : -1;
        $mapping['minimum'] = $min !== $max ? $minIndex : -1;
        $mapping['maximum'] = $min !== $max ? $maxIndex : -1;
        return $mapping;
    }
    /**
     * @param int $index
     * @param int $seriesIndex
     */
    protected function checkPointIndex(int $index, int $seriesIndex)
    {
        $count = $this->getCount($seriesIndex);
        if ($index < 0 || $index >= $count) {
            throw new InvalidArgumentException('Index out of range [0-' . ($count - 1) . '] : ' . $index);
        }
    }
}
