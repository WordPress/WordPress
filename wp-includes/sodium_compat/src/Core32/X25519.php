<?php

if (class_exists('ParagonIE_Sodium_Core32_X25519', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core32_X25519
 */
abstract class ParagonIE_Sodium_Core32_X25519 extends ParagonIE_Sodium_Core32_Curve25519
{
    /**
     * Alters the objects passed to this method in place.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $g
     * @param int $b
     * @return void
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_cswap(
        ParagonIE_Sodium_Core32_Curve25519_Fe $f,
        ParagonIE_Sodium_Core32_Curve25519_Fe $g,
        $b = 0
    ) {
        $f0 = (int) $f[0]->toInt();
        $f1 = (int) $f[1]->toInt();
        $f2 = (int) $f[2]->toInt();
        $f3 = (int) $f[3]->toInt();
        $f4 = (int) $f[4]->toInt();
        $f5 = (int) $f[5]->toInt();
        $f6 = (int) $f[6]->toInt();
        $f7 = (int) $f[7]->toInt();
        $f8 = (int) $f[8]->toInt();
        $f9 = (int) $f[9]->toInt();
        $g0 = (int) $g[0]->toInt();
        $g1 = (int) $g[1]->toInt();
        $g2 = (int) $g[2]->toInt();
        $g3 = (int) $g[3]->toInt();
        $g4 = (int) $g[4]->toInt();
        $g5 = (int) $g[5]->toInt();
        $g6 = (int) $g[6]->toInt();
        $g7 = (int) $g[7]->toInt();
        $g8 = (int) $g[8]->toInt();
        $g9 = (int) $g[9]->toInt();
        $b = -$b;
        /** @var int $x0 */
        $x0 = ($f0 ^ $g0) & $b;
        /** @var int $x1 */
        $x1 = ($f1 ^ $g1) & $b;
        /** @var int $x2 */
        $x2 = ($f2 ^ $g2) & $b;
        /** @var int $x3 */
        $x3 = ($f3 ^ $g3) & $b;
        /** @var int $x4 */
        $x4 = ($f4 ^ $g4) & $b;
        /** @var int $x5 */
        $x5 = ($f5 ^ $g5) & $b;
        /** @var int $x6 */
        $x6 = ($f6 ^ $g6) & $b;
        /** @var int $x7 */
        $x7 = ($f7 ^ $g7) & $b;
        /** @var int $x8 */
        $x8 = ($f8 ^ $g8) & $b;
        /** @var int $x9 */
        $x9 = ($f9 ^ $g9) & $b;
        $f[0] = ParagonIE_Sodium_Core32_Int32::fromInt($f0 ^ $x0);
        $f[1] = ParagonIE_Sodium_Core32_Int32::fromInt($f1 ^ $x1);
        $f[2] = ParagonIE_Sodium_Core32_Int32::fromInt($f2 ^ $x2);
        $f[3] = ParagonIE_Sodium_Core32_Int32::fromInt($f3 ^ $x3);
        $f[4] = ParagonIE_Sodium_Core32_Int32::fromInt($f4 ^ $x4);
        $f[5] = ParagonIE_Sodium_Core32_Int32::fromInt($f5 ^ $x5);
        $f[6] = ParagonIE_Sodium_Core32_Int32::fromInt($f6 ^ $x6);
        $f[7] = ParagonIE_Sodium_Core32_Int32::fromInt($f7 ^ $x7);
        $f[8] = ParagonIE_Sodium_Core32_Int32::fromInt($f8 ^ $x8);
        $f[9] = ParagonIE_Sodium_Core32_Int32::fromInt($f9 ^ $x9);
        $g[0] = ParagonIE_Sodium_Core32_Int32::fromInt($g0 ^ $x0);
        $g[1] = ParagonIE_Sodium_Core32_Int32::fromInt($g1 ^ $x1);
        $g[2] = ParagonIE_Sodium_Core32_Int32::fromInt($g2 ^ $x2);
        $g[3] = ParagonIE_Sodium_Core32_Int32::fromInt($g3 ^ $x3);
        $g[4] = ParagonIE_Sodium_Core32_Int32::fromInt($g4 ^ $x4);
        $g[5] = ParagonIE_Sodium_Core32_Int32::fromInt($g5 ^ $x5);
        $g[6] = ParagonIE_Sodium_Core32_Int32::fromInt($g6 ^ $x6);
        $g[7] = ParagonIE_Sodium_Core32_Int32::fromInt($g7 ^ $x7);
        $g[8] = ParagonIE_Sodium_Core32_Int32::fromInt($g8 ^ $x8);
        $g[9] = ParagonIE_Sodium_Core32_Int32::fromInt($g9 ^ $x9);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedMethodCall
     */
    public static function fe_mul121666(ParagonIE_Sodium_Core32_Curve25519_Fe $f)
    {
        /** @var array<int, ParagonIE_Sodium_Core32_Int32> $h */
        $h = array();
        for ($i = 0; $i < 10; ++$i) {
            $h[$i] = $f[$i]->toInt64()->mulInt(121666, 17);
        }

        /** @var ParagonIE_Sodium_Core32_Int32 $carry9 */
        $carry9 = $h[9]->addInt(1 << 24)->shiftRight(25);
        $h[0] = $h[0]->addInt64($carry9->mulInt(19, 5));
        $h[9] = $h[9]->subInt64($carry9->shiftLeft(25));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry1 */
        $carry1 = $h[1]->addInt(1 << 24)->shiftRight(25);
        $h[2] = $h[2]->addInt64($carry1);
        $h[1] = $h[1]->subInt64($carry1->shiftLeft(25));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry3 */
        $carry3 = $h[3]->addInt(1 << 24)->shiftRight(25);
        $h[4] = $h[4]->addInt64($carry3);
        $h[3] = $h[3]->subInt64($carry3->shiftLeft(25));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry5 */
        $carry5 = $h[5]->addInt(1 << 24)->shiftRight(25);
        $h[6] = $h[6]->addInt64($carry5);
        $h[5] = $h[5]->subInt64($carry5->shiftLeft(25));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry7 */
        $carry7 = $h[7]->addInt(1 << 24)->shiftRight(25);
        $h[8] = $h[8]->addInt64($carry7);
        $h[7] = $h[7]->subInt64($carry7->shiftLeft(25));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry0 */
        $carry0 = $h[0]->addInt(1 << 25)->shiftRight(26);
        $h[1] = $h[1]->addInt64($carry0);
        $h[0] = $h[0]->subInt64($carry0->shiftLeft(26));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry2 */
        $carry2 = $h[2]->addInt(1 << 25)->shiftRight(26);
        $h[3] = $h[3]->addInt64($carry2);
        $h[2] = $h[2]->subInt64($carry2->shiftLeft(26));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry4 */
        $carry4 = $h[4]->addInt(1 << 25)->shiftRight(26);
        $h[5] = $h[5]->addInt64($carry4);
        $h[4] = $h[4]->subInt64($carry4->shiftLeft(26));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry6 */
        $carry6 = $h[6]->addInt(1 << 25)->shiftRight(26);
        $h[7] = $h[7]->addInt64($carry6);
        $h[6] = $h[6]->subInt64($carry6->shiftLeft(26));

        /** @var ParagonIE_Sodium_Core32_Int32 $carry8 */
        $carry8 = $h[8]->addInt(1 << 25)->shiftRight(26);
        $h[9] = $h[9]->addInt64($carry8);
        $h[8] = $h[8]->subInt64($carry8->shiftLeft(26));

        for ($i = 0; $i < 10; ++$i) {
            $h[$i] = $h[$i]->toInt32();
        }
        /** @var array<int, ParagonIE_Sodium_Core32_Int32> $h */
        return ParagonIE_Sodium_Core32_Curve25519_Fe::fromArray($h);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * Inline comments preceded by # are from libsodium's ref10 code.
     *
     * @param string $n
     * @param string $p
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function crypto_scalarmult_curve25519_ref10($n, $p)
    {
        # for (i = 0;i < 32;++i) e[i] = n[i];
        $e = '' . $n;
        # e[0] &= 248;
        $e[0] = self::intToChr(
            self::chrToInt($e[0]) & 248
        );
        # e[31] &= 127;
        # e[31] |= 64;
        $e[31] = self::intToChr(
            (self::chrToInt($e[31]) & 127) | 64
        );
        # fe_frombytes(x1,p);
        $x1 = self::fe_frombytes($p);
        # fe_1(x2);
        $x2 = self::fe_1();
        # fe_0(z2);
        $z2 = self::fe_0();
        # fe_copy(x3,x1);
        $x3 = self::fe_copy($x1);
        # fe_1(z3);
        $z3 = self::fe_1();

        # swap = 0;
        /** @var int $swap */
        $swap = 0;

        # for (pos = 254;pos >= 0;--pos) {
        for ($pos = 254; $pos >= 0; --$pos) {
            # b = e[pos / 8] >> (pos & 7);
            /** @var int $b */
            $b = self::chrToInt(
                    $e[(int) floor($pos / 8)]
                ) >> ($pos & 7);
            # b &= 1;
            $b &= 1;

            # swap ^= b;
            $swap ^= $b;

            # fe_cswap(x2,x3,swap);
            self::fe_cswap($x2, $x3, $swap);

            # fe_cswap(z2,z3,swap);
            self::fe_cswap($z2, $z3, $swap);

            # swap = b;
            /** @var int $swap */
            $swap = $b;

            # fe_sub(tmp0,x3,z3);
            $tmp0 = self::fe_sub($x3, $z3);

            # fe_sub(tmp1,x2,z2);
            $tmp1 = self::fe_sub($x2, $z2);

            # fe_add(x2,x2,z2);
            $x2 = self::fe_add($x2, $z2);

            # fe_add(z2,x3,z3);
            $z2 = self::fe_add($x3, $z3);

            # fe_mul(z3,tmp0,x2);
            $z3 = self::fe_mul($tmp0, $x2);

            # fe_mul(z2,z2,tmp1);
            $z2 = self::fe_mul($z2, $tmp1);

            # fe_sq(tmp0,tmp1);
            $tmp0 = self::fe_sq($tmp1);

            # fe_sq(tmp1,x2);
            $tmp1 = self::fe_sq($x2);

            # fe_add(x3,z3,z2);
            $x3 = self::fe_add($z3, $z2);

            # fe_sub(z2,z3,z2);
            $z2 = self::fe_sub($z3, $z2);

            # fe_mul(x2,tmp1,tmp0);
            $x2 = self::fe_mul($tmp1, $tmp0);

            # fe_sub(tmp1,tmp1,tmp0);
            $tmp1 = self::fe_sub($tmp1, $tmp0);

            # fe_sq(z2,z2);
            $z2 = self::fe_sq($z2);

            # fe_mul121666(z3,tmp1);
            $z3 = self::fe_mul121666($tmp1);

            # fe_sq(x3,x3);
            $x3 = self::fe_sq($x3);

            # fe_add(tmp0,tmp0,z3);
            $tmp0 = self::fe_add($tmp0, $z3);

            # fe_mul(z3,x1,z2);
            $z3 = self::fe_mul($x1, $z2);

            # fe_mul(z2,tmp1,tmp0);
            $z2 = self::fe_mul($tmp1, $tmp0);
        }

        # fe_cswap(x2,x3,swap);
        self::fe_cswap($x2, $x3, $swap);

        # fe_cswap(z2,z3,swap);
        self::fe_cswap($z2, $z3, $swap);

        # fe_invert(z2,z2);
        $z2 = self::fe_invert($z2);

        # fe_mul(x2,x2,z2);
        $x2 = self::fe_mul($x2, $z2);
        # fe_tobytes(q,x2);
        return (string) self::fe_tobytes($x2);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $edwardsY
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe $edwardsZ
     * @return ParagonIE_Sodium_Core32_Curve25519_Fe
     * @throws SodiumException
     * @throws TypeError
     */
    public static function edwards_to_montgomery(
        ParagonIE_Sodium_Core32_Curve25519_Fe $edwardsY,
        ParagonIE_Sodium_Core32_Curve25519_Fe $edwardsZ
    ) {
        $tempX = self::fe_add($edwardsZ, $edwardsY);
        $tempZ = self::fe_sub($edwardsZ, $edwardsY);
        $tempZ = self::fe_invert($tempZ);
        return self::fe_mul($tempX, $tempZ);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $n
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function crypto_scalarmult_curve25519_ref10_base($n)
    {
        # for (i = 0;i < 32;++i) e[i] = n[i];
        $e = '' . $n;

        # e[0] &= 248;
        $e[0] = self::intToChr(
            self::chrToInt($e[0]) & 248
        );

        # e[31] &= 127;
        # e[31] |= 64;
        $e[31] = self::intToChr(
            (self::chrToInt($e[31]) & 127) | 64
        );

        $A = self::ge_scalarmult_base($e);
        if (
            !($A->Y instanceof ParagonIE_Sodium_Core32_Curve25519_Fe)
                ||
            !($A->Z instanceof ParagonIE_Sodium_Core32_Curve25519_Fe)
        ) {
            throw new TypeError('Null points encountered');
        }
        $pk = self::edwards_to_montgomery($A->Y, $A->Z);
        return self::fe_tobytes($pk);
    }
}
