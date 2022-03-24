<?php

if (class_exists('ParagonIE_Sodium_Core_Curve25519', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_Curve25519
 *
 * Implements Curve25519 core functions
 *
 * Based on the ref10 curve25519 code provided by libsodium
 *
 * @ref https://github.com/jedisct1/libsodium/blob/master/src/libsodium/crypto_core/curve25519/ref10/curve25519_ref10.c
 */
abstract class ParagonIE_Sodium_Core_Curve25519 extends ParagonIE_Sodium_Core_Curve25519_H
{
    /**
     * Get a field element of size 10 with a value of 0
     *
     * @internal You should not use this directly from another application
     *
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_0()
    {
        return ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(
            array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0)
        );
    }

    /**
     * Get a field element of size 10 with a value of 1
     *
     * @internal You should not use this directly from another application
     *
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_1()
    {
        return ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(
            array(1, 0, 0, 0, 0, 0, 0, 0, 0, 0)
        );
    }

    /**
     * Add two field elements.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $g
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedOperand
     */
    public static function fe_add(
        ParagonIE_Sodium_Core_Curve25519_Fe $f,
        ParagonIE_Sodium_Core_Curve25519_Fe $g
    ) {
        /** @var array<int, int> $arr */
        $arr = array();
        for ($i = 0; $i < 10; ++$i) {
            $arr[$i] = (int) ($f[$i] + $g[$i]);
        }
        return ParagonIE_Sodium_Core_Curve25519_Fe::fromArray($arr);
    }

    /**
     * Constant-time conditional move.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $g
     * @param int $b
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     * @psalm-suppress MixedAssignment
     */
    public static function fe_cmov(
        ParagonIE_Sodium_Core_Curve25519_Fe $f,
        ParagonIE_Sodium_Core_Curve25519_Fe $g,
        $b = 0
    ) {
        /** @var array<int, int> $h */
        $h = array();
        $b *= -1;
        for ($i = 0; $i < 10; ++$i) {
            $x = (($f[$i] ^ $g[$i]) & $b);
            $h[$i] = ($f[$i]) ^ $x;
        }
        return ParagonIE_Sodium_Core_Curve25519_Fe::fromArray($h);
    }

    /**
     * Create a copy of a field element.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_copy(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $h = clone $f;
        return $h;
    }

    /**
     * Give: 32-byte string.
     * Receive: A field element object to use for internal calculations.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $s
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     * @throws RangeException
     * @throws TypeError
     */
    public static function fe_frombytes($s)
    {
        if (self::strlen($s) !== 32) {
            throw new RangeException('Expected a 32-byte string.');
        }
        $h0 = self::load_4($s);
        $h1 = self::load_3(self::substr($s, 4, 3)) << 6;
        $h2 = self::load_3(self::substr($s, 7, 3)) << 5;
        $h3 = self::load_3(self::substr($s, 10, 3)) << 3;
        $h4 = self::load_3(self::substr($s, 13, 3)) << 2;
        $h5 = self::load_4(self::substr($s, 16, 4));
        $h6 = self::load_3(self::substr($s, 20, 3)) << 7;
        $h7 = self::load_3(self::substr($s, 23, 3)) << 5;
        $h8 = self::load_3(self::substr($s, 26, 3)) << 4;
        $h9 = (self::load_3(self::substr($s, 29, 3)) & 8388607) << 2;

        $carry9 = ($h9 + (1 << 24)) >> 25;
        $h0 += self::mul($carry9, 19, 5);
        $h9 -= $carry9 << 25;
        $carry1 = ($h1 + (1 << 24)) >> 25;
        $h2 += $carry1;
        $h1 -= $carry1 << 25;
        $carry3 = ($h3 + (1 << 24)) >> 25;
        $h4 += $carry3;
        $h3 -= $carry3 << 25;
        $carry5 = ($h5 + (1 << 24)) >> 25;
        $h6 += $carry5;
        $h5 -= $carry5 << 25;
        $carry7 = ($h7 + (1 << 24)) >> 25;
        $h8 += $carry7;
        $h7 -= $carry7 << 25;

        $carry0 = ($h0 + (1 << 25)) >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;
        $carry2 = ($h2 + (1 << 25)) >> 26;
        $h3 += $carry2;
        $h2 -= $carry2 << 26;
        $carry4 = ($h4 + (1 << 25)) >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;
        $carry6 = ($h6 + (1 << 25)) >> 26;
        $h7 += $carry6;
        $h6 -= $carry6 << 26;
        $carry8 = ($h8 + (1 << 25)) >> 26;
        $h9 += $carry8;
        $h8 -= $carry8 << 26;

        return ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(
            array(
                (int) $h0,
                (int) $h1,
                (int) $h2,
                (int) $h3,
                (int) $h4,
                (int) $h5,
                (int) $h6,
                (int) $h7,
                (int) $h8,
                (int) $h9
            )
        );
    }

    /**
     * Convert a field element to a byte string.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $h
     * @return string
     */
    public static function fe_tobytes(ParagonIE_Sodium_Core_Curve25519_Fe $h)
    {
        $h0 = (int) $h[0];
        $h1 = (int) $h[1];
        $h2 = (int) $h[2];
        $h3 = (int) $h[3];
        $h4 = (int) $h[4];
        $h5 = (int) $h[5];
        $h6 = (int) $h[6];
        $h7 = (int) $h[7];
        $h8 = (int) $h[8];
        $h9 = (int) $h[9];

        $q = (self::mul($h9, 19, 5) + (1 << 24)) >> 25;
        $q = ($h0 + $q) >> 26;
        $q = ($h1 + $q) >> 25;
        $q = ($h2 + $q) >> 26;
        $q = ($h3 + $q) >> 25;
        $q = ($h4 + $q) >> 26;
        $q = ($h5 + $q) >> 25;
        $q = ($h6 + $q) >> 26;
        $q = ($h7 + $q) >> 25;
        $q = ($h8 + $q) >> 26;
        $q = ($h9 + $q) >> 25;

        $h0 += self::mul($q, 19, 5);

        $carry0 = $h0 >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;
        $carry1 = $h1 >> 25;
        $h2 += $carry1;
        $h1 -= $carry1 << 25;
        $carry2 = $h2 >> 26;
        $h3 += $carry2;
        $h2 -= $carry2 << 26;
        $carry3 = $h3 >> 25;
        $h4 += $carry3;
        $h3 -= $carry3 << 25;
        $carry4 = $h4 >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;
        $carry5 = $h5 >> 25;
        $h6 += $carry5;
        $h5 -= $carry5 << 25;
        $carry6 = $h6 >> 26;
        $h7 += $carry6;
        $h6 -= $carry6 << 26;
        $carry7 = $h7 >> 25;
        $h8 += $carry7;
        $h7 -= $carry7 << 25;
        $carry8 = $h8 >> 26;
        $h9 += $carry8;
        $h8 -= $carry8 << 26;
        $carry9 = $h9 >> 25;
        $h9 -= $carry9 << 25;

        /**
         * @var array<int, int>
         */
        $s = array(
            (int) (($h0 >> 0) & 0xff),
            (int) (($h0 >> 8) & 0xff),
            (int) (($h0 >> 16) & 0xff),
            (int) ((($h0 >> 24) | ($h1 << 2)) & 0xff),
            (int) (($h1 >> 6) & 0xff),
            (int) (($h1 >> 14) & 0xff),
            (int) ((($h1 >> 22) | ($h2 << 3)) & 0xff),
            (int) (($h2 >> 5) & 0xff),
            (int) (($h2 >> 13) & 0xff),
            (int) ((($h2 >> 21) | ($h3 << 5)) & 0xff),
            (int) (($h3 >> 3) & 0xff),
            (int) (($h3 >> 11) & 0xff),
            (int) ((($h3 >> 19) | ($h4 << 6)) & 0xff),
            (int) (($h4 >> 2) & 0xff),
            (int) (($h4 >> 10) & 0xff),
            (int) (($h4 >> 18) & 0xff),
            (int) (($h5 >> 0) & 0xff),
            (int) (($h5 >> 8) & 0xff),
            (int) (($h5 >> 16) & 0xff),
            (int) ((($h5 >> 24) | ($h6 << 1)) & 0xff),
            (int) (($h6 >> 7) & 0xff),
            (int) (($h6 >> 15) & 0xff),
            (int) ((($h6 >> 23) | ($h7 << 3)) & 0xff),
            (int) (($h7 >> 5) & 0xff),
            (int) (($h7 >> 13) & 0xff),
            (int) ((($h7 >> 21) | ($h8 << 4)) & 0xff),
            (int) (($h8 >> 4) & 0xff),
            (int) (($h8 >> 12) & 0xff),
            (int) ((($h8 >> 20) | ($h9 << 6)) & 0xff),
            (int) (($h9 >> 2) & 0xff),
            (int) (($h9 >> 10) & 0xff),
            (int) (($h9 >> 18) & 0xff)
        );
        return self::intArrayToString($s);
    }

    /**
     * Is a field element negative? (1 = yes, 0 = no. Used in calculations.)
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return int
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_isnegative(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $str = self::fe_tobytes($f);
        return (int) (self::chrToInt($str[0]) & 1);
    }

    /**
     * Returns 0 if this field element results in all NUL bytes.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_isnonzero(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        static $zero;
        if ($zero === null) {
            $zero = str_repeat("\x00", 32);
        }
        /** @var string $zero */
        /** @var string $str */
        $str = self::fe_tobytes($f);
        return !self::verify_32($str, (string) $zero);
    }

    /**
     * Multiply two field elements
     *
     * h = f * g
     *
     * @internal You should not use this directly from another application
     *
     * @security Is multiplication a source of timing leaks? If so, can we do
     *           anything to prevent that from happening?
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $g
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_mul(
        ParagonIE_Sodium_Core_Curve25519_Fe $f,
        ParagonIE_Sodium_Core_Curve25519_Fe $g
    ) {
        // Ensure limbs aren't oversized.
        $f = self::fe_normalize($f);
        $g = self::fe_normalize($g);
        $f0 = $f[0];
        $f1 = $f[1];
        $f2 = $f[2];
        $f3 = $f[3];
        $f4 = $f[4];
        $f5 = $f[5];
        $f6 = $f[6];
        $f7 = $f[7];
        $f8 = $f[8];
        $f9 = $f[9];
        $g0 = $g[0];
        $g1 = $g[1];
        $g2 = $g[2];
        $g3 = $g[3];
        $g4 = $g[4];
        $g5 = $g[5];
        $g6 = $g[6];
        $g7 = $g[7];
        $g8 = $g[8];
        $g9 = $g[9];
        $g1_19 = self::mul($g1, 19, 5);
        $g2_19 = self::mul($g2, 19, 5);
        $g3_19 = self::mul($g3, 19, 5);
        $g4_19 = self::mul($g4, 19, 5);
        $g5_19 = self::mul($g5, 19, 5);
        $g6_19 = self::mul($g6, 19, 5);
        $g7_19 = self::mul($g7, 19, 5);
        $g8_19 = self::mul($g8, 19, 5);
        $g9_19 = self::mul($g9, 19, 5);
        $f1_2 = $f1 << 1;
        $f3_2 = $f3 << 1;
        $f5_2 = $f5 << 1;
        $f7_2 = $f7 << 1;
        $f9_2 = $f9 << 1;
        $f0g0    = self::mul($f0,    $g0, 26);
        $f0g1    = self::mul($f0,    $g1, 25);
        $f0g2    = self::mul($f0,    $g2, 26);
        $f0g3    = self::mul($f0,    $g3, 25);
        $f0g4    = self::mul($f0,    $g4, 26);
        $f0g5    = self::mul($f0,    $g5, 25);
        $f0g6    = self::mul($f0,    $g6, 26);
        $f0g7    = self::mul($f0,    $g7, 25);
        $f0g8    = self::mul($f0,    $g8, 26);
        $f0g9    = self::mul($f0,    $g9, 26);
        $f1g0    = self::mul($f1,    $g0, 26);
        $f1g1_2  = self::mul($f1_2,  $g1, 25);
        $f1g2    = self::mul($f1,    $g2, 26);
        $f1g3_2  = self::mul($f1_2,  $g3, 25);
        $f1g4    = self::mul($f1,    $g4, 26);
        $f1g5_2  = self::mul($f1_2,  $g5, 25);
        $f1g6    = self::mul($f1,    $g6, 26);
        $f1g7_2  = self::mul($f1_2,  $g7, 25);
        $f1g8    = self::mul($f1,    $g8, 26);
        $f1g9_38 = self::mul($g9_19, $f1_2, 26);
        $f2g0    = self::mul($f2,    $g0, 26);
        $f2g1    = self::mul($f2,    $g1, 25);
        $f2g2    = self::mul($f2,    $g2, 26);
        $f2g3    = self::mul($f2,    $g3, 25);
        $f2g4    = self::mul($f2,    $g4, 26);
        $f2g5    = self::mul($f2,    $g5, 25);
        $f2g6    = self::mul($f2,    $g6, 26);
        $f2g7    = self::mul($f2,    $g7, 25);
        $f2g8_19 = self::mul($g8_19, $f2, 26);
        $f2g9_19 = self::mul($g9_19, $f2, 26);
        $f3g0    = self::mul($f3,    $g0, 26);
        $f3g1_2  = self::mul($f3_2,  $g1, 25);
        $f3g2    = self::mul($f3,    $g2, 26);
        $f3g3_2  = self::mul($f3_2,  $g3, 25);
        $f3g4    = self::mul($f3,    $g4, 26);
        $f3g5_2  = self::mul($f3_2,  $g5, 25);
        $f3g6    = self::mul($f3,    $g6, 26);
        $f3g7_38 = self::mul($g7_19, $f3_2, 26);
        $f3g8_19 = self::mul($g8_19, $f3, 25);
        $f3g9_38 = self::mul($g9_19, $f3_2, 26);
        $f4g0    = self::mul($f4,    $g0, 26);
        $f4g1    = self::mul($f4,    $g1, 25);
        $f4g2    = self::mul($f4,    $g2, 26);
        $f4g3    = self::mul($f4,    $g3, 25);
        $f4g4    = self::mul($f4,    $g4, 26);
        $f4g5    = self::mul($f4,    $g5, 25);
        $f4g6_19 = self::mul($g6_19, $f4, 26);
        $f4g7_19 = self::mul($g7_19, $f4, 26);
        $f4g8_19 = self::mul($g8_19, $f4, 26);
        $f4g9_19 = self::mul($g9_19, $f4, 26);
        $f5g0    = self::mul($f5,    $g0, 26);
        $f5g1_2  = self::mul($f5_2,  $g1, 25);
        $f5g2    = self::mul($f5,    $g2, 26);
        $f5g3_2  = self::mul($f5_2,  $g3, 25);
        $f5g4    = self::mul($f5,    $g4, 26);
        $f5g5_38 = self::mul($g5_19, $f5_2, 26);
        $f5g6_19 = self::mul($g6_19, $f5, 25);
        $f5g7_38 = self::mul($g7_19, $f5_2, 26);
        $f5g8_19 = self::mul($g8_19, $f5, 25);
        $f5g9_38 = self::mul($g9_19, $f5_2, 26);
        $f6g0    = self::mul($f6,    $g0, 26);
        $f6g1    = self::mul($f6,    $g1, 25);
        $f6g2    = self::mul($f6,    $g2, 26);
        $f6g3    = self::mul($f6,    $g3, 25);
        $f6g4_19 = self::mul($g4_19, $f6, 26);
        $f6g5_19 = self::mul($g5_19, $f6, 26);
        $f6g6_19 = self::mul($g6_19, $f6, 26);
        $f6g7_19 = self::mul($g7_19, $f6, 26);
        $f6g8_19 = self::mul($g8_19, $f6, 26);
        $f6g9_19 = self::mul($g9_19, $f6, 26);
        $f7g0    = self::mul($f7,    $g0, 26);
        $f7g1_2  = self::mul($f7_2,  $g1, 25);
        $f7g2    = self::mul($f7,    $g2, 26);
        $f7g3_38 = self::mul($g3_19, $f7_2, 26);
        $f7g4_19 = self::mul($g4_19, $f7, 26);
        $f7g5_38 = self::mul($g5_19, $f7_2, 26);
        $f7g6_19 = self::mul($g6_19, $f7, 25);
        $f7g7_38 = self::mul($g7_19, $f7_2, 26);
        $f7g8_19 = self::mul($g8_19, $f7, 25);
        $f7g9_38 = self::mul($g9_19,$f7_2, 26);
        $f8g0    = self::mul($f8,    $g0, 26);
        $f8g1    = self::mul($f8,    $g1, 25);
        $f8g2_19 = self::mul($g2_19, $f8, 26);
        $f8g3_19 = self::mul($g3_19, $f8, 26);
        $f8g4_19 = self::mul($g4_19, $f8, 26);
        $f8g5_19 = self::mul($g5_19, $f8, 26);
        $f8g6_19 = self::mul($g6_19, $f8, 26);
        $f8g7_19 = self::mul($g7_19, $f8, 26);
        $f8g8_19 = self::mul($g8_19, $f8, 26);
        $f8g9_19 = self::mul($g9_19, $f8, 26);
        $f9g0    = self::mul($f9,    $g0, 26);
        $f9g1_38 = self::mul($g1_19, $f9_2, 26);
        $f9g2_19 = self::mul($g2_19, $f9, 25);
        $f9g3_38 = self::mul($g3_19, $f9_2, 26);
        $f9g4_19 = self::mul($g4_19, $f9, 25);
        $f9g5_38 = self::mul($g5_19, $f9_2, 26);
        $f9g6_19 = self::mul($g6_19, $f9, 25);
        $f9g7_38 = self::mul($g7_19, $f9_2, 26);
        $f9g8_19 = self::mul($g8_19, $f9, 25);
        $f9g9_38 = self::mul($g9_19, $f9_2, 26);

        $h0 = $f0g0 + $f1g9_38 + $f2g8_19 + $f3g7_38 + $f4g6_19 + $f5g5_38 + $f6g4_19 + $f7g3_38 + $f8g2_19 + $f9g1_38;
        $h1 = $f0g1 + $f1g0    + $f2g9_19 + $f3g8_19 + $f4g7_19 + $f5g6_19 + $f6g5_19 + $f7g4_19 + $f8g3_19 + $f9g2_19;
        $h2 = $f0g2 + $f1g1_2  + $f2g0    + $f3g9_38 + $f4g8_19 + $f5g7_38 + $f6g6_19 + $f7g5_38 + $f8g4_19 + $f9g3_38;
        $h3 = $f0g3 + $f1g2    + $f2g1    + $f3g0    + $f4g9_19 + $f5g8_19 + $f6g7_19 + $f7g6_19 + $f8g5_19 + $f9g4_19;
        $h4 = $f0g4 + $f1g3_2  + $f2g2    + $f3g1_2  + $f4g0    + $f5g9_38 + $f6g8_19 + $f7g7_38 + $f8g6_19 + $f9g5_38;
        $h5 = $f0g5 + $f1g4    + $f2g3    + $f3g2    + $f4g1    + $f5g0    + $f6g9_19 + $f7g8_19 + $f8g7_19 + $f9g6_19;
        $h6 = $f0g6 + $f1g5_2  + $f2g4    + $f3g3_2  + $f4g2    + $f5g1_2  + $f6g0    + $f7g9_38 + $f8g8_19 + $f9g7_38;
        $h7 = $f0g7 + $f1g6    + $f2g5    + $f3g4    + $f4g3    + $f5g2    + $f6g1    + $f7g0    + $f8g9_19 + $f9g8_19;
        $h8 = $f0g8 + $f1g7_2  + $f2g6    + $f3g5_2  + $f4g4    + $f5g3_2  + $f6g2    + $f7g1_2  + $f8g0    + $f9g9_38;
        $h9 = $f0g9 + $f1g8    + $f2g7    + $f3g6    + $f4g5    + $f5g4    + $f6g3    + $f7g2    + $f8g1    + $f9g0   ;

        $carry0 = ($h0 + (1 << 25)) >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;
        $carry4 = ($h4 + (1 << 25)) >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;

        $carry1 = ($h1 + (1 << 24)) >> 25;
        $h2 += $carry1;
        $h1 -= $carry1 << 25;
        $carry5 = ($h5 + (1 << 24)) >> 25;
        $h6 += $carry5;
        $h5 -= $carry5 << 25;

        $carry2 = ($h2 + (1 << 25)) >> 26;
        $h3 += $carry2;
        $h2 -= $carry2 << 26;
        $carry6 = ($h6 + (1 << 25)) >> 26;
        $h7 += $carry6;
        $h6 -= $carry6 << 26;

        $carry3 = ($h3 + (1 << 24)) >> 25;
        $h4 += $carry3;
        $h3 -= $carry3 << 25;
        $carry7 = ($h7 + (1 << 24)) >> 25;
        $h8 += $carry7;
        $h7 -= $carry7 << 25;

        $carry4 = ($h4 + (1 << 25)) >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;
        $carry8 = ($h8 + (1 << 25)) >> 26;
        $h9 += $carry8;
        $h8 -= $carry8 << 26;

        $carry9 = ($h9 + (1 << 24)) >> 25;
        $h0 += self::mul($carry9, 19, 5);
        $h9 -= $carry9 << 25;

        $carry0 = ($h0 + (1 << 25)) >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;

        return self::fe_normalize(
            ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(
                array(
                    (int) $h0,
                    (int) $h1,
                    (int) $h2,
                    (int) $h3,
                    (int) $h4,
                    (int) $h5,
                    (int) $h6,
                    (int) $h7,
                    (int) $h8,
                    (int) $h9
                )
            )
        );
    }

    /**
     * Get the negative values for each piece of the field element.
     *
     * h = -f
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     * @psalm-suppress MixedAssignment
     */
    public static function fe_neg(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $h = new ParagonIE_Sodium_Core_Curve25519_Fe();
        for ($i = 0; $i < 10; ++$i) {
            $h[$i] = -$f[$i];
        }
        return self::fe_normalize($h);
    }

    /**
     * Square a field element
     *
     * h = f * f
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_sq(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $f = self::fe_normalize($f);
        $f0 = (int) $f[0];
        $f1 = (int) $f[1];
        $f2 = (int) $f[2];
        $f3 = (int) $f[3];
        $f4 = (int) $f[4];
        $f5 = (int) $f[5];
        $f6 = (int) $f[6];
        $f7 = (int) $f[7];
        $f8 = (int) $f[8];
        $f9 = (int) $f[9];

        $f0_2 = $f0 << 1;
        $f1_2 = $f1 << 1;
        $f2_2 = $f2 << 1;
        $f3_2 = $f3 << 1;
        $f4_2 = $f4 << 1;
        $f5_2 = $f5 << 1;
        $f6_2 = $f6 << 1;
        $f7_2 = $f7 << 1;
        $f5_38 = self::mul($f5, 38, 6);
        $f6_19 = self::mul($f6, 19, 5);
        $f7_38 = self::mul($f7, 38, 6);
        $f8_19 = self::mul($f8, 19, 5);
        $f9_38 = self::mul($f9, 38, 6);
        $f0f0    = self::mul($f0,    $f0,    26);
        $f0f1_2  = self::mul($f0_2,  $f1,    26);
        $f0f2_2  = self::mul($f0_2,  $f2,    26);
        $f0f3_2  = self::mul($f0_2,  $f3,    26);
        $f0f4_2  = self::mul($f0_2,  $f4,    26);
        $f0f5_2  = self::mul($f0_2,  $f5,    26);
        $f0f6_2  = self::mul($f0_2,  $f6,    26);
        $f0f7_2  = self::mul($f0_2,  $f7,    26);
        $f0f8_2  = self::mul($f0_2,  $f8,    26);
        $f0f9_2  = self::mul($f0_2,  $f9,    26);
        $f1f1_2  = self::mul($f1_2,  $f1,    26);
        $f1f2_2  = self::mul($f1_2,  $f2,    26);
        $f1f3_4  = self::mul($f1_2,  $f3_2,  26);
        $f1f4_2  = self::mul($f1_2,  $f4,    26);
        $f1f5_4  = self::mul($f1_2,  $f5_2,  26);
        $f1f6_2  = self::mul($f1_2,  $f6,    26);
        $f1f7_4  = self::mul($f1_2,  $f7_2,  26);
        $f1f8_2  = self::mul($f1_2,  $f8,    26);
        $f1f9_76 = self::mul($f9_38, $f1_2,  27);
        $f2f2    = self::mul($f2,    $f2,    27);
        $f2f3_2  = self::mul($f2_2,  $f3,    27);
        $f2f4_2  = self::mul($f2_2,  $f4,    27);
        $f2f5_2  = self::mul($f2_2,  $f5,    27);
        $f2f6_2  = self::mul($f2_2,  $f6,    27);
        $f2f7_2  = self::mul($f2_2,  $f7,    27);
        $f2f8_38 = self::mul($f8_19, $f2_2,  27);
        $f2f9_38 = self::mul($f9_38, $f2,    26);
        $f3f3_2  = self::mul($f3_2,  $f3,    26);
        $f3f4_2  = self::mul($f3_2,  $f4,    26);
        $f3f5_4  = self::mul($f3_2,  $f5_2,  26);
        $f3f6_2  = self::mul($f3_2,  $f6,    26);
        $f3f7_76 = self::mul($f7_38, $f3_2,  26);
        $f3f8_38 = self::mul($f8_19, $f3_2,  26);
        $f3f9_76 = self::mul($f9_38, $f3_2,  26);
        $f4f4    = self::mul($f4,    $f4,    26);
        $f4f5_2  = self::mul($f4_2,  $f5,    26);
        $f4f6_38 = self::mul($f6_19, $f4_2,  27);
        $f4f7_38 = self::mul($f7_38, $f4,    26);
        $f4f8_38 = self::mul($f8_19, $f4_2,  27);
        $f4f9_38 = self::mul($f9_38, $f4,    26);
        $f5f5_38 = self::mul($f5_38, $f5,    26);
        $f5f6_38 = self::mul($f6_19, $f5_2,  26);
        $f5f7_76 = self::mul($f7_38, $f5_2,  26);
        $f5f8_38 = self::mul($f8_19, $f5_2,  26);
        $f5f9_76 = self::mul($f9_38, $f5_2,  26);
        $f6f6_19 = self::mul($f6_19, $f6,    26);
        $f6f7_38 = self::mul($f7_38, $f6,    26);
        $f6f8_38 = self::mul($f8_19, $f6_2,  27);
        $f6f9_38 = self::mul($f9_38, $f6,    26);
        $f7f7_38 = self::mul($f7_38, $f7,    26);
        $f7f8_38 = self::mul($f8_19, $f7_2,  26);
        $f7f9_76 = self::mul($f9_38, $f7_2,  26);
        $f8f8_19 = self::mul($f8_19, $f8,    26);
        $f8f9_38 = self::mul($f9_38, $f8,    26);
        $f9f9_38 = self::mul($f9_38, $f9,    26);
        $h0 = $f0f0   + $f1f9_76 + $f2f8_38 + $f3f7_76 + $f4f6_38 + $f5f5_38;
        $h1 = $f0f1_2 + $f2f9_38 + $f3f8_38 + $f4f7_38 + $f5f6_38;
        $h2 = $f0f2_2 + $f1f1_2  + $f3f9_76 + $f4f8_38 + $f5f7_76 + $f6f6_19;
        $h3 = $f0f3_2 + $f1f2_2  + $f4f9_38 + $f5f8_38 + $f6f7_38;
        $h4 = $f0f4_2 + $f1f3_4  + $f2f2    + $f5f9_76 + $f6f8_38 + $f7f7_38;
        $h5 = $f0f5_2 + $f1f4_2  + $f2f3_2  + $f6f9_38 + $f7f8_38;
        $h6 = $f0f6_2 + $f1f5_4  + $f2f4_2  + $f3f3_2  + $f7f9_76 + $f8f8_19;
        $h7 = $f0f7_2 + $f1f6_2  + $f2f5_2  + $f3f4_2  + $f8f9_38;
        $h8 = $f0f8_2 + $f1f7_4  + $f2f6_2  + $f3f5_4  + $f4f4    + $f9f9_38;
        $h9 = $f0f9_2 + $f1f8_2  + $f2f7_2  + $f3f6_2  + $f4f5_2;

        $carry0 = ($h0 + (1 << 25)) >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;
        $carry4 = ($h4 + (1 << 25)) >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;

        $carry1 = ($h1 + (1 << 24)) >> 25;
        $h2 += $carry1;
        $h1 -= $carry1 << 25;
        $carry5 = ($h5 + (1 << 24)) >> 25;
        $h6 += $carry5;
        $h5 -= $carry5 << 25;

        $carry2 = ($h2 + (1 << 25)) >> 26;
        $h3 += $carry2;
        $h2 -= $carry2 << 26;
        $carry6 = ($h6 + (1 << 25)) >> 26;
        $h7 += $carry6;
        $h6 -= $carry6 << 26;

        $carry3 = ($h3 + (1 << 24)) >> 25;
        $h4 += $carry3;
        $h3 -= $carry3 << 25;
        $carry7 = ($h7 + (1 << 24)) >> 25;
        $h8 += $carry7;
        $h7 -= $carry7 << 25;

        $carry4 = ($h4 + (1 << 25)) >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;
        $carry8 = ($h8 + (1 << 25)) >> 26;
        $h9 += $carry8;
        $h8 -= $carry8 << 26;

        $carry9 = ($h9 + (1 << 24)) >> 25;
        $h0 += self::mul($carry9, 19, 5);
        $h9 -= $carry9 << 25;

        $carry0 = ($h0 + (1 << 25)) >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;

        return self::fe_normalize(
            ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(
                array(
                    (int) $h0,
                    (int) $h1,
                    (int) $h2,
                    (int) $h3,
                    (int) $h4,
                    (int) $h5,
                    (int) $h6,
                    (int) $h7,
                    (int) $h8,
                    (int) $h9
                )
            )
        );
    }


    /**
     * Square and double a field element
     *
     * h = 2 * f * f
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_sq2(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $f = self::fe_normalize($f);
        $f0 = (int) $f[0];
        $f1 = (int) $f[1];
        $f2 = (int) $f[2];
        $f3 = (int) $f[3];
        $f4 = (int) $f[4];
        $f5 = (int) $f[5];
        $f6 = (int) $f[6];
        $f7 = (int) $f[7];
        $f8 = (int) $f[8];
        $f9 = (int) $f[9];

        $f0_2 = $f0 << 1;
        $f1_2 = $f1 << 1;
        $f2_2 = $f2 << 1;
        $f3_2 = $f3 << 1;
        $f4_2 = $f4 << 1;
        $f5_2 = $f5 << 1;
        $f6_2 = $f6 << 1;
        $f7_2 = $f7 << 1;
        $f5_38 = self::mul($f5, 38, 6); /* 1.959375*2^30 */
        $f6_19 = self::mul($f6, 19, 5); /* 1.959375*2^30 */
        $f7_38 = self::mul($f7, 38, 6); /* 1.959375*2^30 */
        $f8_19 = self::mul($f8, 19, 5); /* 1.959375*2^30 */
        $f9_38 = self::mul($f9, 38, 6); /* 1.959375*2^30 */
        $f0f0 = self::mul($f0, $f0, 24);
        $f0f1_2 = self::mul($f0_2, $f1, 24);
        $f0f2_2 = self::mul($f0_2, $f2, 24);
        $f0f3_2 = self::mul($f0_2, $f3, 24);
        $f0f4_2 = self::mul($f0_2, $f4, 24);
        $f0f5_2 = self::mul($f0_2, $f5, 24);
        $f0f6_2 = self::mul($f0_2, $f6, 24);
        $f0f7_2 = self::mul($f0_2, $f7, 24);
        $f0f8_2 = self::mul($f0_2, $f8, 24);
        $f0f9_2 = self::mul($f0_2, $f9, 24);
        $f1f1_2 = self::mul($f1_2,  $f1, 24);
        $f1f2_2 = self::mul($f1_2,  $f2, 24);
        $f1f3_4 = self::mul($f1_2,  $f3_2, 24);
        $f1f4_2 = self::mul($f1_2,  $f4, 24);
        $f1f5_4 = self::mul($f1_2,  $f5_2, 24);
        $f1f6_2 = self::mul($f1_2,  $f6, 24);
        $f1f7_4 = self::mul($f1_2,  $f7_2, 24);
        $f1f8_2 = self::mul($f1_2,  $f8, 24);
        $f1f9_76 = self::mul($f9_38, $f1_2, 24);
        $f2f2 = self::mul($f2,  $f2, 24);
        $f2f3_2 = self::mul($f2_2,  $f3, 24);
        $f2f4_2 = self::mul($f2_2,  $f4, 24);
        $f2f5_2 = self::mul($f2_2,  $f5, 24);
        $f2f6_2 = self::mul($f2_2,  $f6, 24);
        $f2f7_2 = self::mul($f2_2,  $f7, 24);
        $f2f8_38 = self::mul($f8_19, $f2_2, 25);
        $f2f9_38 = self::mul($f9_38, $f2, 24);
        $f3f3_2 = self::mul($f3_2,  $f3, 24);
        $f3f4_2 = self::mul($f3_2,  $f4, 24);
        $f3f5_4 = self::mul($f3_2,  $f5_2, 24);
        $f3f6_2 = self::mul($f3_2,  $f6, 24);
        $f3f7_76 = self::mul($f7_38, $f3_2, 24);
        $f3f8_38 = self::mul($f8_19, $f3_2, 24);
        $f3f9_76 = self::mul($f9_38, $f3_2, 24);
        $f4f4 = self::mul($f4,  $f4, 24);
        $f4f5_2 = self::mul($f4_2,  $f5, 24);
        $f4f6_38 = self::mul($f6_19, $f4_2, 25);
        $f4f7_38 = self::mul($f7_38, $f4, 24);
        $f4f8_38 = self::mul($f8_19, $f4_2, 25);
        $f4f9_38 = self::mul($f9_38, $f4, 24);
        $f5f5_38 = self::mul($f5_38, $f5, 24);
        $f5f6_38 = self::mul($f6_19, $f5_2, 24);
        $f5f7_76 = self::mul($f7_38, $f5_2, 24);
        $f5f8_38 = self::mul($f8_19, $f5_2, 24);
        $f5f9_76 = self::mul($f9_38, $f5_2, 24);
        $f6f6_19 = self::mul($f6_19, $f6, 24);
        $f6f7_38 = self::mul($f7_38, $f6, 24);
        $f6f8_38 = self::mul($f8_19, $f6_2, 25);
        $f6f9_38 = self::mul($f9_38, $f6, 24);
        $f7f7_38 = self::mul($f7_38, $f7, 24);
        $f7f8_38 = self::mul($f8_19, $f7_2, 24);
        $f7f9_76 = self::mul($f9_38, $f7_2, 24);
        $f8f8_19 = self::mul($f8_19, $f8, 24);
        $f8f9_38 = self::mul($f9_38, $f8, 24);
        $f9f9_38 = self::mul($f9_38, $f9, 24);

        $h0 = (int) ($f0f0 + $f1f9_76 + $f2f8_38 + $f3f7_76 + $f4f6_38 + $f5f5_38) << 1;
        $h1 = (int) ($f0f1_2 + $f2f9_38 + $f3f8_38 + $f4f7_38 + $f5f6_38) << 1;
        $h2 = (int) ($f0f2_2 + $f1f1_2  + $f3f9_76 + $f4f8_38 + $f5f7_76 + $f6f6_19) << 1;
        $h3 = (int) ($f0f3_2 + $f1f2_2  + $f4f9_38 + $f5f8_38 + $f6f7_38) << 1;
        $h4 = (int) ($f0f4_2 + $f1f3_4  + $f2f2    + $f5f9_76 + $f6f8_38 + $f7f7_38) << 1;
        $h5 = (int) ($f0f5_2 + $f1f4_2  + $f2f3_2  + $f6f9_38 + $f7f8_38) << 1;
        $h6 = (int) ($f0f6_2 + $f1f5_4  + $f2f4_2  + $f3f3_2  + $f7f9_76 + $f8f8_19) << 1;
        $h7 = (int) ($f0f7_2 + $f1f6_2  + $f2f5_2  + $f3f4_2  + $f8f9_38) << 1;
        $h8 = (int) ($f0f8_2 + $f1f7_4  + $f2f6_2  + $f3f5_4  + $f4f4    + $f9f9_38) << 1;
        $h9 = (int) ($f0f9_2 + $f1f8_2  + $f2f7_2  + $f3f6_2  + $f4f5_2) << 1;

        $carry0 = ($h0 + (1 << 25)) >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;
        $carry4 = ($h4 + (1 << 25)) >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;

        $carry1 = ($h1 + (1 << 24)) >> 25;
        $h2 += $carry1;
        $h1 -= $carry1 << 25;
        $carry5 = ($h5 + (1 << 24)) >> 25;
        $h6 += $carry5;
        $h5 -= $carry5 << 25;

        $carry2 = ($h2 + (1 << 25)) >> 26;
        $h3 += $carry2;
        $h2 -= $carry2 << 26;
        $carry6 = ($h6 + (1 << 25)) >> 26;
        $h7 += $carry6;
        $h6 -= $carry6 << 26;

        $carry3 = ($h3 + (1 << 24)) >> 25;
        $h4 += $carry3;
        $h3 -= $carry3 << 25;
        $carry7 = ($h7 + (1 << 24)) >> 25;
        $h8 += $carry7;
        $h7 -= $carry7 << 25;

        $carry4 = ($h4 + (1 << 25)) >> 26;
        $h5 += $carry4;
        $h4 -= $carry4 << 26;
        $carry8 = ($h8 + (1 << 25)) >> 26;
        $h9 += $carry8;
        $h8 -= $carry8 << 26;

        $carry9 = ($h9 + (1 << 24)) >> 25;
        $h0 += self::mul($carry9, 19, 5);
        $h9 -= $carry9 << 25;

        $carry0 = ($h0 + (1 << 25)) >> 26;
        $h1 += $carry0;
        $h0 -= $carry0 << 26;

        return self::fe_normalize(
            ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(
                array(
                    (int) $h0,
                    (int) $h1,
                    (int) $h2,
                    (int) $h3,
                    (int) $h4,
                    (int) $h5,
                    (int) $h6,
                    (int) $h7,
                    (int) $h8,
                    (int) $h9
                )
            )
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $Z
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_invert(ParagonIE_Sodium_Core_Curve25519_Fe $Z)
    {
        $z = clone $Z;
        $t0 = self::fe_sq($z);
        $t1 = self::fe_sq($t0);
        $t1 = self::fe_sq($t1);
        $t1 = self::fe_mul($z, $t1);
        $t0 = self::fe_mul($t0, $t1);
        $t2 = self::fe_sq($t0);
        $t1 = self::fe_mul($t1, $t2);
        $t2 = self::fe_sq($t1);
        for ($i = 1; $i < 5; ++$i) {
            $t2 = self::fe_sq($t2);
        }
        $t1 = self::fe_mul($t2, $t1);
        $t2 = self::fe_sq($t1);
        for ($i = 1; $i < 10; ++$i) {
            $t2 = self::fe_sq($t2);
        }
        $t2 = self::fe_mul($t2, $t1);
        $t3 = self::fe_sq($t2);
        for ($i = 1; $i < 20; ++$i) {
            $t3 = self::fe_sq($t3);
        }
        $t2 = self::fe_mul($t3, $t2);
        $t2 = self::fe_sq($t2);
        for ($i = 1; $i < 10; ++$i) {
            $t2 = self::fe_sq($t2);
        }
        $t1 = self::fe_mul($t2, $t1);
        $t2 = self::fe_sq($t1);
        for ($i = 1; $i < 50; ++$i) {
            $t2 = self::fe_sq($t2);
        }
        $t2 = self::fe_mul($t2, $t1);
        $t3 = self::fe_sq($t2);
        for ($i = 1; $i < 100; ++$i) {
            $t3 = self::fe_sq($t3);
        }
        $t2 = self::fe_mul($t3, $t2);
        $t2 = self::fe_sq($t2);
        for ($i = 1; $i < 50; ++$i) {
            $t2 = self::fe_sq($t2);
        }
        $t1 = self::fe_mul($t2, $t1);
        $t1 = self::fe_sq($t1);
        for ($i = 1; $i < 5; ++$i) {
            $t1 = self::fe_sq($t1);
        }
        return self::fe_mul($t1, $t0);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @ref https://github.com/jedisct1/libsodium/blob/68564326e1e9dc57ef03746f85734232d20ca6fb/src/libsodium/crypto_core/curve25519/ref10/curve25519_ref10.c#L1054-L1106
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $z
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_pow22523(ParagonIE_Sodium_Core_Curve25519_Fe $z)
    {
        $z = self::fe_normalize($z);
        # fe_sq(t0, z);
        # fe_sq(t1, t0);
        # fe_sq(t1, t1);
        # fe_mul(t1, z, t1);
        # fe_mul(t0, t0, t1);
        # fe_sq(t0, t0);
        # fe_mul(t0, t1, t0);
        # fe_sq(t1, t0);
        $t0 = self::fe_sq($z);
        $t1 = self::fe_sq($t0);
        $t1 = self::fe_sq($t1);
        $t1 = self::fe_mul($z, $t1);
        $t0 = self::fe_mul($t0, $t1);
        $t0 = self::fe_sq($t0);
        $t0 = self::fe_mul($t1, $t0);
        $t1 = self::fe_sq($t0);

        # for (i = 1; i < 5; ++i) {
        #     fe_sq(t1, t1);
        # }
        for ($i = 1; $i < 5; ++$i) {
            $t1 = self::fe_sq($t1);
        }

        # fe_mul(t0, t1, t0);
        # fe_sq(t1, t0);
        $t0 = self::fe_mul($t1, $t0);
        $t1 = self::fe_sq($t0);

        # for (i = 1; i < 10; ++i) {
        #     fe_sq(t1, t1);
        # }
        for ($i = 1; $i < 10; ++$i) {
            $t1 = self::fe_sq($t1);
        }

        # fe_mul(t1, t1, t0);
        # fe_sq(t2, t1);
        $t1 = self::fe_mul($t1, $t0);
        $t2 = self::fe_sq($t1);

        # for (i = 1; i < 20; ++i) {
        #     fe_sq(t2, t2);
        # }
        for ($i = 1; $i < 20; ++$i) {
            $t2 = self::fe_sq($t2);
        }

        # fe_mul(t1, t2, t1);
        # fe_sq(t1, t1);
        $t1 = self::fe_mul($t2, $t1);
        $t1 = self::fe_sq($t1);

        # for (i = 1; i < 10; ++i) {
        #     fe_sq(t1, t1);
        # }
        for ($i = 1; $i < 10; ++$i) {
            $t1 = self::fe_sq($t1);
        }

        # fe_mul(t0, t1, t0);
        # fe_sq(t1, t0);
        $t0 = self::fe_mul($t1, $t0);
        $t1 = self::fe_sq($t0);

        # for (i = 1; i < 50; ++i) {
        #     fe_sq(t1, t1);
        # }
        for ($i = 1; $i < 50; ++$i) {
            $t1 = self::fe_sq($t1);
        }

        # fe_mul(t1, t1, t0);
        # fe_sq(t2, t1);
        $t1 = self::fe_mul($t1, $t0);
        $t2 = self::fe_sq($t1);

        # for (i = 1; i < 100; ++i) {
        #     fe_sq(t2, t2);
        # }
        for ($i = 1; $i < 100; ++$i) {
            $t2 = self::fe_sq($t2);
        }

        # fe_mul(t1, t2, t1);
        # fe_sq(t1, t1);
        $t1 = self::fe_mul($t2, $t1);
        $t1 = self::fe_sq($t1);

        # for (i = 1; i < 50; ++i) {
        #     fe_sq(t1, t1);
        # }
        for ($i = 1; $i < 50; ++$i) {
            $t1 = self::fe_sq($t1);
        }

        # fe_mul(t0, t1, t0);
        # fe_sq(t0, t0);
        # fe_sq(t0, t0);
        # fe_mul(out, t0, z);
        $t0 = self::fe_mul($t1, $t0);
        $t0 = self::fe_sq($t0);
        $t0 = self::fe_sq($t0);
        return self::fe_mul($t0, $z);
    }

    /**
     * Subtract two field elements.
     *
     * h = f - g
     *
     * Preconditions:
     * |f| bounded by 1.1*2^25,1.1*2^24,1.1*2^25,1.1*2^24,etc.
     * |g| bounded by 1.1*2^25,1.1*2^24,1.1*2^25,1.1*2^24,etc.
     *
     * Postconditions:
     * |h| bounded by 1.1*2^26,1.1*2^25,1.1*2^26,1.1*2^25,etc.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $g
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     * @psalm-suppress MixedOperand
     */
    public static function fe_sub(ParagonIE_Sodium_Core_Curve25519_Fe $f, ParagonIE_Sodium_Core_Curve25519_Fe $g)
    {
        return self::fe_normalize(
            ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(
                array(
                    (int) ($f[0] - $g[0]),
                    (int) ($f[1] - $g[1]),
                    (int) ($f[2] - $g[2]),
                    (int) ($f[3] - $g[3]),
                    (int) ($f[4] - $g[4]),
                    (int) ($f[5] - $g[5]),
                    (int) ($f[6] - $g[6]),
                    (int) ($f[7] - $g[7]),
                    (int) ($f[8] - $g[8]),
                    (int) ($f[9] - $g[9])
                )
            )
        );
    }

    /**
     * Add two group elements.
     *
     * r = p + q
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Cached $q
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P1p1
     */
    public static function ge_add(
        ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core_Curve25519_Ge_Cached $q
    ) {
        $r = new ParagonIE_Sodium_Core_Curve25519_Ge_P1p1();
        $r->X = self::fe_add($p->Y, $p->X);
        $r->Y = self::fe_sub($p->Y, $p->X);
        $r->Z = self::fe_mul($r->X, $q->YplusX);
        $r->Y = self::fe_mul($r->Y, $q->YminusX);
        $r->T = self::fe_mul($q->T2d, $p->T);
        $r->X = self::fe_mul($p->Z, $q->Z);
        $t0   = self::fe_add($r->X, $r->X);
        $r->X = self::fe_sub($r->Z, $r->Y);
        $r->Y = self::fe_add($r->Z, $r->Y);
        $r->Z = self::fe_add($t0, $r->T);
        $r->T = self::fe_sub($t0, $r->T);
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @ref https://github.com/jedisct1/libsodium/blob/157c4a80c13b117608aeae12178b2d38825f9f8f/src/libsodium/crypto_core/curve25519/ref10/curve25519_ref10.c#L1185-L1215
     * @param string $a
     * @return array<int, mixed>
     * @throws SodiumException
     * @throws TypeError
     */
    public static function slide($a)
    {
        if (self::strlen($a) < 256) {
            if (self::strlen($a) < 16) {
                $a = str_pad($a, 256, '0', STR_PAD_RIGHT);
            }
        }
        /** @var array<int, int> $r */
        $r = array();

        /** @var int $i */
        for ($i = 0; $i < 256; ++$i) {
            $r[$i] = (int) (
                1 & (
                    self::chrToInt($a[(int) ($i >> 3)])
                        >>
                    ($i & 7)
                )
            );
        }

        for ($i = 0;$i < 256;++$i) {
            if ($r[$i]) {
                for ($b = 1;$b <= 6 && $i + $b < 256;++$b) {
                    if ($r[$i + $b]) {
                        if ($r[$i] + ($r[$i + $b] << $b) <= 15) {
                            $r[$i] += $r[$i + $b] << $b;
                            $r[$i + $b] = 0;
                        } elseif ($r[$i] - ($r[$i + $b] << $b) >= -15) {
                            $r[$i] -= $r[$i + $b] << $b;
                            for ($k = $i + $b; $k < 256; ++$k) {
                                if (!$r[$k]) {
                                    $r[$k] = 1;
                                    break;
                                }
                                $r[$k] = 0;
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $s
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P3
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_frombytes_negate_vartime($s)
    {
        static $d = null;
        if (!$d) {
            $d = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$d);
        }

        # fe_frombytes(h->Y,s);
        # fe_1(h->Z);
        $h = new ParagonIE_Sodium_Core_Curve25519_Ge_P3(
            self::fe_0(),
            self::fe_frombytes($s),
            self::fe_1()
        );

        # fe_sq(u,h->Y);
        # fe_mul(v,u,d);
        # fe_sub(u,u,h->Z);       /* u = y^2-1 */
        # fe_add(v,v,h->Z);       /* v = dy^2+1 */
        $u = self::fe_sq($h->Y);
        /** @var ParagonIE_Sodium_Core_Curve25519_Fe $d */
        $v = self::fe_mul($u, $d);
        $u = self::fe_sub($u, $h->Z); /* u =  y^2 - 1 */
        $v = self::fe_add($v, $h->Z); /* v = dy^2 + 1 */

        # fe_sq(v3,v);
        # fe_mul(v3,v3,v);        /* v3 = v^3 */
        # fe_sq(h->X,v3);
        # fe_mul(h->X,h->X,v);
        # fe_mul(h->X,h->X,u);    /* x = uv^7 */
        $v3 = self::fe_sq($v);
        $v3 = self::fe_mul($v3, $v); /* v3 = v^3 */
        $h->X = self::fe_sq($v3);
        $h->X = self::fe_mul($h->X, $v);
        $h->X = self::fe_mul($h->X, $u); /* x = uv^7 */

        # fe_pow22523(h->X,h->X); /* x = (uv^7)^((q-5)/8) */
        # fe_mul(h->X,h->X,v3);
        # fe_mul(h->X,h->X,u);    /* x = uv^3(uv^7)^((q-5)/8) */
        $h->X = self::fe_pow22523($h->X); /* x = (uv^7)^((q-5)/8) */
        $h->X = self::fe_mul($h->X, $v3);
        $h->X = self::fe_mul($h->X, $u); /* x = uv^3(uv^7)^((q-5)/8) */

        # fe_sq(vxx,h->X);
        # fe_mul(vxx,vxx,v);
        # fe_sub(check,vxx,u);    /* vx^2-u */
        $vxx = self::fe_sq($h->X);
        $vxx = self::fe_mul($vxx, $v);
        $check = self::fe_sub($vxx, $u); /* vx^2 - u */

        # if (fe_isnonzero(check)) {
        #     fe_add(check,vxx,u);  /* vx^2+u */
        #     if (fe_isnonzero(check)) {
        #         return -1;
        #     }
        #     fe_mul(h->X,h->X,sqrtm1);
        # }
        if (self::fe_isnonzero($check)) {
            $check = self::fe_add($vxx, $u); /* vx^2 + u */
            if (self::fe_isnonzero($check)) {
                throw new RangeException('Internal check failed.');
            }
            $h->X = self::fe_mul(
                $h->X,
                ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$sqrtm1)
            );
        }

        # if (fe_isnegative(h->X) == (s[31] >> 7)) {
        #     fe_neg(h->X,h->X);
        # }
        $i = self::chrToInt($s[31]);
        if (self::fe_isnegative($h->X) === ($i >> 7)) {
            $h->X = self::fe_neg($h->X);
        }

        # fe_mul(h->T,h->X,h->Y);
        $h->T = self::fe_mul($h->X, $h->Y);
        return $h;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $R
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $q
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P1p1
     */
    public static function ge_madd(
        ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $R,
        ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $q
    ) {
        $r = clone $R;
        $r->X = self::fe_add($p->Y, $p->X);
        $r->Y = self::fe_sub($p->Y, $p->X);
        $r->Z = self::fe_mul($r->X, $q->yplusx);
        $r->Y = self::fe_mul($r->Y, $q->yminusx);
        $r->T = self::fe_mul($q->xy2d, $p->T);
        $t0 = self::fe_add(clone $p->Z, clone $p->Z);
        $r->X = self::fe_sub($r->Z, $r->Y);
        $r->Y = self::fe_add($r->Z, $r->Y);
        $r->Z = self::fe_add($t0, $r->T);
        $r->T = self::fe_sub($t0, $r->T);

        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $R
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $q
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P1p1
     */
    public static function ge_msub(
        ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $R,
        ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $q
    ) {
        $r = clone $R;

        $r->X = self::fe_add($p->Y, $p->X);
        $r->Y = self::fe_sub($p->Y, $p->X);
        $r->Z = self::fe_mul($r->X, $q->yminusx);
        $r->Y = self::fe_mul($r->Y, $q->yplusx);
        $r->T = self::fe_mul($q->xy2d, $p->T);
        $t0 = self::fe_add($p->Z, $p->Z);
        $r->X = self::fe_sub($r->Z, $r->Y);
        $r->Y = self::fe_add($r->Z, $r->Y);
        $r->Z = self::fe_sub($t0, $r->T);
        $r->T = self::fe_add($t0, $r->T);

        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $p
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P2
     */
    public static function ge_p1p1_to_p2(ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $p)
    {
        $r = new ParagonIE_Sodium_Core_Curve25519_Ge_P2();
        $r->X = self::fe_mul($p->X, $p->T);
        $r->Y = self::fe_mul($p->Y, $p->Z);
        $r->Z = self::fe_mul($p->Z, $p->T);
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $p
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P3
     */
    public static function ge_p1p1_to_p3(ParagonIE_Sodium_Core_Curve25519_Ge_P1p1 $p)
    {
        $r = new ParagonIE_Sodium_Core_Curve25519_Ge_P3();
        $r->X = self::fe_mul($p->X, $p->T);
        $r->Y = self::fe_mul($p->Y, $p->Z);
        $r->Z = self::fe_mul($p->Z, $p->T);
        $r->T = self::fe_mul($p->X, $p->Y);
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P2
     */
    public static function ge_p2_0()
    {
        return new ParagonIE_Sodium_Core_Curve25519_Ge_P2(
            self::fe_0(),
            self::fe_1(),
            self::fe_1()
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P2 $p
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P1p1
     */
    public static function ge_p2_dbl(ParagonIE_Sodium_Core_Curve25519_Ge_P2 $p)
    {
        $r = new ParagonIE_Sodium_Core_Curve25519_Ge_P1p1();

        $r->X = self::fe_sq($p->X);
        $r->Z = self::fe_sq($p->Y);
        $r->T = self::fe_sq2($p->Z);
        $r->Y = self::fe_add($p->X, $p->Y);
        $t0   = self::fe_sq($r->Y);
        $r->Y = self::fe_add($r->Z, $r->X);
        $r->Z = self::fe_sub($r->Z, $r->X);
        $r->X = self::fe_sub($t0, $r->Y);
        $r->T = self::fe_sub($r->T, $r->Z);

        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P3
     */
    public static function ge_p3_0()
    {
        return new ParagonIE_Sodium_Core_Curve25519_Ge_P3(
            self::fe_0(),
            self::fe_1(),
            self::fe_1(),
            self::fe_0()
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_Cached
     */
    public static function ge_p3_to_cached(ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p)
    {
        static $d2 = null;
        if ($d2 === null) {
            $d2 = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$d2);
        }
        /** @var ParagonIE_Sodium_Core_Curve25519_Fe $d2 */
        $r = new ParagonIE_Sodium_Core_Curve25519_Ge_Cached();
        $r->YplusX = self::fe_add($p->Y, $p->X);
        $r->YminusX = self::fe_sub($p->Y, $p->X);
        $r->Z = self::fe_copy($p->Z);
        $r->T2d = self::fe_mul($p->T, $d2);
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P2
     */
    public static function ge_p3_to_p2(ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p)
    {
        return new ParagonIE_Sodium_Core_Curve25519_Ge_P2(
            self::fe_copy($p->X),
            self::fe_copy($p->Y),
            self::fe_copy($p->Z)
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $h
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p3_tobytes(ParagonIE_Sodium_Core_Curve25519_Ge_P3 $h)
    {
        $recip = self::fe_invert($h->Z);
        $x = self::fe_mul($h->X, $recip);
        $y = self::fe_mul($h->Y, $recip);
        $s = self::fe_tobytes($y);
        $s[31] = self::intToChr(
            self::chrToInt($s[31]) ^ (self::fe_isnegative($x) << 7)
        );
        return $s;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P1p1
     */
    public static function ge_p3_dbl(ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p)
    {
        $q = self::ge_p3_to_p2($p);
        return self::ge_p2_dbl($q);
    }

    /**
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_Precomp
     */
    public static function ge_precomp_0()
    {
        return new ParagonIE_Sodium_Core_Curve25519_Ge_Precomp(
            self::fe_1(),
            self::fe_1(),
            self::fe_0()
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $b
     * @param int $c
     * @return int
     */
    public static function equal($b, $c)
    {
        return (int) ((($b ^ $c) - 1) >> 31) & 1;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int|string $char
     * @return int (1 = yes, 0 = no)
     * @throws SodiumException
     * @throws TypeError
     */
    public static function negative($char)
    {
        if (is_int($char)) {
            return ($char >> 63) & 1;
        }
        $x = self::chrToInt(self::substr($char, 0, 1));
        return (int) ($x >> 63);
    }

    /**
     * Conditional move
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $t
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $u
     * @param int $b
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_Precomp
     */
    public static function cmov(
        ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $t,
        ParagonIE_Sodium_Core_Curve25519_Ge_Precomp $u,
        $b
    ) {
        if (!is_int($b)) {
            throw new InvalidArgumentException('Expected an integer.');
        }
        return new ParagonIE_Sodium_Core_Curve25519_Ge_Precomp(
            self::fe_cmov($t->yplusx,  $u->yplusx,  $b),
            self::fe_cmov($t->yminusx, $u->yminusx, $b),
            self::fe_cmov($t->xy2d,    $u->xy2d,    $b)
        );
    }

    /**
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Cached $t
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Cached $u
     * @param int $b
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_Cached
     */
    public static function ge_cmov_cached(
        ParagonIE_Sodium_Core_Curve25519_Ge_Cached $t,
        ParagonIE_Sodium_Core_Curve25519_Ge_Cached $u,
        $b
    ) {
        $b &= 1;
        $ret = new ParagonIE_Sodium_Core_Curve25519_Ge_Cached();
        $ret->YplusX  = self::fe_cmov($t->YplusX,  $u->YplusX,  $b);
        $ret->YminusX = self::fe_cmov($t->YminusX, $u->YminusX, $b);
        $ret->Z       = self::fe_cmov($t->Z,       $u->Z,       $b);
        $ret->T2d     = self::fe_cmov($t->T2d,     $u->T2d,     $b);
        return $ret;
    }

    /**
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Cached[] $cached
     * @param int $b
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_Cached
     * @throws SodiumException
     */
    public static function ge_cmov8_cached(array $cached, $b)
    {
        // const unsigned char bnegative = negative(b);
        // const unsigned char babs      = b - (((-bnegative) & b) * ((signed char) 1 << 1));
        $bnegative = self::negative($b);
        $babs = $b - (((-$bnegative) & $b) << 1);

        // ge25519_cached_0(t);
        $t = new ParagonIE_Sodium_Core_Curve25519_Ge_Cached(
            self::fe_1(),
            self::fe_1(),
            self::fe_1(),
            self::fe_0()
        );

        // ge25519_cmov_cached(t, &cached[0], equal(babs, 1));
        // ge25519_cmov_cached(t, &cached[1], equal(babs, 2));
        // ge25519_cmov_cached(t, &cached[2], equal(babs, 3));
        // ge25519_cmov_cached(t, &cached[3], equal(babs, 4));
        // ge25519_cmov_cached(t, &cached[4], equal(babs, 5));
        // ge25519_cmov_cached(t, &cached[5], equal(babs, 6));
        // ge25519_cmov_cached(t, &cached[6], equal(babs, 7));
        // ge25519_cmov_cached(t, &cached[7], equal(babs, 8));
        for ($x = 0; $x < 8; ++$x) {
            $t = self::ge_cmov_cached($t, $cached[$x], self::equal($babs, $x + 1));
        }

        // fe25519_copy(minust.YplusX, t->YminusX);
        // fe25519_copy(minust.YminusX, t->YplusX);
        // fe25519_copy(minust.Z, t->Z);
        // fe25519_neg(minust.T2d, t->T2d);
        $minust = new ParagonIE_Sodium_Core_Curve25519_Ge_Cached(
            self::fe_copy($t->YminusX),
            self::fe_copy($t->YplusX),
            self::fe_copy($t->Z),
            self::fe_neg($t->T2d)
        );
        return self::ge_cmov_cached($t, $minust, $bnegative);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $pos
     * @param int $b
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_Precomp
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayOffset
     */
    public static function ge_select($pos = 0, $b = 0)
    {
        static $base = null;
        if ($base === null) {
            $base = array();
            /** @var int $i */
            foreach (self::$base as $i => $bas) {
                for ($j = 0; $j < 8; ++$j) {
                    $base[$i][$j] = new ParagonIE_Sodium_Core_Curve25519_Ge_Precomp(
                        ParagonIE_Sodium_Core_Curve25519_Fe::fromArray($bas[$j][0]),
                        ParagonIE_Sodium_Core_Curve25519_Fe::fromArray($bas[$j][1]),
                        ParagonIE_Sodium_Core_Curve25519_Fe::fromArray($bas[$j][2])
                    );
                }
            }
        }
        /** @var array<int, array<int, ParagonIE_Sodium_Core_Curve25519_Ge_Precomp>> $base */
        if (!is_int($pos)) {
            throw new InvalidArgumentException('Position must be an integer');
        }
        if ($pos < 0 || $pos > 31) {
            throw new RangeException('Position is out of range [0, 31]');
        }

        $bnegative = self::negative($b);
        $babs = $b - (((-$bnegative) & $b) << 1);

        $t = self::ge_precomp_0();
        for ($i = 0; $i < 8; ++$i) {
            $t = self::cmov(
                $t,
                $base[$pos][$i],
                self::equal($babs, $i + 1)
            );
        }
        $minusT = new ParagonIE_Sodium_Core_Curve25519_Ge_Precomp(
            self::fe_copy($t->yminusx),
            self::fe_copy($t->yplusx),
            self::fe_neg($t->xy2d)
        );
        return self::cmov($t, $minusT, $bnegative);
    }

    /**
     * Subtract two group elements.
     *
     * r = p - q
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_Cached $q
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P1p1
     */
    public static function ge_sub(
        ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core_Curve25519_Ge_Cached $q
    ) {
        $r = new ParagonIE_Sodium_Core_Curve25519_Ge_P1p1();

        $r->X = self::fe_add($p->Y, $p->X);
        $r->Y = self::fe_sub($p->Y, $p->X);
        $r->Z = self::fe_mul($r->X, $q->YminusX);
        $r->Y = self::fe_mul($r->Y, $q->YplusX);
        $r->T = self::fe_mul($q->T2d, $p->T);
        $r->X = self::fe_mul($p->Z, $q->Z);
        $t0 = self::fe_add($r->X, $r->X);
        $r->X = self::fe_sub($r->Z, $r->Y);
        $r->Y = self::fe_add($r->Z, $r->Y);
        $r->Z = self::fe_sub($t0, $r->T);
        $r->T = self::fe_add($t0, $r->T);

        return $r;
    }

    /**
     * Convert a group element to a byte string.
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P2 $h
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_tobytes(ParagonIE_Sodium_Core_Curve25519_Ge_P2 $h)
    {
        $recip = self::fe_invert($h->Z);
        $x = self::fe_mul($h->X, $recip);
        $y = self::fe_mul($h->Y, $recip);
        $s = self::fe_tobytes($y);
        $s[31] = self::intToChr(
            self::chrToInt($s[31]) ^ (self::fe_isnegative($x) << 7)
        );
        return $s;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $a
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $A
     * @param string $b
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P2
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     */
    public static function ge_double_scalarmult_vartime(
        $a,
        ParagonIE_Sodium_Core_Curve25519_Ge_P3 $A,
        $b
    ) {
        /** @var array<int, ParagonIE_Sodium_Core_Curve25519_Ge_Cached> $Ai */
        $Ai = array();

        /** @var array<int, ParagonIE_Sodium_Core_Curve25519_Ge_Precomp> $Bi */
        static $Bi = array();
        if (!$Bi) {
            for ($i = 0; $i < 8; ++$i) {
                $Bi[$i] = new ParagonIE_Sodium_Core_Curve25519_Ge_Precomp(
                    ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$base2[$i][0]),
                    ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$base2[$i][1]),
                    ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$base2[$i][2])
                );
            }
        }
        for ($i = 0; $i < 8; ++$i) {
            $Ai[$i] = new ParagonIE_Sodium_Core_Curve25519_Ge_Cached(
                self::fe_0(),
                self::fe_0(),
                self::fe_0(),
                self::fe_0()
            );
        }

        # slide(aslide,a);
        # slide(bslide,b);
        /** @var array<int, int> $aslide */
        $aslide = self::slide($a);
        /** @var array<int, int> $bslide */
        $bslide = self::slide($b);

        # ge_p3_to_cached(&Ai[0],A);
        # ge_p3_dbl(&t,A); ge_p1p1_to_p3(&A2,&t);
        $Ai[0] = self::ge_p3_to_cached($A);
        $t = self::ge_p3_dbl($A);
        $A2 = self::ge_p1p1_to_p3($t);

        # ge_add(&t,&A2,&Ai[0]); ge_p1p1_to_p3(&u,&t); ge_p3_to_cached(&Ai[1],&u);
        # ge_add(&t,&A2,&Ai[1]); ge_p1p1_to_p3(&u,&t); ge_p3_to_cached(&Ai[2],&u);
        # ge_add(&t,&A2,&Ai[2]); ge_p1p1_to_p3(&u,&t); ge_p3_to_cached(&Ai[3],&u);
        # ge_add(&t,&A2,&Ai[3]); ge_p1p1_to_p3(&u,&t); ge_p3_to_cached(&Ai[4],&u);
        # ge_add(&t,&A2,&Ai[4]); ge_p1p1_to_p3(&u,&t); ge_p3_to_cached(&Ai[5],&u);
        # ge_add(&t,&A2,&Ai[5]); ge_p1p1_to_p3(&u,&t); ge_p3_to_cached(&Ai[6],&u);
        # ge_add(&t,&A2,&Ai[6]); ge_p1p1_to_p3(&u,&t); ge_p3_to_cached(&Ai[7],&u);
        for ($i = 0; $i < 7; ++$i) {
            $t = self::ge_add($A2, $Ai[$i]);
            $u = self::ge_p1p1_to_p3($t);
            $Ai[$i + 1] = self::ge_p3_to_cached($u);
        }

        # ge_p2_0(r);
        $r = self::ge_p2_0();

        # for (i = 255;i >= 0;--i) {
        #     if (aslide[i] || bslide[i]) break;
        # }
        $i = 255;
        for (; $i >= 0; --$i) {
            if ($aslide[$i] || $bslide[$i]) {
                break;
            }
        }

        # for (;i >= 0;--i) {
        for (; $i >= 0; --$i) {
            # ge_p2_dbl(&t,r);
            $t = self::ge_p2_dbl($r);

            # if (aslide[i] > 0) {
            if ($aslide[$i] > 0) {
                # ge_p1p1_to_p3(&u,&t);
                # ge_add(&t,&u,&Ai[aslide[i]/2]);
                $u = self::ge_p1p1_to_p3($t);
                $t = self::ge_add(
                    $u,
                    $Ai[(int) floor($aslide[$i] / 2)]
                );
            # } else if (aslide[i] < 0) {
            } elseif ($aslide[$i] < 0) {
                # ge_p1p1_to_p3(&u,&t);
                # ge_sub(&t,&u,&Ai[(-aslide[i])/2]);
                $u = self::ge_p1p1_to_p3($t);
                $t = self::ge_sub(
                    $u,
                    $Ai[(int) floor(-$aslide[$i] / 2)]
                );
            }

            # if (bslide[i] > 0) {
            if ($bslide[$i] > 0) {
                /** @var int $index */
                $index = (int) floor($bslide[$i] / 2);
                # ge_p1p1_to_p3(&u,&t);
                # ge_madd(&t,&u,&Bi[bslide[i]/2]);
                $u = self::ge_p1p1_to_p3($t);
                $t = self::ge_madd($t, $u, $Bi[$index]);
            # } else if (bslide[i] < 0) {
            } elseif ($bslide[$i] < 0) {
                /** @var int $index */
                $index = (int) floor(-$bslide[$i] / 2);
                # ge_p1p1_to_p3(&u,&t);
                # ge_msub(&t,&u,&Bi[(-bslide[i])/2]);
                $u = self::ge_p1p1_to_p3($t);
                $t = self::ge_msub($t, $u, $Bi[$index]);
            }
            # ge_p1p1_to_p2(r,&t);
            $r = self::ge_p1p1_to_p2($t);
        }
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $a
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $p
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P3
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedOperand
     */
    public static function ge_scalarmult($a, $p)
    {
        $e = array_fill(0, 64, 0);

        /** @var ParagonIE_Sodium_Core_Curve25519_Ge_Cached[] $pi */
        $pi = array();

        //        ge25519_p3_to_cached(&pi[1 - 1], p);   /* p */
        $pi[0] = self::ge_p3_to_cached($p);

        //        ge25519_p3_dbl(&t2, p);
        //        ge25519_p1p1_to_p3(&p2, &t2);
        //        ge25519_p3_to_cached(&pi[2 - 1], &p2); /* 2p = 2*p */
        $t2 = self::ge_p3_dbl($p);
        $p2 = self::ge_p1p1_to_p3($t2);
        $pi[1] = self::ge_p3_to_cached($p2);

        //        ge25519_add_cached(&t3, p, &pi[2 - 1]);
        //        ge25519_p1p1_to_p3(&p3, &t3);
        //        ge25519_p3_to_cached(&pi[3 - 1], &p3); /* 3p = 2p+p */
        $t3 = self::ge_add($p, $pi[1]);
        $p3 = self::ge_p1p1_to_p3($t3);
        $pi[2] = self::ge_p3_to_cached($p3);

        //        ge25519_p3_dbl(&t4, &p2);
        //        ge25519_p1p1_to_p3(&p4, &t4);
        //        ge25519_p3_to_cached(&pi[4 - 1], &p4); /* 4p = 2*2p */
        $t4 = self::ge_p3_dbl($p2);
        $p4 = self::ge_p1p1_to_p3($t4);
        $pi[3] = self::ge_p3_to_cached($p4);

        //        ge25519_add_cached(&t5, p, &pi[4 - 1]);
        //        ge25519_p1p1_to_p3(&p5, &t5);
        //        ge25519_p3_to_cached(&pi[5 - 1], &p5); /* 5p = 4p+p */
        $t5 = self::ge_add($p, $pi[3]);
        $p5 = self::ge_p1p1_to_p3($t5);
        $pi[4] = self::ge_p3_to_cached($p5);

        //        ge25519_p3_dbl(&t6, &p3);
        //        ge25519_p1p1_to_p3(&p6, &t6);
        //        ge25519_p3_to_cached(&pi[6 - 1], &p6); /* 6p = 2*3p */
        $t6 = self::ge_p3_dbl($p3);
        $p6 = self::ge_p1p1_to_p3($t6);
        $pi[5] = self::ge_p3_to_cached($p6);

        //        ge25519_add_cached(&t7, p, &pi[6 - 1]);
        //        ge25519_p1p1_to_p3(&p7, &t7);
        //        ge25519_p3_to_cached(&pi[7 - 1], &p7); /* 7p = 6p+p */
        $t7 = self::ge_add($p, $pi[5]);
        $p7 = self::ge_p1p1_to_p3($t7);
        $pi[6] = self::ge_p3_to_cached($p7);

        //        ge25519_p3_dbl(&t8, &p4);
        //        ge25519_p1p1_to_p3(&p8, &t8);
        //        ge25519_p3_to_cached(&pi[8 - 1], &p8); /* 8p = 2*4p */
        $t8 = self::ge_p3_dbl($p4);
        $p8 = self::ge_p1p1_to_p3($t8);
        $pi[7] = self::ge_p3_to_cached($p8);


        //        for (i = 0; i < 32; ++i) {
        //            e[2 * i + 0] = (a[i] >> 0) & 15;
        //            e[2 * i + 1] = (a[i] >> 4) & 15;
        //        }
        for ($i = 0; $i < 32; ++$i) {
            $e[($i << 1)    ] =  self::chrToInt($a[$i]) & 15;
            $e[($i << 1) + 1] = (self::chrToInt($a[$i]) >> 4) & 15;
        }
        //        /* each e[i] is between 0 and 15 */
        //        /* e[63] is between 0 and 7 */

        //        carry = 0;
        //        for (i = 0; i < 63; ++i) {
        //            e[i] += carry;
        //            carry = e[i] + 8;
        //            carry >>= 4;
        //            e[i] -= carry * ((signed char) 1 << 4);
        //        }
        $carry = 0;
        for ($i = 0; $i < 63; ++$i) {
            $e[$i] += $carry;
            $carry = $e[$i] + 8;
            $carry >>= 4;
            $e[$i] -= $carry << 4;
        }
        //        e[63] += carry;
        //        /* each e[i] is between -8 and 8 */
        $e[63] += $carry;

        //        ge25519_p3_0(h);
        $h = self::ge_p3_0();

        //        for (i = 63; i != 0; i--) {
        for ($i = 63; $i != 0; --$i) {
            // ge25519_cmov8_cached(&t, pi, e[i]);
            $t = self::ge_cmov8_cached($pi, $e[$i]);
            // ge25519_add_cached(&r, h, &t);
            $r = self::ge_add($h, $t);

            // ge25519_p1p1_to_p2(&s, &r);
            // ge25519_p2_dbl(&r, &s);
            // ge25519_p1p1_to_p2(&s, &r);
            // ge25519_p2_dbl(&r, &s);
            // ge25519_p1p1_to_p2(&s, &r);
            // ge25519_p2_dbl(&r, &s);
            // ge25519_p1p1_to_p2(&s, &r);
            // ge25519_p2_dbl(&r, &s);
            $s = self::ge_p1p1_to_p2($r);
            $r = self::ge_p2_dbl($s);
            $s = self::ge_p1p1_to_p2($r);
            $r = self::ge_p2_dbl($s);
            $s = self::ge_p1p1_to_p2($r);
            $r = self::ge_p2_dbl($s);
            $s = self::ge_p1p1_to_p2($r);
            $r = self::ge_p2_dbl($s);

            // ge25519_p1p1_to_p3(h, &r);  /* *16 */
            $h = self::ge_p1p1_to_p3($r); /* *16 */
        }

        //        ge25519_cmov8_cached(&t, pi, e[i]);
        //        ge25519_add_cached(&r, h, &t);
        //        ge25519_p1p1_to_p3(h, &r);
        $t = self::ge_cmov8_cached($pi, $e[0]);
        $r = self::ge_add($h, $t);
        return self::ge_p1p1_to_p3($r);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $a
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P3
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedOperand
     */
    public static function ge_scalarmult_base($a)
    {
        /** @var array<int, int> $e */
        $e = array();
        $r = new ParagonIE_Sodium_Core_Curve25519_Ge_P1p1();

        for ($i = 0; $i < 32; ++$i) {
            $dbl = (int) $i << 1;
            $e[$dbl] = (int) self::chrToInt($a[$i]) & 15;
            $e[$dbl + 1] = (int) (self::chrToInt($a[$i]) >> 4) & 15;
        }

        $carry = 0;
        for ($i = 0; $i < 63; ++$i) {
            $e[$i] += $carry;
            $carry = $e[$i] + 8;
            $carry >>= 4;
            $e[$i] -= $carry << 4;
        }
        $e[63] += (int) $carry;

        $h = self::ge_p3_0();

        for ($i = 1; $i < 64; $i += 2) {
            $t = self::ge_select((int) floor($i / 2), (int) $e[$i]);
            $r = self::ge_madd($r, $h, $t);
            $h = self::ge_p1p1_to_p3($r);
        }

        $r = self::ge_p3_dbl($h);

        $s = self::ge_p1p1_to_p2($r);
        $r = self::ge_p2_dbl($s);
        $s = self::ge_p1p1_to_p2($r);
        $r = self::ge_p2_dbl($s);
        $s = self::ge_p1p1_to_p2($r);
        $r = self::ge_p2_dbl($s);

        $h = self::ge_p1p1_to_p3($r);

        for ($i = 0; $i < 64; $i += 2) {
            $t = self::ge_select($i >> 1, (int) $e[$i]);
            $r = self::ge_madd($r, $h, $t);
            $h = self::ge_p1p1_to_p3($r);
        }
        return $h;
    }

    /**
     * Calculates (ab + c) mod l
     * where l = 2^252 + 27742317777372353535851937790883648493
     *
     * @internal You should not use this directly from another application
     *
     * @param string $a
     * @param string $b
     * @param string $c
     * @return string
     * @throws TypeError
     */
    public static function sc_muladd($a, $b, $c)
    {
        $a0 = 2097151 & self::load_3(self::substr($a, 0, 3));
        $a1 = 2097151 & (self::load_4(self::substr($a, 2, 4)) >> 5);
        $a2 = 2097151 & (self::load_3(self::substr($a, 5, 3)) >> 2);
        $a3 = 2097151 & (self::load_4(self::substr($a, 7, 4)) >> 7);
        $a4 = 2097151 & (self::load_4(self::substr($a, 10, 4)) >> 4);
        $a5 = 2097151 & (self::load_3(self::substr($a, 13, 3)) >> 1);
        $a6 = 2097151 & (self::load_4(self::substr($a, 15, 4)) >> 6);
        $a7 = 2097151 & (self::load_3(self::substr($a, 18, 3)) >> 3);
        $a8 = 2097151 & self::load_3(self::substr($a, 21, 3));
        $a9 = 2097151 & (self::load_4(self::substr($a, 23, 4)) >> 5);
        $a10 = 2097151 & (self::load_3(self::substr($a, 26, 3)) >> 2);
        $a11 = (self::load_4(self::substr($a, 28, 4)) >> 7);

        $b0 = 2097151 & self::load_3(self::substr($b, 0, 3));
        $b1 = 2097151 & (self::load_4(self::substr($b, 2, 4)) >> 5);
        $b2 = 2097151 & (self::load_3(self::substr($b, 5, 3)) >> 2);
        $b3 = 2097151 & (self::load_4(self::substr($b, 7, 4)) >> 7);
        $b4 = 2097151 & (self::load_4(self::substr($b, 10, 4)) >> 4);
        $b5 = 2097151 & (self::load_3(self::substr($b, 13, 3)) >> 1);
        $b6 = 2097151 & (self::load_4(self::substr($b, 15, 4)) >> 6);
        $b7 = 2097151 & (self::load_3(self::substr($b, 18, 3)) >> 3);
        $b8 = 2097151 & self::load_3(self::substr($b, 21, 3));
        $b9 = 2097151 & (self::load_4(self::substr($b, 23, 4)) >> 5);
        $b10 = 2097151 & (self::load_3(self::substr($b, 26, 3)) >> 2);
        $b11 = (self::load_4(self::substr($b, 28, 4)) >> 7);

        $c0 = 2097151 & self::load_3(self::substr($c, 0, 3));
        $c1 = 2097151 & (self::load_4(self::substr($c, 2, 4)) >> 5);
        $c2 = 2097151 & (self::load_3(self::substr($c, 5, 3)) >> 2);
        $c3 = 2097151 & (self::load_4(self::substr($c, 7, 4)) >> 7);
        $c4 = 2097151 & (self::load_4(self::substr($c, 10, 4)) >> 4);
        $c5 = 2097151 & (self::load_3(self::substr($c, 13, 3)) >> 1);
        $c6 = 2097151 & (self::load_4(self::substr($c, 15, 4)) >> 6);
        $c7 = 2097151 & (self::load_3(self::substr($c, 18, 3)) >> 3);
        $c8 = 2097151 & self::load_3(self::substr($c, 21, 3));
        $c9 = 2097151 & (self::load_4(self::substr($c, 23, 4)) >> 5);
        $c10 = 2097151 & (self::load_3(self::substr($c, 26, 3)) >> 2);
        $c11 = (self::load_4(self::substr($c, 28, 4)) >> 7);

        /* Can't really avoid the pyramid here: */
        $s0 = $c0 + self::mul($a0, $b0, 24);
        $s1 = $c1 + self::mul($a0, $b1, 24) + self::mul($a1, $b0, 24);
        $s2 = $c2 + self::mul($a0, $b2, 24) + self::mul($a1, $b1, 24) + self::mul($a2, $b0, 24);
        $s3 = $c3 + self::mul($a0, $b3, 24) + self::mul($a1, $b2, 24) + self::mul($a2, $b1, 24) + self::mul($a3, $b0, 24);
        $s4 = $c4 + self::mul($a0, $b4, 24) + self::mul($a1, $b3, 24) + self::mul($a2, $b2, 24) + self::mul($a3, $b1, 24) +
               self::mul($a4, $b0, 24);
        $s5 = $c5 + self::mul($a0, $b5, 24) + self::mul($a1, $b4, 24) + self::mul($a2, $b3, 24) + self::mul($a3, $b2, 24) +
               self::mul($a4, $b1, 24) + self::mul($a5, $b0, 24);
        $s6 = $c6 + self::mul($a0, $b6, 24) + self::mul($a1, $b5, 24) + self::mul($a2, $b4, 24) + self::mul($a3, $b3, 24) +
               self::mul($a4, $b2, 24) + self::mul($a5, $b1, 24) + self::mul($a6, $b0, 24);
        $s7 = $c7 + self::mul($a0, $b7, 24) + self::mul($a1, $b6, 24) + self::mul($a2, $b5, 24) + self::mul($a3, $b4, 24) +
               self::mul($a4, $b3, 24) + self::mul($a5, $b2, 24) + self::mul($a6, $b1, 24) + self::mul($a7, $b0, 24);
        $s8 = $c8 + self::mul($a0, $b8, 24) + self::mul($a1, $b7, 24) + self::mul($a2, $b6, 24) + self::mul($a3, $b5, 24) +
               self::mul($a4, $b4, 24) + self::mul($a5, $b3, 24) + self::mul($a6, $b2, 24) + self::mul($a7, $b1, 24) +
               self::mul($a8, $b0, 24);
        $s9 = $c9 + self::mul($a0, $b9, 24) + self::mul($a1, $b8, 24) + self::mul($a2, $b7, 24) + self::mul($a3, $b6, 24) +
               self::mul($a4, $b5, 24) + self::mul($a5, $b4, 24) + self::mul($a6, $b3, 24) + self::mul($a7, $b2, 24) +
               self::mul($a8, $b1, 24) + self::mul($a9, $b0, 24);
        $s10 = $c10 + self::mul($a0, $b10, 24) + self::mul($a1, $b9, 24) + self::mul($a2, $b8, 24) + self::mul($a3, $b7, 24) +
               self::mul($a4, $b6, 24) + self::mul($a5, $b5, 24) + self::mul($a6, $b4, 24) + self::mul($a7, $b3, 24) +
               self::mul($a8, $b2, 24) + self::mul($a9, $b1, 24) + self::mul($a10, $b0, 24);
        $s11 = $c11 + self::mul($a0, $b11, 24) + self::mul($a1, $b10, 24) + self::mul($a2, $b9, 24) + self::mul($a3, $b8, 24) +
               self::mul($a4, $b7, 24) + self::mul($a5, $b6, 24) + self::mul($a6, $b5, 24) + self::mul($a7, $b4, 24) +
               self::mul($a8, $b3, 24) + self::mul($a9, $b2, 24) + self::mul($a10, $b1, 24) + self::mul($a11, $b0, 24);
        $s12 = self::mul($a1, $b11, 24) + self::mul($a2, $b10, 24) + self::mul($a3, $b9, 24) + self::mul($a4, $b8, 24) +
               self::mul($a5, $b7, 24) + self::mul($a6, $b6, 24) + self::mul($a7, $b5, 24) + self::mul($a8, $b4, 24) +
               self::mul($a9, $b3, 24) + self::mul($a10, $b2, 24) + self::mul($a11, $b1, 24);
        $s13 = self::mul($a2, $b11, 24) + self::mul($a3, $b10, 24) + self::mul($a4, $b9, 24) + self::mul($a5, $b8, 24) +
               self::mul($a6, $b7, 24) + self::mul($a7, $b6, 24) + self::mul($a8, $b5, 24) + self::mul($a9, $b4, 24) +
               self::mul($a10, $b3, 24) + self::mul($a11, $b2, 24);
        $s14 = self::mul($a3, $b11, 24) + self::mul($a4, $b10, 24) + self::mul($a5, $b9, 24) + self::mul($a6, $b8, 24) +
               self::mul($a7, $b7, 24) + self::mul($a8, $b6, 24) + self::mul($a9, $b5, 24) + self::mul($a10, $b4, 24) +
               self::mul($a11, $b3, 24);
        $s15 = self::mul($a4, $b11, 24) + self::mul($a5, $b10, 24) + self::mul($a6, $b9, 24) + self::mul($a7, $b8, 24) +
               self::mul($a8, $b7, 24) + self::mul($a9, $b6, 24) + self::mul($a10, $b5, 24) + self::mul($a11, $b4, 24);
        $s16 = self::mul($a5, $b11, 24) + self::mul($a6, $b10, 24) + self::mul($a7, $b9, 24) + self::mul($a8, $b8, 24) +
               self::mul($a9, $b7, 24) + self::mul($a10, $b6, 24) + self::mul($a11, $b5, 24);
        $s17 = self::mul($a6, $b11, 24) + self::mul($a7, $b10, 24) + self::mul($a8, $b9, 24) + self::mul($a9, $b8, 24) +
               self::mul($a10, $b7, 24) + self::mul($a11, $b6, 24);
        $s18 = self::mul($a7, $b11, 24) + self::mul($a8, $b10, 24) + self::mul($a9, $b9, 24) + self::mul($a10, $b8, 24) +
               self::mul($a11, $b7, 24);
        $s19 = self::mul($a8, $b11, 24) + self::mul($a9, $b10, 24) + self::mul($a10, $b9, 24) + self::mul($a11, $b8, 24);
        $s20 = self::mul($a9, $b11, 24) + self::mul($a10, $b10, 24) + self::mul($a11, $b9, 24);
        $s21 = self::mul($a10, $b11, 24) + self::mul($a11, $b10, 24);
        $s22 = self::mul($a11, $b11, 24);
        $s23 = 0;

        $carry0 = ($s0 + (1 << 20)) >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        $carry2 = ($s2 + (1 << 20)) >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        $carry4 = ($s4 + (1 << 20)) >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        $carry12 = ($s12 + (1 << 20)) >> 21;
        $s13 += $carry12;
        $s12 -= $carry12 << 21;
        $carry14 = ($s14 + (1 << 20)) >> 21;
        $s15 += $carry14;
        $s14 -= $carry14 << 21;
        $carry16 = ($s16 + (1 << 20)) >> 21;
        $s17 += $carry16;
        $s16 -= $carry16 << 21;
        $carry18 = ($s18 + (1 << 20)) >> 21;
        $s19 += $carry18;
        $s18 -= $carry18 << 21;
        $carry20 = ($s20 + (1 << 20)) >> 21;
        $s21 += $carry20;
        $s20 -= $carry20 << 21;
        $carry22 = ($s22 + (1 << 20)) >> 21;
        $s23 += $carry22;
        $s22 -= $carry22 << 21;

        $carry1 = ($s1 + (1 << 20)) >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        $carry3 = ($s3 + (1 << 20)) >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        $carry5 = ($s5 + (1 << 20)) >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;
        $carry13 = ($s13 + (1 << 20)) >> 21;
        $s14 += $carry13;
        $s13 -= $carry13 << 21;
        $carry15 = ($s15 + (1 << 20)) >> 21;
        $s16 += $carry15;
        $s15 -= $carry15 << 21;
        $carry17 = ($s17 + (1 << 20)) >> 21;
        $s18 += $carry17;
        $s17 -= $carry17 << 21;
        $carry19 = ($s19 + (1 << 20)) >> 21;
        $s20 += $carry19;
        $s19 -= $carry19 << 21;
        $carry21 = ($s21 + (1 << 20)) >> 21;
        $s22 += $carry21;
        $s21 -= $carry21 << 21;

        $s11 += self::mul($s23, 666643, 20);
        $s12 += self::mul($s23, 470296, 19);
        $s13 += self::mul($s23, 654183, 20);
        $s14 -= self::mul($s23, 997805, 20);
        $s15 += self::mul($s23, 136657, 18);
        $s16 -= self::mul($s23, 683901, 20);

        $s10 += self::mul($s22, 666643, 20);
        $s11 += self::mul($s22, 470296, 19);
        $s12 += self::mul($s22, 654183, 20);
        $s13 -= self::mul($s22, 997805, 20);
        $s14 += self::mul($s22, 136657, 18);
        $s15 -= self::mul($s22, 683901, 20);

        $s9  += self::mul($s21,  666643, 20);
        $s10 += self::mul($s21,  470296, 19);
        $s11 += self::mul($s21,  654183, 20);
        $s12 -= self::mul($s21,  997805, 20);
        $s13 += self::mul($s21,  136657, 18);
        $s14 -= self::mul($s21,  683901, 20);

        $s8  += self::mul($s20,  666643, 20);
        $s9  += self::mul($s20,  470296, 19);
        $s10 += self::mul($s20,  654183, 20);
        $s11 -= self::mul($s20,  997805, 20);
        $s12 += self::mul($s20,  136657, 18);
        $s13 -= self::mul($s20,  683901, 20);

        $s7  += self::mul($s19,  666643, 20);
        $s8  += self::mul($s19,  470296, 19);
        $s9  += self::mul($s19,  654183, 20);
        $s10 -= self::mul($s19,  997805, 20);
        $s11 += self::mul($s19,  136657, 18);
        $s12 -= self::mul($s19,  683901, 20);

        $s6  += self::mul($s18,  666643, 20);
        $s7  += self::mul($s18,  470296, 19);
        $s8  += self::mul($s18,  654183, 20);
        $s9  -= self::mul($s18,  997805, 20);
        $s10 += self::mul($s18,  136657, 18);
        $s11 -= self::mul($s18,  683901, 20);

        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        $carry12 = ($s12 + (1 << 20)) >> 21;
        $s13 += $carry12;
        $s12 -= $carry12 << 21;
        $carry14 = ($s14 + (1 << 20)) >> 21;
        $s15 += $carry14;
        $s14 -= $carry14 << 21;
        $carry16 = ($s16 + (1 << 20)) >> 21;
        $s17 += $carry16;
        $s16 -= $carry16 << 21;

        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;
        $carry13 = ($s13 + (1 << 20)) >> 21;
        $s14 += $carry13;
        $s13 -= $carry13 << 21;
        $carry15 = ($s15 + (1 << 20)) >> 21;
        $s16 += $carry15;
        $s15 -= $carry15 << 21;

        $s5  += self::mul($s17,  666643, 20);
        $s6  += self::mul($s17,  470296, 19);
        $s7  += self::mul($s17,  654183, 20);
        $s8  -= self::mul($s17,  997805, 20);
        $s9  += self::mul($s17,  136657, 18);
        $s10 -= self::mul($s17,  683901, 20);

        $s4 += self::mul($s16,  666643, 20);
        $s5 += self::mul($s16,  470296, 19);
        $s6 += self::mul($s16,  654183, 20);
        $s7 -= self::mul($s16,  997805, 20);
        $s8 += self::mul($s16,  136657, 18);
        $s9 -= self::mul($s16,  683901, 20);

        $s3 += self::mul($s15,  666643, 20);
        $s4 += self::mul($s15,  470296, 19);
        $s5 += self::mul($s15,  654183, 20);
        $s6 -= self::mul($s15,  997805, 20);
        $s7 += self::mul($s15,  136657, 18);
        $s8 -= self::mul($s15,  683901, 20);

        $s2 += self::mul($s14,  666643, 20);
        $s3 += self::mul($s14,  470296, 19);
        $s4 += self::mul($s14,  654183, 20);
        $s5 -= self::mul($s14,  997805, 20);
        $s6 += self::mul($s14,  136657, 18);
        $s7 -= self::mul($s14,  683901, 20);

        $s1 += self::mul($s13,  666643, 20);
        $s2 += self::mul($s13,  470296, 19);
        $s3 += self::mul($s13,  654183, 20);
        $s4 -= self::mul($s13,  997805, 20);
        $s5 += self::mul($s13,  136657, 18);
        $s6 -= self::mul($s13,  683901, 20);

        $s0 += self::mul($s12,  666643, 20);
        $s1 += self::mul($s12,  470296, 19);
        $s2 += self::mul($s12,  654183, 20);
        $s3 -= self::mul($s12,  997805, 20);
        $s4 += self::mul($s12,  136657, 18);
        $s5 -= self::mul($s12,  683901, 20);
        $s12 = 0;

        $carry0 = ($s0 + (1 << 20)) >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        $carry2 = ($s2 + (1 << 20)) >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        $carry4 = ($s4 + (1 << 20)) >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;

        $carry1 = ($s1 + (1 << 20)) >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        $carry3 = ($s3 + (1 << 20)) >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        $carry5 = ($s5 + (1 << 20)) >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;

        $s0 += self::mul($s12,  666643, 20);
        $s1 += self::mul($s12,  470296, 19);
        $s2 += self::mul($s12,  654183, 20);
        $s3 -= self::mul($s12,  997805, 20);
        $s4 += self::mul($s12,  136657, 18);
        $s5 -= self::mul($s12,  683901, 20);
        $s12 = 0;

        $carry0 = $s0 >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        $carry1 = $s1 >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        $carry2 = $s2 >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        $carry3 = $s3 >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        $carry4 = $s4 >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        $carry5 = $s5 >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        $carry6 = $s6 >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry7 = $s7 >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry8 = $s8 >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry9 = $s9 >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry10 = $s10 >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        $carry11 = $s11 >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;

        $s0 += self::mul($s12,  666643, 20);
        $s1 += self::mul($s12,  470296, 19);
        $s2 += self::mul($s12,  654183, 20);
        $s3 -= self::mul($s12,  997805, 20);
        $s4 += self::mul($s12,  136657, 18);
        $s5 -= self::mul($s12,  683901, 20);

        $carry0 = $s0 >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        $carry1 = $s1 >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        $carry2 = $s2 >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        $carry3 = $s3 >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        $carry4 = $s4 >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        $carry5 = $s5 >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        $carry6 = $s6 >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry7 = $s7 >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry8 = $s8 >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry9 = $s9 >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry10 = $s10 >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;

        /**
         * @var array<int, int>
         */
        $arr = array(
            (int) (0xff & ($s0 >> 0)),
            (int) (0xff & ($s0 >> 8)),
            (int) (0xff & (($s0 >> 16) | $s1 << 5)),
            (int) (0xff & ($s1 >> 3)),
            (int) (0xff & ($s1 >> 11)),
            (int) (0xff & (($s1 >> 19) | $s2 << 2)),
            (int) (0xff & ($s2 >> 6)),
            (int) (0xff & (($s2 >> 14) | $s3 << 7)),
            (int) (0xff & ($s3 >> 1)),
            (int) (0xff & ($s3 >> 9)),
            (int) (0xff & (($s3 >> 17) | $s4 << 4)),
            (int) (0xff & ($s4 >> 4)),
            (int) (0xff & ($s4 >> 12)),
            (int) (0xff & (($s4 >> 20) | $s5 << 1)),
            (int) (0xff & ($s5 >> 7)),
            (int) (0xff & (($s5 >> 15) | $s6 << 6)),
            (int) (0xff & ($s6 >> 2)),
            (int) (0xff & ($s6 >> 10)),
            (int) (0xff & (($s6 >> 18) | $s7 << 3)),
            (int) (0xff & ($s7 >> 5)),
            (int) (0xff & ($s7 >> 13)),
            (int) (0xff & ($s8 >> 0)),
            (int) (0xff & ($s8 >> 8)),
            (int) (0xff & (($s8 >> 16) | $s9 << 5)),
            (int) (0xff & ($s9 >> 3)),
            (int) (0xff & ($s9 >> 11)),
            (int) (0xff & (($s9 >> 19) | $s10 << 2)),
            (int) (0xff & ($s10 >> 6)),
            (int) (0xff & (($s10 >> 14) | $s11 << 7)),
            (int) (0xff & ($s11 >> 1)),
            (int) (0xff & ($s11 >> 9)),
            0xff & ($s11 >> 17)
        );
        return self::intArrayToString($arr);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $s
     * @return string
     * @throws TypeError
     */
    public static function sc_reduce($s)
    {
        $s0 = 2097151 & self::load_3(self::substr($s, 0, 3));
        $s1 = 2097151 & (self::load_4(self::substr($s, 2, 4)) >> 5);
        $s2 = 2097151 & (self::load_3(self::substr($s, 5, 3)) >> 2);
        $s3 = 2097151 & (self::load_4(self::substr($s, 7, 4)) >> 7);
        $s4 = 2097151 & (self::load_4(self::substr($s, 10, 4)) >> 4);
        $s5 = 2097151 & (self::load_3(self::substr($s, 13, 3)) >> 1);
        $s6 = 2097151 & (self::load_4(self::substr($s, 15, 4)) >> 6);
        $s7 = 2097151 & (self::load_3(self::substr($s, 18, 4)) >> 3);
        $s8 = 2097151 & self::load_3(self::substr($s, 21, 3));
        $s9 = 2097151 & (self::load_4(self::substr($s, 23, 4)) >> 5);
        $s10 = 2097151 & (self::load_3(self::substr($s, 26, 3)) >> 2);
        $s11 = 2097151 & (self::load_4(self::substr($s, 28, 4)) >> 7);
        $s12 = 2097151 & (self::load_4(self::substr($s, 31, 4)) >> 4);
        $s13 = 2097151 & (self::load_3(self::substr($s, 34, 3)) >> 1);
        $s14 = 2097151 & (self::load_4(self::substr($s, 36, 4)) >> 6);
        $s15 = 2097151 & (self::load_3(self::substr($s, 39, 4)) >> 3);
        $s16 = 2097151 & self::load_3(self::substr($s, 42, 3));
        $s17 = 2097151 & (self::load_4(self::substr($s, 44, 4)) >> 5);
        $s18 = 2097151 & (self::load_3(self::substr($s, 47, 3)) >> 2);
        $s19 = 2097151 & (self::load_4(self::substr($s, 49, 4)) >> 7);
        $s20 = 2097151 & (self::load_4(self::substr($s, 52, 4)) >> 4);
        $s21 = 2097151 & (self::load_3(self::substr($s, 55, 3)) >> 1);
        $s22 = 2097151 & (self::load_4(self::substr($s, 57, 4)) >> 6);
        $s23 = 0x1fffffff & (self::load_4(self::substr($s, 60, 4)) >> 3);

        $s11 += self::mul($s23,  666643, 20);
        $s12 += self::mul($s23,  470296, 19);
        $s13 += self::mul($s23,  654183, 20);
        $s14 -= self::mul($s23,  997805, 20);
        $s15 += self::mul($s23,  136657, 18);
        $s16 -= self::mul($s23,  683901, 20);

        $s10 += self::mul($s22,  666643, 20);
        $s11 += self::mul($s22,  470296, 19);
        $s12 += self::mul($s22,  654183, 20);
        $s13 -= self::mul($s22,  997805, 20);
        $s14 += self::mul($s22,  136657, 18);
        $s15 -= self::mul($s22,  683901, 20);

        $s9  += self::mul($s21,  666643, 20);
        $s10 += self::mul($s21,  470296, 19);
        $s11 += self::mul($s21,  654183, 20);
        $s12 -= self::mul($s21,  997805, 20);
        $s13 += self::mul($s21,  136657, 18);
        $s14 -= self::mul($s21,  683901, 20);

        $s8  += self::mul($s20,  666643, 20);
        $s9  += self::mul($s20,  470296, 19);
        $s10 += self::mul($s20,  654183, 20);
        $s11 -= self::mul($s20,  997805, 20);
        $s12 += self::mul($s20,  136657, 18);
        $s13 -= self::mul($s20,  683901, 20);

        $s7  += self::mul($s19,  666643, 20);
        $s8  += self::mul($s19,  470296, 19);
        $s9  += self::mul($s19,  654183, 20);
        $s10 -= self::mul($s19,  997805, 20);
        $s11 += self::mul($s19,  136657, 18);
        $s12 -= self::mul($s19,  683901, 20);

        $s6  += self::mul($s18,  666643, 20);
        $s7  += self::mul($s18,  470296, 19);
        $s8  += self::mul($s18,  654183, 20);
        $s9  -= self::mul($s18,  997805, 20);
        $s10 += self::mul($s18,  136657, 18);
        $s11 -= self::mul($s18,  683901, 20);

        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        $carry12 = ($s12 + (1 << 20)) >> 21;
        $s13 += $carry12;
        $s12 -= $carry12 << 21;
        $carry14 = ($s14 + (1 << 20)) >> 21;
        $s15 += $carry14;
        $s14 -= $carry14 << 21;
        $carry16 = ($s16 + (1 << 20)) >> 21;
        $s17 += $carry16;
        $s16 -= $carry16 << 21;

        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;
        $carry13 = ($s13 + (1 << 20)) >> 21;
        $s14 += $carry13;
        $s13 -= $carry13 << 21;
        $carry15 = ($s15 + (1 << 20)) >> 21;
        $s16 += $carry15;
        $s15 -= $carry15 << 21;

        $s5  += self::mul($s17,  666643, 20);
        $s6  += self::mul($s17,  470296, 19);
        $s7  += self::mul($s17,  654183, 20);
        $s8  -= self::mul($s17,  997805, 20);
        $s9  += self::mul($s17,  136657, 18);
        $s10 -= self::mul($s17,  683901, 20);

        $s4 += self::mul($s16,  666643, 20);
        $s5 += self::mul($s16,  470296, 19);
        $s6 += self::mul($s16,  654183, 20);
        $s7 -= self::mul($s16,  997805, 20);
        $s8 += self::mul($s16,  136657, 18);
        $s9 -= self::mul($s16,  683901, 20);

        $s3 += self::mul($s15,  666643, 20);
        $s4 += self::mul($s15,  470296, 19);
        $s5 += self::mul($s15,  654183, 20);
        $s6 -= self::mul($s15,  997805, 20);
        $s7 += self::mul($s15,  136657, 18);
        $s8 -= self::mul($s15,  683901, 20);

        $s2 += self::mul($s14,  666643, 20);
        $s3 += self::mul($s14,  470296, 19);
        $s4 += self::mul($s14,  654183, 20);
        $s5 -= self::mul($s14,  997805, 20);
        $s6 += self::mul($s14,  136657, 18);
        $s7 -= self::mul($s14,  683901, 20);

        $s1 += self::mul($s13,  666643, 20);
        $s2 += self::mul($s13,  470296, 19);
        $s3 += self::mul($s13,  654183, 20);
        $s4 -= self::mul($s13,  997805, 20);
        $s5 += self::mul($s13,  136657, 18);
        $s6 -= self::mul($s13,  683901, 20);

        $s0 += self::mul($s12,  666643, 20);
        $s1 += self::mul($s12,  470296, 19);
        $s2 += self::mul($s12,  654183, 20);
        $s3 -= self::mul($s12,  997805, 20);
        $s4 += self::mul($s12,  136657, 18);
        $s5 -= self::mul($s12,  683901, 20);
        $s12 = 0;

        $carry0 = ($s0 + (1 << 20)) >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        $carry2 = ($s2 + (1 << 20)) >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        $carry4 = ($s4 + (1 << 20)) >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;

        $carry1 = ($s1 + (1 << 20)) >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        $carry3 = ($s3 + (1 << 20)) >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        $carry5 = ($s5 + (1 << 20)) >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;

        $s0 += self::mul($s12,  666643, 20);
        $s1 += self::mul($s12,  470296, 19);
        $s2 += self::mul($s12,  654183, 20);
        $s3 -= self::mul($s12,  997805, 20);
        $s4 += self::mul($s12,  136657, 18);
        $s5 -= self::mul($s12,  683901, 20);
        $s12 = 0;

        $carry0 = $s0 >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        $carry1 = $s1 >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        $carry2 = $s2 >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        $carry3 = $s3 >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        $carry4 = $s4 >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        $carry5 = $s5 >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        $carry6 = $s6 >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry7 = $s7 >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry8 = $s8 >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry9 = $s9 >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry10 = $s10 >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        $carry11 = $s11 >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;

        $s0 += self::mul($s12,  666643, 20);
        $s1 += self::mul($s12,  470296, 19);
        $s2 += self::mul($s12,  654183, 20);
        $s3 -= self::mul($s12,  997805, 20);
        $s4 += self::mul($s12,  136657, 18);
        $s5 -= self::mul($s12,  683901, 20);

        $carry0 = $s0 >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        $carry1 = $s1 >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        $carry2 = $s2 >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        $carry3 = $s3 >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        $carry4 = $s4 >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        $carry5 = $s5 >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        $carry6 = $s6 >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        $carry7 = $s7 >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        $carry8 = $s8 >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        $carry9 = $s9 >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        $carry10 = $s10 >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;

        /**
         * @var array<int, int>
         */
        $arr = array(
            (int) ($s0 >> 0),
            (int) ($s0 >> 8),
            (int) (($s0 >> 16) | $s1 << 5),
            (int) ($s1 >> 3),
            (int) ($s1 >> 11),
            (int) (($s1 >> 19) | $s2 << 2),
            (int) ($s2 >> 6),
            (int) (($s2 >> 14) | $s3 << 7),
            (int) ($s3 >> 1),
            (int) ($s3 >> 9),
            (int) (($s3 >> 17) | $s4 << 4),
            (int) ($s4 >> 4),
            (int) ($s4 >> 12),
            (int) (($s4 >> 20) | $s5 << 1),
            (int) ($s5 >> 7),
            (int) (($s5 >> 15) | $s6 << 6),
            (int) ($s6 >> 2),
            (int) ($s6 >> 10),
            (int) (($s6 >> 18) | $s7 << 3),
            (int) ($s7 >> 5),
            (int) ($s7 >> 13),
            (int) ($s8 >> 0),
            (int) ($s8 >> 8),
            (int) (($s8 >> 16) | $s9 << 5),
            (int) ($s9 >> 3),
            (int) ($s9 >> 11),
            (int) (($s9 >> 19) | $s10 << 2),
            (int) ($s10 >> 6),
            (int) (($s10 >> 14) | $s11 << 7),
            (int) ($s11 >> 1),
            (int) ($s11 >> 9),
            (int) $s11 >> 17
        );
        return self::intArrayToString($arr);
    }

    /**
     * multiply by the order of the main subgroup l = 2^252+27742317777372353535851937790883648493
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $A
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P3
     */
    public static function ge_mul_l(ParagonIE_Sodium_Core_Curve25519_Ge_P3 $A)
    {
        $aslide = array(
            13, 0, 0, 0, 0, -1, 0, 0, 0, 0, -11, 0, 0, 0, 0, 0, 0, -5, 0, 0, 0,
            0, 0, 0, -3, 0, 0, 0, 0, -13, 0, 0, 0, 0, 7, 0, 0, 0, 0, 0, 3, 0,
            0, 0, 0, -13, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 0, 0, 0, 11, 0, 0, 0,
            0, 0, 11, 0, 0, 0, 0, -13, 0, 0, 0, 0, 0, 0, -3, 0, 0, 0, 0, 0, -1,
            0, 0, 0, 0, 3, 0, 0, 0, 0, -11, 0, 0, 0, 0, 0, 0, 0, 15, 0, 0, 0,
            0, 0, -1, 0, 0, 0, 0, -1, 0, 0, 0, 0, 7, 0, 0, 0, 0, 5, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1
        );

        /** @var array<int, ParagonIE_Sodium_Core_Curve25519_Ge_Cached> $Ai size 8 */
        $Ai = array();

        # ge_p3_to_cached(&Ai[0], A);
        $Ai[0] = self::ge_p3_to_cached($A);
        # ge_p3_dbl(&t, A);
        $t = self::ge_p3_dbl($A);
        # ge_p1p1_to_p3(&A2, &t);
        $A2 = self::ge_p1p1_to_p3($t);

        for ($i = 1; $i < 8; ++$i) {
            # ge_add(&t, &A2, &Ai[0]);
            $t = self::ge_add($A2, $Ai[$i - 1]);
            # ge_p1p1_to_p3(&u, &t);
            $u = self::ge_p1p1_to_p3($t);
            # ge_p3_to_cached(&Ai[i], &u);
            $Ai[$i] = self::ge_p3_to_cached($u);
        }

        $r = self::ge_p3_0();
        for ($i = 252; $i >= 0; --$i) {
            $t = self::ge_p3_dbl($r);
            if ($aslide[$i] > 0) {
                # ge_p1p1_to_p3(&u, &t);
                $u = self::ge_p1p1_to_p3($t);
                # ge_add(&t, &u, &Ai[aslide[i] / 2]);
                $t = self::ge_add($u, $Ai[(int)($aslide[$i] / 2)]);
            } elseif ($aslide[$i] < 0) {
                # ge_p1p1_to_p3(&u, &t);
                $u = self::ge_p1p1_to_p3($t);
                # ge_sub(&t, &u, &Ai[(-aslide[i]) / 2]);
                $t = self::ge_sub($u, $Ai[(int)(-$aslide[$i] / 2)]);
            }
        }

        # ge_p1p1_to_p3(r, &t);
        return self::ge_p1p1_to_p3($t);
    }

    /**
     * @param string $a
     * @param string $b
     * @return string
     */
    public static function sc25519_mul($a, $b)
    {
        //    int64_t a0  = 2097151 & load_3(a);
        //    int64_t a1  = 2097151 & (load_4(a + 2) >> 5);
        //    int64_t a2  = 2097151 & (load_3(a + 5) >> 2);
        //    int64_t a3  = 2097151 & (load_4(a + 7) >> 7);
        //    int64_t a4  = 2097151 & (load_4(a + 10) >> 4);
        //    int64_t a5  = 2097151 & (load_3(a + 13) >> 1);
        //    int64_t a6  = 2097151 & (load_4(a + 15) >> 6);
        //    int64_t a7  = 2097151 & (load_3(a + 18) >> 3);
        //    int64_t a8  = 2097151 & load_3(a + 21);
        //    int64_t a9  = 2097151 & (load_4(a + 23) >> 5);
        //    int64_t a10 = 2097151 & (load_3(a + 26) >> 2);
        //    int64_t a11 = (load_4(a + 28) >> 7);
        $a0  = 2097151 &  self::load_3(self::substr($a, 0, 3));
        $a1  = 2097151 & (self::load_4(self::substr($a, 2, 4)) >> 5);
        $a2  = 2097151 & (self::load_3(self::substr($a, 5, 3)) >> 2);
        $a3  = 2097151 & (self::load_4(self::substr($a, 7, 4)) >> 7);
        $a4  = 2097151 & (self::load_4(self::substr($a, 10, 4)) >> 4);
        $a5  = 2097151 & (self::load_3(self::substr($a, 13, 3)) >> 1);
        $a6  = 2097151 & (self::load_4(self::substr($a, 15, 4)) >> 6);
        $a7  = 2097151 & (self::load_3(self::substr($a, 18, 3)) >> 3);
        $a8  = 2097151 &  self::load_3(self::substr($a, 21, 3));
        $a9  = 2097151 & (self::load_4(self::substr($a, 23, 4)) >> 5);
        $a10 = 2097151 & (self::load_3(self::substr($a, 26, 3)) >> 2);
        $a11 = (self::load_4(self::substr($a, 28, 4)) >> 7);

        //    int64_t b0  = 2097151 & load_3(b);
        //    int64_t b1  = 2097151 & (load_4(b + 2) >> 5);
        //    int64_t b2  = 2097151 & (load_3(b + 5) >> 2);
        //    int64_t b3  = 2097151 & (load_4(b + 7) >> 7);
        //    int64_t b4  = 2097151 & (load_4(b + 10) >> 4);
        //    int64_t b5  = 2097151 & (load_3(b + 13) >> 1);
        //    int64_t b6  = 2097151 & (load_4(b + 15) >> 6);
        //    int64_t b7  = 2097151 & (load_3(b + 18) >> 3);
        //    int64_t b8  = 2097151 & load_3(b + 21);
        //    int64_t b9  = 2097151 & (load_4(b + 23) >> 5);
        //    int64_t b10 = 2097151 & (load_3(b + 26) >> 2);
        //    int64_t b11 = (load_4(b + 28) >> 7);
        $b0  = 2097151 &  self::load_3(self::substr($b, 0, 3));
        $b1  = 2097151 & (self::load_4(self::substr($b, 2, 4)) >> 5);
        $b2  = 2097151 & (self::load_3(self::substr($b, 5, 3)) >> 2);
        $b3  = 2097151 & (self::load_4(self::substr($b, 7, 4)) >> 7);
        $b4  = 2097151 & (self::load_4(self::substr($b, 10, 4)) >> 4);
        $b5  = 2097151 & (self::load_3(self::substr($b, 13, 3)) >> 1);
        $b6  = 2097151 & (self::load_4(self::substr($b, 15, 4)) >> 6);
        $b7  = 2097151 & (self::load_3(self::substr($b, 18, 3)) >> 3);
        $b8  = 2097151 &  self::load_3(self::substr($b, 21, 3));
        $b9  = 2097151 & (self::load_4(self::substr($b, 23, 4)) >> 5);
        $b10 = 2097151 & (self::load_3(self::substr($b, 26, 3)) >> 2);
        $b11 = (self::load_4(self::substr($b, 28, 4)) >> 7);

        //    s0 = a0 * b0;
        //    s1 = a0 * b1 + a1 * b0;
        //    s2 = a0 * b2 + a1 * b1 + a2 * b0;
        //    s3 = a0 * b3 + a1 * b2 + a2 * b1 + a3 * b0;
        //    s4 = a0 * b4 + a1 * b3 + a2 * b2 + a3 * b1 + a4 * b0;
        //    s5 = a0 * b5 + a1 * b4 + a2 * b3 + a3 * b2 + a4 * b1 + a5 * b0;
        //    s6 = a0 * b6 + a1 * b5 + a2 * b4 + a3 * b3 + a4 * b2 + a5 * b1 + a6 * b0;
        //    s7 = a0 * b7 + a1 * b6 + a2 * b5 + a3 * b4 + a4 * b3 + a5 * b2 +
        //        a6 * b1 + a7 * b0;
        //    s8 = a0 * b8 + a1 * b7 + a2 * b6 + a3 * b5 + a4 * b4 + a5 * b3 +
        //        a6 * b2 + a7 * b1 + a8 * b0;
        //    s9 = a0 * b9 + a1 * b8 + a2 * b7 + a3 * b6 + a4 * b5 + a5 * b4 +
        //        a6 * b3 + a7 * b2 + a8 * b1 + a9 * b0;
        //    s10 = a0 * b10 + a1 * b9 + a2 * b8 + a3 * b7 + a4 * b6 + a5 * b5 +
        //        a6 * b4 + a7 * b3 + a8 * b2 + a9 * b1 + a10 * b0;
        //    s11 = a0 * b11 + a1 * b10 + a2 * b9 + a3 * b8 + a4 * b7 + a5 * b6 +
        //        a6 * b5 + a7 * b4 + a8 * b3 + a9 * b2 + a10 * b1 + a11 * b0;
        //    s12 = a1 * b11 + a2 * b10 + a3 * b9 + a4 * b8 + a5 * b7 + a6 * b6 +
        //        a7 * b5 + a8 * b4 + a9 * b3 + a10 * b2 + a11 * b1;
        //    s13 = a2 * b11 + a3 * b10 + a4 * b9 + a5 * b8 + a6 * b7 + a7 * b6 +
        //        a8 * b5 + a9 * b4 + a10 * b3 + a11 * b2;
        //    s14 = a3 * b11 + a4 * b10 + a5 * b9 + a6 * b8 + a7 * b7 + a8 * b6 +
        //        a9 * b5 + a10 * b4 + a11 * b3;
        //    s15 = a4 * b11 + a5 * b10 + a6 * b9 + a7 * b8 + a8 * b7 + a9 * b6 +
        //        a10 * b5 + a11 * b4;
        //    s16 =
        //        a5 * b11 + a6 * b10 + a7 * b9 + a8 * b8 + a9 * b7 + a10 * b6 + a11 * b5;
        //    s17 = a6 * b11 + a7 * b10 + a8 * b9 + a9 * b8 + a10 * b7 + a11 * b6;
        //    s18 = a7 * b11 + a8 * b10 + a9 * b9 + a10 * b8 + a11 * b7;
        //    s19 = a8 * b11 + a9 * b10 + a10 * b9 + a11 * b8;
        //    s20 = a9 * b11 + a10 * b10 + a11 * b9;
        //    s21 = a10 * b11 + a11 * b10;
        //    s22 = a11 * b11;
        //    s23 = 0;
        $s0 = self::mul($a0, $b0, 22);
        $s1 = self::mul($a0, $b1, 22) + self::mul($a1, $b0, 22);
        $s2 = self::mul($a0, $b2, 22) + self::mul($a1, $b1, 22) + self::mul($a2, $b0, 22);
        $s3 = self::mul($a0, $b3, 22) + self::mul($a1, $b2, 22) + self::mul($a2, $b1, 22) + self::mul($a3, $b0, 22);
        $s4 = self::mul($a0, $b4, 22) + self::mul($a1, $b3, 22) + self::mul($a2, $b2, 22) + self::mul($a3, $b1, 22) +
            self::mul($a4, $b0, 22);
        $s5 = self::mul($a0, $b5, 22) + self::mul($a1, $b4, 22) + self::mul($a2, $b3, 22) + self::mul($a3, $b2, 22) +
            self::mul($a4, $b1, 22) + self::mul($a5, $b0, 22);
        $s6 = self::mul($a0, $b6, 22) + self::mul($a1, $b5, 22) + self::mul($a2, $b4, 22) + self::mul($a3, $b3, 22) +
            self::mul($a4, $b2, 22) + self::mul($a5, $b1, 22) + self::mul($a6, $b0, 22);
        $s7 = self::mul($a0, $b7, 22) + self::mul($a1, $b6, 22) + self::mul($a2, $b5, 22) + self::mul($a3, $b4, 22) +
            self::mul($a4, $b3, 22) + self::mul($a5, $b2, 22) + self::mul($a6, $b1, 22) + self::mul($a7, $b0, 22);
        $s8 = self::mul($a0, $b8, 22) + self::mul($a1, $b7, 22) + self::mul($a2, $b6, 22) + self::mul($a3, $b5, 22) +
            self::mul($a4, $b4, 22) + self::mul($a5, $b3, 22) + self::mul($a6, $b2, 22) + self::mul($a7, $b1, 22) +
            self::mul($a8, $b0, 22);
        $s9 = self::mul($a0, $b9, 22) + self::mul($a1, $b8, 22) + self::mul($a2, $b7, 22) + self::mul($a3, $b6, 22) +
            self::mul($a4, $b5, 22) + self::mul($a5, $b4, 22) + self::mul($a6, $b3, 22) + self::mul($a7, $b2, 22) +
            self::mul($a8, $b1, 22) + self::mul($a9, $b0, 22);
        $s10 = self::mul($a0, $b10, 22) + self::mul($a1, $b9, 22) + self::mul($a2, $b8, 22) + self::mul($a3, $b7, 22) +
            self::mul($a4, $b6, 22) + self::mul($a5, $b5, 22) + self::mul($a6, $b4, 22) + self::mul($a7, $b3, 22) +
            self::mul($a8, $b2, 22) + self::mul($a9, $b1, 22) + self::mul($a10, $b0, 22);
        $s11 = self::mul($a0, $b11, 22) + self::mul($a1, $b10, 22) + self::mul($a2, $b9, 22) + self::mul($a3, $b8, 22) +
            self::mul($a4, $b7, 22) + self::mul($a5, $b6, 22) + self::mul($a6, $b5, 22) + self::mul($a7, $b4, 22) +
            self::mul($a8, $b3, 22) + self::mul($a9, $b2, 22) + self::mul($a10, $b1, 22) + self::mul($a11, $b0, 22);
        $s12 = self::mul($a1, $b11, 22) + self::mul($a2, $b10, 22) + self::mul($a3, $b9, 22) + self::mul($a4, $b8, 22) +
            self::mul($a5, $b7, 22) + self::mul($a6, $b6, 22) + self::mul($a7, $b5, 22) + self::mul($a8, $b4, 22) +
            self::mul($a9, $b3, 22) + self::mul($a10, $b2, 22) + self::mul($a11, $b1, 22);
        $s13 = self::mul($a2, $b11, 22) + self::mul($a3, $b10, 22) + self::mul($a4, $b9, 22) + self::mul($a5, $b8, 22) +
            self::mul($a6, $b7, 22) + self::mul($a7, $b6, 22) + self::mul($a8, $b5, 22) + self::mul($a9, $b4, 22) +
            self::mul($a10, $b3, 22) + self::mul($a11, $b2, 22);
        $s14 = self::mul($a3, $b11, 22) + self::mul($a4, $b10, 22) + self::mul($a5, $b9, 22) + self::mul($a6, $b8, 22) +
            self::mul($a7, $b7, 22) + self::mul($a8, $b6, 22) + self::mul($a9, $b5, 22) + self::mul($a10, $b4, 22) +
            self::mul($a11, $b3, 22);
        $s15 = self::mul($a4, $b11, 22) + self::mul($a5, $b10, 22) + self::mul($a6, $b9, 22) + self::mul($a7, $b8, 22) +
            self::mul($a8, $b7, 22) + self::mul($a9, $b6, 22) + self::mul($a10, $b5, 22) + self::mul($a11, $b4, 22);
        $s16 =
            self::mul($a5, $b11, 22) + self::mul($a6, $b10, 22) + self::mul($a7, $b9, 22) + self::mul($a8, $b8, 22) +
            self::mul($a9, $b7, 22) + self::mul($a10, $b6, 22) + self::mul($a11, $b5, 22);
        $s17 = self::mul($a6, $b11, 22) + self::mul($a7, $b10, 22) + self::mul($a8, $b9, 22) + self::mul($a9, $b8, 22) +
            self::mul($a10, $b7, 22) + self::mul($a11, $b6, 22);
        $s18 = self::mul($a7, $b11, 22) + self::mul($a8, $b10, 22) + self::mul($a9, $b9, 22) + self::mul($a10, $b8, 22)
            + self::mul($a11, $b7, 22);
        $s19 = self::mul($a8, $b11, 22) + self::mul($a9, $b10, 22) + self::mul($a10, $b9, 22) +
            self::mul($a11, $b8, 22);
        $s20 = self::mul($a9, $b11, 22) + self::mul($a10, $b10, 22) + self::mul($a11, $b9, 22);
        $s21 = self::mul($a10, $b11, 22) + self::mul($a11, $b10, 22);
        $s22 = self::mul($a11, $b11, 22);
        $s23 = 0;

        //    carry0 = (s0 + (int64_t) (1L << 20)) >> 21;
        //    s1 += carry0;
        //    s0 -= carry0 * ((uint64_t) 1L << 21);
        $carry0 = ($s0 + (1 << 20)) >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        //    carry2 = (s2 + (int64_t) (1L << 20)) >> 21;
        //    s3 += carry2;
        //    s2 -= carry2 * ((uint64_t) 1L << 21);
        $carry2 = ($s2 + (1 << 20)) >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        //    carry4 = (s4 + (int64_t) (1L << 20)) >> 21;
        //    s5 += carry4;
        //    s4 -= carry4 * ((uint64_t) 1L << 21);
        $carry4 = ($s4 + (1 << 20)) >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        //    carry6 = (s6 + (int64_t) (1L << 20)) >> 21;
        //    s7 += carry6;
        //    s6 -= carry6 * ((uint64_t) 1L << 21);
        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        //    carry8 = (s8 + (int64_t) (1L << 20)) >> 21;
        //    s9 += carry8;
        //    s8 -= carry8 * ((uint64_t) 1L << 21);
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        //    carry10 = (s10 + (int64_t) (1L << 20)) >> 21;
        //    s11 += carry10;
        //    s10 -= carry10 * ((uint64_t) 1L << 21);
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        //    carry12 = (s12 + (int64_t) (1L << 20)) >> 21;
        //    s13 += carry12;
        //    s12 -= carry12 * ((uint64_t) 1L << 21);
        $carry12 = ($s12 + (1 << 20)) >> 21;
        $s13 += $carry12;
        $s12 -= $carry12 << 21;
        //    carry14 = (s14 + (int64_t) (1L << 20)) >> 21;
        //    s15 += carry14;
        //    s14 -= carry14 * ((uint64_t) 1L << 21);
        $carry14 = ($s14 + (1 << 20)) >> 21;
        $s15 += $carry14;
        $s14 -= $carry14 << 21;
        //    carry16 = (s16 + (int64_t) (1L << 20)) >> 21;
        //    s17 += carry16;
        //    s16 -= carry16 * ((uint64_t) 1L << 21);
        $carry16 = ($s16 + (1 << 20)) >> 21;
        $s17 += $carry16;
        $s16 -= $carry16 << 21;
        //    carry18 = (s18 + (int64_t) (1L << 20)) >> 21;
        //    s19 += carry18;
        //    s18 -= carry18 * ((uint64_t) 1L << 21);
        $carry18 = ($s18 + (1 << 20)) >> 21;
        $s19 += $carry18;
        $s18 -= $carry18 << 21;
        //    carry20 = (s20 + (int64_t) (1L << 20)) >> 21;
        //    s21 += carry20;
        //    s20 -= carry20 * ((uint64_t) 1L << 21);
        $carry20 = ($s20 + (1 << 20)) >> 21;
        $s21 += $carry20;
        $s20 -= $carry20 << 21;
        //    carry22 = (s22 + (int64_t) (1L << 20)) >> 21;
        //    s23 += carry22;
        //    s22 -= carry22 * ((uint64_t) 1L << 21);
        $carry22 = ($s22 + (1 << 20)) >> 21;
        $s23 += $carry22;
        $s22 -= $carry22 << 21;

        //    carry1 = (s1 + (int64_t) (1L << 20)) >> 21;
        //    s2 += carry1;
        //    s1 -= carry1 * ((uint64_t) 1L << 21);
        $carry1 = ($s1 + (1 << 20)) >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        //    carry3 = (s3 + (int64_t) (1L << 20)) >> 21;
        //    s4 += carry3;
        //    s3 -= carry3 * ((uint64_t) 1L << 21);
        $carry3 = ($s3 + (1 << 20)) >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        //    carry5 = (s5 + (int64_t) (1L << 20)) >> 21;
        //    s6 += carry5;
        //    s5 -= carry5 * ((uint64_t) 1L << 21);
        $carry5 = ($s5 + (1 << 20)) >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        //    carry7 = (s7 + (int64_t) (1L << 20)) >> 21;
        //    s8 += carry7;
        //    s7 -= carry7 * ((uint64_t) 1L << 21);
        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        //    carry9 = (s9 + (int64_t) (1L << 20)) >> 21;
        //    s10 += carry9;
        //    s9 -= carry9 * ((uint64_t) 1L << 21);
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        //    carry11 = (s11 + (int64_t) (1L << 20)) >> 21;
        //    s12 += carry11;
        //    s11 -= carry11 * ((uint64_t) 1L << 21);
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;
        //    carry13 = (s13 + (int64_t) (1L << 20)) >> 21;
        //    s14 += carry13;
        //    s13 -= carry13 * ((uint64_t) 1L << 21);
        $carry13 = ($s13 + (1 << 20)) >> 21;
        $s14 += $carry13;
        $s13 -= $carry13 << 21;
        //    carry15 = (s15 + (int64_t) (1L << 20)) >> 21;
        //    s16 += carry15;
        //    s15 -= carry15 * ((uint64_t) 1L << 21);
        $carry15 = ($s15 + (1 << 20)) >> 21;
        $s16 += $carry15;
        $s15 -= $carry15 << 21;
        //    carry17 = (s17 + (int64_t) (1L << 20)) >> 21;
        //    s18 += carry17;
        //    s17 -= carry17 * ((uint64_t) 1L << 21);
        $carry17 = ($s17 + (1 << 20)) >> 21;
        $s18 += $carry17;
        $s17 -= $carry17 << 21;
        //    carry19 = (s19 + (int64_t) (1L << 20)) >> 21;
        //    s20 += carry19;
        //    s19 -= carry19 * ((uint64_t) 1L << 21);
        $carry19 = ($s19 + (1 << 20)) >> 21;
        $s20 += $carry19;
        $s19 -= $carry19 << 21;
        //    carry21 = (s21 + (int64_t) (1L << 20)) >> 21;
        //    s22 += carry21;
        //    s21 -= carry21 * ((uint64_t) 1L << 21);
        $carry21 = ($s21 + (1 << 20)) >> 21;
        $s22 += $carry21;
        $s21 -= $carry21 << 21;

        //    s11 += s23 * 666643;
        //    s12 += s23 * 470296;
        //    s13 += s23 * 654183;
        //    s14 -= s23 * 997805;
        //    s15 += s23 * 136657;
        //    s16 -= s23 * 683901;
        $s11 += self::mul($s23, 666643, 20);
        $s12 += self::mul($s23, 470296, 19);
        $s13 += self::mul($s23, 654183, 20);
        $s14 -= self::mul($s23, 997805, 20);
        $s15 += self::mul($s23, 136657, 18);
        $s16 -= self::mul($s23, 683901, 20);

        //    s10 += s22 * 666643;
        //    s11 += s22 * 470296;
        //    s12 += s22 * 654183;
        //    s13 -= s22 * 997805;
        //    s14 += s22 * 136657;
        //    s15 -= s22 * 683901;
        $s10 += self::mul($s22, 666643, 20);
        $s11 += self::mul($s22, 470296, 19);
        $s12 += self::mul($s22, 654183, 20);
        $s13 -= self::mul($s22, 997805, 20);
        $s14 += self::mul($s22, 136657, 18);
        $s15 -= self::mul($s22, 683901, 20);

        //    s9 += s21 * 666643;
        //    s10 += s21 * 470296;
        //    s11 += s21 * 654183;
        //    s12 -= s21 * 997805;
        //    s13 += s21 * 136657;
        //    s14 -= s21 * 683901;
        $s9 += self::mul($s21, 666643, 20);
        $s10 += self::mul($s21, 470296, 19);
        $s11 += self::mul($s21, 654183, 20);
        $s12 -= self::mul($s21, 997805, 20);
        $s13 += self::mul($s21, 136657, 18);
        $s14 -= self::mul($s21, 683901, 20);

        //    s8 += s20 * 666643;
        //    s9 += s20 * 470296;
        //    s10 += s20 * 654183;
        //    s11 -= s20 * 997805;
        //    s12 += s20 * 136657;
        //    s13 -= s20 * 683901;
        $s8 += self::mul($s20, 666643, 20);
        $s9 += self::mul($s20, 470296, 19);
        $s10 += self::mul($s20, 654183, 20);
        $s11 -= self::mul($s20, 997805, 20);
        $s12 += self::mul($s20, 136657, 18);
        $s13 -= self::mul($s20, 683901, 20);

        //    s7 += s19 * 666643;
        //    s8 += s19 * 470296;
        //    s9 += s19 * 654183;
        //    s10 -= s19 * 997805;
        //    s11 += s19 * 136657;
        //    s12 -= s19 * 683901;
        $s7 += self::mul($s19, 666643, 20);
        $s8 += self::mul($s19, 470296, 19);
        $s9 += self::mul($s19, 654183, 20);
        $s10 -= self::mul($s19, 997805, 20);
        $s11 += self::mul($s19, 136657, 18);
        $s12 -= self::mul($s19, 683901, 20);

        //    s6 += s18 * 666643;
        //    s7 += s18 * 470296;
        //    s8 += s18 * 654183;
        //    s9 -= s18 * 997805;
        //    s10 += s18 * 136657;
        //    s11 -= s18 * 683901;
        $s6 += self::mul($s18, 666643, 20);
        $s7 += self::mul($s18, 470296, 19);
        $s8 += self::mul($s18, 654183, 20);
        $s9 -= self::mul($s18, 997805, 20);
        $s10 += self::mul($s18, 136657, 18);
        $s11 -= self::mul($s18, 683901, 20);

        //    carry6 = (s6 + (int64_t) (1L << 20)) >> 21;
        //    s7 += carry6;
        //    s6 -= carry6 * ((uint64_t) 1L << 21);
        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        //    carry8 = (s8 + (int64_t) (1L << 20)) >> 21;
        //    s9 += carry8;
        //    s8 -= carry8 * ((uint64_t) 1L << 21);
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        //    carry10 = (s10 + (int64_t) (1L << 20)) >> 21;
        //    s11 += carry10;
        //    s10 -= carry10 * ((uint64_t) 1L << 21);
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        //    carry12 = (s12 + (int64_t) (1L << 20)) >> 21;
        //    s13 += carry12;
        //    s12 -= carry12 * ((uint64_t) 1L << 21);
        $carry12 = ($s12 + (1 << 20)) >> 21;
        $s13 += $carry12;
        $s12 -= $carry12 << 21;
        //    carry14 = (s14 + (int64_t) (1L << 20)) >> 21;
        //    s15 += carry14;
        //    s14 -= carry14 * ((uint64_t) 1L << 21);
        $carry14 = ($s14 + (1 << 20)) >> 21;
        $s15 += $carry14;
        $s14 -= $carry14 << 21;
        //    carry16 = (s16 + (int64_t) (1L << 20)) >> 21;
        //    s17 += carry16;
        //    s16 -= carry16 * ((uint64_t) 1L << 21);
        $carry16 = ($s16 + (1 << 20)) >> 21;
        $s17 += $carry16;
        $s16 -= $carry16 << 21;

        //    carry7 = (s7 + (int64_t) (1L << 20)) >> 21;
        //    s8 += carry7;
        //    s7 -= carry7 * ((uint64_t) 1L << 21);
        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        //    carry9 = (s9 + (int64_t) (1L << 20)) >> 21;
        //    s10 += carry9;
        //    s9 -= carry9 * ((uint64_t) 1L << 21);
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        //    carry11 = (s11 + (int64_t) (1L << 20)) >> 21;
        //    s12 += carry11;
        //    s11 -= carry11 * ((uint64_t) 1L << 21);
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;
        //    carry13 = (s13 + (int64_t) (1L << 20)) >> 21;
        //    s14 += carry13;
        //    s13 -= carry13 * ((uint64_t) 1L << 21);
        $carry13 = ($s13 + (1 << 20)) >> 21;
        $s14 += $carry13;
        $s13 -= $carry13 << 21;
        //    carry15 = (s15 + (int64_t) (1L << 20)) >> 21;
        //    s16 += carry15;
        //    s15 -= carry15 * ((uint64_t) 1L << 21);
        $carry15 = ($s15 + (1 << 20)) >> 21;
        $s16 += $carry15;
        $s15 -= $carry15 << 21;

        //    s5 += s17 * 666643;
        //    s6 += s17 * 470296;
        //    s7 += s17 * 654183;
        //    s8 -= s17 * 997805;
        //    s9 += s17 * 136657;
        //    s10 -= s17 * 683901;
        $s5 += self::mul($s17, 666643, 20);
        $s6 += self::mul($s17, 470296, 19);
        $s7 += self::mul($s17, 654183, 20);
        $s8 -= self::mul($s17, 997805, 20);
        $s9 += self::mul($s17, 136657, 18);
        $s10 -= self::mul($s17, 683901, 20);

        //    s4 += s16 * 666643;
        //    s5 += s16 * 470296;
        //    s6 += s16 * 654183;
        //    s7 -= s16 * 997805;
        //    s8 += s16 * 136657;
        //    s9 -= s16 * 683901;
        $s4 += self::mul($s16, 666643, 20);
        $s5 += self::mul($s16, 470296, 19);
        $s6 += self::mul($s16, 654183, 20);
        $s7 -= self::mul($s16, 997805, 20);
        $s8 += self::mul($s16, 136657, 18);
        $s9 -= self::mul($s16, 683901, 20);

        //    s3 += s15 * 666643;
        //    s4 += s15 * 470296;
        //    s5 += s15 * 654183;
        //    s6 -= s15 * 997805;
        //    s7 += s15 * 136657;
        //    s8 -= s15 * 683901;
        $s3 += self::mul($s15, 666643, 20);
        $s4 += self::mul($s15, 470296, 19);
        $s5 += self::mul($s15, 654183, 20);
        $s6 -= self::mul($s15, 997805, 20);
        $s7 += self::mul($s15, 136657, 18);
        $s8 -= self::mul($s15, 683901, 20);

        //    s2 += s14 * 666643;
        //    s3 += s14 * 470296;
        //    s4 += s14 * 654183;
        //    s5 -= s14 * 997805;
        //    s6 += s14 * 136657;
        //    s7 -= s14 * 683901;
        $s2 += self::mul($s14, 666643, 20);
        $s3 += self::mul($s14, 470296, 19);
        $s4 += self::mul($s14, 654183, 20);
        $s5 -= self::mul($s14, 997805, 20);
        $s6 += self::mul($s14, 136657, 18);
        $s7 -= self::mul($s14, 683901, 20);

        //    s1 += s13 * 666643;
        //    s2 += s13 * 470296;
        //    s3 += s13 * 654183;
        //    s4 -= s13 * 997805;
        //    s5 += s13 * 136657;
        //    s6 -= s13 * 683901;
        $s1 += self::mul($s13, 666643, 20);
        $s2 += self::mul($s13, 470296, 19);
        $s3 += self::mul($s13, 654183, 20);
        $s4 -= self::mul($s13, 997805, 20);
        $s5 += self::mul($s13, 136657, 18);
        $s6 -= self::mul($s13, 683901, 20);

        //    s0 += s12 * 666643;
        //    s1 += s12 * 470296;
        //    s2 += s12 * 654183;
        //    s3 -= s12 * 997805;
        //    s4 += s12 * 136657;
        //    s5 -= s12 * 683901;
        //    s12 = 0;
        $s0 += self::mul($s12, 666643, 20);
        $s1 += self::mul($s12, 470296, 19);
        $s2 += self::mul($s12, 654183, 20);
        $s3 -= self::mul($s12, 997805, 20);
        $s4 += self::mul($s12, 136657, 18);
        $s5 -= self::mul($s12, 683901, 20);
        $s12 = 0;

        //    carry0 = (s0 + (int64_t) (1L << 20)) >> 21;
        //    s1 += carry0;
        //    s0 -= carry0 * ((uint64_t) 1L << 21);
        $carry0 = ($s0 + (1 << 20)) >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        //    carry2 = (s2 + (int64_t) (1L << 20)) >> 21;
        //    s3 += carry2;
        //    s2 -= carry2 * ((uint64_t) 1L << 21);
        $carry2 = ($s2 + (1 << 20)) >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        //    carry4 = (s4 + (int64_t) (1L << 20)) >> 21;
        //    s5 += carry4;
        //    s4 -= carry4 * ((uint64_t) 1L << 21);
        $carry4 = ($s4 + (1 << 20)) >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        //    carry6 = (s6 + (int64_t) (1L << 20)) >> 21;
        //    s7 += carry6;
        //    s6 -= carry6 * ((uint64_t) 1L << 21);
        $carry6 = ($s6 + (1 << 20)) >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        //    carry8 = (s8 + (int64_t) (1L << 20)) >> 21;
        //    s9 += carry8;
        //    s8 -= carry8 * ((uint64_t) 1L << 21);
        $carry8 = ($s8 + (1 << 20)) >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        //    carry10 = (s10 + (int64_t) (1L << 20)) >> 21;
        //    s11 += carry10;
        //    s10 -= carry10 * ((uint64_t) 1L << 21);
        $carry10 = ($s10 + (1 << 20)) >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;

        //    carry1 = (s1 + (int64_t) (1L << 20)) >> 21;
        //    s2 += carry1;
        //    s1 -= carry1 * ((uint64_t) 1L << 21);
        $carry1 = ($s1 + (1 << 20)) >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        //    carry3 = (s3 + (int64_t) (1L << 20)) >> 21;
        //    s4 += carry3;
        //    s3 -= carry3 * ((uint64_t) 1L << 21);
        $carry3 = ($s3 + (1 << 20)) >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        //    carry5 = (s5 + (int64_t) (1L << 20)) >> 21;
        //    s6 += carry5;
        //    s5 -= carry5 * ((uint64_t) 1L << 21);
        $carry5 = ($s5 + (1 << 20)) >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        //    carry7 = (s7 + (int64_t) (1L << 20)) >> 21;
        //    s8 += carry7;
        //    s7 -= carry7 * ((uint64_t) 1L << 21);
        $carry7 = ($s7 + (1 << 20)) >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        //    carry9 = (s9 + (int64_t) (1L << 20)) >> 21;
        //    s10 += carry9;
        //    s9 -= carry9 * ((uint64_t) 1L << 21);
        $carry9 = ($s9 + (1 << 20)) >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        //    carry11 = (s11 + (int64_t) (1L << 20)) >> 21;
        //    s12 += carry11;
        //    s11 -= carry11 * ((uint64_t) 1L << 21);
        $carry11 = ($s11 + (1 << 20)) >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;

        //    s0 += s12 * 666643;
        //    s1 += s12 * 470296;
        //    s2 += s12 * 654183;
        //    s3 -= s12 * 997805;
        //    s4 += s12 * 136657;
        //    s5 -= s12 * 683901;
        //    s12 = 0;
        $s0 += self::mul($s12, 666643, 20);
        $s1 += self::mul($s12, 470296, 19);
        $s2 += self::mul($s12, 654183, 20);
        $s3 -= self::mul($s12, 997805, 20);
        $s4 += self::mul($s12, 136657, 18);
        $s5 -= self::mul($s12, 683901, 20);
        $s12 = 0;

        //    carry0 = s0 >> 21;
        //    s1 += carry0;
        //    s0 -= carry0 * ((uint64_t) 1L << 21);
        $carry0 = $s0 >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        //    carry1 = s1 >> 21;
        //    s2 += carry1;
        //    s1 -= carry1 * ((uint64_t) 1L << 21);
        $carry1 = $s1 >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        //    carry2 = s2 >> 21;
        //    s3 += carry2;
        //    s2 -= carry2 * ((uint64_t) 1L << 21);
        $carry2 = $s2 >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        //    carry3 = s3 >> 21;
        //    s4 += carry3;
        //    s3 -= carry3 * ((uint64_t) 1L << 21);
        $carry3 = $s3 >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        //    carry4 = s4 >> 21;
        //    s5 += carry4;
        //    s4 -= carry4 * ((uint64_t) 1L << 21);
        $carry4 = $s4 >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        //    carry5 = s5 >> 21;
        //    s6 += carry5;
        //    s5 -= carry5 * ((uint64_t) 1L << 21);
        $carry5 = $s5 >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        //    carry6 = s6 >> 21;
        //    s7 += carry6;
        //    s6 -= carry6 * ((uint64_t) 1L << 21);
        $carry6 = $s6 >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        //    carry7 = s7 >> 21;
        //    s8 += carry7;
        //    s7 -= carry7 * ((uint64_t) 1L << 21);
        $carry7 = $s7 >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        //    carry8 = s8 >> 21;
        //    s9 += carry8;
        //    s8 -= carry8 * ((uint64_t) 1L << 21);
        $carry8 = $s8 >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        //    carry9 = s9 >> 21;
        //    s10 += carry9;
        //    s9 -= carry9 * ((uint64_t) 1L << 21);
        $carry9 = $s9 >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        //    carry10 = s10 >> 21;
        //    s11 += carry10;
        //    s10 -= carry10 * ((uint64_t) 1L << 21);
        $carry10 = $s10 >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;
        //    carry11 = s11 >> 21;
        //    s12 += carry11;
        //    s11 -= carry11 * ((uint64_t) 1L << 21);
        $carry11 = $s11 >> 21;
        $s12 += $carry11;
        $s11 -= $carry11 << 21;

        //    s0 += s12 * 666643;
        //    s1 += s12 * 470296;
        //    s2 += s12 * 654183;
        //    s3 -= s12 * 997805;
        //    s4 += s12 * 136657;
        //    s5 -= s12 * 683901;
        $s0 += self::mul($s12, 666643, 20);
        $s1 += self::mul($s12, 470296, 19);
        $s2 += self::mul($s12, 654183, 20);
        $s3 -= self::mul($s12, 997805, 20);
        $s4 += self::mul($s12, 136657, 18);
        $s5 -= self::mul($s12, 683901, 20);

        //    carry0 = s0 >> 21;
        //    s1 += carry0;
        //    s0 -= carry0 * ((uint64_t) 1L << 21);
        $carry0 = $s0 >> 21;
        $s1 += $carry0;
        $s0 -= $carry0 << 21;
        //    carry1 = s1 >> 21;
        //    s2 += carry1;
        //    s1 -= carry1 * ((uint64_t) 1L << 21);
        $carry1 = $s1 >> 21;
        $s2 += $carry1;
        $s1 -= $carry1 << 21;
        //    carry2 = s2 >> 21;
        //    s3 += carry2;
        //    s2 -= carry2 * ((uint64_t) 1L << 21);
        $carry2 = $s2 >> 21;
        $s3 += $carry2;
        $s2 -= $carry2 << 21;
        //    carry3 = s3 >> 21;
        //    s4 += carry3;
        //    s3 -= carry3 * ((uint64_t) 1L << 21);
        $carry3 = $s3 >> 21;
        $s4 += $carry3;
        $s3 -= $carry3 << 21;
        //    carry4 = s4 >> 21;
        //    s5 += carry4;
        //    s4 -= carry4 * ((uint64_t) 1L << 21);
        $carry4 = $s4 >> 21;
        $s5 += $carry4;
        $s4 -= $carry4 << 21;
        //    carry5 = s5 >> 21;
        //    s6 += carry5;
        //    s5 -= carry5 * ((uint64_t) 1L << 21);
        $carry5 = $s5 >> 21;
        $s6 += $carry5;
        $s5 -= $carry5 << 21;
        //    carry6 = s6 >> 21;
        //    s7 += carry6;
        //    s6 -= carry6 * ((uint64_t) 1L << 21);
        $carry6 = $s6 >> 21;
        $s7 += $carry6;
        $s6 -= $carry6 << 21;
        //    carry7 = s7 >> 21;
        //    s8 += carry7;
        //    s7 -= carry7 * ((uint64_t) 1L << 21);
        $carry7 = $s7 >> 21;
        $s8 += $carry7;
        $s7 -= $carry7 << 21;
        //    carry8 = s8 >> 21;
        //    s9 += carry8;
        //    s8 -= carry8 * ((uint64_t) 1L << 21);
        $carry8 = $s8 >> 21;
        $s9 += $carry8;
        $s8 -= $carry8 << 21;
        //    carry9 = s9 >> 21;
        //    s10 += carry9;
        //    s9 -= carry9 * ((uint64_t) 1L << 21);
        $carry9 = $s9 >> 21;
        $s10 += $carry9;
        $s9 -= $carry9 << 21;
        //    carry10 = s10 >> 21;
        //    s11 += carry10;
        //    s10 -= carry10 * ((uint64_t) 1L << 21);
        $carry10 = $s10 >> 21;
        $s11 += $carry10;
        $s10 -= $carry10 << 21;

        $s = array_fill(0, 32, 0);
        // s[0]  = s0 >> 0;
        $s[0]  = $s0 >> 0;
        // s[1]  = s0 >> 8;
        $s[1]  = $s0 >> 8;
        // s[2]  = (s0 >> 16) | (s1 * ((uint64_t) 1 << 5));
        $s[2]  = ($s0 >> 16) | ($s1 << 5);
        // s[3]  = s1 >> 3;
        $s[3]  = $s1 >> 3;
        // s[4]  = s1 >> 11;
        $s[4]  = $s1 >> 11;
        // s[5]  = (s1 >> 19) | (s2 * ((uint64_t) 1 << 2));
        $s[5]  = ($s1 >> 19) | ($s2 << 2);
        // s[6]  = s2 >> 6;
        $s[6]  = $s2 >> 6;
        // s[7]  = (s2 >> 14) | (s3 * ((uint64_t) 1 << 7));
        $s[7]  = ($s2 >> 14) | ($s3 << 7);
        // s[8]  = s3 >> 1;
        $s[8]  = $s3 >> 1;
        // s[9]  = s3 >> 9;
        $s[9]  = $s3 >> 9;
        // s[10] = (s3 >> 17) | (s4 * ((uint64_t) 1 << 4));
        $s[10] = ($s3 >> 17) | ($s4 << 4);
        // s[11] = s4 >> 4;
        $s[11] = $s4 >> 4;
        // s[12] = s4 >> 12;
        $s[12] = $s4 >> 12;
        // s[13] = (s4 >> 20) | (s5 * ((uint64_t) 1 << 1));
        $s[13] = ($s4 >> 20) | ($s5 << 1);
        // s[14] = s5 >> 7;
        $s[14] = $s5 >> 7;
        // s[15] = (s5 >> 15) | (s6 * ((uint64_t) 1 << 6));
        $s[15] = ($s5 >> 15) | ($s6 << 6);
        // s[16] = s6 >> 2;
        $s[16] = $s6 >> 2;
        // s[17] = s6 >> 10;
        $s[17] = $s6 >> 10;
        // s[18] = (s6 >> 18) | (s7 * ((uint64_t) 1 << 3));
        $s[18] = ($s6 >> 18) | ($s7 << 3);
        // s[19] = s7 >> 5;
        $s[19] = $s7 >> 5;
        // s[20] = s7 >> 13;
        $s[20] = $s7 >> 13;
        // s[21] = s8 >> 0;
        $s[21] = $s8 >> 0;
        // s[22] = s8 >> 8;
        $s[22] = $s8 >> 8;
        // s[23] = (s8 >> 16) | (s9 * ((uint64_t) 1 << 5));
        $s[23] = ($s8 >> 16) | ($s9 << 5);
        // s[24] = s9 >> 3;
        $s[24] = $s9 >> 3;
        // s[25] = s9 >> 11;
        $s[25] = $s9 >> 11;
        // s[26] = (s9 >> 19) | (s10 * ((uint64_t) 1 << 2));
        $s[26] = ($s9 >> 19) | ($s10 << 2);
        // s[27] = s10 >> 6;
        $s[27] = $s10 >> 6;
        // s[28] = (s10 >> 14) | (s11 * ((uint64_t) 1 << 7));
        $s[28] = ($s10 >> 14) | ($s11 << 7);
        // s[29] = s11 >> 1;
        $s[29] = $s11 >> 1;
        // s[30] = s11 >> 9;
        $s[30] = $s11 >> 9;
        // s[31] = s11 >> 17;
        $s[31] = $s11 >> 17;
        return self::intArrayToString($s);
    }

    /**
     * @param string $s
     * @return string
     */
    public static function sc25519_sq($s)
    {
        return self::sc25519_mul($s, $s);
    }

    /**
     * @param string $s
     * @param int $n
     * @param string $a
     * @return string
     */
    public static function sc25519_sqmul($s, $n, $a)
    {
        for ($i = 0; $i < $n; ++$i) {
            $s = self::sc25519_sq($s);
        }
        return self::sc25519_mul($s, $a);
    }

    /**
     * @param string $s
     * @return string
     */
    public static function sc25519_invert($s)
    {
        $_10 = self::sc25519_sq($s);
        $_11 = self::sc25519_mul($s, $_10);
        $_100 = self::sc25519_mul($s, $_11);
        $_1000 = self::sc25519_sq($_100);
        $_1010 = self::sc25519_mul($_10, $_1000);
        $_1011 = self::sc25519_mul($s, $_1010);
        $_10000 = self::sc25519_sq($_1000);
        $_10110 = self::sc25519_sq($_1011);
        $_100000 = self::sc25519_mul($_1010, $_10110);
        $_100110 = self::sc25519_mul($_10000, $_10110);
        $_1000000 = self::sc25519_sq($_100000);
        $_1010000 = self::sc25519_mul($_10000, $_1000000);
        $_1010011 = self::sc25519_mul($_11, $_1010000);
        $_1100011 = self::sc25519_mul($_10000, $_1010011);
        $_1100111 = self::sc25519_mul($_100, $_1100011);
        $_1101011 = self::sc25519_mul($_100, $_1100111);
        $_10010011 = self::sc25519_mul($_1000000, $_1010011);
        $_10010111 = self::sc25519_mul($_100, $_10010011);
        $_10111101 = self::sc25519_mul($_100110, $_10010111);
        $_11010011 = self::sc25519_mul($_10110, $_10111101);
        $_11100111 = self::sc25519_mul($_1010000, $_10010111);
        $_11101011 = self::sc25519_mul($_100, $_11100111);
        $_11110101 = self::sc25519_mul($_1010, $_11101011);

        $recip = self::sc25519_mul($_1011, $_11110101);
        $recip = self::sc25519_sqmul($recip, 126, $_1010011);
        $recip = self::sc25519_sqmul($recip, 9, $_10);
        $recip = self::sc25519_mul($recip, $_11110101);
        $recip = self::sc25519_sqmul($recip, 7, $_1100111);
        $recip = self::sc25519_sqmul($recip, 9, $_11110101);
        $recip = self::sc25519_sqmul($recip, 11, $_10111101);
        $recip = self::sc25519_sqmul($recip, 8, $_11100111);
        $recip = self::sc25519_sqmul($recip, 9, $_1101011);
        $recip = self::sc25519_sqmul($recip, 6, $_1011);
        $recip = self::sc25519_sqmul($recip, 14, $_10010011);
        $recip = self::sc25519_sqmul($recip, 10, $_1100011);
        $recip = self::sc25519_sqmul($recip, 9, $_10010111);
        $recip = self::sc25519_sqmul($recip, 10, $_11110101);
        $recip = self::sc25519_sqmul($recip, 8, $_11010011);
        return self::sc25519_sqmul($recip, 8, $_11101011);
    }

    /**
     * @param string $s
     * @return string
     */
    public static function clamp($s)
    {
        $s_ = self::stringToIntArray($s);
        $s_[0] &= 248;
        $s_[31] |= 64;
        $s_[31] &= 128;
        return self::intArrayToString($s_);
    }

    /**
     * Ensure limbs are less than 28 bits long to prevent float promotion.
     *
     * This uses a constant-time conditional swap under the hood.
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_normalize(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $x = (PHP_INT_SIZE << 3) - 1; // 31 or 63

        $g = self::fe_copy($f);
        for ($i = 0; $i < 10; ++$i) {
            $mask = -(($g[$i] >> $x) & 1);

            /*
             * Get two candidate normalized values for $g[$i], depending on the sign of $g[$i]:
             */
            $a = $g[$i] & 0x7ffffff;
            $b = -((-$g[$i]) & 0x7ffffff);

            /*
             * Return the appropriate candidate value, based on the sign of the original input:
             *
             * The following is equivalent to this ternary:
             *
             * $g[$i] = (($g[$i] >> $x) & 1) ? $a : $b;
             *
             * Except what's written doesn't contain timing leaks.
             */
            $g[$i] = ($a ^ (($a ^ $b) & $mask));
        }
        return $g;
    }
}
