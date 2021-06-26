<?php

if (class_exists('ParagonIE_Sodium_Core32_Curve25519', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core32_Curve25519
 *
 * Implements Curve25519 core functions
 *
 * Based on the ref10 curve25519 code provided by libsodium
 *
 * @ref https://github.com/jedisct1/libsodium/blob/master/src/libsodium/crypto_core/curve25519/ref10/curve25519_ref10.c
 */
abstract class ParagonIE_Sodium_Core32_Curve25519 extends ParagonIE_Sodium_Core32_Curve25519_H
{
    /**
     * Get a field element of size 10 with a value of 0
     *
     * @internal You should not use this directly from another application
     *
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_0()
    {
        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
            array(
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32()
            )
        );
    }

    /**
     * Get a field element of size 10 with a value of 1
     *
     * @internal You should not use this directly from another application
     *
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_1()
    {
        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
            array(
                ParagonIE_Sodium_Core32_Int32::fromInt(1),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32(),
                new ParagonIE_Sodium_Core32_Int32()
            )
        );
    }

    /**
     * Add two field elements.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $g
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_add(
        ParagonIE_Sodium_Core32_Curve25519_Fe $f,
        ParagonIE_Sodium_Core32_Curve25519_Fe $g
    ) {
        $arr = array();
        for ($i = 0; $i < 10; ++$i) {
            $arr[$i] = $f[$i]->addInt32($g[$i]);
        }
        /** @var array<int, ParagonIE_Sodium_Core32_Int32> $arr */
        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray($arr);
    }

    /**
     * Constant-time conditional move.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $g
     * @param int $b
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_cmov(
        ParagonIE_Sodium_Core32_Curve25519_Fe $f,
        ParagonIE_Sodium_Core32_Curve25519_Fe $g,
        $b = 0
    ) {
        /** @var array<int, ParagonIE_Sodium_Core32_Int32> $h */
        $h = array();
        for ($i = 0; $i < 10; ++$i) {
            if (!($f[$i] instanceof ParagonIE_Sodium_Core32_Int32)) {
                throw new TypeError('Expected Int32');
            }
            if (!($g[$i] instanceof ParagonIE_Sodium_Core32_Int32)) {
                throw new TypeError('Expected Int32');
            }
            $h[$i] = $f[$i]->xorInt32(
                $f[$i]->xorInt32($g[$i])->mask($b)
            );
        }
        /** @var array<int, ParagonIE_Sodium_Core32_Int32> $h */
        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray($h);
    }

    /**
     * Create a copy of a field element.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     */
    public static function fe_copy(ParagonIE_Sodium_Core32_Curve25519_Fe $f)
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
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws RangeException
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_frombytes($s)
    {
        if (self::strlen($s) !== 32) {
            throw new RangeException('Expected a 32-byte string.');
        }
        /** @var ParagonIE_Sodium_Core32_Int32 $h0 */
        $h0 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_4($s)
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h1 */
        $h1 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_3(self::substr($s, 4, 3)) << 6
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h2 */
        $h2 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_3(self::substr($s, 7, 3)) << 5
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h3 */
        $h3 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_3(self::substr($s, 10, 3)) << 3
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h4 */
        $h4 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_3(self::substr($s, 13, 3)) << 2
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h5 */
        $h5 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_4(self::substr($s, 16, 4))
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h6 */
        $h6 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_3(self::substr($s, 20, 3)) << 7
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h7 */
        $h7 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_3(self::substr($s, 23, 3)) << 5
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h8 */
        $h8 = ParagonIE_Sodium_Core32_Int32::fromInt(
            self::load_3(self::substr($s, 26, 3)) << 4
        );
        /** @var ParagonIE_Sodium_Core32_Int32 $h9 */
        $h9 = ParagonIE_Sodium_Core32_Int32::fromInt(
            (self::load_3(self::substr($s, 29, 3)) & 8388607) << 2
        );

        $carry9 = $h9->addInt(1 << 24)->shiftRight(25);
        $h0 = $h0->addInt32($carry9->mulInt(19, 5));
        $h9 = $h9->subInt32($carry9->shiftLeft(25));

        $carry1 = $h1->addInt(1 << 24)->shiftRight(25);
        $h2 = $h2->addInt32($carry1);
        $h1 = $h1->subInt32($carry1->shiftLeft(25));

        $carry3 = $h3->addInt(1 << 24)->shiftRight(25);
        $h4 = $h4->addInt32($carry3);
        $h3 = $h3->subInt32($carry3->shiftLeft(25));

        $carry5 = $h5->addInt(1 << 24)->shiftRight(25);
        $h6 = $h6->addInt32($carry5);
        $h5 = $h5->subInt32($carry5->shiftLeft(25));

        $carry7 = $h7->addInt(1 << 24)->shiftRight(25);
        $h8 = $h8->addInt32($carry7);
        $h7 = $h7->subInt32($carry7->shiftLeft(25));

        $carry0 = $h0->addInt(1 << 25)->shiftRight(26);
        $h1 = $h1->addInt32($carry0);
        $h0 = $h0->subInt32($carry0->shiftLeft(26));

        $carry2 = $h2->addInt(1 << 25)->shiftRight(26);
        $h3 = $h3->addInt32($carry2);
        $h2 = $h2->subInt32($carry2->shiftLeft(26));

        $carry4 = $h4->addInt(1 << 25)->shiftRight(26);
        $h5 = $h5->addInt32($carry4);
        $h4 = $h4->subInt32($carry4->shiftLeft(26));

        $carry6 = $h6->addInt(1 << 25)->shiftRight(26);
        $h7 = $h7->addInt32($carry6);
        $h6 = $h6->subInt32($carry6->shiftLeft(26));

        $carry8 = $h8->addInt(1 << 25)->shiftRight(26);
        $h9 = $h9->addInt32($carry8);
        $h8 = $h8->subInt32($carry8->shiftLeft(26));

        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
            array($h0, $h1, $h2,$h3, $h4, $h5, $h6, $h7, $h8, $h9)
        );
    }

    /**
     * Convert a field element to a byte string.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $h
     * @return string
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_tobytes(ParagonIE_Sodium_Core32_Curve25519_Fe $h)
    {
        /**
         * @var ParagonIE_Sodium_Core32_Int64[] $f
         * @var ParagonIE_Sodium_Core32_Int64 $q
         */
        $f = array();

        for ($i = 0; $i < 10; ++$i) {
            $f[$i] = $h[$i]->toInt64();
        }

        $q = $f[9]->mulInt(19, 5)->addInt(1 << 14)->shiftRight(25)
            ->addInt64($f[0])->shiftRight(26)
            ->addInt64($f[1])->shiftRight(25)
            ->addInt64($f[2])->shiftRight(26)
            ->addInt64($f[3])->shiftRight(25)
            ->addInt64($f[4])->shiftRight(26)
            ->addInt64($f[5])->shiftRight(25)
            ->addInt64($f[6])->shiftRight(26)
            ->addInt64($f[7])->shiftRight(25)
            ->addInt64($f[8])->shiftRight(26)
            ->addInt64($f[9])->shiftRight(25);

        $f[0] = $f[0]->addInt64($q->mulInt(19, 5));

        $carry0 = $f[0]->shiftRight(26);
        $f[1] = $f[1]->addInt64($carry0);
        $f[0] = $f[0]->subInt64($carry0->shiftLeft(26));

        $carry1 = $f[1]->shiftRight(25);
        $f[2] = $f[2]->addInt64($carry1);
        $f[1] = $f[1]->subInt64($carry1->shiftLeft(25));

        $carry2 = $f[2]->shiftRight(26);
        $f[3] = $f[3]->addInt64($carry2);
        $f[2] = $f[2]->subInt64($carry2->shiftLeft(26));

        $carry3 = $f[3]->shiftRight(25);
        $f[4] = $f[4]->addInt64($carry3);
        $f[3] = $f[3]->subInt64($carry3->shiftLeft(25));

        $carry4 = $f[4]->shiftRight(26);
        $f[5] = $f[5]->addInt64($carry4);
        $f[4] = $f[4]->subInt64($carry4->shiftLeft(26));

        $carry5 = $f[5]->shiftRight(25);
        $f[6] = $f[6]->addInt64($carry5);
        $f[5] = $f[5]->subInt64($carry5->shiftLeft(25));

        $carry6 = $f[6]->shiftRight(26);
        $f[7] = $f[7]->addInt64($carry6);
        $f[6] = $f[6]->subInt64($carry6->shiftLeft(26));

        $carry7 = $f[7]->shiftRight(25);
        $f[8] = $f[8]->addInt64($carry7);
        $f[7] = $f[7]->subInt64($carry7->shiftLeft(25));

        $carry8 = $f[8]->shiftRight(26);
        $f[9] = $f[9]->addInt64($carry8);
        $f[8] = $f[8]->subInt64($carry8->shiftLeft(26));

        $carry9 = $f[9]->shiftRight(25);
        $f[9] = $f[9]->subInt64($carry9->shiftLeft(25));

        /** @var int $h0 */
        $h0 = $f[0]->toInt32()->toInt();
        /** @var int $h1 */
        $h1 = $f[1]->toInt32()->toInt();
        /** @var int $h2 */
        $h2 = $f[2]->toInt32()->toInt();
        /** @var int $h3 */
        $h3 = $f[3]->toInt32()->toInt();
        /** @var int $h4 */
        $h4 = $f[4]->toInt32()->toInt();
        /** @var int $h5 */
        $h5 = $f[5]->toInt32()->toInt();
        /** @var int $h6 */
        $h6 = $f[6]->toInt32()->toInt();
        /** @var int $h7 */
        $h7 = $f[7]->toInt32()->toInt();
        /** @var int $h8 */
        $h8 = $f[8]->toInt32()->toInt();
        /** @var int $h9 */
        $h9 = $f[9]->toInt32()->toInt();

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
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @return int
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_isnegative(ParagonIE_Sodium_Core32_Curve25519_Fe $f)
    {
        $str = self::fe_tobytes($f);
        return (int) (self::chrToInt($str[0]) & 1);
    }

    /**
     * Returns 0 if this field element results in all NUL bytes.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_isnonzero(ParagonIE_Sodium_Core32_Curve25519_Fe $f)
    {
        static $zero;
        if ($zero === null) {
            $zero = str_repeat("\x00", 32);
        }
        /** @var string $str */
        $str = self::fe_tobytes($f);
        /** @var string $zero */
        return !self::verify_32($str, $zero);
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $g
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_mul(
        ParagonIE_Sodium_Core32_Curve25519_Fe $f,
        ParagonIE_Sodium_Core32_Curve25519_Fe $g
    ) {
        /**
         * @var ParagonIE_Sodium_Core32_Int32[] $f
         * @var ParagonIE_Sodium_Core32_Int32[] $g
         * @var ParagonIE_Sodium_Core32_Int64 $f0
         * @var ParagonIE_Sodium_Core32_Int64 $f1
         * @var ParagonIE_Sodium_Core32_Int64 $f2
         * @var ParagonIE_Sodium_Core32_Int64 $f3
         * @var ParagonIE_Sodium_Core32_Int64 $f4
         * @var ParagonIE_Sodium_Core32_Int64 $f5
         * @var ParagonIE_Sodium_Core32_Int64 $f6
         * @var ParagonIE_Sodium_Core32_Int64 $f7
         * @var ParagonIE_Sodium_Core32_Int64 $f8
         * @var ParagonIE_Sodium_Core32_Int64 $f9
         * @var ParagonIE_Sodium_Core32_Int64 $g0
         * @var ParagonIE_Sodium_Core32_Int64 $g1
         * @var ParagonIE_Sodium_Core32_Int64 $g2
         * @var ParagonIE_Sodium_Core32_Int64 $g3
         * @var ParagonIE_Sodium_Core32_Int64 $g4
         * @var ParagonIE_Sodium_Core32_Int64 $g5
         * @var ParagonIE_Sodium_Core32_Int64 $g6
         * @var ParagonIE_Sodium_Core32_Int64 $g7
         * @var ParagonIE_Sodium_Core32_Int64 $g8
         * @var ParagonIE_Sodium_Core32_Int64 $g9
         */
        $f0 = $f[0]->toInt64();
        $f1 = $f[1]->toInt64();
        $f2 = $f[2]->toInt64();
        $f3 = $f[3]->toInt64();
        $f4 = $f[4]->toInt64();
        $f5 = $f[5]->toInt64();
        $f6 = $f[6]->toInt64();
        $f7 = $f[7]->toInt64();
        $f8 = $f[8]->toInt64();
        $f9 = $f[9]->toInt64();
        $g0 = $g[0]->toInt64();
        $g1 = $g[1]->toInt64();
        $g2 = $g[2]->toInt64();
        $g3 = $g[3]->toInt64();
        $g4 = $g[4]->toInt64();
        $g5 = $g[5]->toInt64();
        $g6 = $g[6]->toInt64();
        $g7 = $g[7]->toInt64();
        $g8 = $g[8]->toInt64();
        $g9 = $g[9]->toInt64();
        $g1_19 = $g1->mulInt(19, 5); /* 2^4 <= 19 <= 2^5, but we only want 5 bits */
        $g2_19 = $g2->mulInt(19, 5);
        $g3_19 = $g3->mulInt(19, 5);
        $g4_19 = $g4->mulInt(19, 5);
        $g5_19 = $g5->mulInt(19, 5);
        $g6_19 = $g6->mulInt(19, 5);
        $g7_19 = $g7->mulInt(19, 5);
        $g8_19 = $g8->mulInt(19, 5);
        $g9_19 = $g9->mulInt(19, 5);
        /** @var ParagonIE_Sodium_Core32_Int64 $f1_2 */
        $f1_2 = $f1->shiftLeft(1);
        /** @var ParagonIE_Sodium_Core32_Int64 $f3_2 */
        $f3_2 = $f3->shiftLeft(1);
        /** @var ParagonIE_Sodium_Core32_Int64 $f5_2 */
        $f5_2 = $f5->shiftLeft(1);
        /** @var ParagonIE_Sodium_Core32_Int64 $f7_2 */
        $f7_2 = $f7->shiftLeft(1);
        /** @var ParagonIE_Sodium_Core32_Int64 $f9_2 */
        $f9_2 = $f9->shiftLeft(1);
        $f0g0    = $f0->mulInt64($g0, 27);
        $f0g1    = $f0->mulInt64($g1, 27);
        $f0g2    = $f0->mulInt64($g2, 27);
        $f0g3    = $f0->mulInt64($g3, 27);
        $f0g4    = $f0->mulInt64($g4, 27);
        $f0g5    = $f0->mulInt64($g5, 27);
        $f0g6    = $f0->mulInt64($g6, 27);
        $f0g7    = $f0->mulInt64($g7, 27);
        $f0g8    = $f0->mulInt64($g8, 27);
        $f0g9    = $f0->mulInt64($g9, 27);
        $f1g0    = $f1->mulInt64($g0, 27);
        $f1g1_2  = $f1_2->mulInt64($g1, 27);
        $f1g2    = $f1->mulInt64($g2, 27);
        $f1g3_2  = $f1_2->mulInt64($g3, 27);
        $f1g4    = $f1->mulInt64($g4, 30);
        $f1g5_2  = $f1_2->mulInt64($g5, 30);
        $f1g6    = $f1->mulInt64($g6, 30);
        $f1g7_2  = $f1_2->mulInt64($g7, 30);
        $f1g8    = $f1->mulInt64($g8, 30);
        $f1g9_38 = $g9_19->mulInt64($f1_2, 30);
        $f2g0    = $f2->mulInt64($g0, 30);
        $f2g1    = $f2->mulInt64($g1, 29);
        $f2g2    = $f2->mulInt64($g2, 30);
        $f2g3    = $f2->mulInt64($g3, 29);
        $f2g4    = $f2->mulInt64($g4, 30);
        $f2g5    = $f2->mulInt64($g5, 29);
        $f2g6    = $f2->mulInt64($g6, 30);
        $f2g7    = $f2->mulInt64($g7, 29);
        $f2g8_19 = $g8_19->mulInt64($f2, 30);
        $f2g9_19 = $g9_19->mulInt64($f2, 30);
        $f3g0    = $f3->mulInt64($g0, 30);
        $f3g1_2  = $f3_2->mulInt64($g1, 30);
        $f3g2    = $f3->mulInt64($g2, 30);
        $f3g3_2  = $f3_2->mulInt64($g3, 30);
        $f3g4    = $f3->mulInt64($g4, 30);
        $f3g5_2  = $f3_2->mulInt64($g5, 30);
        $f3g6    = $f3->mulInt64($g6, 30);
        $f3g7_38 = $g7_19->mulInt64($f3_2, 30);
        $f3g8_19 = $g8_19->mulInt64($f3, 30);
        $f3g9_38 = $g9_19->mulInt64($f3_2, 30);
        $f4g0    = $f4->mulInt64($g0, 30);
        $f4g1    = $f4->mulInt64($g1, 30);
        $f4g2    = $f4->mulInt64($g2, 30);
        $f4g3    = $f4->mulInt64($g3, 30);
        $f4g4    = $f4->mulInt64($g4, 30);
        $f4g5    = $f4->mulInt64($g5, 30);
        $f4g6_19 = $g6_19->mulInt64($f4, 30);
        $f4g7_19 = $g7_19->mulInt64($f4, 30);
        $f4g8_19 = $g8_19->mulInt64($f4, 30);
        $f4g9_19 = $g9_19->mulInt64($f4, 30);
        $f5g0    = $f5->mulInt64($g0, 30);
        $f5g1_2  = $f5_2->mulInt64($g1, 30);
        $f5g2    = $f5->mulInt64($g2, 30);
        $f5g3_2  = $f5_2->mulInt64($g3, 30);
        $f5g4    = $f5->mulInt64($g4, 30);
        $f5g5_38 = $g5_19->mulInt64($f5_2, 30);
        $f5g6_19 = $g6_19->mulInt64($f5, 30);
        $f5g7_38 = $g7_19->mulInt64($f5_2, 30);
        $f5g8_19 = $g8_19->mulInt64($f5, 30);
        $f5g9_38 = $g9_19->mulInt64($f5_2, 30);
        $f6g0    = $f6->mulInt64($g0, 30);
        $f6g1    = $f6->mulInt64($g1, 30);
        $f6g2    = $f6->mulInt64($g2, 30);
        $f6g3    = $f6->mulInt64($g3, 30);
        $f6g4_19 = $g4_19->mulInt64($f6, 30);
        $f6g5_19 = $g5_19->mulInt64($f6, 30);
        $f6g6_19 = $g6_19->mulInt64($f6, 30);
        $f6g7_19 = $g7_19->mulInt64($f6, 30);
        $f6g8_19 = $g8_19->mulInt64($f6, 30);
        $f6g9_19 = $g9_19->mulInt64($f6, 30);
        $f7g0    = $f7->mulInt64($g0, 30);
        $f7g1_2  = $g1->mulInt64($f7_2, 30);
        $f7g2    = $f7->mulInt64($g2, 30);
        $f7g3_38 = $g3_19->mulInt64($f7_2, 30);
        $f7g4_19 = $g4_19->mulInt64($f7, 30);
        $f7g5_38 = $g5_19->mulInt64($f7_2, 30);
        $f7g6_19 = $g6_19->mulInt64($f7, 30);
        $f7g7_38 = $g7_19->mulInt64($f7_2, 30);
        $f7g8_19 = $g8_19->mulInt64($f7, 30);
        $f7g9_38 = $g9_19->mulInt64($f7_2, 30);
        $f8g0    = $f8->mulInt64($g0, 30);
        $f8g1    = $f8->mulInt64($g1, 29);
        $f8g2_19 = $g2_19->mulInt64($f8, 30);
        $f8g3_19 = $g3_19->mulInt64($f8, 30);
        $f8g4_19 = $g4_19->mulInt64($f8, 30);
        $f8g5_19 = $g5_19->mulInt64($f8, 30);
        $f8g6_19 = $g6_19->mulInt64($f8, 30);
        $f8g7_19 = $g7_19->mulInt64($f8, 30);
        $f8g8_19 = $g8_19->mulInt64($f8, 30);
        $f8g9_19 = $g9_19->mulInt64($f8, 30);
        $f9g0    = $f9->mulInt64($g0, 30);
        $f9g1_38 = $g1_19->mulInt64($f9_2, 30);
        $f9g2_19 = $g2_19->mulInt64($f9, 30);
        $f9g3_38 = $g3_19->mulInt64($f9_2, 30);
        $f9g4_19 = $g4_19->mulInt64($f9, 30);
        $f9g5_38 = $g5_19->mulInt64($f9_2, 30);
        $f9g6_19 = $g6_19->mulInt64($f9, 30);
        $f9g7_38 = $g7_19->mulInt64($f9_2, 30);
        $f9g8_19 = $g8_19->mulInt64($f9, 30);
        $f9g9_38 = $g9_19->mulInt64($f9_2, 30);

        // $h0 = $f0g0 + $f1g9_38 + $f2g8_19 + $f3g7_38 + $f4g6_19 + $f5g5_38 + $f6g4_19 + $f7g3_38 + $f8g2_19 + $f9g1_38;
        $h0 = $f0g0->addInt64($f1g9_38)->addInt64($f2g8_19)->addInt64($f3g7_38)
            ->addInt64($f4g6_19)->addInt64($f5g5_38)->addInt64($f6g4_19)
            ->addInt64($f7g3_38)->addInt64($f8g2_19)->addInt64($f9g1_38);

        // $h1 = $f0g1 + $f1g0    + $f2g9_19 + $f3g8_19 + $f4g7_19 + $f5g6_19 + $f6g5_19 + $f7g4_19 + $f8g3_19 + $f9g2_19;
        $h1 = $f0g1->addInt64($f1g0)->addInt64($f2g9_19)->addInt64($f3g8_19)
            ->addInt64($f4g7_19)->addInt64($f5g6_19)->addInt64($f6g5_19)
            ->addInt64($f7g4_19)->addInt64($f8g3_19)->addInt64($f9g2_19);

        // $h2 = $f0g2 + $f1g1_2  + $f2g0    + $f3g9_38 + $f4g8_19 + $f5g7_38 + $f6g6_19 + $f7g5_38 + $f8g4_19 + $f9g3_38;
        $h2 = $f0g2->addInt64($f1g1_2)->addInt64($f2g0)->addInt64($f3g9_38)
            ->addInt64($f4g8_19)->addInt64($f5g7_38)->addInt64($f6g6_19)
            ->addInt64($f7g5_38)->addInt64($f8g4_19)->addInt64($f9g3_38);

        // $h3 = $f0g3 + $f1g2    + $f2g1    + $f3g0    + $f4g9_19 + $f5g8_19 + $f6g7_19 + $f7g6_19 + $f8g5_19 + $f9g4_19;
        $h3 = $f0g3->addInt64($f1g2)->addInt64($f2g1)->addInt64($f3g0)
            ->addInt64($f4g9_19)->addInt64($f5g8_19)->addInt64($f6g7_19)
            ->addInt64($f7g6_19)->addInt64($f8g5_19)->addInt64($f9g4_19);

        // $h4 = $f0g4 + $f1g3_2  + $f2g2    + $f3g1_2  + $f4g0    + $f5g9_38 + $f6g8_19 + $f7g7_38 + $f8g6_19 + $f9g5_38;
        $h4 = $f0g4->addInt64($f1g3_2)->addInt64($f2g2)->addInt64($f3g1_2)
            ->addInt64($f4g0)->addInt64($f5g9_38)->addInt64($f6g8_19)
            ->addInt64($f7g7_38)->addInt64($f8g6_19)->addInt64($f9g5_38);

        // $h5 = $f0g5 + $f1g4    + $f2g3    + $f3g2    + $f4g1    + $f5g0    + $f6g9_19 + $f7g8_19 + $f8g7_19 + $f9g6_19;
        $h5 = $f0g5->addInt64($f1g4)->addInt64($f2g3)->addInt64($f3g2)
            ->addInt64($f4g1)->addInt64($f5g0)->addInt64($f6g9_19)
            ->addInt64($f7g8_19)->addInt64($f8g7_19)->addInt64($f9g6_19);

        // $h6 = $f0g6 + $f1g5_2  + $f2g4    + $f3g3_2  + $f4g2    + $f5g1_2  + $f6g0    + $f7g9_38 + $f8g8_19 + $f9g7_38;
        $h6 = $f0g6->addInt64($f1g5_2)->addInt64($f2g4)->addInt64($f3g3_2)
            ->addInt64($f4g2)->addInt64($f5g1_2)->addInt64($f6g0)
            ->addInt64($f7g9_38)->addInt64($f8g8_19)->addInt64($f9g7_38);

        // $h7 = $f0g7 + $f1g6    + $f2g5    + $f3g4    + $f4g3    + $f5g2    + $f6g1    + $f7g0    + $f8g9_19 + $f9g8_19;
        $h7 = $f0g7->addInt64($f1g6)->addInt64($f2g5)->addInt64($f3g4)
            ->addInt64($f4g3)->addInt64($f5g2)->addInt64($f6g1)
            ->addInt64($f7g0)->addInt64($f8g9_19)->addInt64($f9g8_19);

        // $h8 = $f0g8 + $f1g7_2  + $f2g6    + $f3g5_2  + $f4g4    + $f5g3_2  + $f6g2    + $f7g1_2  + $f8g0    + $f9g9_38;
        $h8 = $f0g8->addInt64($f1g7_2)->addInt64($f2g6)->addInt64($f3g5_2)
            ->addInt64($f4g4)->addInt64($f5g3_2)->addInt64($f6g2)
            ->addInt64($f7g1_2)->addInt64($f8g0)->addInt64($f9g9_38);

        // $h9 = $f0g9 + $f1g8    + $f2g7    + $f3g6    + $f4g5    + $f5g4    + $f6g3    + $f7g2    + $f8g1    + $f9g0   ;
        $h9 = $f0g9->addInt64($f1g8)->addInt64($f2g7)->addInt64($f3g6)
            ->addInt64($f4g5)->addInt64($f5g4)->addInt64($f6g3)
            ->addInt64($f7g2)->addInt64($f8g1)->addInt64($f9g0);

        /**
         * @var ParagonIE_Sodium_Core32_Int64 $h0
         * @var ParagonIE_Sodium_Core32_Int64 $h1
         * @var ParagonIE_Sodium_Core32_Int64 $h2
         * @var ParagonIE_Sodium_Core32_Int64 $h3
         * @var ParagonIE_Sodium_Core32_Int64 $h4
         * @var ParagonIE_Sodium_Core32_Int64 $h5
         * @var ParagonIE_Sodium_Core32_Int64 $h6
         * @var ParagonIE_Sodium_Core32_Int64 $h7
         * @var ParagonIE_Sodium_Core32_Int64 $h8
         * @var ParagonIE_Sodium_Core32_Int64 $h9
         * @var ParagonIE_Sodium_Core32_Int64 $carry0
         * @var ParagonIE_Sodium_Core32_Int64 $carry1
         * @var ParagonIE_Sodium_Core32_Int64 $carry2
         * @var ParagonIE_Sodium_Core32_Int64 $carry3
         * @var ParagonIE_Sodium_Core32_Int64 $carry4
         * @var ParagonIE_Sodium_Core32_Int64 $carry5
         * @var ParagonIE_Sodium_Core32_Int64 $carry6
         * @var ParagonIE_Sodium_Core32_Int64 $carry7
         * @var ParagonIE_Sodium_Core32_Int64 $carry8
         * @var ParagonIE_Sodium_Core32_Int64 $carry9
         */
        $carry0 = $h0->addInt(1 << 25)->shiftRight(26);
        $h1 = $h1->addInt64($carry0);
        $h0 = $h0->subInt64($carry0->shiftLeft(26));
        $carry4 = $h4->addInt(1 << 25)->shiftRight(26);
        $h5 = $h5->addInt64($carry4);
        $h4 = $h4->subInt64($carry4->shiftLeft(26));

        $carry1 = $h1->addInt(1 << 24)->shiftRight(25);
        $h2 = $h2->addInt64($carry1);
        $h1 = $h1->subInt64($carry1->shiftLeft(25));
        $carry5 = $h5->addInt(1 << 24)->shiftRight(25);
        $h6 = $h6->addInt64($carry5);
        $h5 = $h5->subInt64($carry5->shiftLeft(25));

        $carry2 = $h2->addInt(1 << 25)->shiftRight(26);
        $h3 = $h3->addInt64($carry2);
        $h2 = $h2->subInt64($carry2->shiftLeft(26));
        $carry6 = $h6->addInt(1 << 25)->shiftRight(26);
        $h7 = $h7->addInt64($carry6);
        $h6 = $h6->subInt64($carry6->shiftLeft(26));

        $carry3 = $h3->addInt(1 << 24)->shiftRight(25);
        $h4 = $h4->addInt64($carry3);
        $h3 = $h3->subInt64($carry3->shiftLeft(25));
        $carry7 = $h7->addInt(1 << 24)->shiftRight(25);
        $h8 = $h8->addInt64($carry7);
        $h7 = $h7->subInt64($carry7->shiftLeft(25));

        $carry4 = $h4->addInt(1 << 25)->shiftRight(26);
        $h5 = $h5->addInt64($carry4);
        $h4 = $h4->subInt64($carry4->shiftLeft(26));
        $carry8 = $h8->addInt(1 << 25)->shiftRight(26);
        $h9 = $h9->addInt64($carry8);
        $h8 = $h8->subInt64($carry8->shiftLeft(26));

        $carry9 = $h9->addInt(1 << 24)->shiftRight(25);
        $h0 = $h0->addInt64($carry9->mulInt(19, 5));
        $h9 = $h9->subInt64($carry9->shiftLeft(25));

        $carry0 = $h0->addInt(1 << 25)->shiftRight(26);
        $h1 = $h1->addInt64($carry0);
        $h0 = $h0->subInt64($carry0->shiftLeft(26));

        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
            array(
                $h0->toInt32(),
                $h1->toInt32(),
                $h2->toInt32(),
                $h3->toInt32(),
                $h4->toInt32(),
                $h5->toInt32(),
                $h6->toInt32(),
                $h7->toInt32(),
                $h8->toInt32(),
                $h9->toInt32()
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_neg(ParagonIE_Sodium_Core32_Curve25519_Fe $f)
    {
        $h = new ParagonIE_Sodium_Core32_Curve25519_Fe();
        for ($i = 0; $i < 10; ++$i) {
            $h[$i] = $h[$i]->subInt32($f[$i]);
        }
        return $h;
    }

    /**
     * Square a field element
     *
     * h = f * f
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_sq(ParagonIE_Sodium_Core32_Curve25519_Fe $f)
    {
        /** @var ParagonIE_Sodium_Core32_Int64 $f0 */
        $f0 = $f[0]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f1 */
        $f1 = $f[1]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f2 */
        $f2 = $f[2]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f3 */
        $f3 = $f[3]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f4 */
        $f4 = $f[4]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f5 */
        $f5 = $f[5]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f6 */
        $f6 = $f[6]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f7 */
        $f7 = $f[7]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f8 */
        $f8 = $f[8]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f9 */
        $f9 = $f[9]->toInt64();

        /** @var ParagonIE_Sodium_Core32_Int64 $f0_2 */
        $f0_2 = $f0->shiftLeft(1);
        $f1_2 = $f1->shiftLeft(1);
        $f2_2 = $f2->shiftLeft(1);
        $f3_2 = $f3->shiftLeft(1);
        $f4_2 = $f4->shiftLeft(1);
        $f5_2 = $f5->shiftLeft(1);
        $f6_2 = $f6->shiftLeft(1);
        $f7_2 = $f7->shiftLeft(1);
        $f5_38 = $f5->mulInt(38, 6);
        $f6_19 = $f6->mulInt(19, 5);
        $f7_38 = $f7->mulInt(38, 6);
        $f8_19 = $f8->mulInt(19, 5);
        $f9_38 = $f9->mulInt(38, 6);
        /** @var ParagonIE_Sodium_Core32_Int64 $f0f0*/
        $f0f0    = $f0->mulInt64($f0, 28);
        $f0f1_2  = $f0_2->mulInt64($f1, 28);
        $f0f2_2 =  $f0_2->mulInt64($f2, 28);
        $f0f3_2 =  $f0_2->mulInt64($f3, 28);
        $f0f4_2 =  $f0_2->mulInt64($f4, 28);
        $f0f5_2 =  $f0_2->mulInt64($f5, 28);
        $f0f6_2 =  $f0_2->mulInt64($f6, 28);
        $f0f7_2 =  $f0_2->mulInt64($f7, 28);
        $f0f8_2 =  $f0_2->mulInt64($f8, 28);
        $f0f9_2 =  $f0_2->mulInt64($f9, 28);

        $f1f1_2 = $f1_2->mulInt64($f1, 28);
        $f1f2_2 = $f1_2->mulInt64($f2, 28);
        $f1f3_4 = $f1_2->mulInt64($f3_2, 28);
        $f1f4_2 = $f1_2->mulInt64($f4, 28);
        $f1f5_4 = $f1_2->mulInt64($f5_2, 30);
        $f1f6_2 = $f1_2->mulInt64($f6, 28);
        $f1f7_4 = $f1_2->mulInt64($f7_2, 28);
        $f1f8_2 = $f1_2->mulInt64($f8, 28);
        $f1f9_76 = $f9_38->mulInt64($f1_2, 30);

        $f2f2 = $f2->mulInt64($f2, 28);
        $f2f3_2 = $f2_2->mulInt64($f3, 28);
        $f2f4_2 = $f2_2->mulInt64($f4, 28);
        $f2f5_2 = $f2_2->mulInt64($f5, 28);
        $f2f6_2 = $f2_2->mulInt64($f6, 28);
        $f2f7_2 = $f2_2->mulInt64($f7, 28);
        $f2f8_38 = $f8_19->mulInt64($f2_2, 30);
        $f2f9_38 = $f9_38->mulInt64($f2, 30);

        $f3f3_2 = $f3_2->mulInt64($f3, 28);
        $f3f4_2 = $f3_2->mulInt64($f4, 28);
        $f3f5_4 = $f3_2->mulInt64($f5_2, 30);
        $f3f6_2 = $f3_2->mulInt64($f6, 28);
        $f3f7_76 = $f7_38->mulInt64($f3_2, 30);
        $f3f8_38 = $f8_19->mulInt64($f3_2, 30);
        $f3f9_76 = $f9_38->mulInt64($f3_2, 30);

        $f4f4 = $f4->mulInt64($f4, 28);
        $f4f5_2 = $f4_2->mulInt64($f5, 28);
        $f4f6_38 = $f6_19->mulInt64($f4_2, 30);
        $f4f7_38 = $f7_38->mulInt64($f4, 30);
        $f4f8_38 = $f8_19->mulInt64($f4_2, 30);
        $f4f9_38 = $f9_38->mulInt64($f4, 30);

        $f5f5_38 = $f5_38->mulInt64($f5, 30);
        $f5f6_38 = $f6_19->mulInt64($f5_2, 30);
        $f5f7_76 = $f7_38->mulInt64($f5_2, 30);
        $f5f8_38 = $f8_19->mulInt64($f5_2, 30);
        $f5f9_76 = $f9_38->mulInt64($f5_2, 30);

        $f6f6_19 = $f6_19->mulInt64($f6, 30);
        $f6f7_38 = $f7_38->mulInt64($f6, 30);
        $f6f8_38 = $f8_19->mulInt64($f6_2, 30);
        $f6f9_38 = $f9_38->mulInt64($f6, 30);

        $f7f7_38 = $f7_38->mulInt64($f7, 28);
        $f7f8_38 = $f8_19->mulInt64($f7_2, 30);
        $f7f9_76 = $f9_38->mulInt64($f7_2, 30);

        $f8f8_19 = $f8_19->mulInt64($f8, 30);
        $f8f9_38 = $f9_38->mulInt64($f8, 30);

        $f9f9_38 = $f9_38->mulInt64($f9, 28);

        $h0 = $f0f0->addInt64($f1f9_76)->addInt64($f2f8_38)->addInt64($f3f7_76)->addInt64($f4f6_38)->addInt64($f5f5_38);
        $h1 = $f0f1_2->addInt64($f2f9_38)->addInt64($f3f8_38)->addInt64($f4f7_38)->addInt64($f5f6_38);
        $h2 = $f0f2_2->addInt64($f1f1_2)->addInt64($f3f9_76)->addInt64($f4f8_38)->addInt64($f5f7_76)->addInt64($f6f6_19);
        $h3 = $f0f3_2->addInt64($f1f2_2)->addInt64($f4f9_38)->addInt64($f5f8_38)->addInt64($f6f7_38);
        $h4 = $f0f4_2->addInt64($f1f3_4)->addInt64($f2f2)->addInt64($f5f9_76)->addInt64($f6f8_38)->addInt64($f7f7_38);
        $h5 = $f0f5_2->addInt64($f1f4_2)->addInt64($f2f3_2)->addInt64($f6f9_38)->addInt64($f7f8_38);
        $h6 = $f0f6_2->addInt64($f1f5_4)->addInt64($f2f4_2)->addInt64($f3f3_2)->addInt64($f7f9_76)->addInt64($f8f8_19);
        $h7 = $f0f7_2->addInt64($f1f6_2)->addInt64($f2f5_2)->addInt64($f3f4_2)->addInt64($f8f9_38);
        $h8 = $f0f8_2->addInt64($f1f7_4)->addInt64($f2f6_2)->addInt64($f3f5_4)->addInt64($f4f4)->addInt64($f9f9_38);
        $h9 = $f0f9_2->addInt64($f1f8_2)->addInt64($f2f7_2)->addInt64($f3f6_2)->addInt64($f4f5_2);

        /**
         * @var ParagonIE_Sodium_Core32_Int64 $h0
         * @var ParagonIE_Sodium_Core32_Int64 $h1
         * @var ParagonIE_Sodium_Core32_Int64 $h2
         * @var ParagonIE_Sodium_Core32_Int64 $h3
         * @var ParagonIE_Sodium_Core32_Int64 $h4
         * @var ParagonIE_Sodium_Core32_Int64 $h5
         * @var ParagonIE_Sodium_Core32_Int64 $h6
         * @var ParagonIE_Sodium_Core32_Int64 $h7
         * @var ParagonIE_Sodium_Core32_Int64 $h8
         * @var ParagonIE_Sodium_Core32_Int64 $h9
         */

        $carry0 = $h0->addInt(1 << 25)->shiftRight(26);
        $h1 = $h1->addInt64($carry0);
        $h0 = $h0->subInt64($carry0->shiftLeft(26));

        $carry4 = $h4->addInt(1 << 25)->shiftRight(26);
        $h5 = $h5->addInt64($carry4);
        $h4 = $h4->subInt64($carry4->shiftLeft(26));

        $carry1 = $h1->addInt(1 << 24)->shiftRight(25);
        $h2 = $h2->addInt64($carry1);
        $h1 = $h1->subInt64($carry1->shiftLeft(25));

        $carry5 = $h5->addInt(1 << 24)->shiftRight(25);
        $h6 = $h6->addInt64($carry5);
        $h5 = $h5->subInt64($carry5->shiftLeft(25));

        $carry2 = $h2->addInt(1 << 25)->shiftRight(26);
        $h3 = $h3->addInt64($carry2);
        $h2 = $h2->subInt64($carry2->shiftLeft(26));

        $carry6 = $h6->addInt(1 << 25)->shiftRight(26);
        $h7 = $h7->addInt64($carry6);
        $h6 = $h6->subInt64($carry6->shiftLeft(26));

        $carry3 = $h3->addInt(1 << 24)->shiftRight(25);
        $h4 = $h4->addInt64($carry3);
        $h3 = $h3->subInt64($carry3->shiftLeft(25));

        $carry7 = $h7->addInt(1 << 24)->shiftRight(25);
        $h8 = $h8->addInt64($carry7);
        $h7 = $h7->subInt64($carry7->shiftLeft(25));

        $carry4 = $h4->addInt(1 << 25)->shiftRight(26);
        $h5 = $h5->addInt64($carry4);
        $h4 = $h4->subInt64($carry4->shiftLeft(26));

        $carry8 = $h8->addInt(1 << 25)->shiftRight(26);
        $h9 = $h9->addInt64($carry8);
        $h8 = $h8->subInt64($carry8->shiftLeft(26));

        $carry9 = $h9->addInt(1 << 24)->shiftRight(25);
        $h0 = $h0->addInt64($carry9->mulInt(19, 5));
        $h9 = $h9->subInt64($carry9->shiftLeft(25));

        $carry0 = $h0->addInt(1 << 25)->shiftRight(26);
        $h1 = $h1->addInt64($carry0);
        $h0 = $h0->subInt64($carry0->shiftLeft(26));

        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
            array(
                $h0->toInt32(),
                $h1->toInt32(),
                $h2->toInt32(),
                $h3->toInt32(),
                $h4->toInt32(),
                $h5->toInt32(),
                $h6->toInt32(),
                $h7->toInt32(),
                $h8->toInt32(),
                $h9->toInt32()
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_sq2(ParagonIE_Sodium_Core32_Curve25519_Fe $f)
    {
        /** @var ParagonIE_Sodium_Core32_Int64 $f0 */
        $f0 = $f[0]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f1 */
        $f1 = $f[1]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f2 */
        $f2 = $f[2]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f3 */
        $f3 = $f[3]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f4 */
        $f4 = $f[4]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f5 */
        $f5 = $f[5]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f6 */
        $f6 = $f[6]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f7 */
        $f7 = $f[7]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f8 */
        $f8 = $f[8]->toInt64();
        /** @var ParagonIE_Sodium_Core32_Int64 $f9 */
        $f9 = $f[9]->toInt64();

        $f0_2 = $f0->shiftLeft(1);
        $f1_2 = $f1->shiftLeft(1);
        $f2_2 = $f2->shiftLeft(1);
        $f3_2 = $f3->shiftLeft(1);
        $f4_2 = $f4->shiftLeft(1);
        $f5_2 = $f5->shiftLeft(1);
        $f6_2 = $f6->shiftLeft(1);
        $f7_2 = $f7->shiftLeft(1);
        $f5_38 = $f5->mulInt(38, 6); /* 1.959375*2^30 */
        $f6_19 = $f6->mulInt(19, 5); /* 1.959375*2^30 */
        $f7_38 = $f7->mulInt(38, 6); /* 1.959375*2^30 */
        $f8_19 = $f8->mulInt(19, 5); /* 1.959375*2^30 */
        $f9_38 = $f9->mulInt(38, 6); /* 1.959375*2^30 */
        $f0f0 = $f0->mulInt64($f0, 28);
        $f0f1_2 = $f0_2->mulInt64($f1, 28);
        $f0f2_2 = $f0_2->mulInt64($f2, 28);
        $f0f3_2 = $f0_2->mulInt64($f3, 28);
        $f0f4_2 = $f0_2->mulInt64($f4, 28);
        $f0f5_2 = $f0_2->mulInt64($f5, 28);
        $f0f6_2 = $f0_2->mulInt64($f6, 28);
        $f0f7_2 = $f0_2->mulInt64($f7, 28);
        $f0f8_2 = $f0_2->mulInt64($f8, 28);
        $f0f9_2 = $f0_2->mulInt64($f9, 28);
        $f1f1_2 = $f1_2->mulInt64($f1, 28);
        $f1f2_2 = $f1_2->mulInt64($f2, 28);
        $f1f3_4 = $f1_2->mulInt64($f3_2, 29);
        $f1f4_2 = $f1_2->mulInt64($f4, 28);
        $f1f5_4 = $f1_2->mulInt64($f5_2, 29);
        $f1f6_2 = $f1_2->mulInt64($f6, 28);
        $f1f7_4 = $f1_2->mulInt64($f7_2, 29);
        $f1f8_2 = $f1_2->mulInt64($f8, 28);
        $f1f9_76 = $f9_38->mulInt64($f1_2, 29);
        $f2f2 = $f2->mulInt64($f2, 28);
        $f2f3_2 = $f2_2->mulInt64($f3, 28);
        $f2f4_2 = $f2_2->mulInt64($f4, 28);
        $f2f5_2 = $f2_2->mulInt64($f5, 28);
        $f2f6_2 = $f2_2->mulInt64($f6, 28);
        $f2f7_2 = $f2_2->mulInt64($f7, 28);
        $f2f8_38 = $f8_19->mulInt64($f2_2, 29);
        $f2f9_38 = $f9_38->mulInt64($f2, 29);
        $f3f3_2 = $f3_2->mulInt64($f3, 28);
        $f3f4_2 = $f3_2->mulInt64($f4, 28);
        $f3f5_4 = $f3_2->mulInt64($f5_2, 28);
        $f3f6_2 = $f3_2->mulInt64($f6, 28);
        $f3f7_76 = $f7_38->mulInt64($f3_2, 29);
        $f3f8_38 = $f8_19->mulInt64($f3_2, 29);
        $f3f9_76 = $f9_38->mulInt64($f3_2, 29);
        $f4f4 = $f4->mulInt64($f4, 28);
        $f4f5_2 = $f4_2->mulInt64($f5, 28);
        $f4f6_38 = $f6_19->mulInt64($f4_2, 29);
        $f4f7_38 = $f7_38->mulInt64($f4, 29);
        $f4f8_38 = $f8_19->mulInt64($f4_2, 29);
        $f4f9_38 = $f9_38->mulInt64($f4, 29);
        $f5f5_38 = $f5_38->mulInt64($f5, 29);
        $f5f6_38 = $f6_19->mulInt64($f5_2, 29);
        $f5f7_76 = $f7_38->mulInt64($f5_2, 29);
        $f5f8_38 = $f8_19->mulInt64($f5_2, 29);
        $f5f9_76 = $f9_38->mulInt64($f5_2, 29);
        $f6f6_19 = $f6_19->mulInt64($f6, 29);
        $f6f7_38 = $f7_38->mulInt64($f6, 29);
        $f6f8_38 = $f8_19->mulInt64($f6_2, 29);
        $f6f9_38 = $f9_38->mulInt64($f6, 29);
        $f7f7_38 = $f7_38->mulInt64($f7, 29);
        $f7f8_38 = $f8_19->mulInt64($f7_2, 29);
        $f7f9_76 = $f9_38->mulInt64($f7_2, 29);
        $f8f8_19 = $f8_19->mulInt64($f8, 29);
        $f8f9_38 = $f9_38->mulInt64($f8, 29);
        $f9f9_38 = $f9_38->mulInt64($f9, 29);

        $h0 = $f0f0->addInt64($f1f9_76)->addInt64($f2f8_38)->addInt64($f3f7_76)->addInt64($f4f6_38)->addInt64($f5f5_38);
        $h1 = $f0f1_2->addInt64($f2f9_38)->addInt64($f3f8_38)->addInt64($f4f7_38)->addInt64($f5f6_38);
        $h2 = $f0f2_2->addInt64($f1f1_2)->addInt64($f3f9_76)->addInt64($f4f8_38)->addInt64($f5f7_76)->addInt64($f6f6_19);
        $h3 = $f0f3_2->addInt64($f1f2_2)->addInt64($f4f9_38)->addInt64($f5f8_38)->addInt64($f6f7_38);
        $h4 = $f0f4_2->addInt64($f1f3_4)->addInt64($f2f2)->addInt64($f5f9_76)->addInt64($f6f8_38)->addInt64($f7f7_38);
        $h5 = $f0f5_2->addInt64($f1f4_2)->addInt64($f2f3_2)->addInt64($f6f9_38)->addInt64($f7f8_38);
        $h6 = $f0f6_2->addInt64($f1f5_4)->addInt64($f2f4_2)->addInt64($f3f3_2)->addInt64($f7f9_76)->addInt64($f8f8_19);
        $h7 = $f0f7_2->addInt64($f1f6_2)->addInt64($f2f5_2)->addInt64($f3f4_2)->addInt64($f8f9_38);
        $h8 = $f0f8_2->addInt64($f1f7_4)->addInt64($f2f6_2)->addInt64($f3f5_4)->addInt64($f4f4)->addInt64($f9f9_38);
        $h9 = $f0f9_2->addInt64($f1f8_2)->addInt64($f2f7_2)->addInt64($f3f6_2)->addInt64($f4f5_2);

        /**
         * @var ParagonIE_Sodium_Core32_Int64 $h0
         * @var ParagonIE_Sodium_Core32_Int64 $h1
         * @var ParagonIE_Sodium_Core32_Int64 $h2
         * @var ParagonIE_Sodium_Core32_Int64 $h3
         * @var ParagonIE_Sodium_Core32_Int64 $h4
         * @var ParagonIE_Sodium_Core32_Int64 $h5
         * @var ParagonIE_Sodium_Core32_Int64 $h6
         * @var ParagonIE_Sodium_Core32_Int64 $h7
         * @var ParagonIE_Sodium_Core32_Int64 $h8
         * @var ParagonIE_Sodium_Core32_Int64 $h9
         */
        $h0 = $h0->shiftLeft(1);
        $h1 = $h1->shiftLeft(1);
        $h2 = $h2->shiftLeft(1);
        $h3 = $h3->shiftLeft(1);
        $h4 = $h4->shiftLeft(1);
        $h5 = $h5->shiftLeft(1);
        $h6 = $h6->shiftLeft(1);
        $h7 = $h7->shiftLeft(1);
        $h8 = $h8->shiftLeft(1);
        $h9 = $h9->shiftLeft(1);

        $carry0 = $h0->addInt(1 << 25)->shiftRight(26);
        $h1 = $h1->addInt64($carry0);
        $h0 = $h0->subInt64($carry0->shiftLeft(26));
        $carry4 = $h4->addInt(1 << 25)->shiftRight(26);
        $h5 = $h5->addInt64($carry4);
        $h4 = $h4->subInt64($carry4->shiftLeft(26));

        $carry1 = $h1->addInt(1 << 24)->shiftRight(25);
        $h2 = $h2->addInt64($carry1);
        $h1 = $h1->subInt64($carry1->shiftLeft(25));
        $carry5 = $h5->addInt(1 << 24)->shiftRight(25);
        $h6 = $h6->addInt64($carry5);
        $h5 = $h5->subInt64($carry5->shiftLeft(25));

        $carry2 = $h2->addInt(1 << 25)->shiftRight(26);
        $h3 = $h3->addInt64($carry2);
        $h2 = $h2->subInt64($carry2->shiftLeft(26));
        $carry6 = $h6->addInt(1 << 25)->shiftRight(26);
        $h7 = $h7->addInt64($carry6);
        $h6 = $h6->subInt64($carry6->shiftLeft(26));

        $carry3 = $h3->addInt(1 << 24)->shiftRight(25);
        $h4 = $h4->addInt64($carry3);
        $h3 = $h3->subInt64($carry3->shiftLeft(25));
        $carry7 = $h7->addInt(1 << 24)->shiftRight(25);
        $h8 = $h8->addInt64($carry7);
        $h7 = $h7->subInt64($carry7->shiftLeft(25));

        $carry4 = $h4->addInt(1 << 25)->shiftRight(26);
        $h5 = $h5->addInt64($carry4);
        $h4 = $h4->subInt64($carry4->shiftLeft(26));
        $carry8 = $h8->addInt(1 << 25)->shiftRight(26);
        $h9 = $h9->addInt64($carry8);
        $h8 = $h8->subInt64($carry8->shiftLeft(26));

        $carry9 = $h9->addInt(1 << 24)->shiftRight(25);
        $h0 = $h0->addInt64($carry9->mulInt(19, 5));
        $h9 = $h9->subInt64($carry9->shiftLeft(25));

        $carry0 = $h0->addInt(1 << 25)->shiftRight(26);
        $h1 = $h1->addInt64($carry0);
        $h0 = $h0->subInt64($carry0->shiftLeft(26));

        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
            array(
                $h0->toInt32(),
                $h1->toInt32(),
                $h2->toInt32(),
                $h3->toInt32(),
                $h4->toInt32(),
                $h5->toInt32(),
                $h6->toInt32(),
                $h7->toInt32(),
                $h8->toInt32(),
                $h9->toInt32()
            )
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $Z
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_invert(ParagonIE_Sodium_Core32_Curve25519_Fe $Z)
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $z
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fe_pow22523(ParagonIE_Sodium_Core32_Curve25519_Fe $z)
    {
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $g
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress MixedTypeCoercion
     */
    public static function fe_sub(ParagonIE_Sodium_Core32_Curve25519_Fe $f, ParagonIE_Sodium_Core32_Curve25519_Fe $g)
    {
        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
            array(
                $f[0]->subInt32($g[0]),
                $f[1]->subInt32($g[1]),
                $f[2]->subInt32($g[2]),
                $f[3]->subInt32($g[3]),
                $f[4]->subInt32($g[4]),
                $f[5]->subInt32($g[5]),
                $f[6]->subInt32($g[6]),
                $f[7]->subInt32($g[7]),
                $f[8]->subInt32($g[8]),
                $f[9]->subInt32($g[9])
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_Cached $q
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_add(
        ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core32_Curve25519_Ge_Cached $q
    ) {
        $r = new ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1();
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
     * @psalm-suppress MixedArrayOffset
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
        for ($i = 0; $i < 256; ++$i) {
            $r[$i] = (int) (1 &
                (
                    self::chrToInt($a[$i >> 3])
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
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P3
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_frombytes_negate_vartime($s)
    {
        static $d = null;
        if (!$d) {
            /** @var ParagonIE_Sodium_Core32_Curve25519_Fe $d */
            $d = ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                array(
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[0]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[1]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[2]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[3]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[4]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[5]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[6]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[7]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[8]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d[9])
                )
            );
        }

        # fe_frombytes(h->Y,s);
        # fe_1(h->Z);
        $h = new ParagonIE_Sodium_Core32_Curve25519_Ge_P3(
            self::fe_0(),
            self::fe_frombytes($s),
            self::fe_1()
        );

        # fe_sq(u,h->Y);
        # fe_mul(v,u,d);
        # fe_sub(u,u,h->Z);       /* u = y^2-1 */
        # fe_add(v,v,h->Z);       /* v = dy^2+1 */
        $u = self::fe_sq($h->Y);
        /** @var ParagonIE_Sodium_Core32_Curve25519_Fe $d */
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
                ParagonIE_Sodium_Core32_Curve25519_Fe::fromIntArray(self::$sqrtm1)
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $R
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $q
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_madd(
        ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $R,
        ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $q
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $R
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $q
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_msub(
        ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $R,
        ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $q
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $p
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P2
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p1p1_to_p2(ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $p)
    {
        $r = new ParagonIE_Sodium_Core32_Curve25519_Ge_P2();
        $r->X = self::fe_mul($p->X, $p->T);
        $r->Y = self::fe_mul($p->Y, $p->Z);
        $r->Z = self::fe_mul($p->Z, $p->T);
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $p
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P3
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p1p1_to_p3(ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 $p)
    {
        $r = new ParagonIE_Sodium_Core32_Curve25519_Ge_P3();
        $r->X = self::fe_mul($p->X, $p->T);
        $r->Y = self::fe_mul($p->Y, $p->Z);
        $r->Z = self::fe_mul($p->Z, $p->T);
        $r->T = self::fe_mul($p->X, $p->Y);
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P2
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p2_0()
    {
        return new ParagonIE_Sodium_Core32_Curve25519_Ge_P2(
            self::fe_0(),
            self::fe_1(),
            self::fe_1()
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P2 $p
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p2_dbl(ParagonIE_Sodium_Core32_Curve25519_Ge_P2 $p)
    {
        $r = new ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1();

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
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P3
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p3_0()
    {
        return new ParagonIE_Sodium_Core32_Curve25519_Ge_P3(
            self::fe_0(),
            self::fe_1(),
            self::fe_1(),
            self::fe_0()
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_Cached
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p3_to_cached(ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p)
    {
        static $d2 = null;
        if ($d2 === null) {
            $d2 = ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                array(
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[0]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[1]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[2]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[3]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[4]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[5]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[6]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[7]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[8]),
                    ParagonIE_Sodium_Core32_Int32::fromInt(self::$d2[9])
                )
            );
        }
        /** @var ParagonIE_Sodium_Core32_Curve25519_Fe $d2 */
        $r = new ParagonIE_Sodium_Core32_Curve25519_Ge_Cached();
        $r->YplusX = self::fe_add($p->Y, $p->X);
        $r->YminusX = self::fe_sub($p->Y, $p->X);
        $r->Z = self::fe_copy($p->Z);
        $r->T2d = self::fe_mul($p->T, $d2);
        return $r;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P2
     */
    public static function ge_p3_to_p2(ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p)
    {
        return new ParagonIE_Sodium_Core32_Curve25519_Ge_P2(
            $p->X,
            $p->Y,
            $p->Z
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $h
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p3_tobytes(ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $h)
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_p3_dbl(ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p)
    {
        $q = self::ge_p3_to_p2($p);
        return self::ge_p2_dbl($q);
    }

    /**
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_precomp_0()
    {
        return new ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp(
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
     * @psalm-suppress MixedReturnStatement
     */
    public static function equal($b, $c)
    {
        return (int) ((($b ^ $c) - 1 & 0xffffffff) >> 31);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string|int $char
     * @return int (1 = yes, 0 = no)
     * @throws SodiumException
     * @throws TypeError
     */
    public static function negative($char)
    {
        if (is_int($char)) {
            return $char < 0 ? 1 : 0;
        }
        /** @var string $char */
        /** @var int $x */
        $x = self::chrToInt(self::substr($char, 0, 1));
        return (int) ($x >> 31);
    }

    /**
     * Conditional move
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $t
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $u
     * @param int $b
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp
     * @throws SodiumException
     * @throws TypeError
     */
    public static function cmov(
        ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $t,
        ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $u,
        $b
    ) {
        if (!is_int($b)) {
            throw new InvalidArgumentException('Expected an integer.');
        }
        return new ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp(
            self::fe_cmov($t->yplusx, $u->yplusx, $b),
            self::fe_cmov($t->yminusx, $u->yminusx, $b),
            self::fe_cmov($t->xy2d, $u->xy2d, $b)
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $pos
     * @param int $b
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayOffset
     * @psalm-suppress MixedArgument
     */
    public static function ge_select($pos = 0, $b = 0)
    {
        static $base = null;
        if ($base === null) {
            $base = array();
            foreach (self::$base as $i => $bas) {
                for ($j = 0; $j < 8; ++$j) {
                    $base[$i][$j] = new ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp(
                        ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                            array(
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][0]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][1]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][2]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][3]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][4]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][5]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][6]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][7]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][8]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][0][9])
                            )
                        ),
                        ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                            array(
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][0]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][1]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][2]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][3]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][4]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][5]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][6]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][7]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][8]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][1][9])
                            )
                        ),
                        ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                            array(
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][0]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][1]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][2]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][3]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][4]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][5]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][6]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][7]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][8]),
                                ParagonIE_Sodium_Core32_Int32::fromInt($bas[$j][2][9])
                            )
                        )
                    );
                }
            }
        }
        if (!is_int($pos)) {
            throw new InvalidArgumentException('Position must be an integer');
        }
        if ($pos < 0 || $pos > 31) {
            throw new RangeException('Position is out of range [0, 31]');
        }

        $bnegative = self::negative($b);
        /** @var int $babs */
        $babs = $b - (((-$bnegative) & $b) << 1);

        $t = self::ge_precomp_0();
        for ($i = 0; $i < 8; ++$i) {
            $t = self::cmov(
                $t,
                $base[$pos][$i],
                self::equal($babs, $i + 1)
            );
        }
        $minusT = new ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp(
            self::fe_copy($t->yminusx),
            self::fe_copy($t->yplusx),
            self::fe_neg($t->xy2d)
        );
        return self::cmov($t, $minusT, -$bnegative);
    }

    /**
     * Subtract two group elements.
     *
     * r = p - q
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_Cached $q
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_sub(
        ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $p,
        ParagonIE_Sodium_Core32_Curve25519_Ge_Cached $q
    ) {
        $r = new ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1();

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
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P2 $h
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_tobytes(ParagonIE_Sodium_Core32_Curve25519_Ge_P2 $h)
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
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $A
     * @param string $b
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P2
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArrayAccess
     */
    public static function ge_double_scalarmult_vartime(
        $a,
        ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $A,
        $b
    ) {
        /** @var array<int, ParagonIE_Sodium_Core32_Curve25519_Ge_Cached> $Ai */
        $Ai = array();

        static $Bi = array();
        /** @var array<int, ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp> $Bi */
        if (!$Bi) {
            for ($i = 0; $i < 8; ++$i) {
                $Bi[$i] = new ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp(
                    ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                        array(
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][0]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][1]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][2]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][3]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][4]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][5]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][6]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][7]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][8]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][0][9])
                        )
                    ),
                    ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                        array(
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][0]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][1]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][2]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][3]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][4]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][5]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][6]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][7]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][8]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][1][9])
                        )
                    ),
                    ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray(
                        array(
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][0]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][1]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][2]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][3]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][4]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][5]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][6]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][7]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][8]),
                            ParagonIE_Sodium_Core32_Int32::fromInt(self::$base2[$i][2][9])
                        )
                    )
                );
            }
        }

        for ($i = 0; $i < 8; ++$i) {
            $Ai[$i] = new ParagonIE_Sodium_Core32_Curve25519_Ge_Cached(
                self::fe_0(),
                self::fe_0(),
                self::fe_0(),
                self::fe_0()
            );
        }
        /** @var array<int, ParagonIE_Sodium_Core32_Curve25519_Ge_Cached> $Ai */

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
            /** @var array<int, ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp> $Bi */

            # if (bslide[i] > 0) {
            if ($bslide[$i] > 0) {
                # ge_p1p1_to_p3(&u,&t);
                # ge_madd(&t,&u,&Bi[bslide[i]/2]);
                $u = self::ge_p1p1_to_p3($t);
                /** @var int $index */
                $index = (int) floor($bslide[$i] / 2);
                /** @var ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $thisB */
                $thisB = $Bi[$index];
                $t = self::ge_madd($t, $u, $thisB);
                # } else if (bslide[i] < 0) {
            } elseif ($bslide[$i] < 0) {
                # ge_p1p1_to_p3(&u,&t);
                # ge_msub(&t,&u,&Bi[(-bslide[i])/2]);
                $u = self::ge_p1p1_to_p3($t);

                /** @var int $index */
                $index = (int) floor(-$bslide[$i] / 2);

                /** @var ParagonIE_Sodium_Core32_Curve25519_Ge_Precomp $thisB */
                $thisB = $Bi[$index];
                $t = self::ge_msub($t, $u, $thisB);
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
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P3
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedOperand
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_scalarmult_base($a)
    {
        /** @var array<int, int> $e */
        $e = array();
        $r = new ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1();

        for ($i = 0; $i < 32; ++$i) {
            /** @var int $dbl */
            $dbl = (int) $i << 1;
            $e[$dbl] = (int) self::chrToInt($a[$i]) & 15;
            $e[$dbl + 1] = (int) (self::chrToInt($a[$i]) >> 4) & 15;
        }

        /** @var int $carry */
        $carry = 0;
        for ($i = 0; $i < 63; ++$i) {
            $e[$i] += $carry;
            /** @var int $carry */
            $carry = $e[$i] + 8;
            /** @var int $carry */
            $carry >>= 4;
            $e[$i] -= $carry << 4;
        }

        /** @var array<int, int> $e */
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
     * @throws SodiumException
     * @throws TypeError
     */
    public static function sc_muladd($a, $b, $c)
    {
        $a0 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($a, 0, 3)));
        $a1 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($a, 2, 4)) >> 5));
        $a2 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($a, 5, 3)) >> 2));
        $a3 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($a, 7, 4)) >> 7));
        $a4 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($a, 10, 4)) >> 4));
        $a5 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($a, 13, 3)) >> 1));
        $a6 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($a, 15, 4)) >> 6));
        $a7 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($a, 18, 3)) >> 3));
        $a8 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($a, 21, 3)));
        $a9 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($a, 23, 4)) >> 5));
        $a10 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($a, 26, 3)) >> 2));
        $a11 = ParagonIE_Sodium_Core32_Int64::fromInt(0x1fffffff & (self::load_4(self::substr($a, 28, 4)) >> 7));
        $b0 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($b, 0, 3)));
        $b1 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($b, 2, 4)) >> 5));
        $b2 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($b, 5, 3)) >> 2));
        $b3 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($b, 7, 4)) >> 7));
        $b4 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($b, 10, 4)) >> 4));
        $b5 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($b, 13, 3)) >> 1));
        $b6 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($b, 15, 4)) >> 6));
        $b7 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($b, 18, 3)) >> 3));
        $b8 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($b, 21, 3)));
        $b9 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($b, 23, 4)) >> 5));
        $b10 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($b, 26, 3)) >> 2));
        $b11 = ParagonIE_Sodium_Core32_Int64::fromInt(0x1fffffff & (self::load_4(self::substr($b, 28, 4)) >> 7));
        $c0 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($c, 0, 3)));
        $c1 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($c, 2, 4)) >> 5));
        $c2 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($c, 5, 3)) >> 2));
        $c3 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($c, 7, 4)) >> 7));
        $c4 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($c, 10, 4)) >> 4));
        $c5 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($c, 13, 3)) >> 1));
        $c6 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($c, 15, 4)) >> 6));
        $c7 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($c, 18, 3)) >> 3));
        $c8 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($c, 21, 3)));
        $c9 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($c, 23, 4)) >> 5));
        $c10 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($c, 26, 3)) >> 2));
        $c11 = ParagonIE_Sodium_Core32_Int64::fromInt(0x1fffffff & (self::load_4(self::substr($c, 28, 4)) >> 7));

        /* Can't really avoid the pyramid here: */
        /**
         * @var ParagonIE_Sodium_Core32_Int64 $s0
         * @var ParagonIE_Sodium_Core32_Int64 $s1
         * @var ParagonIE_Sodium_Core32_Int64 $s2
         * @var ParagonIE_Sodium_Core32_Int64 $s3
         * @var ParagonIE_Sodium_Core32_Int64 $s4
         * @var ParagonIE_Sodium_Core32_Int64 $s5
         * @var ParagonIE_Sodium_Core32_Int64 $s6
         * @var ParagonIE_Sodium_Core32_Int64 $s7
         * @var ParagonIE_Sodium_Core32_Int64 $s8
         * @var ParagonIE_Sodium_Core32_Int64 $s9
         * @var ParagonIE_Sodium_Core32_Int64 $s10
         * @var ParagonIE_Sodium_Core32_Int64 $s11
         * @var ParagonIE_Sodium_Core32_Int64 $s12
         * @var ParagonIE_Sodium_Core32_Int64 $s13
         * @var ParagonIE_Sodium_Core32_Int64 $s14
         * @var ParagonIE_Sodium_Core32_Int64 $s15
         * @var ParagonIE_Sodium_Core32_Int64 $s16
         * @var ParagonIE_Sodium_Core32_Int64 $s17
         * @var ParagonIE_Sodium_Core32_Int64 $s18
         * @var ParagonIE_Sodium_Core32_Int64 $s19
         * @var ParagonIE_Sodium_Core32_Int64 $s20
         * @var ParagonIE_Sodium_Core32_Int64 $s21
         * @var ParagonIE_Sodium_Core32_Int64 $s22
         * @var ParagonIE_Sodium_Core32_Int64 $s23
         */

        $s0 = $c0->addInt64($a0->mulInt64($b0, 24));
        $s1 = $c1->addInt64($a0->mulInt64($b1, 24))->addInt64($a1->mulInt64($b0, 24));
        $s2 = $c2->addInt64($a0->mulInt64($b2, 24))->addInt64($a1->mulInt64($b1, 24))->addInt64($a2->mulInt64($b0, 24));
        $s3 = $c3->addInt64($a0->mulInt64($b3, 24))->addInt64($a1->mulInt64($b2, 24))->addInt64($a2->mulInt64($b1, 24))
                 ->addInt64($a3->mulInt64($b0, 24));
        $s4 = $c4->addInt64($a0->mulInt64($b4, 24))->addInt64($a1->mulInt64($b3, 24))->addInt64($a2->mulInt64($b2, 24))
                 ->addInt64($a3->mulInt64($b1, 24))->addInt64($a4->mulInt64($b0, 24));
        $s5 = $c5->addInt64($a0->mulInt64($b5, 24))->addInt64($a1->mulInt64($b4, 24))->addInt64($a2->mulInt64($b3, 24))
                 ->addInt64($a3->mulInt64($b2, 24))->addInt64($a4->mulInt64($b1, 24))->addInt64($a5->mulInt64($b0, 24));
        $s6 = $c6->addInt64($a0->mulInt64($b6, 24))->addInt64($a1->mulInt64($b5, 24))->addInt64($a2->mulInt64($b4, 24))
                 ->addInt64($a3->mulInt64($b3, 24))->addInt64($a4->mulInt64($b2, 24))->addInt64($a5->mulInt64($b1, 24))
                 ->addInt64($a6->mulInt64($b0, 24));
        $s7 = $c7->addInt64($a0->mulInt64($b7, 24))->addInt64($a1->mulInt64($b6, 24))->addInt64($a2->mulInt64($b5, 24))
                 ->addInt64($a3->mulInt64($b4, 24))->addInt64($a4->mulInt64($b3, 24))->addInt64($a5->mulInt64($b2, 24))
                 ->addInt64($a6->mulInt64($b1, 24))->addInt64($a7->mulInt64($b0, 24));
        $s8 = $c8->addInt64($a0->mulInt64($b8, 24))->addInt64($a1->mulInt64($b7, 24))->addInt64($a2->mulInt64($b6, 24))
                 ->addInt64($a3->mulInt64($b5, 24))->addInt64($a4->mulInt64($b4, 24))->addInt64($a5->mulInt64($b3, 24))
                 ->addInt64($a6->mulInt64($b2, 24))->addInt64($a7->mulInt64($b1, 24))->addInt64($a8->mulInt64($b0, 24));
        $s9 = $c9->addInt64($a0->mulInt64($b9, 24))->addInt64($a1->mulInt64($b8, 24))->addInt64($a2->mulInt64($b7, 24))
                 ->addInt64($a3->mulInt64($b6, 24))->addInt64($a4->mulInt64($b5, 24))->addInt64($a5->mulInt64($b4, 24))
                 ->addInt64($a6->mulInt64($b3, 24))->addInt64($a7->mulInt64($b2, 24))->addInt64($a8->mulInt64($b1, 24))
                 ->addInt64($a9->mulInt64($b0, 24));
        $s10 = $c10->addInt64($a0->mulInt64($b10, 24))->addInt64($a1->mulInt64($b9, 24))->addInt64($a2->mulInt64($b8, 24))
                   ->addInt64($a3->mulInt64($b7, 24))->addInt64($a4->mulInt64($b6, 24))->addInt64($a5->mulInt64($b5, 24))
                   ->addInt64($a6->mulInt64($b4, 24))->addInt64($a7->mulInt64($b3, 24))->addInt64($a8->mulInt64($b2, 24))
                   ->addInt64($a9->mulInt64($b1, 24))->addInt64($a10->mulInt64($b0, 24));
        $s11 = $c11->addInt64($a0->mulInt64($b11, 24))->addInt64($a1->mulInt64($b10, 24))->addInt64($a2->mulInt64($b9, 24))
                   ->addInt64($a3->mulInt64($b8, 24))->addInt64($a4->mulInt64($b7, 24))->addInt64($a5->mulInt64($b6, 24))
                   ->addInt64($a6->mulInt64($b5, 24))->addInt64($a7->mulInt64($b4, 24))->addInt64($a8->mulInt64($b3, 24))
                   ->addInt64($a9->mulInt64($b2, 24))->addInt64($a10->mulInt64($b1, 24))->addInt64($a11->mulInt64($b0, 24));
        $s12 = $a1->mulInt64($b11, 24)->addInt64($a2->mulInt64($b10, 24))->addInt64($a3->mulInt64($b9, 24))
                  ->addInt64($a4->mulInt64($b8, 24))->addInt64($a5->mulInt64($b7, 24))->addInt64($a6->mulInt64($b6, 24))
                  ->addInt64($a7->mulInt64($b5, 24))->addInt64($a8->mulInt64($b4, 24))->addInt64($a9->mulInt64($b3, 24))
                  ->addInt64($a10->mulInt64($b2, 24))->addInt64($a11->mulInt64($b1, 24));
        $s13 = $a2->mulInt64($b11, 24)->addInt64($a3->mulInt64($b10, 24))->addInt64($a4->mulInt64($b9, 24))
                  ->addInt64($a5->mulInt64($b8, 24))->addInt64($a6->mulInt64($b7, 24))->addInt64($a7->mulInt64($b6, 24))
                  ->addInt64($a8->mulInt64($b5, 24))->addInt64($a9->mulInt64($b4, 24))->addInt64($a10->mulInt64($b3, 24))
                  ->addInt64($a11->mulInt64($b2, 24));
        $s14 = $a3->mulInt64($b11, 24)->addInt64($a4->mulInt64($b10, 24))->addInt64($a5->mulInt64($b9, 24))
                  ->addInt64($a6->mulInt64($b8, 24))->addInt64($a7->mulInt64($b7, 24))->addInt64($a8->mulInt64($b6, 24))
                  ->addInt64($a9->mulInt64($b5, 24))->addInt64($a10->mulInt64($b4, 24))->addInt64($a11->mulInt64($b3, 24));
        $s15 = $a4->mulInt64($b11, 24)->addInt64($a5->mulInt64($b10, 24))->addInt64($a6->mulInt64($b9, 24))
                  ->addInt64($a7->mulInt64($b8, 24))->addInt64($a8->mulInt64($b7, 24))->addInt64($a9->mulInt64($b6, 24))
                  ->addInt64($a10->mulInt64($b5, 24))->addInt64($a11->mulInt64($b4, 24));
        $s16 = $a5->mulInt64($b11, 24)->addInt64($a6->mulInt64($b10, 24))->addInt64($a7->mulInt64($b9, 24))
                  ->addInt64($a8->mulInt64($b8, 24))->addInt64($a9->mulInt64($b7, 24))->addInt64($a10->mulInt64($b6, 24))
                  ->addInt64($a11->mulInt64($b5, 24));
        $s17 = $a6->mulInt64($b11, 24)->addInt64($a7->mulInt64($b10, 24))->addInt64($a8->mulInt64($b9, 24))
                  ->addInt64($a9->mulInt64($b8, 24))->addInt64($a10->mulInt64($b7, 24))->addInt64($a11->mulInt64($b6, 24));
        $s18 = $a7->mulInt64($b11, 24)->addInt64($a8->mulInt64($b10, 24))->addInt64($a9->mulInt64($b9, 24))
                  ->addInt64($a10->mulInt64($b8, 24))->addInt64($a11->mulInt64($b7, 24));
        $s19 = $a8->mulInt64($b11, 24)->addInt64($a9->mulInt64($b10, 24))->addInt64($a10->mulInt64($b9, 24))
                  ->addInt64($a11->mulInt64($b8, 24));
        $s20 = $a9->mulInt64($b11, 24)->addInt64($a10->mulInt64($b10, 24))->addInt64($a11->mulInt64($b9, 24));
        $s21 = $a10->mulInt64($b11, 24)->addInt64($a11->mulInt64($b10, 24));
        $s22 = $a11->mulInt64($b11, 24);
        $s23 = new ParagonIE_Sodium_Core32_Int64();

        $carry0 = $s0->addInt(1 << 20)->shiftRight(21);
        $s1 = $s1->addInt64($carry0);
        $s0 = $s0->subInt64($carry0->shiftLeft(21));
        $carry2 = $s2->addInt(1 << 20)->shiftRight(21);
        $s3 = $s3->addInt64($carry2);
        $s2 = $s2->subInt64($carry2->shiftLeft(21));
        $carry4 = $s4->addInt(1 << 20)->shiftRight(21);
        $s5 = $s5->addInt64($carry4);
        $s4 = $s4->subInt64($carry4->shiftLeft(21));
        $carry6 = $s6->addInt(1 << 20)->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry8 = $s8->addInt(1 << 20)->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry10 = $s10->addInt(1 << 20)->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));
        $carry12 = $s12->addInt(1 << 20)->shiftRight(21);
        $s13 = $s13->addInt64($carry12);
        $s12 = $s12->subInt64($carry12->shiftLeft(21));
        $carry14 = $s14->addInt(1 << 20)->shiftRight(21);
        $s15 = $s15->addInt64($carry14);
        $s14 = $s14->subInt64($carry14->shiftLeft(21));
        $carry16 = $s16->addInt(1 << 20)->shiftRight(21);
        $s17 = $s17->addInt64($carry16);
        $s16 = $s16->subInt64($carry16->shiftLeft(21));
        $carry18 = $s18->addInt(1 << 20)->shiftRight(21);
        $s19 = $s19->addInt64($carry18);
        $s18 = $s18->subInt64($carry18->shiftLeft(21));
        $carry20 = $s20->addInt(1 << 20)->shiftRight(21);
        $s21 = $s21->addInt64($carry20);
        $s20 = $s20->subInt64($carry20->shiftLeft(21));
        $carry22 = $s22->addInt(1 << 20)->shiftRight(21);
        $s23 = $s23->addInt64($carry22);
        $s22 = $s22->subInt64($carry22->shiftLeft(21));

        $carry1 = $s1->addInt(1 << 20)->shiftRight(21);
        $s2 = $s2->addInt64($carry1);
        $s1 = $s1->subInt64($carry1->shiftLeft(21));
        $carry3 = $s3->addInt(1 << 20)->shiftRight(21);
        $s4 = $s4->addInt64($carry3);
        $s3 = $s3->subInt64($carry3->shiftLeft(21));
        $carry5 = $s5->addInt(1 << 20)->shiftRight(21);
        $s6 = $s6->addInt64($carry5);
        $s5 = $s5->subInt64($carry5->shiftLeft(21));
        $carry7 = $s7->addInt(1 << 20)->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry9 = $s9->addInt(1 << 20)->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry11 = $s11->addInt(1 << 20)->shiftRight(21);
        $s12 = $s12->addInt64($carry11);
        $s11 = $s11->subInt64($carry11->shiftLeft(21));
        $carry13 = $s13->addInt(1 << 20)->shiftRight(21);
        $s14 = $s14->addInt64($carry13);
        $s13 = $s13->subInt64($carry13->shiftLeft(21));
        $carry15 = $s15->addInt(1 << 20)->shiftRight(21);
        $s16 = $s16->addInt64($carry15);
        $s15 = $s15->subInt64($carry15->shiftLeft(21));
        $carry17 = $s17->addInt(1 << 20)->shiftRight(21);
        $s18 = $s18->addInt64($carry17);
        $s17 = $s17->subInt64($carry17->shiftLeft(21));
        $carry19 = $s19->addInt(1 << 20)->shiftRight(21);
        $s20 = $s20->addInt64($carry19);
        $s19 = $s19->subInt64($carry19->shiftLeft(21));
        $carry21 = $s21->addInt(1 << 20)->shiftRight(21);
        $s22 = $s22->addInt64($carry21);
        $s21 = $s21->subInt64($carry21->shiftLeft(21));

        $s11 = $s11->addInt64($s23->mulInt(666643, 20));
        $s12 = $s12->addInt64($s23->mulInt(470296, 19));
        $s13 = $s13->addInt64($s23->mulInt(654183, 20));
        $s14 = $s14->subInt64($s23->mulInt(997805, 20));
        $s15 = $s15->addInt64($s23->mulInt(136657, 18));
        $s16 = $s16->subInt64($s23->mulInt(683901, 20));

        $s10 = $s10->addInt64($s22->mulInt(666643, 20));
        $s11 = $s11->addInt64($s22->mulInt(470296, 19));
        $s12 = $s12->addInt64($s22->mulInt(654183, 20));
        $s13 = $s13->subInt64($s22->mulInt(997805, 20));
        $s14 = $s14->addInt64($s22->mulInt(136657, 18));
        $s15 = $s15->subInt64($s22->mulInt(683901, 20));

        $s9  =  $s9->addInt64($s21->mulInt(666643, 20));
        $s10 = $s10->addInt64($s21->mulInt(470296, 19));
        $s11 = $s11->addInt64($s21->mulInt(654183, 20));
        $s12 = $s12->subInt64($s21->mulInt(997805, 20));
        $s13 = $s13->addInt64($s21->mulInt(136657, 18));
        $s14 = $s14->subInt64($s21->mulInt(683901, 20));

        $s8  =  $s8->addInt64($s20->mulInt(666643, 20));
        $s9  =  $s9->addInt64($s20->mulInt(470296, 19));
        $s10 = $s10->addInt64($s20->mulInt(654183, 20));
        $s11 = $s11->subInt64($s20->mulInt(997805, 20));
        $s12 = $s12->addInt64($s20->mulInt(136657, 18));
        $s13 = $s13->subInt64($s20->mulInt(683901, 20));

        $s7  =  $s7->addInt64($s19->mulInt(666643, 20));
        $s8  =  $s8->addInt64($s19->mulInt(470296, 19));
        $s9  =  $s9->addInt64($s19->mulInt(654183, 20));
        $s10 = $s10->subInt64($s19->mulInt(997805, 20));
        $s11 = $s11->addInt64($s19->mulInt(136657, 18));
        $s12 = $s12->subInt64($s19->mulInt(683901, 20));

        $s6  =  $s6->addInt64($s18->mulInt(666643, 20));
        $s7  =  $s7->addInt64($s18->mulInt(470296, 19));
        $s8  =  $s8->addInt64($s18->mulInt(654183, 20));
        $s9  =  $s9->subInt64($s18->mulInt(997805, 20));
        $s10 = $s10->addInt64($s18->mulInt(136657, 18));
        $s11 = $s11->subInt64($s18->mulInt(683901, 20));

        $carry6 = $s6->addInt(1 << 20)->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry8 = $s8->addInt(1 << 20)->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry10 = $s10->addInt(1 << 20)->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));
        $carry12 = $s12->addInt(1 << 20)->shiftRight(21);
        $s13 = $s13->addInt64($carry12);
        $s12 = $s12->subInt64($carry12->shiftLeft(21));
        $carry14 = $s14->addInt(1 << 20)->shiftRight(21);
        $s15 = $s15->addInt64($carry14);
        $s14 = $s14->subInt64($carry14->shiftLeft(21));
        $carry16 = $s16->addInt(1 << 20)->shiftRight(21);
        $s17 = $s17->addInt64($carry16);
        $s16 = $s16->subInt64($carry16->shiftLeft(21));

        $carry7 = $s7->addInt(1 << 20)->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry9 = $s9->addInt(1 << 20)->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry11 = $s11->addInt(1 << 20)->shiftRight(21);
        $s12 = $s12->addInt64($carry11);
        $s11 = $s11->subInt64($carry11->shiftLeft(21));
        $carry13 = $s13->addInt(1 << 20)->shiftRight(21);
        $s14 = $s14->addInt64($carry13);
        $s13 = $s13->subInt64($carry13->shiftLeft(21));
        $carry15 = $s15->addInt(1 << 20)->shiftRight(21);
        $s16 = $s16->addInt64($carry15);
        $s15 = $s15->subInt64($carry15->shiftLeft(21));

        $s5  =  $s5->addInt64($s17->mulInt(666643, 20));
        $s6  =  $s6->addInt64($s17->mulInt(470296, 19));
        $s7  =  $s7->addInt64($s17->mulInt(654183, 20));
        $s8  =  $s8->subInt64($s17->mulInt(997805, 20));
        $s9  =  $s9->addInt64($s17->mulInt(136657, 18));
        $s10 = $s10->subInt64($s17->mulInt(683901, 20));

        $s4  =  $s4->addInt64($s16->mulInt(666643, 20));
        $s5  =  $s5->addInt64($s16->mulInt(470296, 19));
        $s6  =  $s6->addInt64($s16->mulInt(654183, 20));
        $s7  =  $s7->subInt64($s16->mulInt(997805, 20));
        $s8  =  $s8->addInt64($s16->mulInt(136657, 18));
        $s9  =  $s9->subInt64($s16->mulInt(683901, 20));

        $s3  =  $s3->addInt64($s15->mulInt(666643, 20));
        $s4  =  $s4->addInt64($s15->mulInt(470296, 19));
        $s5  =  $s5->addInt64($s15->mulInt(654183, 20));
        $s6  =  $s6->subInt64($s15->mulInt(997805, 20));
        $s7  =  $s7->addInt64($s15->mulInt(136657, 18));
        $s8  =  $s8->subInt64($s15->mulInt(683901, 20));

        $s2  =  $s2->addInt64($s14->mulInt(666643, 20));
        $s3  =  $s3->addInt64($s14->mulInt(470296, 19));
        $s4  =  $s4->addInt64($s14->mulInt(654183, 20));
        $s5  =  $s5->subInt64($s14->mulInt(997805, 20));
        $s6  =  $s6->addInt64($s14->mulInt(136657, 18));
        $s7  =  $s7->subInt64($s14->mulInt(683901, 20));

        $s1  =  $s1->addInt64($s13->mulInt(666643, 20));
        $s2  =  $s2->addInt64($s13->mulInt(470296, 19));
        $s3  =  $s3->addInt64($s13->mulInt(654183, 20));
        $s4  =  $s4->subInt64($s13->mulInt(997805, 20));
        $s5  =  $s5->addInt64($s13->mulInt(136657, 18));
        $s6  =  $s6->subInt64($s13->mulInt(683901, 20));

        $s0  =  $s0->addInt64($s12->mulInt(666643, 20));
        $s1  =  $s1->addInt64($s12->mulInt(470296, 19));
        $s2  =  $s2->addInt64($s12->mulInt(654183, 20));
        $s3  =  $s3->subInt64($s12->mulInt(997805, 20));
        $s4  =  $s4->addInt64($s12->mulInt(136657, 18));
        $s5  =  $s5->subInt64($s12->mulInt(683901, 20));
        $s12 = new ParagonIE_Sodium_Core32_Int64();

        $carry0 = $s0->addInt(1 << 20)->shiftRight(21);
        $s1 = $s1->addInt64($carry0);
        $s0 = $s0->subInt64($carry0->shiftLeft(21));
        $carry2 = $s2->addInt(1 << 20)->shiftRight(21);
        $s3 = $s3->addInt64($carry2);
        $s2 = $s2->subInt64($carry2->shiftLeft(21));
        $carry4 = $s4->addInt(1 << 20)->shiftRight(21);
        $s5 = $s5->addInt64($carry4);
        $s4 = $s4->subInt64($carry4->shiftLeft(21));
        $carry6 = $s6->addInt(1 << 20)->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry8 = $s8->addInt(1 << 20)->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry10 = $s10->addInt(1 << 20)->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));

        $carry1 = $s1->addInt(1 << 20)->shiftRight(21);
        $s2 = $s2->addInt64($carry1);
        $s1 = $s1->subInt64($carry1->shiftLeft(21));
        $carry3 = $s3->addInt(1 << 20)->shiftRight(21);
        $s4 = $s4->addInt64($carry3);
        $s3 = $s3->subInt64($carry3->shiftLeft(21));
        $carry5 = $s5->addInt(1 << 20)->shiftRight(21);
        $s6 = $s6->addInt64($carry5);
        $s5 = $s5->subInt64($carry5->shiftLeft(21));
        $carry7 = $s7->addInt(1 << 20)->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry9 = $s9->addInt(1 << 20)->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry11 = $s11->addInt(1 << 20)->shiftRight(21);
        $s12 = $s12->addInt64($carry11);
        $s11 = $s11->subInt64($carry11->shiftLeft(21));

        $s0  =  $s0->addInt64($s12->mulInt(666643, 20));
        $s1  =  $s1->addInt64($s12->mulInt(470296, 19));
        $s2  =  $s2->addInt64($s12->mulInt(654183, 20));
        $s3  =  $s3->subInt64($s12->mulInt(997805, 20));
        $s4  =  $s4->addInt64($s12->mulInt(136657, 18));
        $s5  =  $s5->subInt64($s12->mulInt(683901, 20));
        $s12 = new ParagonIE_Sodium_Core32_Int64();

        $carry0 = $s0->shiftRight(21);
        $s1 = $s1->addInt64($carry0);
        $s0 = $s0->subInt64($carry0->shiftLeft(21));
        $carry1 = $s1->shiftRight(21);
        $s2 = $s2->addInt64($carry1);
        $s1 = $s1->subInt64($carry1->shiftLeft(21));
        $carry2 = $s2->shiftRight(21);
        $s3 = $s3->addInt64($carry2);
        $s2 = $s2->subInt64($carry2->shiftLeft(21));
        $carry3 = $s3->shiftRight(21);
        $s4 = $s4->addInt64($carry3);
        $s3 = $s3->subInt64($carry3->shiftLeft(21));
        $carry4 = $s4->shiftRight(21);
        $s5 = $s5->addInt64($carry4);
        $s4 = $s4->subInt64($carry4->shiftLeft(21));
        $carry5 = $s5->shiftRight(21);
        $s6 = $s6->addInt64($carry5);
        $s5 = $s5->subInt64($carry5->shiftLeft(21));
        $carry6 = $s6->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry7 = $s7->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry8 = $s8->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry9 = $s9->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry10 = $s10->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));
        $carry11 = $s11->shiftRight(21);
        $s12 = $s12->addInt64($carry11);
        $s11 = $s11->subInt64($carry11->shiftLeft(21));

        $s0  =  $s0->addInt64($s12->mulInt(666643, 20));
        $s1  =  $s1->addInt64($s12->mulInt(470296, 19));
        $s2  =  $s2->addInt64($s12->mulInt(654183, 20));
        $s3  =  $s3->subInt64($s12->mulInt(997805, 20));
        $s4  =  $s4->addInt64($s12->mulInt(136657, 18));
        $s5  =  $s5->subInt64($s12->mulInt(683901, 20));

        $carry0 = $s0->shiftRight(21);
        $s1 = $s1->addInt64($carry0);
        $s0 = $s0->subInt64($carry0->shiftLeft(21));
        $carry1 = $s1->shiftRight(21);
        $s2 = $s2->addInt64($carry1);
        $s1 = $s1->subInt64($carry1->shiftLeft(21));
        $carry2 = $s2->shiftRight(21);
        $s3 = $s3->addInt64($carry2);
        $s2 = $s2->subInt64($carry2->shiftLeft(21));
        $carry3 = $s3->shiftRight(21);
        $s4 = $s4->addInt64($carry3);
        $s3 = $s3->subInt64($carry3->shiftLeft(21));
        $carry4 = $s4->shiftRight(21);
        $s5 = $s5->addInt64($carry4);
        $s4 = $s4->subInt64($carry4->shiftLeft(21));
        $carry5 = $s5->shiftRight(21);
        $s6 = $s6->addInt64($carry5);
        $s5 = $s5->subInt64($carry5->shiftLeft(21));
        $carry6 = $s6->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry7 = $s7->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry8 = $s10->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry9 = $s9->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry10 = $s10->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));

        $S0  =  $s0->toInt();
        $S1  =  $s1->toInt();
        $S2  =  $s2->toInt();
        $S3  =  $s3->toInt();
        $S4  =  $s4->toInt();
        $S5  =  $s5->toInt();
        $S6  =  $s6->toInt();
        $S7  =  $s7->toInt();
        $S8  =  $s8->toInt();
        $S9  =  $s9->toInt();
        $S10 = $s10->toInt();
        $S11 = $s11->toInt();

        /**
         * @var array<int, int>
         */
        $arr = array(
            (int) (0xff & ($S0 >> 0)),
            (int) (0xff & ($S0 >> 8)),
            (int) (0xff & (($S0 >> 16) | ($S1 << 5))),
            (int) (0xff & ($S1 >> 3)),
            (int) (0xff & ($S1 >> 11)),
            (int) (0xff & (($S1 >> 19) | ($S2 << 2))),
            (int) (0xff & ($S2 >> 6)),
            (int) (0xff & (($S2 >> 14) | ($S3 << 7))),
            (int) (0xff & ($S3 >> 1)),
            (int) (0xff & ($S3 >> 9)),
            (int) (0xff & (($S3 >> 17) | ($S4 << 4))),
            (int) (0xff & ($S4 >> 4)),
            (int) (0xff & ($S4 >> 12)),
            (int) (0xff & (($S4 >> 20) | ($S5 << 1))),
            (int) (0xff & ($S5 >> 7)),
            (int) (0xff & (($S5 >> 15) | ($S6 << 6))),
            (int) (0xff & ($S6 >> 2)),
            (int) (0xff & ($S6 >> 10)),
            (int) (0xff & (($S6 >> 18) | ($S7 << 3))),
            (int) (0xff & ($S7 >> 5)),
            (int) (0xff & ($S7 >> 13)),
            (int) (0xff & ($S8 >> 0)),
            (int) (0xff & ($S8 >> 8)),
            (int) (0xff & (($S8 >> 16) | ($S9 << 5))),
            (int) (0xff & ($S9 >> 3)),
            (int) (0xff & ($S9 >> 11)),
            (int) (0xff & (($S9 >> 19) | ($S10 << 2))),
            (int) (0xff & ($S10 >> 6)),
            (int) (0xff & (($S10 >> 14) | ($S11 << 7))),
            (int) (0xff & ($S11 >> 1)),
            (int) (0xff & ($S11 >> 9)),
            (int) (0xff & ($S11 >> 17))
        );
        return self::intArrayToString($arr);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $s
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function sc_reduce($s)
    {
        /**
         * @var ParagonIE_Sodium_Core32_Int64 $s0
         * @var ParagonIE_Sodium_Core32_Int64 $s1
         * @var ParagonIE_Sodium_Core32_Int64 $s2
         * @var ParagonIE_Sodium_Core32_Int64 $s3
         * @var ParagonIE_Sodium_Core32_Int64 $s4
         * @var ParagonIE_Sodium_Core32_Int64 $s5
         * @var ParagonIE_Sodium_Core32_Int64 $s6
         * @var ParagonIE_Sodium_Core32_Int64 $s7
         * @var ParagonIE_Sodium_Core32_Int64 $s8
         * @var ParagonIE_Sodium_Core32_Int64 $s9
         * @var ParagonIE_Sodium_Core32_Int64 $s10
         * @var ParagonIE_Sodium_Core32_Int64 $s11
         * @var ParagonIE_Sodium_Core32_Int64 $s12
         * @var ParagonIE_Sodium_Core32_Int64 $s13
         * @var ParagonIE_Sodium_Core32_Int64 $s14
         * @var ParagonIE_Sodium_Core32_Int64 $s15
         * @var ParagonIE_Sodium_Core32_Int64 $s16
         * @var ParagonIE_Sodium_Core32_Int64 $s17
         * @var ParagonIE_Sodium_Core32_Int64 $s18
         * @var ParagonIE_Sodium_Core32_Int64 $s19
         * @var ParagonIE_Sodium_Core32_Int64 $s20
         * @var ParagonIE_Sodium_Core32_Int64 $s21
         * @var ParagonIE_Sodium_Core32_Int64 $s22
         * @var ParagonIE_Sodium_Core32_Int64 $s23
         */
        $s0 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($s, 0, 3)));
        $s1 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 2, 4)) >> 5));
        $s2 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 5, 3)) >> 2));
        $s3 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 7, 4)) >> 7));
        $s4 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 10, 4)) >> 4));
        $s5 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 13, 3)) >> 1));
        $s6 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 15, 4)) >> 6));
        $s7 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 18, 4)) >> 3));
        $s8 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($s, 21, 3)));
        $s9 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 23, 4)) >> 5));
        $s10 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 26, 3)) >> 2));
        $s11 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 28, 4)) >> 7));
        $s12 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 31, 4)) >> 4));
        $s13 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 34, 3)) >> 1));
        $s14 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 36, 4)) >> 6));
        $s15 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 39, 4)) >> 3));
        $s16 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & self::load_3(self::substr($s, 42, 3)));
        $s17 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 44, 4)) >> 5));
        $s18 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 47, 3)) >> 2));
        $s19 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 49, 4)) >> 7));
        $s20 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 52, 4)) >> 4));
        $s21 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_3(self::substr($s, 55, 3)) >> 1));
        $s22 = ParagonIE_Sodium_Core32_Int64::fromInt(2097151 & (self::load_4(self::substr($s, 57, 4)) >> 6));
        $s23 = ParagonIE_Sodium_Core32_Int64::fromInt(0x1fffffff & (self::load_4(self::substr($s, 60, 4)) >> 3));

        $s11 = $s11->addInt64($s23->mulInt(666643, 20));
        $s12 = $s12->addInt64($s23->mulInt(470296, 19));
        $s13 = $s13->addInt64($s23->mulInt(654183, 20));
        $s14 = $s14->subInt64($s23->mulInt(997805, 20));
        $s15 = $s15->addInt64($s23->mulInt(136657, 18));
        $s16 = $s16->subInt64($s23->mulInt(683901, 20));

        $s10 = $s10->addInt64($s22->mulInt(666643, 20));
        $s11 = $s11->addInt64($s22->mulInt(470296, 19));
        $s12 = $s12->addInt64($s22->mulInt(654183, 20));
        $s13 = $s13->subInt64($s22->mulInt(997805, 20));
        $s14 = $s14->addInt64($s22->mulInt(136657, 18));
        $s15 = $s15->subInt64($s22->mulInt(683901, 20));

        $s9  =  $s9->addInt64($s21->mulInt(666643, 20));
        $s10 = $s10->addInt64($s21->mulInt(470296, 19));
        $s11 = $s11->addInt64($s21->mulInt(654183, 20));
        $s12 = $s12->subInt64($s21->mulInt(997805, 20));
        $s13 = $s13->addInt64($s21->mulInt(136657, 18));
        $s14 = $s14->subInt64($s21->mulInt(683901, 20));

        $s8  =  $s8->addInt64($s20->mulInt(666643, 20));
        $s9  =  $s9->addInt64($s20->mulInt(470296, 19));
        $s10 = $s10->addInt64($s20->mulInt(654183, 20));
        $s11 = $s11->subInt64($s20->mulInt(997805, 20));
        $s12 = $s12->addInt64($s20->mulInt(136657, 18));
        $s13 = $s13->subInt64($s20->mulInt(683901, 20));

        $s7  =  $s7->addInt64($s19->mulInt(666643, 20));
        $s8  =  $s8->addInt64($s19->mulInt(470296, 19));
        $s9  =  $s9->addInt64($s19->mulInt(654183, 20));
        $s10 = $s10->subInt64($s19->mulInt(997805, 20));
        $s11 = $s11->addInt64($s19->mulInt(136657, 18));
        $s12 = $s12->subInt64($s19->mulInt(683901, 20));

        $s6  =  $s6->addInt64($s18->mulInt(666643, 20));
        $s7  =  $s7->addInt64($s18->mulInt(470296, 19));
        $s8  =  $s8->addInt64($s18->mulInt(654183, 20));
        $s9  =  $s9->subInt64($s18->mulInt(997805, 20));
        $s10 = $s10->addInt64($s18->mulInt(136657, 18));
        $s11 = $s11->subInt64($s18->mulInt(683901, 20));

        $carry6 = $s6->addInt(1 << 20)->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry8 = $s8->addInt(1 << 20)->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry10 = $s10->addInt(1 << 20)->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));
        $carry12 = $s12->addInt(1 << 20)->shiftRight(21);
        $s13 = $s13->addInt64($carry12);
        $s12 = $s12->subInt64($carry12->shiftLeft(21));
        $carry14 = $s14->addInt(1 << 20)->shiftRight(21);
        $s15 = $s15->addInt64($carry14);
        $s14 = $s14->subInt64($carry14->shiftLeft(21));
        $carry16 = $s16->addInt(1 << 20)->shiftRight(21);
        $s17 = $s17->addInt64($carry16);
        $s16 = $s16->subInt64($carry16->shiftLeft(21));

        $carry7 = $s7->addInt(1 << 20)->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry9 = $s9->addInt(1 << 20)->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry11 = $s11->addInt(1 << 20)->shiftRight(21);
        $s12 = $s12->addInt64($carry11);
        $s11 = $s11->subInt64($carry11->shiftLeft(21));
        $carry13 = $s13->addInt(1 << 20)->shiftRight(21);
        $s14 = $s14->addInt64($carry13);
        $s13 = $s13->subInt64($carry13->shiftLeft(21));
        $carry15 = $s15->addInt(1 << 20)->shiftRight(21);
        $s16 = $s16->addInt64($carry15);
        $s15 = $s15->subInt64($carry15->shiftLeft(21));

        $s5  =  $s5->addInt64($s17->mulInt(666643, 20));
        $s6  =  $s6->addInt64($s17->mulInt(470296, 19));
        $s7  =  $s7->addInt64($s17->mulInt(654183, 20));
        $s8  =  $s8->subInt64($s17->mulInt(997805, 20));
        $s9  =  $s9->addInt64($s17->mulInt(136657, 18));
        $s10 = $s10->subInt64($s17->mulInt(683901, 20));

        $s4  =  $s4->addInt64($s16->mulInt(666643, 20));
        $s5  =  $s5->addInt64($s16->mulInt(470296, 19));
        $s6  =  $s6->addInt64($s16->mulInt(654183, 20));
        $s7  =  $s7->subInt64($s16->mulInt(997805, 20));
        $s8  =  $s8->addInt64($s16->mulInt(136657, 18));
        $s9  =  $s9->subInt64($s16->mulInt(683901, 20));

        $s3  =  $s3->addInt64($s15->mulInt(666643, 20));
        $s4  =  $s4->addInt64($s15->mulInt(470296, 19));
        $s5  =  $s5->addInt64($s15->mulInt(654183, 20));
        $s6  =  $s6->subInt64($s15->mulInt(997805, 20));
        $s7  =  $s7->addInt64($s15->mulInt(136657, 18));
        $s8  =  $s8->subInt64($s15->mulInt(683901, 20));

        $s2  =  $s2->addInt64($s14->mulInt(666643, 20));
        $s3  =  $s3->addInt64($s14->mulInt(470296, 19));
        $s4  =  $s4->addInt64($s14->mulInt(654183, 20));
        $s5  =  $s5->subInt64($s14->mulInt(997805, 20));
        $s6  =  $s6->addInt64($s14->mulInt(136657, 18));
        $s7  =  $s7->subInt64($s14->mulInt(683901, 20));

        $s1  =  $s1->addInt64($s13->mulInt(666643, 20));
        $s2  =  $s2->addInt64($s13->mulInt(470296, 19));
        $s3  =  $s3->addInt64($s13->mulInt(654183, 20));
        $s4  =  $s4->subInt64($s13->mulInt(997805, 20));
        $s5  =  $s5->addInt64($s13->mulInt(136657, 18));
        $s6  =  $s6->subInt64($s13->mulInt(683901, 20));

        $s0  =  $s0->addInt64($s12->mulInt(666643, 20));
        $s1  =  $s1->addInt64($s12->mulInt(470296, 19));
        $s2  =  $s2->addInt64($s12->mulInt(654183, 20));
        $s3  =  $s3->subInt64($s12->mulInt(997805, 20));
        $s4  =  $s4->addInt64($s12->mulInt(136657, 18));
        $s5  =  $s5->subInt64($s12->mulInt(683901, 20));
        $s12 = new ParagonIE_Sodium_Core32_Int64();

        $carry0 = $s0->addInt(1 << 20)->shiftRight(21);
        $s1 = $s1->addInt64($carry0);
        $s0 = $s0->subInt64($carry0->shiftLeft(21));
        $carry2 = $s2->addInt(1 << 20)->shiftRight(21);
        $s3 = $s3->addInt64($carry2);
        $s2 = $s2->subInt64($carry2->shiftLeft(21));
        $carry4 = $s4->addInt(1 << 20)->shiftRight(21);
        $s5 = $s5->addInt64($carry4);
        $s4 = $s4->subInt64($carry4->shiftLeft(21));
        $carry6 = $s6->addInt(1 << 20)->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry8 = $s8->addInt(1 << 20)->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry10 = $s10->addInt(1 << 20)->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));
        $carry1 = $s1->addInt(1 << 20)->shiftRight(21);
        $s2 = $s2->addInt64($carry1);
        $s1 = $s1->subInt64($carry1->shiftLeft(21));
        $carry3 = $s3->addInt(1 << 20)->shiftRight(21);
        $s4 = $s4->addInt64($carry3);
        $s3 = $s3->subInt64($carry3->shiftLeft(21));
        $carry5 = $s5->addInt(1 << 20)->shiftRight(21);
        $s6 = $s6->addInt64($carry5);
        $s5 = $s5->subInt64($carry5->shiftLeft(21));
        $carry7 = $s7->addInt(1 << 20)->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry9 = $s9->addInt(1 << 20)->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry11 = $s11->addInt(1 << 20)->shiftRight(21);
        $s12 = $s12->addInt64($carry11);
        $s11 = $s11->subInt64($carry11->shiftLeft(21));

        $s0  =  $s0->addInt64($s12->mulInt(666643, 20));
        $s1  =  $s1->addInt64($s12->mulInt(470296, 19));
        $s2  =  $s2->addInt64($s12->mulInt(654183, 20));
        $s3  =  $s3->subInt64($s12->mulInt(997805, 20));
        $s4  =  $s4->addInt64($s12->mulInt(136657, 18));
        $s5  =  $s5->subInt64($s12->mulInt(683901, 20));
        $s12 = new ParagonIE_Sodium_Core32_Int64();

        $carry0 = $s0->shiftRight(21);
        $s1 = $s1->addInt64($carry0);
        $s0 = $s0->subInt64($carry0->shiftLeft(21));
        $carry1 = $s1->shiftRight(21);
        $s2 = $s2->addInt64($carry1);
        $s1 = $s1->subInt64($carry1->shiftLeft(21));
        $carry2 = $s2->shiftRight(21);
        $s3 = $s3->addInt64($carry2);
        $s2 = $s2->subInt64($carry2->shiftLeft(21));
        $carry3 = $s3->shiftRight(21);
        $s4 = $s4->addInt64($carry3);
        $s3 = $s3->subInt64($carry3->shiftLeft(21));
        $carry4 = $s4->shiftRight(21);
        $s5 = $s5->addInt64($carry4);
        $s4 = $s4->subInt64($carry4->shiftLeft(21));
        $carry5 = $s5->shiftRight(21);
        $s6 = $s6->addInt64($carry5);
        $s5 = $s5->subInt64($carry5->shiftLeft(21));
        $carry6 = $s6->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry7 = $s7->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry8 = $s8->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry9 = $s9->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry10 = $s10->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));
        $carry11 = $s11->shiftRight(21);
        $s12 = $s12->addInt64($carry11);
        $s11 = $s11->subInt64($carry11->shiftLeft(21));

        $s0  =  $s0->addInt64($s12->mulInt(666643, 20));
        $s1  =  $s1->addInt64($s12->mulInt(470296, 19));
        $s2  =  $s2->addInt64($s12->mulInt(654183, 20));
        $s3  =  $s3->subInt64($s12->mulInt(997805, 20));
        $s4  =  $s4->addInt64($s12->mulInt(136657, 18));
        $s5  =  $s5->subInt64($s12->mulInt(683901, 20));

        $carry0 = $s0->shiftRight(21);
        $s1 = $s1->addInt64($carry0);
        $s0 = $s0->subInt64($carry0->shiftLeft(21));
        $carry1 = $s1->shiftRight(21);
        $s2 = $s2->addInt64($carry1);
        $s1 = $s1->subInt64($carry1->shiftLeft(21));
        $carry2 = $s2->shiftRight(21);
        $s3 = $s3->addInt64($carry2);
        $s2 = $s2->subInt64($carry2->shiftLeft(21));
        $carry3 = $s3->shiftRight(21);
        $s4 = $s4->addInt64($carry3);
        $s3 = $s3->subInt64($carry3->shiftLeft(21));
        $carry4 = $s4->shiftRight(21);
        $s5 = $s5->addInt64($carry4);
        $s4 = $s4->subInt64($carry4->shiftLeft(21));
        $carry5 = $s5->shiftRight(21);
        $s6 = $s6->addInt64($carry5);
        $s5 = $s5->subInt64($carry5->shiftLeft(21));
        $carry6 = $s6->shiftRight(21);
        $s7 = $s7->addInt64($carry6);
        $s6 = $s6->subInt64($carry6->shiftLeft(21));
        $carry7 = $s7->shiftRight(21);
        $s8 = $s8->addInt64($carry7);
        $s7 = $s7->subInt64($carry7->shiftLeft(21));
        $carry8 = $s8->shiftRight(21);
        $s9 = $s9->addInt64($carry8);
        $s8 = $s8->subInt64($carry8->shiftLeft(21));
        $carry9 = $s9->shiftRight(21);
        $s10 = $s10->addInt64($carry9);
        $s9 = $s9->subInt64($carry9->shiftLeft(21));
        $carry10 = $s10->shiftRight(21);
        $s11 = $s11->addInt64($carry10);
        $s10 = $s10->subInt64($carry10->shiftLeft(21));

        $S0 = $s0->toInt32()->toInt();
        $S1 = $s1->toInt32()->toInt();
        $S2 = $s2->toInt32()->toInt();
        $S3 = $s3->toInt32()->toInt();
        $S4 = $s4->toInt32()->toInt();
        $S5 = $s5->toInt32()->toInt();
        $S6 = $s6->toInt32()->toInt();
        $S7 = $s7->toInt32()->toInt();
        $S8 = $s8->toInt32()->toInt();
        $S9 = $s9->toInt32()->toInt();
        $S10 = $s10->toInt32()->toInt();
        $S11 = $s11->toInt32()->toInt();

        /**
         * @var array<int, int>
         */
        $arr = array(
            (int) ($S0 >> 0),
            (int) ($S0 >> 8),
            (int) (($S0 >> 16) | ($S1 << 5)),
            (int) ($S1 >> 3),
            (int) ($S1 >> 11),
            (int) (($S1 >> 19) | ($S2 << 2)),
            (int) ($S2 >> 6),
            (int) (($S2 >> 14) | ($S3 << 7)),
            (int) ($S3 >> 1),
            (int) ($S3 >> 9),
            (int) (($S3 >> 17) | ($S4 << 4)),
            (int) ($S4 >> 4),
            (int) ($S4 >> 12),
            (int) (($S4 >> 20) | ($S5 << 1)),
            (int) ($S5 >> 7),
            (int) (($S5 >> 15) | ($S6 << 6)),
            (int) ($S6 >> 2),
            (int) ($S6 >> 10),
            (int) (($S6 >> 18) | ($S7 << 3)),
            (int) ($S7 >> 5),
            (int) ($S7 >> 13),
            (int) ($S8 >> 0),
            (int) ($S8 >> 8),
            (int) (($S8 >> 16) | ($S9 << 5)),
            (int) ($S9 >> 3),
            (int) ($S9 >> 11),
            (int) (($S9 >> 19) | ($S10 << 2)),
            (int) ($S10 >> 6),
            (int) (($S10 >> 14) | ($S11 << 7)),
            (int) ($S11 >> 1),
            (int) ($S11 >> 9),
            (int) $S11 >> 17
        );
        return self::intArrayToString($arr);
    }

    /**
     * multiply by the order of the main subgroup l = 2^252+27742317777372353535851937790883648493
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $A
     * @return ParagonIE_Sodium_Core32_Curve25519_Ge_P3
     * @throws SodiumException
     * @throws TypeError
     */
    public static function ge_mul_l(ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $A)
    {
        /** @var array<int, int> $aslide */
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

        /** @var array<int, ParagonIE_Sodium_Core32_Curve25519_Ge_Cached> $Ai size 8 */
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
}
