<?php

if (class_exists('ParagonIE_Sodium_Core_Curve25519_Fe', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_Curve25519_Fe
 *
 * This represents a Field Element
 */
class ParagonIE_Sodium_Core_Curve25519_Fe implements ArrayAccess
{
    /**
     * @var array
     */
    protected $container = array();

    /**
     * @var int
     */
    protected $size = 10;

    /**
     * @internal You should not use this directly from another application
     *
     * @param array $array
     * @param bool $save_indexes
     * @return self
     */
    public static function fromArray($array, $save_indexes = null)
    {
        $count = count($array);
        if ($save_indexes) {
            $keys = array_keys($array);
        } else {
            $keys = range(0, $count - 1);
        }
        $array = array_values($array);

        $obj = new ParagonIE_Sodium_Core_Curve25519_Fe();
        if ($save_indexes) {
            for ($i = 0; $i < $count; ++$i) {
                $obj->offsetSet($keys[$i], $array[$i]);
            }
        } else {
            for ($i = 0; $i < $count; ++$i) {
                $obj->offsetSet($i, $array[$i]);
            }
        }
        return $obj;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetSet($offset, $value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException('Expected an integer');
        }
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param mixed $offset
     * @return bool
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param mixed $offset
     * @return void
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param mixed $offset
     * @return mixed|null
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset])
            ? $this->container[$offset]
            : null;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return array
     */
    public function __debugInfo()
    {
        return array(implode(', ', $this->container));
    }
}
