<?php

if (class_exists('ParagonIE_Sodium_Core_XChaCha20', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_XChaCha20
 */
class ParagonIE_Sodium_Core_XChaCha20 extends ParagonIE_Sodium_Core_HChaCha20
{
    /**
     * @internal You should not use this directly from another application
     *
     * @param int $len
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function stream($len, $nonce, $key)
    {
        if (self::strlen($nonce) !== 24) {
            throw new SodiumException('Nonce must be 24 bytes long');
        }
        return self::encryptBytes(
            new ParagonIE_Sodium_Core_ChaCha20_Ctx(
                self::hChaCha20(
                    self::substr($nonce, 0, 16),
                    $key
                ),
                self::substr($nonce, 16, 8)
            ),
            str_repeat("\x00", $len)
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $len
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ietfStream($len, $nonce, $key)
    {
        if (self::strlen($nonce) !== 24) {
            throw new SodiumException('Nonce must be 24 bytes long');
        }
        return self::encryptBytes(
            new ParagonIE_Sodium_Core_ChaCha20_IetfCtx(
                self::hChaCha20(
                    self::substr($nonce, 0, 16),
                    $key
                ),
                "\x00\x00\x00\x00" . self::substr($nonce, 16, 8)
            ),
            str_repeat("\x00", $len)
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $message
     * @param string $nonce
     * @param string $key
     * @param string $ic
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function streamXorIc($message, $nonce, $key, $ic = '')
    {
        if (self::strlen($nonce) !== 24) {
            throw new SodiumException('Nonce must be 24 bytes long');
        }
        return self::encryptBytes(
            new ParagonIE_Sodium_Core_ChaCha20_Ctx(
                self::hChaCha20(self::substr($nonce, 0, 16), $key),
                self::substr($nonce, 16, 8),
                $ic
            ),
            $message
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $message
     * @param string $nonce
     * @param string $key
     * @param string $ic
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ietfStreamXorIc($message, $nonce, $key, $ic = '')
    {
        if (self::strlen($nonce) !== 24) {
            throw new SodiumException('Nonce must be 24 bytes long');
        }
        return self::encryptBytes(
            new ParagonIE_Sodium_Core_ChaCha20_IetfCtx(
                self::hChaCha20(self::substr($nonce, 0, 16), $key),
                "\x00\x00\x00\x00" . self::substr($nonce, 16, 8),
                $ic
            ),
            $message
        );
    }
}
