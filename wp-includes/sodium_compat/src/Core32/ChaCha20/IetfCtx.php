<?php

if (class_exists('ParagonIE_Sodium_Core_ChaCha20_IetfCtx', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core32_ChaCha20_IetfCtx
 */
class ParagonIE_Sodium_Core32_ChaCha20_IetfCtx extends ParagonIE_Sodium_Core32_ChaCha20_Ctx
{
    /**
     * ParagonIE_Sodium_Core_ChaCha20_IetfCtx constructor.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $key     ChaCha20 key.
     * @param string $iv      Initialization Vector (a.k.a. nonce).
     * @param string $counter The initial counter value.
     *                        Defaults to 4 0x00 bytes.
     * @throws InvalidArgumentException
     * @throws SodiumException
     * @throws TypeError
     */
    public function __construct($key = '', $iv = '', $counter = '')
    {
        if (self::strlen($iv) !== 12) {
            throw new InvalidArgumentException('ChaCha20 expects a 96-bit nonce in IETF mode.');
        }
        parent::__construct($key, self::substr($iv, 0, 8), $counter);

        if (!empty($counter)) {
            $this->container[12] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($counter, 0, 4));
        }
        $this->container[13] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($iv, 0, 4));
        $this->container[14] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($iv, 4, 4));
        $this->container[15] = ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($iv, 8, 4));
    }
}
