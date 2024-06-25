<?php

if (class_exists('ParagonIE_Sodium_Core_X25519', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_X25519
 */
abstract class ParagonIE_Sodium_Core_X25519 extends ParagonIE_Sodium_Core_Curve25519
{
    /**
     * Alters the objects passed to this method in place.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $g
     * @param int $b
     * @return void
     * @psalm-suppress MixedAssignment
     */
    public static function fe_cswap(
        ParagonIE_Sodium_Core_Curve25519_Fe $f,
        ParagonIE_Sodium_Core_Curve25519_Fe $g,
        $b = 0
    ) {
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
        $g0 = (int) $g[0];
        $g1 = (int) $g[1];
        $g2 = (int) $g[2];
        $g3 = (int) $g[3];
        $g4 = (int) $g[4];
        $g5 = (int) $g[5];
        $g6 = (int) $g[6];
        $g7 = (int) $g[7];
        $g8 = (int) $g[8];
        $g9 = (int) $g[9];
        $b = -$b;
        $x0 = ($f0 ^ $g0) & $b;
        $x1 = ($f1 ^ $g1) & $b;
        $x2 = ($f2 ^ $g2) & $b;
        $x3 = ($f3 ^ $g3) & $b;
        $x4 = ($f4 ^ $g4) & $b;
        $x5 = ($f5 ^ $g5) & $b;
        $x6 = ($f6 ^ $g6) & $b;
        $x7 = ($f7 ^ $g7) & $b;
        $x8 = ($f8 ^ $g8) & $b;
        $x9 = ($f9 ^ $g9) & $b;
        $f[0] = $f0 ^ $x0;
        $f[1] = $f1 ^ $x1;
        $f[2] = $f2 ^ $x2;
        $f[3] = $f3 ^ $x3;
        $f[4] = $f4 ^ $x4;
        $f[5] = $f5 ^ $x5;
        $f[6] = $f6 ^ $x6;
        $f[7] = $f7 ^ $x7;
        $f[8] = $f8 ^ $x8;
        $f[9] = $f9 ^ $x9;
        $g[0] = $g0 ^ $x0;
        $g[1] = $g1 ^ $x1;
        $g[2] = $g2 ^ $x2;
        $g[3] = $g3 ^ $x3;
        $g[4] = $g4 ^ $x4;
        $g[5] = $g5 ^ $x5;
        $g[6] = $g6 ^ $x6;
        $g[7] = $g7 ^ $x7;
        $g[8] = $g8 ^ $x8;
        $g[9] = $g9 ^ $x9;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_mul121666(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $h = array(
            self::mul((int) $f[0], 121666, 17),
            self::mul((int) $f[1], 121666, 17),
            self::mul((int) $f[2], 121666, 17),
            self::mul((int) $f[3], 121666, 17),
            self::mul((int) $f[4], 121666, 17),
            self::mul((int) $f[5], 121666, 17),
            self::mul((int) $f[6], 121666, 17),
            self::mul((int) $f[7], 121666, 17),
            self::mul((int) $f[8], 121666, 17),
            self::mul((int) $f[9], 121666, 17)
        );

        /** @var int $carry9 */
        $carry9 = ($h[9] + (1 << 24)) >> 25;
        $h[0] += self::mul($carry9, 19, 5);
        $h[9] -= $carry9 << 25;
        /** @var int $carry1 */
        $carry1 = ($h[1] + (1 << 24)) >> 25;
        $h[2] += $carry1;
        $h[1] -= $carry1 << 25;
        /** @var int $carry3 */
        $carry3 = ($h[3] + (1 << 24)) >> 25;
        $h[4] += $carry3;
        $h[3] -= $carry3 << 25;
        /** @var int $carry5 */
        $carry5 = ($h[5] + (1 << 24)) >> 25;
        $h[6] += $carry5;
        $h[5] -= $carry5 << 25;
        /** @var int $carry7 */
        $carry7 = ($h[7] + (1 << 24)) >> 25;
        $h[8] += $carry7;
        $h[7] -= $carry7 << 25;

        /** @var int $carry0 */
        $carry0 = ($h[0] + (1 << 25)) >> 26;
        $h[1] += $carry0;
        $h[0] -= $carry0 << 26;
        /** @var int $carry2 */
        $carry2 = ($h[2] + (1 << 25)) >> 26;
        $h[3] += $carry2;
        $h[2] -= $carry2 << 26;
        /** @var int $carry4 */
        $carry4 = ($h[4] + (1 << 25)) >> 26;
        $h[5] += $carry4;
        $h[4] -= $carry4 << 26;
        /** @var int $carry6 */
        $carry6 = ($h[6] + (1 << 25)) >> 26;
        $h[7] += $carry6;
        $h[6] -= $carry6 << 26;
        /** @var int $carry8 */
        $carry8 = ($h[8] + (1 << 25)) >> 26;
        $h[9] += $carry8;
        $h[8] -= $carry8 << 26;

        foreach ($h as $i => $value) {
            $h[$i] = (int) $value;
        }
        return ParagonIE_Sodium_Core_Curve25519_Fe::fromArray($h);
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
        return self::fe_tobytes($x2);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $edwardsY
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $edwardsZ
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function edwards_to_montgomery(
        ParagonIE_Sodium_Core_Curve25519_Fe $edwardsY,
        ParagonIE_Sodium_Core_Curve25519_Fe $edwardsZ
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
            !($A->Y instanceof ParagonIE_Sodium_Core_Curve25519_Fe)
                ||
            !($A->Z instanceof ParagonIE_Sodium_Core_Curve25519_Fe)
        ) {
            throw new TypeError('Null points encountered');
        }
        $pk = self::edwards_to_montgomery($A->Y, $A->Z);
        return self::fe_tobytes($pk);
    }
}
