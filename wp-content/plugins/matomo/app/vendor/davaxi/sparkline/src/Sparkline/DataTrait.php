<?php

namespace Davaxi\Sparkline;

/**
 * Trait DataTrait.
 */
trait DataTrait
{
    /**
     * @var int
     *          Base of value
     */
    protected $base;
    /**
     * @var int
     *          Original value of chart
     */
    protected $originValue = 0;
    /**
     * @var array
     */
    protected $data = [[0, 0]];
    /**
     * @param $base
     * Set base for values
     */
    public function setBase($base)
    {
        $this->base = $base;
    }
    /**
     * @param float $originValue
     *                           Set origin value of chart
     */
    public function setOriginValue(float $originValue)
    {
        $this->originValue = $originValue;
    }
    /**
     * @param ...$allSeries
     */
    public function setData(...$allSeries)
    {
        $allSeries = func_get_args();
        $this->data = [];
        foreach ($allSeries as $data) {
            $this->addSeries($data);
        }
    }
    /**
     * @param array $data
     */
    public function addSeries(array $data)
    {
        $data = array_values($data);
        $count = count($data);
        if (!$count) {
            $this->data[] = [0, 0];
            return;
        }
        if ($count < static::MIN_DATA_LENGTH) {
            $this->data[] = array_fill(0, 2, $data[0]);
            return;
        }
        $this->data[] = $data;
    }
    /**
     * @return int
     */
    public function getSeriesCount() : int
    {
        return count($this->data);
    }
    /**
     * @param int $seriesIndex
     * @return array
     */
    public function getNormalizedData(int $seriesIndex = 0) : array
    {
        $data = $this->data[$seriesIndex];
        foreach ($data as $i => $value) {
            $data[$i] = max(0, $value - $this->originValue);
        }
        return $data;
    }
    /**
     * @param int $seriesIndex
     * @return array
     */
    public function getData(int $seriesIndex = 0) : array
    {
        return $this->data[$seriesIndex];
    }
    /**
     * @param int $seriesIndex
     * @return int
     */
    public function getCount(int $seriesIndex = 0) : int
    {
        return count($this->data[$seriesIndex]);
    }
    /**
     * @param int $seriesIndex
     * @return array
     */
    protected function getMaxValueWithIndex(int $seriesIndex = 0) : array
    {
        $max = max($this->data[$seriesIndex]);
        $maxKeys = array_keys($this->data[$seriesIndex], $max);
        $maxIndex = end($maxKeys);
        if ($this->base) {
            $max = $this->base;
        }
        return [$maxIndex, $max];
    }
    /**
     * @param int $seriesIndex
     * @return float
     */
    protected function getMaxValue(int $seriesIndex = 0) : float
    {
        if ($this->base) {
            return $this->base;
        }
        return max($this->data[$seriesIndex]);
    }
    /**
     * TODO: this could be cached somehow
     * @return float
     */
    protected function getMaxValueAcrossSeries() : float
    {
        if ($this->base) {
            return $this->base;
        }
        $maxes = array_map('max', $this->data);
        return max($maxes);
    }
    protected function getMaxNumberOfDataPointsAcrossSerieses()
    {
        $counts = array_map('count', $this->data);
        return max($counts);
    }
    /**
     * @param int $seriesIndex
     * @return array
     */
    protected function getMinValueWithIndex(int $seriesIndex = 0) : array
    {
        $min = min($this->data[$seriesIndex]);
        $minKey = array_keys($this->data[$seriesIndex], $min);
        $minIndex = end($minKey);
        return [$minIndex, $min];
    }
    /**
     * @param int $seriesIndex
     * @return array
     */
    protected function getExtremeValues(int $seriesIndex = 0) : array
    {
        list($minIndex, $min) = $this->getMinValueWithIndex($seriesIndex);
        list($maxIndex, $max) = $this->getMaxValueWithIndex($seriesIndex);
        return [$minIndex, $min, $maxIndex, $max];
    }
}
