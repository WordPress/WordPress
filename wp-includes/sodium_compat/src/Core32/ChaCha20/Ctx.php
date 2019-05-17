<?php

if (class_exists('ParagonIE_Sodium_Core_ChaCha20_Ctx', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core32_ChaCha20_Ctx
 */
class ParagonIE_Sodium_Core32_ChaCha20_Ctx extends ParagonIE_Sodium_Core32_Util implements ArrayAccess
{
    /**
     * @var SplFixedArray internally, <int, ParagonIE_Sodium_Core32_Int32>
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
     * @throws SodiumException
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
        $this->container[0]  = new ParagonIE_Sodium_Core32_Int32(array(0x6170, 0x7865));
        $this->container[1]  = new ParagonIE_Sodium_Core32_Int32(array(0x3320, 0x646e));
        $this->container[2]  = new ParagonIE_Sodium_Core32_Int32(array(0x7962, 0x2d32));
        $this->container[3]  = new ParagonIE_Sodium_Core32_Int32(array(0x6b20, 0x6574));

        $this->container[4]  = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 0, 4));
        $this->container[5]  = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 4, 4));
        $this->container[6]  = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 8, 4));
        $this->container[7]  = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 12, 4));
        $this->container[8]  = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 16, 4));
        $this->container[9]  = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 20, 4));
        $this->container[10] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 24, 4));
        $this->container[11] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 28, 4));

        if (empty($counter)) {
            $this->container[12] = new ParagonIE_Sodium_Core32_Int32();
            $this->container[13] = new ParagonIE_Sodium_Core32_Int32();
        } else {
            $this->container[12] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($counter, 0, 4));
            $this->container[13] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($counter, 4, 4));
        }
        $this->container[14] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($iv, 0, 4));
        $this->container[15] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($iv, 4, 4));
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @param int|ParagonIE_Sodium_Core32_Int32 $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!is_int($offset)) {
            throw new InvalidArgumentException('Expected an integer');
        }
        if ($value instanceof ParagonIE_Sodium_Core32_Int32) {
            /*
        } elseif (is_int($value)) {
            $value = ParagonIE_Sodium_Core32_Int32::fromInt($value);
            */
        } else {
            throw new InvalidArgumentException('Expected an integer');
        }
        $this->container[$offset] = $value;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
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
