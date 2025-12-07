<?php

declare(strict_types=1);

namespace Sentry\Tracing;

final class TransactionMetadata
{
    /**
     * @var float|int|null
     */
    private $samplingRate;

    /**
     * @var DynamicSamplingContext|null
     */
    private $dynamicSamplingContext;

    /**
     * @var TransactionSource|null
     */
    private $source;

    /**
     * @var float|int|null
     */
    private $parentSamplingRate;

    /**
     * @var float|int|null
     */
    private $sampleRand;

    /**
     * Constructor.
     *
     * @param float|int|null              $samplingRate           The sampling rate
     * @param DynamicSamplingContext|null $dynamicSamplingContext The Dynamic Sampling Context
     * @param TransactionSource|null      $source                 The transaction source
     * @param float|null                  $parentSamplingRate     The parent sampling rate
     * @param float|null                  $sampleRand             The trace sample rand
     */
    public function __construct(
        $samplingRate = null,
        ?DynamicSamplingContext $dynamicSamplingContext = null,
        ?TransactionSource $source = null,
        ?float $parentSamplingRate = null,
        ?float $sampleRand = null
    ) {
        $this->samplingRate = $samplingRate;
        $this->dynamicSamplingContext = $dynamicSamplingContext;
        $this->source = $source ?? TransactionSource::custom();
        $this->parentSamplingRate = $parentSamplingRate;
        $this->sampleRand = $sampleRand ?? round(mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax(), 6);
    }

    /**
     * @return float|int|null
     */
    public function getSamplingRate()
    {
        return $this->samplingRate;
    }

    /**
     * @param float|int|null $samplingRate
     */
    public function setSamplingRate($samplingRate): self
    {
        $this->samplingRate = $samplingRate;

        return $this;
    }

    public function getParentSamplingRate(): ?float
    {
        return $this->parentSamplingRate;
    }

    public function setParentSamplingRate(?float $parentSamplingRate): self
    {
        $this->parentSamplingRate = $parentSamplingRate;

        return $this;
    }

    public function getSampleRand(): ?float
    {
        return $this->sampleRand;
    }

    public function setSampleRand(?float $sampleRand): self
    {
        $this->sampleRand = $sampleRand;

        return $this;
    }

    public function getDynamicSamplingContext(): ?DynamicSamplingContext
    {
        return $this->dynamicSamplingContext;
    }

    public function setDynamicSamplingContext(?DynamicSamplingContext $dynamicSamplingContext): self
    {
        $this->dynamicSamplingContext = $dynamicSamplingContext;

        return $this;
    }

    public function getSource(): ?TransactionSource
    {
        return $this->source;
    }

    public function setSource(?TransactionSource $source): self
    {
        $this->source = $source;

        return $this;
    }
}
