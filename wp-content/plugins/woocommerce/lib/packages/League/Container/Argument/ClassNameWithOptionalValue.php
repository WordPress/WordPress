<?php

namespace Automattic\WooCommerce\Vendor\League\Container\Argument;

class ClassNameWithOptionalValue implements ClassNameInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var mixed
     */
    private $optionalValue;

    /**
     * @param string $className
     * @param mixed $optionalValue
     */
    public function __construct(string $className, $optionalValue)
    {
        $this->className = $className;
        $this->optionalValue = $optionalValue;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function getOptionalValue()
    {
        return $this->optionalValue;
    }
}
