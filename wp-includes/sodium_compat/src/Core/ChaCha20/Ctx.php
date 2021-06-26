<?php

if (class_exists('ParagonIE_Sodium_Core_ChaCha20_Ctx', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_ChaCha20_Ctx
 */
class ParagonIE_Sodium_Core_ChaCha20_Ctx extends ParagonIE_Sodium_Core_Util implements ArrayAccess
{
    /**
     * @var SplFixedArray internally, <int, int>
     */
    protected $container;

    /**
     * ParagonIE_Sodium_Core_ChaCha20_Ctx constructor.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $key     ChaCha20 key.
     * @param string $iv      Initialization Vector (a.k.a. nonce).
     * @param string $counter The initial counter value.
     *                        Defaults to 8 0x00 bytes.
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function __construct($key = '', $iv = '', $counter = '')
    {
        if (self::strlen($key) !== 32) {
            throw new InvalidArgumentException('ChaCha20 expects a 256-bit key.');
        }
        if (self::strlen($iv) !== 8) {
            throw new InvalidArgumentException('ChaCha20 expects a 64-bit nonce.');
        }
        $this->container = new SplFixedArray(16);

        /* "expand 32-byte k" as per ChaCha20 spec */
        $this->container[0]  = 0x61707865;
        $this->container[1]  = 0x3320646e;
        $this->container[2]  = 0x79622d32;
        $this->container[3]  = 0x6b206574;
        $this->container[4]  = self::load_4(self::substr($key, 0, 4));
        $this->container[5]  = self::load_4(self::substr($key, 4, 4));
        $this->container[6]  = self::load_4(self::substr($key, 8, 4));
        $this->container[7]  = self::load_4(self::substr($key, 12, 4));
        $this->container[8]  = self::load_4(self::substr($key, 16, 4));
        $this->container[9]  = self::load_4(self::substr($key, 20, 4));
        $this->container[10] = self::load_4(self::substr($key, 24, 4));
        $this->container[11] = self::load_4(self::substr($key, 28, 4));

        if (empty($counter)) {
            $this->container[12] = 0;
            $this->container[13] = 0;
        } else {
            $this->container[12] = self::load_4(self::substr($counter, 0, 4));
            $this->container[13] = self::load_4(self::substr($counter, 4, 4));
        }
        $this->container[14] = self::load_4(self::substr($iv, 0, 4));
        $this->container[15] = self::load_4(self::substr($iv, 4, 4));
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @param int $value
     * @return void
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetSet($offset, $value)
    {
        if (!is_int($offset)) {
            throw new InvalidArgumentException('Expected an integer');
        }
        if (!is_int($value)) {
            throw new InvalidArgumentException('Expected an integer');
        }
        $this->container[$offset] = $value;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
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
     * @param int $offset
     * @return mixed|null
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset])
            ? $this->container[$offset]
            : null;
    }
}
