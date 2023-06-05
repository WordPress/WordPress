<?php declare(strict_types=1);

namespace Automattic\WooCommerce\Vendor\League\Container\Argument;

class RawArgument implements RawArgumentInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Construct.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }
}
