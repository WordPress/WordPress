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
        $b = -$b;
        $x0 = ($f->e0 ^ $g->e0) & $b;
        $x1 = ($f->e1 ^ $g->e1) & $b;
        $x2 = ($f->e2 ^ $g->e2) & $b;
        $x3 = ($f->e3 ^ $g->e3) & $b;
        $x4 = ($f->e4 ^ $g->e4) & $b;
        $x5 = ($f->e5 ^ $g->e5) & $b;
        $x6 = ($f->e6 ^ $g->e6) & $b;
        $x7 = ($f->e7 ^ $g->e7) & $b;
        $x8 = ($f->e8 ^ $g->e8) & $b;
        $x9 = ($f->e9 ^ $g->e9) & $b;
        $f->e0 ^= $x0;
        $f->e1 ^= $x1;
        $f->e2 ^= $x2;
        $f->e3 ^= $x3;
        $f->e4 ^= $x4;
        $f->e5 ^= $x5;
        $f->e6 ^= $x6;
        $f->e7 ^= $x7;
        $f->e8 ^= $x8;
        $f->e9 ^= $x9;
        $g->e0 ^= $x0;
        $g->e1 ^= $x1;
        $g->e2 ^= $x2;
        $g->e3 ^= $x3;
        $g->e4 ^= $x4;
        $g->e5 ^= $x5;
        $g->e6 ^= $x6;
        $g->e7 ^= $x7;
        $g->e8 ^= $x8;
        $g->e9 ^= $x9;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_mul121666(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        $h0 = self::mul($f->e0, 121666, 17);
        $h1 = self::mul($f->e1, 121666, 17);
        $h2 = self::mul($f->e2, 121666, 17);
        $h3 = self::mul($f->e3, 121666, 17);
        $h4 = self::mul($f->e4, 121666, 17);
        $h5 = self::mul($f->e5, 121666, 17);
        $h6 = self::mul($f->e6, 121666, 17);
        $h7 = self::mul($f->e7, 121666, 17);
        $h8 = self::mul($f->e8, 121666, 17);
        $h9 = self::mul($f->e9, 121666, 17);

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
        return new ParagonIE_Sodium_Core_Curve25519_Fe($h0, $h1, $h2, $h3, $h4, $h5, $h6, $h7, $h8, $h9);
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
