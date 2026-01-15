<?php

if (class_exists('ParagonIE_Sodium_Core_HChaCha20', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_HChaCha20
 */
class ParagonIE_Sodium_Core_HChaCha20 extends ParagonIE_Sodium_Core_ChaCha20
{
    /**
     * @param string $in
     * @param string $key
     * @param string|null $c
     * @return string
     *
     * @throws SodiumException
     * @throws TypeError
     */
    public static function hChaCha20($in, $key, $c = null)
    {
        if (self::strlen($in) !== 16) {
            throw new SodiumException('Argument 1 must be 16 bytes');
        }
        if (self::strlen($key) !== 32) {
            throw new SodiumException('Argument 2 must be 32 bytes');
        }
        $ctx = array();

        if ($c === null) {
            $ctx[0] = 0x61707865;
            $ctx[1] = 0x3320646e;
            $ctx[2] = 0x79622d32;
            $ctx[3] = 0x6b206574;
        } else {
            $ctx[0] = self::load_4(self::substr($c,  0, 4));
            $ctx[1] = self::load_4(self::substr($c,  4, 4));
            $ctx[2] = self::load_4(self::substr($c,  8, 4));
            $ctx[3] = self::load_4(self::substr($c, 12, 4));
        }
        $ctx[4]  = self::load_4(self::substr($key,  0, 4));
        $ctx[5]  = self::load_4(self::substr($key,  4, 4));
        $ctx[6]  = self::load_4(self::substr($key,  8, 4));
        $ctx[7]  = self::load_4(self::substr($key, 12, 4));
        $ctx[8]  = self::load_4(self::substr($key, 16, 4));
        $ctx[9]  = self::load_4(self::substr($key, 20, 4));
        $ctx[10] = self::load_4(self::substr($key, 24, 4));
        $ctx[11] = self::load_4(self::substr($key, 28, 4));
        $ctx[12] = self::load_4(self::substr($in,   0, 4));
        $ctx[13] = self::load_4(self::substr($in,   4, 4));
        $ctx[14] = self::load_4(self::substr($in,   8, 4));
        $ctx[15] = self::load_4(self::substr($in,  12, 4));
        return self::hChaCha20Bytes($ctx);
    }

    /**
     * @param array $ctx
     * @return string
     * @throws TypeError
     */
    protected static function hChaCha20Bytes(array $ctx)
    {
        $x0  = (int) $ctx[0];
        $x1  = (int) $ctx[1];
        $x2  = (int) $ctx[2];
        $x3  = (int) $ctx[3];
        $x4  = (int) $ctx[4];
        $x5  = (int) $ctx[5];
        $x6  = (int) $ctx[6];
        $x7  = (int) $ctx[7];
        $x8  = (int) $ctx[8];
        $x9  = (int) $ctx[9];
        $x10 = (int) $ctx[10];
        $x11 = (int) $ctx[11];
        $x12 = (int) $ctx[12];
        $x13 = (int) $ctx[13];
        $x14 = (int) $ctx[14];
        $x15 = (int) $ctx[15];

        for ($i = 0; $i < 10; ++$i) {
            # QUARTERROUND( x0,  x4,  x8,  x12)
            list($x0, $x4, $x8, $x12) = self::quarterRound($x0, $x4, $x8, $x12);

            # QUARTERROUND( x1,  x5,  x9,  x13)
            list($x1, $x5, $x9, $x13) = self::quarterRound($x1, $x5, $x9, $x13);

            # QUARTERROUND( x2,  x6,  x10,  x14)
            list($x2, $x6, $x10, $x14) = self::quarterRound($x2, $x6, $x10, $x14);

            # QUARTERROUND( x3,  x7,  x11,  x15)
            list($x3, $x7, $x11, $x15) = self::quarterRound($x3, $x7, $x11, $x15);

            # QUARTERROUND( x0,  x5,  x10,  x15)
            list($x0, $x5, $x10, $x15) = self::quarterRound($x0, $x5, $x10, $x15);

            # QUARTERROUND( x1,  x6,  x11,  x12)
            list($x1, $x6, $x11, $x12) = self::quarterRound($x1, $x6, $x11, $x12);

            # QUARTERROUND( x2,  x7,  x8,  x13)
            list($x2, $x7, $x8, $x13) = self::quarterRound($x2, $x7, $x8, $x13);

            # QUARTERROUND( x3,  x4,  x9,  x14)
            list($x3, $x4, $x9, $x14) = self::quarterRound($x3, $x4, $x9, $x14);
        }

        return self::store32_le((int) ($x0  & 0xffffffff)) .
            self::store32_le((int) ($x1  & 0xffffffff)) .
            self::store32_le((int) ($x2  & 0xffffffff)) .
            self::store32_le((int) ($x3  & 0xffffffff)) .
            self::store32_le((int) ($x12 & 0xffffffff)) .
            self::store32_le((int) ($x13 & 0xffffffff)) .
            self::store32_le((int) ($x14 & 0xffffffff)) .
            self::store32_le((int) ($x15 & 0xffffffff));
    }
}
