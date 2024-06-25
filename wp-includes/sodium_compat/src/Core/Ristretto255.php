<?php

/**
 * Class ParagonIE_Sodium_Core_Ristretto255
 */
class ParagonIE_Sodium_Core_Ristretto255 extends ParagonIE_Sodium_Core_Ed25519
{
    const crypto_core_ristretto255_HASHBYTES = 64;
    const HASH_SC_L = 48;
    const CORE_H2C_SHA256 = 1;
    const CORE_H2C_SHA512 = 2;

    /**
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @param int $b
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public static function fe_cneg(ParagonIE_Sodium_Core_Curve25519_Fe $f, $b)
    {
        $negf = self::fe_neg($f);
        return self::fe_cmov($f, $negf, $b);
    }

    /**
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return ParagonIE_Sodium_Core_Curve25519_Fe
     * @throws SodiumException
     */
    public static function fe_abs(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        return self::fe_cneg($f, self::fe_isnegative($f));
    }

    /**
     * Returns 0 if this field element results in all NUL bytes.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $f
     * @return int
     * @throws SodiumException
     */
    public static function fe_iszero(ParagonIE_Sodium_Core_Curve25519_Fe $f)
    {
        static $zero;
        if ($zero === null) {
            $zero = str_repeat("\x00", 32);
        }
        /** @var string $zero */
        $str = self::fe_tobytes($f);

        $d = 0;
        for ($i = 0; $i < 32; ++$i) {
            $d |= self::chrToInt($str[$i]);
        }
        return (($d - 1) >> 31) & 1;
    }


    /**
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $u
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $v
     * @return array{x: ParagonIE_Sodium_Core_Curve25519_Fe, nonsquare: int}
     *
     * @throws SodiumException
     */
    public static function ristretto255_sqrt_ratio_m1(
        ParagonIE_Sodium_Core_Curve25519_Fe $u,
        ParagonIE_Sodium_Core_Curve25519_Fe $v
    ) {
        $sqrtm1 = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$sqrtm1);

        $v3 = self::fe_mul(
            self::fe_sq($v),
            $v
        ); /* v3 = v^3 */
        $x = self::fe_mul(
            self::fe_mul(
                self::fe_sq($v3),
                $u
            ),
            $v
        ); /* x = uv^7 */

        $x = self::fe_mul(
            self::fe_mul(
                self::fe_pow22523($x), /* x = (uv^7)^((q-5)/8) */
                $v3
            ),
            $u
        ); /* x = uv^3(uv^7)^((q-5)/8) */

        $vxx = self::fe_mul(
            self::fe_sq($x),
            $v
        ); /* vx^2 */

        $m_root_check = self::fe_sub($vxx, $u); /* vx^2-u */
        $p_root_check = self::fe_add($vxx, $u); /* vx^2+u */
        $f_root_check = self::fe_mul($u, $sqrtm1); /* u*sqrt(-1) */
        $f_root_check = self::fe_add($vxx, $f_root_check); /* vx^2+u*sqrt(-1) */

        $has_m_root = self::fe_iszero($m_root_check);
        $has_p_root = self::fe_iszero($p_root_check);
        $has_f_root = self::fe_iszero($f_root_check);

        $x_sqrtm1 = self::fe_mul($x, $sqrtm1); /* x*sqrt(-1) */

        $x = self::fe_abs(
            self::fe_cmov($x, $x_sqrtm1, $has_p_root | $has_f_root)
        );
        return array(
            'x' => $x,
            'nonsquare' => $has_m_root | $has_p_root
        );
    }

    /**
     * @param string $s
     * @return int
     * @throws SodiumException
     */
    public static function ristretto255_point_is_canonical($s)
    {
        $c = (self::chrToInt($s[31]) & 0x7f) ^ 0x7f;
        for ($i = 30; $i > 0; --$i) {
            $c |= self::chrToInt($s[$i]) ^ 0xff;
        }
        $c = ($c - 1) >> 8;
        $d = (0xed - 1 - self::chrToInt($s[0])) >> 8;
        $e = self::chrToInt($s[31]) >> 7;

        return 1 - ((($c & $d) | $e | self::chrToInt($s[0])) & 1);
    }

    /**
     * @param string $s
     * @param bool $skipCanonicalCheck
     * @return array{h: ParagonIE_Sodium_Core_Curve25519_Ge_P3, res: int}
     * @throws SodiumException
     */
    public static function ristretto255_frombytes($s, $skipCanonicalCheck = false)
    {
        if (!$skipCanonicalCheck) {
            if (!self::ristretto255_point_is_canonical($s)) {
                throw new SodiumException('S is not canonical');
            }
        }

        $s_ = self::fe_frombytes($s);
        $ss = self::fe_sq($s_); /* ss = s^2 */

        $u1 = self::fe_sub(self::fe_1(), $ss); /* u1 = 1-ss */
        $u1u1 = self::fe_sq($u1); /* u1u1 = u1^2 */

        $u2 = self::fe_add(self::fe_1(), $ss); /* u2 = 1+ss */
        $u2u2 = self::fe_sq($u2); /* u2u2 = u2^2 */

        $v = self::fe_mul(
            ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$d),
            $u1u1
        ); /* v = d*u1^2 */
        $v = self::fe_neg($v); /* v = -d*u1^2 */
        $v = self::fe_sub($v, $u2u2); /* v = -(d*u1^2)-u2^2 */
        $v_u2u2 = self::fe_mul($v, $u2u2); /* v_u2u2 = v*u2^2 */

        // fe25519_1(one);
        // notsquare = ristretto255_sqrt_ratio_m1(inv_sqrt, one, v_u2u2);
        $one = self::fe_1();
        $result = self::ristretto255_sqrt_ratio_m1($one, $v_u2u2);
        $inv_sqrt = $result['x'];
        $notsquare = $result['nonsquare'];

        $h = new ParagonIE_Sodium_Core_Curve25519_Ge_P3();

        $h->X = self::fe_mul($inv_sqrt, $u2);
        $h->Y = self::fe_mul(self::fe_mul($inv_sqrt, $h->X), $v);

        $h->X = self::fe_mul($h->X, $s_);
        $h->X = self::fe_abs(
            self::fe_add($h->X, $h->X)
        );
        $h->Y = self::fe_mul($u1, $h->Y);
        $h->Z = self::fe_1();
        $h->T = self::fe_mul($h->X, $h->Y);

        $res = - ((1 - $notsquare) | self::fe_isnegative($h->T) | self::fe_iszero($h->Y));
        return array('h' => $h, 'res' => $res);
    }

    /**
     * @param ParagonIE_Sodium_Core_Curve25519_Ge_P3 $h
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_p3_tobytes(ParagonIE_Sodium_Core_Curve25519_Ge_P3 $h)
    {
        $sqrtm1 = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$sqrtm1);
        $invsqrtamd = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$invsqrtamd);

        $u1 = self::fe_add($h->Z, $h->Y); /* u1 = Z+Y */
        $zmy = self::fe_sub($h->Z, $h->Y); /* zmy = Z-Y */
        $u1 = self::fe_mul($u1, $zmy); /* u1 = (Z+Y)*(Z-Y) */
        $u2 = self::fe_mul($h->X, $h->Y); /* u2 = X*Y */

        $u1_u2u2 = self::fe_mul(self::fe_sq($u2), $u1); /* u1_u2u2 = u1*u2^2 */
        $one = self::fe_1();

        // fe25519_1(one);
        // (void) ristretto255_sqrt_ratio_m1(inv_sqrt, one, u1_u2u2);
        $result = self::ristretto255_sqrt_ratio_m1($one, $u1_u2u2);
        $inv_sqrt = $result['x'];

        $den1 = self::fe_mul($inv_sqrt, $u1); /* den1 = inv_sqrt*u1 */
        $den2 = self::fe_mul($inv_sqrt, $u2); /* den2 = inv_sqrt*u2 */
        $z_inv = self::fe_mul($h->T, self::fe_mul($den1, $den2)); /* z_inv = den1*den2*T */

        $ix = self::fe_mul($h->X, $sqrtm1); /* ix = X*sqrt(-1) */
        $iy = self::fe_mul($h->Y, $sqrtm1); /* iy = Y*sqrt(-1) */
        $eden = self::fe_mul($den1, $invsqrtamd);

        $t_z_inv =  self::fe_mul($h->T, $z_inv); /* t_z_inv = T*z_inv */
        $rotate = self::fe_isnegative($t_z_inv);

        $x_ = self::fe_copy($h->X);
        $y_ = self::fe_copy($h->Y);
        $den_inv = self::fe_copy($den2);

        $x_ = self::fe_cmov($x_, $iy, $rotate);
        $y_ = self::fe_cmov($y_, $ix, $rotate);
        $den_inv = self::fe_cmov($den_inv, $eden, $rotate);

        $x_z_inv = self::fe_mul($x_, $z_inv);
        $y_ = self::fe_cneg($y_, self::fe_isnegative($x_z_inv));


        // fe25519_sub(s_, h->Z, y_);
        // fe25519_mul(s_, den_inv, s_);
        // fe25519_abs(s_, s_);
        // fe25519_tobytes(s, s_);
        return self::fe_tobytes(
            self::fe_abs(
                self::fe_mul(
                    $den_inv,
                    self::fe_sub($h->Z, $y_)
                )
            )
        );
    }

    /**
     * @param ParagonIE_Sodium_Core_Curve25519_Fe $t
     * @return ParagonIE_Sodium_Core_Curve25519_Ge_P3
     *
     * @throws SodiumException
     */
    public static function ristretto255_elligator(ParagonIE_Sodium_Core_Curve25519_Fe $t)
    {
        $sqrtm1   = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$sqrtm1);
        $onemsqd  = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$onemsqd);
        $d        = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$d);
        $sqdmone  = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$sqdmone);
        $sqrtadm1 = ParagonIE_Sodium_Core_Curve25519_Fe::fromArray(self::$sqrtadm1);

        $one = self::fe_1();
        $r   = self::fe_mul($sqrtm1, self::fe_sq($t));         /* r = sqrt(-1)*t^2 */
        $u   = self::fe_mul(self::fe_add($r, $one), $onemsqd); /* u = (r+1)*(1-d^2) */
        $c   = self::fe_neg(self::fe_1());                     /* c = -1 */
        $rpd = self::fe_add($r, $d);                           /* rpd = r+d */

        $v = self::fe_mul(
            self::fe_sub(
                $c,
                self::fe_mul($r, $d)
            ),
            $rpd
        ); /* v = (c-r*d)*(r+d) */

        $result = self::ristretto255_sqrt_ratio_m1($u, $v);
        $s = $result['x'];
        $wasnt_square = 1 - $result['nonsquare'];

        $s_prime = self::fe_neg(
            self::fe_abs(
                self::fe_mul($s, $t)
            )
        ); /* s_prime = -|s*t| */
        $s = self::fe_cmov($s, $s_prime, $wasnt_square);
        $c = self::fe_cmov($c, $r, $wasnt_square);

        // fe25519_sub(n, r, one);            /* n = r-1 */
        // fe25519_mul(n, n, c);              /* n = c*(r-1) */
        // fe25519_mul(n, n, ed25519_sqdmone); /* n = c*(r-1)*(d-1)^2 */
        // fe25519_sub(n, n, v);              /* n =  c*(r-1)*(d-1)^2-v */
        $n = self::fe_sub(
            self::fe_mul(
                self::fe_mul(
                    self::fe_sub($r, $one),
                    $c
                ),
                $sqdmone
            ),
            $v
        ); /* n =  c*(r-1)*(d-1)^2-v */

        $w0 = self::fe_mul(
            self::fe_add($s, $s),
            $v
        ); /* w0 = 2s*v */

        $w1 = self::fe_mul($n, $sqrtadm1); /* w1 = n*sqrt(ad-1) */
        $ss = self::fe_sq($s); /* ss = s^2 */
        $w2 = self::fe_sub($one, $ss); /* w2 = 1-s^2 */
        $w3 = self::fe_add($one, $ss); /* w3 = 1+s^2 */

        return new ParagonIE_Sodium_Core_Curve25519_Ge_P3(
            self::fe_mul($w0, $w3),
            self::fe_mul($w2, $w1),
            self::fe_mul($w1, $w3),
            self::fe_mul($w0, $w2)
        );
    }

    /**
     * @param string $h
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_from_hash($h)
    {
        if (self::strlen($h) !== 64) {
            throw new SodiumException('Hash must be 64 bytes');
        }
        //fe25519_frombytes(r0, h);
        //fe25519_frombytes(r1, h + 32);
        $r0 = self::fe_frombytes(self::substr($h, 0, 32));
        $r1 = self::fe_frombytes(self::substr($h, 32, 32));

        //ristretto255_elligator(&p0, r0);
        //ristretto255_elligator(&p1, r1);
        $p0 = self::ristretto255_elligator($r0);
        $p1 = self::ristretto255_elligator($r1);

        //ge25519_p3_to_cached(&p1_cached, &p1);
        //ge25519_add_cached(&p_p1p1, &p0, &p1_cached);
        $p_p1p1 = self::ge_add(
            $p0,
            self::ge_p3_to_cached($p1)
        );

        //ge25519_p1p1_to_p3(&p, &p_p1p1);
        //ristretto255_p3_tobytes(s, &p);
        return self::ristretto255_p3_tobytes(
            self::ge_p1p1_to_p3($p_p1p1)
        );
    }

    /**
     * @param string $p
     * @return int
     * @throws SodiumException
     */
    public static function is_valid_point($p)
    {
        $result = self::ristretto255_frombytes($p);
        if ($result['res'] !== 0) {
            return 0;
        }
        return 1;
    }

    /**
     * @param string $p
     * @param string $q
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_add($p, $q)
    {
        $p_res = self::ristretto255_frombytes($p);
        $q_res = self::ristretto255_frombytes($q);
        if ($p_res['res'] !== 0 || $q_res['res'] !== 0) {
            throw new SodiumException('Could not add points');
        }
        $p_p3 = $p_res['h'];
        $q_p3 = $q_res['h'];
        $q_cached = self::ge_p3_to_cached($q_p3);
        $r_p1p1 = self::ge_add($p_p3, $q_cached);
        $r_p3 = self::ge_p1p1_to_p3($r_p1p1);
        return self::ristretto255_p3_tobytes($r_p3);
    }

    /**
     * @param string $p
     * @param string $q
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_sub($p, $q)
    {
        $p_res = self::ristretto255_frombytes($p);
        $q_res = self::ristretto255_frombytes($q);
        if ($p_res['res'] !== 0 || $q_res['res'] !== 0) {
            throw new SodiumException('Could not add points');
        }
        $p_p3 = $p_res['h'];
        $q_p3 = $q_res['h'];
        $q_cached = self::ge_p3_to_cached($q_p3);
        $r_p1p1 = self::ge_sub($p_p3, $q_cached);
        $r_p3 = self::ge_p1p1_to_p3($r_p1p1);
        return self::ristretto255_p3_tobytes($r_p3);
    }


    /**
     * @param int $hLen
     * @param ?string $ctx
     * @param string $msg
     * @return string
     * @throws SodiumException
     * @psalm-suppress PossiblyInvalidArgument hash API
     */
    protected static function h2c_string_to_hash_sha256($hLen, $ctx, $msg)
    {
        $h = array_fill(0, $hLen, 0);
        $ctx_len = !is_null($ctx) ? self::strlen($ctx) : 0;
        if ($hLen > 0xff) {
            throw new SodiumException('Hash must be less than 256 bytes');
        }

        if ($ctx_len > 0xff) {
            $st = hash_init('sha256');
            self::hash_update($st, "H2C-OVERSIZE-DST-");
            self::hash_update($st, $ctx);
            $ctx = hash_final($st, true);
            $ctx_len = 32;
        }
        $t = array(0, $hLen, 0);
        $ux = str_repeat("\0", 64);
        $st = hash_init('sha256');
        self::hash_update($st, $ux);
        self::hash_update($st, $msg);
        self::hash_update($st, self::intArrayToString($t));
        self::hash_update($st, $ctx);
        self::hash_update($st, self::intToChr($ctx_len));
        $u0 = hash_final($st, true);

        for ($i = 0; $i < $hLen; $i += 64) {
            $ux = self::xorStrings($ux, $u0);
            ++$t[2];
            $st = hash_init('sha256');
            self::hash_update($st, $ux);
            self::hash_update($st, self::intToChr($t[2]));
            self::hash_update($st, $ctx);
            self::hash_update($st, self::intToChr($ctx_len));
            $ux = hash_final($st, true);
            $amount = min($hLen - $i, 64);
            for ($j = 0; $j < $amount; ++$j) {
                $h[$i + $j] = self::chrToInt($ux[$i]);
            }
        }
        return self::intArrayToString(array_slice($h, 0, $hLen));
    }

    /**
     * @param int $hLen
     * @param ?string $ctx
     * @param string $msg
     * @return string
     * @throws SodiumException
     * @psalm-suppress PossiblyInvalidArgument hash API
     */
    protected static function h2c_string_to_hash_sha512($hLen, $ctx, $msg)
    {
        $h = array_fill(0, $hLen, 0);
        $ctx_len = !is_null($ctx) ? self::strlen($ctx) : 0;
        if ($hLen > 0xff) {
            throw new SodiumException('Hash must be less than 256 bytes');
        }

        if ($ctx_len > 0xff) {
            $st = hash_init('sha256');
            self::hash_update($st, "H2C-OVERSIZE-DST-");
            self::hash_update($st, $ctx);
            $ctx = hash_final($st, true);
            $ctx_len = 32;
        }
        $t = array(0, $hLen, 0);
        $ux = str_repeat("\0", 128);
        $st = hash_init('sha512');
        self::hash_update($st, $ux);
        self::hash_update($st, $msg);
        self::hash_update($st, self::intArrayToString($t));
        self::hash_update($st, $ctx);
        self::hash_update($st, self::intToChr($ctx_len));
        $u0 = hash_final($st, true);

        for ($i = 0; $i < $hLen; $i += 128) {
            $ux = self::xorStrings($ux, $u0);
            ++$t[2];
            $st = hash_init('sha512');
            self::hash_update($st, $ux);
            self::hash_update($st, self::intToChr($t[2]));
            self::hash_update($st, $ctx);
            self::hash_update($st, self::intToChr($ctx_len));
            $ux = hash_final($st, true);
            $amount = min($hLen - $i, 128);
            for ($j = 0; $j < $amount; ++$j) {
                $h[$i + $j] = self::chrToInt($ux[$i]);
            }
        }
        return self::intArrayToString(array_slice($h, 0, $hLen));
    }

    /**
     * @param int $hLen
     * @param ?string $ctx
     * @param string $msg
     * @param int $hash_alg
     * @return string
     * @throws SodiumException
     */
    public static function h2c_string_to_hash($hLen, $ctx, $msg, $hash_alg)
    {
        switch ($hash_alg) {
            case self::CORE_H2C_SHA256:
                return self::h2c_string_to_hash_sha256($hLen, $ctx, $msg);
            case self::CORE_H2C_SHA512:
                return self::h2c_string_to_hash_sha512($hLen, $ctx, $msg);
            default:
                throw new SodiumException('Invalid H2C hash algorithm');
        }
    }

    /**
     * @param ?string $ctx
     * @param string $msg
     * @param int $hash_alg
     * @return string
     * @throws SodiumException
     */
    protected static function _string_to_element($ctx, $msg, $hash_alg)
    {
        return self::ristretto255_from_hash(
            self::h2c_string_to_hash(self::crypto_core_ristretto255_HASHBYTES, $ctx, $msg, $hash_alg)
        );
    }

    /**
     * @return string
     * @throws SodiumException
     * @throws Exception
     */
    public static function ristretto255_random()
    {
        return self::ristretto255_from_hash(
            ParagonIE_Sodium_Compat::randombytes_buf(self::crypto_core_ristretto255_HASHBYTES)
        );
    }

    /**
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_scalar_random()
    {
        return self::scalar_random();
    }

    /**
     * @param string $s
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_scalar_complement($s)
    {
        return self::scalar_complement($s);
    }


    /**
     * @param string $s
     * @return string
     */
    public static function ristretto255_scalar_invert($s)
    {
        return self::sc25519_invert($s);
    }

    /**
     * @param string $s
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_scalar_negate($s)
    {
        return self::scalar_negate($s);
    }

    /**
     * @param string $x
     * @param string $y
     * @return string
     */
    public static function ristretto255_scalar_add($x, $y)
    {
        return self::scalar_add($x, $y);
    }

    /**
     * @param string $x
     * @param string $y
     * @return string
     */
    public static function ristretto255_scalar_sub($x, $y)
    {
        return self::scalar_sub($x, $y);
    }

    /**
     * @param string $x
     * @param string $y
     * @return string
     */
    public static function ristretto255_scalar_mul($x, $y)
    {
        return self::sc25519_mul($x, $y);
    }

    /**
     * @param string $ctx
     * @param string $msg
     * @param int $hash_alg
     * @return string
     * @throws SodiumException
     */
    public static function ristretto255_scalar_from_string($ctx, $msg, $hash_alg)
    {
        $h = array_fill(0, 64, 0);
        $h_be = self::stringToIntArray(
            self::h2c_string_to_hash(
                self::HASH_SC_L, $ctx, $msg, $hash_alg
            )
        );

        for ($i = 0; $i < self::HASH_SC_L; ++$i) {
            $h[$i] = $h_be[self::HASH_SC_L - 1 - $i];
        }
        return self::ristretto255_scalar_reduce(self::intArrayToString($h));
    }

    /**
     * @param string $s
     * @return string
     */
    public static function ristretto255_scalar_reduce($s)
    {
        return self::sc_reduce($s);
    }

    /**
     * @param string $n
     * @param string $p
     * @return string
     * @throws SodiumException
     */
    public static function scalarmult_ristretto255($n, $p)
    {
        if (self::strlen($n) !== 32) {
            throw new SodiumException('Scalar must be 32 bytes, ' . self::strlen($p) . ' given.');
        }
        if (self::strlen($p) !== 32) {
            throw new SodiumException('Point must be 32 bytes, ' . self::strlen($p) . ' given.');
        }
        $result = self::ristretto255_frombytes($p);
        if ($result['res'] !== 0) {
            throw new SodiumException('Could not multiply points');
        }
        $P = $result['h'];

        $t = self::stringToIntArray($n);
        $t[31] &= 0x7f;
        $Q = self::ge_scalarmult(self::intArrayToString($t), $P);
        $q = self::ristretto255_p3_tobytes($Q);
        if (ParagonIE_Sodium_Compat::is_zero($q)) {
            throw new SodiumException('An unknown error has occurred');
        }
        return $q;
    }

    /**
     * @param string $n
     * @return string
     * @throws SodiumException
     */
    public static function scalarmult_ristretto255_base($n)
    {
        $t = self::stringToIntArray($n);
        $t[31] &= 0x7f;
        $Q = self::ge_scalarmult_base(self::intArrayToString($t));
        $q = self::ristretto255_p3_tobytes($Q);
        if (ParagonIE_Sodium_Compat::is_zero($q)) {
            throw new SodiumException('An unknown error has occurred');
        }
        return $q;
    }
}
