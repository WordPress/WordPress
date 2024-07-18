<?php

if (class_exists('ParagonIE_Sodium_Core_AES', false)) {
    return;
}

/**
 * Bitsliced implementation of the AES block cipher.
 *
 * Based on the implementation provided by BearSSL.
 *
 * @internal This should only be used by sodium_compat
 */
class ParagonIE_Sodium_Core_AES extends ParagonIE_Sodium_Core_Util
{
    /**
     * @var int[] AES round constants
     */
    private static $Rcon = array(
        0x01, 0x02, 0x04, 0x08, 0x10, 0x20, 0x40, 0x80, 0x1B, 0x36
    );

    /**
     * Mutates the values of $q!
     *
     * @param ParagonIE_Sodium_Core_AES_Block $q
     * @return void
     */
    public static function sbox(ParagonIE_Sodium_Core_AES_Block $q)
    {
        /**
         * @var int $x0
         * @var int $x1
         * @var int $x2
         * @var int $x3
         * @var int $x4
         * @var int $x5
         * @var int $x6
         * @var int $x7
         */
        $x0 = $q[7] & self::U32_MAX;
        $x1 = $q[6] & self::U32_MAX;
        $x2 = $q[5] & self::U32_MAX;
        $x3 = $q[4] & self::U32_MAX;
        $x4 = $q[3] & self::U32_MAX;
        $x5 = $q[2] & self::U32_MAX;
        $x6 = $q[1] & self::U32_MAX;
        $x7 = $q[0] & self::U32_MAX;

        $y14 = $x3 ^ $x5;
        $y13 = $x0 ^ $x6;
        $y9 = $x0 ^ $x3;
        $y8 = $x0 ^ $x5;
        $t0 = $x1 ^ $x2;
        $y1 = $t0 ^ $x7;
        $y4 = $y1 ^ $x3;
        $y12 = $y13 ^ $y14;
        $y2 = $y1 ^ $x0;
        $y5 = $y1 ^ $x6;
        $y3 = $y5 ^ $y8;
        $t1 = $x4 ^ $y12;
        $y15 = $t1 ^ $x5;
        $y20 = $t1 ^ $x1;
        $y6 = $y15 ^ $x7;
        $y10 = $y15 ^ $t0;
        $y11 = $y20 ^ $y9;
        $y7 = $x7 ^ $y11;
        $y17 = $y10 ^ $y11;
        $y19 = $y10 ^ $y8;
        $y16 = $t0 ^ $y11;
        $y21 = $y13 ^ $y16;
        $y18 = $x0 ^ $y16;

        /*
         * Non-linear section.
         */
        $t2 = $y12 & $y15;
        $t3 = $y3 & $y6;
        $t4 = $t3 ^ $t2;
        $t5 = $y4 & $x7;
        $t6 = $t5 ^ $t2;
        $t7 = $y13 & $y16;
        $t8 = $y5 & $y1;
        $t9 = $t8 ^ $t7;
        $t10 = $y2 & $y7;
        $t11 = $t10 ^ $t7;
        $t12 = $y9 & $y11;
        $t13 = $y14 & $y17;
        $t14 = $t13 ^ $t12;
        $t15 = $y8 & $y10;
        $t16 = $t15 ^ $t12;
        $t17 = $t4 ^ $t14;
        $t18 = $t6 ^ $t16;
        $t19 = $t9 ^ $t14;
        $t20 = $t11 ^ $t16;
        $t21 = $t17 ^ $y20;
        $t22 = $t18 ^ $y19;
        $t23 = $t19 ^ $y21;
        $t24 = $t20 ^ $y18;

        $t25 = $t21 ^ $t22;
        $t26 = $t21 & $t23;
        $t27 = $t24 ^ $t26;
        $t28 = $t25 & $t27;
        $t29 = $t28 ^ $t22;
        $t30 = $t23 ^ $t24;
        $t31 = $t22 ^ $t26;
        $t32 = $t31 & $t30;
        $t33 = $t32 ^ $t24;
        $t34 = $t23 ^ $t33;
        $t35 = $t27 ^ $t33;
        $t36 = $t24 & $t35;
        $t37 = $t36 ^ $t34;
        $t38 = $t27 ^ $t36;
        $t39 = $t29 & $t38;
        $t40 = $t25 ^ $t39;

        $t41 = $t40 ^ $t37;
        $t42 = $t29 ^ $t33;
        $t43 = $t29 ^ $t40;
        $t44 = $t33 ^ $t37;
        $t45 = $t42 ^ $t41;
        $z0 = $t44 & $y15;
        $z1 = $t37 & $y6;
        $z2 = $t33 & $x7;
        $z3 = $t43 & $y16;
        $z4 = $t40 & $y1;
        $z5 = $t29 & $y7;
        $z6 = $t42 & $y11;
        $z7 = $t45 & $y17;
        $z8 = $t41 & $y10;
        $z9 = $t44 & $y12;
        $z10 = $t37 & $y3;
        $z11 = $t33 & $y4;
        $z12 = $t43 & $y13;
        $z13 = $t40 & $y5;
        $z14 = $t29 & $y2;
        $z15 = $t42 & $y9;
        $z16 = $t45 & $y14;
        $z17 = $t41 & $y8;

        /*
         * Bottom linear transformation.
         */
        $t46 = $z15 ^ $z16;
        $t47 = $z10 ^ $z11;
        $t48 = $z5 ^ $z13;
        $t49 = $z9 ^ $z10;
        $t50 = $z2 ^ $z12;
        $t51 = $z2 ^ $z5;
        $t52 = $z7 ^ $z8;
        $t53 = $z0 ^ $z3;
        $t54 = $z6 ^ $z7;
        $t55 = $z16 ^ $z17;
        $t56 = $z12 ^ $t48;
        $t57 = $t50 ^ $t53;
        $t58 = $z4 ^ $t46;
        $t59 = $z3 ^ $t54;
        $t60 = $t46 ^ $t57;
        $t61 = $z14 ^ $t57;
        $t62 = $t52 ^ $t58;
        $t63 = $t49 ^ $t58;
        $t64 = $z4 ^ $t59;
        $t65 = $t61 ^ $t62;
        $t66 = $z1 ^ $t63;
        $s0 = $t59 ^ $t63;
        $s6 = $t56 ^ ~$t62;
        $s7 = $t48 ^ ~$t60;
        $t67 = $t64 ^ $t65;
        $s3 = $t53 ^ $t66;
        $s4 = $t51 ^ $t66;
        $s5 = $t47 ^ $t65;
        $s1 = $t64 ^ ~$s3;
        $s2 = $t55 ^ ~$t67;

        $q[7] = $s0 & self::U32_MAX;
        $q[6] = $s1 & self::U32_MAX;
        $q[5] = $s2 & self::U32_MAX;
        $q[4] = $s3 & self::U32_MAX;
        $q[3] = $s4 & self::U32_MAX;
        $q[2] = $s5 & self::U32_MAX;
        $q[1] = $s6 & self::U32_MAX;
        $q[0] = $s7 & self::U32_MAX;
    }

    /**
     * Mutates the values of $q!
     *
     * @param ParagonIE_Sodium_Core_AES_Block $q
     * @return void
     */
    public static function invSbox(ParagonIE_Sodium_Core_AES_Block $q)
    {
        self::processInversion($q);
        self::sbox($q);
        self::processInversion($q);
    }

    /**
     * This is some boilerplate code needed to invert an S-box. Rather than repeat the code
     * twice, I moved it to a protected method.
     *
     * Mutates $q
     *
     * @param ParagonIE_Sodium_Core_AES_Block $q
     * @return void
     */
    protected static function processInversion(ParagonIE_Sodium_Core_AES_Block $q)
    {
        $q0 = (~$q[0]) & self::U32_MAX;
        $q1 = (~$q[1]) & self::U32_MAX;
        $q2 = $q[2] & self::U32_MAX;
        $q3 = $q[3] & self::U32_MAX;
        $q4 = $q[4] & self::U32_MAX;
        $q5 = (~$q[5])  & self::U32_MAX;
        $q6 = (~$q[6])  & self::U32_MAX;
        $q7 = $q[7] & self::U32_MAX;
        $q[7] = ($q1 ^ $q4 ^ $q6) & self::U32_MAX;
        $q[6] = ($q0 ^ $q3 ^ $q5) & self::U32_MAX;
        $q[5] = ($q7 ^ $q2 ^ $q4) & self::U32_MAX;
        $q[4] = ($q6 ^ $q1 ^ $q3) & self::U32_MAX;
        $q[3] = ($q5 ^ $q0 ^ $q2) & self::U32_MAX;
        $q[2] = ($q4 ^ $q7 ^ $q1) & self::U32_MAX;
        $q[1] = ($q3 ^ $q6 ^ $q0) & self::U32_MAX;
        $q[0] = ($q2 ^ $q5 ^ $q7) & self::U32_MAX;
    }

    /**
     * @param int $x
     * @return int
     */
    public static function subWord($x)
    {
        $q = ParagonIE_Sodium_Core_AES_Block::fromArray(
            array($x, $x, $x, $x, $x, $x, $x, $x)
        );
        $q->orthogonalize();
        self::sbox($q);
        $q->orthogonalize();
        return $q[0] & self::U32_MAX;
    }

    /**
     * Calculate the key schedule from a given random key
     *
     * @param string $key
     * @return ParagonIE_Sodium_Core_AES_KeySchedule
     * @throws SodiumException
     */
    public static function keySchedule($key)
    {
        $key_len = self::strlen($key);
        switch ($key_len) {
            case 16:
                $num_rounds = 10;
                break;
            case 24:
                $num_rounds = 12;
                break;
            case 32:
                $num_rounds = 14;
                break;
            default:
                throw new SodiumException('Invalid key length: ' . $key_len);
        }
        $skey = array();
        $comp_skey = array();
        $nk = $key_len >> 2;
        $nkf = ($num_rounds + 1) << 2;
        $tmp = 0;

        for ($i = 0; $i < $nk; ++$i) {
            $tmp = self::load_4(self::substr($key, $i << 2, 4));
            $skey[($i << 1)] = $tmp;
            $skey[($i << 1) + 1] = $tmp;
        }

        for ($i = $nk, $j = 0, $k = 0; $i < $nkf; ++$i) {
            if ($j === 0) {
                $tmp = (($tmp & 0xff) << 24) | ($tmp >> 8);
                $tmp = (self::subWord($tmp) ^ self::$Rcon[$k]) & self::U32_MAX;
            } elseif ($nk > 6 && $j === 4) {
                $tmp = self::subWord($tmp);
            }
            $tmp ^= $skey[($i - $nk) << 1];
            $skey[($i << 1)] = $tmp & self::U32_MAX;
            $skey[($i << 1) + 1] = $tmp & self::U32_MAX;
            if (++$j === $nk) {
                /** @psalm-suppress LoopInvalidation */
                $j = 0;
                ++$k;
            }
        }
        for ($i = 0; $i < $nkf; $i += 4) {
            $q = ParagonIE_Sodium_Core_AES_Block::fromArray(
                array_slice($skey, $i << 1, 8)
            );
            $q->orthogonalize();
            // We have to overwrite $skey since we're not using C pointers like BearSSL did
            for ($j = 0; $j < 8; ++$j) {
                $skey[($i << 1) + $j] = $q[$j];
            }
        }
        for ($i = 0, $j = 0; $i < $nkf; ++$i, $j += 2) {
            $comp_skey[$i] = ($skey[$j] & 0x55555555)
                | ($skey[$j + 1] & 0xAAAAAAAA);
        }
        return new ParagonIE_Sodium_Core_AES_KeySchedule($comp_skey, $num_rounds);
    }

    /**
     * Mutates $q
     *
     * @param ParagonIE_Sodium_Core_AES_KeySchedule $skey
     * @param ParagonIE_Sodium_Core_AES_Block $q
     * @param int $offset
     * @return void
     */
    public static function addRoundKey(
        ParagonIE_Sodium_Core_AES_Block $q,
        ParagonIE_Sodium_Core_AES_KeySchedule $skey,
        $offset = 0
    ) {
        $block = $skey->getRoundKey($offset);
        for ($j = 0; $j < 8; ++$j) {
            $q[$j] = ($q[$j] ^ $block[$j]) & ParagonIE_Sodium_Core_Util::U32_MAX;
        }
    }

    /**
     * This mainly exists for testing, as we need the round key features for AEGIS.
     *
     * @param string $message
     * @param string $key
     * @return string
     * @throws SodiumException
     */
    public static function decryptBlockECB($message, $key)
    {
        if (self::strlen($message) !== 16) {
            throw new SodiumException('decryptBlockECB() expects a 16 byte message');
        }
        $skey = self::keySchedule($key)->expand();
        $q = ParagonIE_Sodium_Core_AES_Block::init();
        $q[0] = self::load_4(self::substr($message, 0, 4));
        $q[2] = self::load_4(self::substr($message, 4, 4));
        $q[4] = self::load_4(self::substr($message, 8, 4));
        $q[6] = self::load_4(self::substr($message, 12, 4));

        $q->orthogonalize();
        self::bitsliceDecryptBlock($skey, $q);
        $q->orthogonalize();

        return self::store32_le($q[0]) .
            self::store32_le($q[2]) .
            self::store32_le($q[4]) .
            self::store32_le($q[6]);
    }

    /**
     * This mainly exists for testing, as we need the round key features for AEGIS.
     *
     * @param string $message
     * @param string $key
     * @return string
     * @throws SodiumException
     */
    public static function encryptBlockECB($message, $key)
    {
        if (self::strlen($message) !== 16) {
            throw new SodiumException('encryptBlockECB() expects a 16 byte message');
        }
        $comp_skey = self::keySchedule($key);
        $skey = $comp_skey->expand();
        $q = ParagonIE_Sodium_Core_AES_Block::init();
        $q[0] = self::load_4(self::substr($message, 0, 4));
        $q[2] = self::load_4(self::substr($message, 4, 4));
        $q[4] = self::load_4(self::substr($message, 8, 4));
        $q[6] = self::load_4(self::substr($message, 12, 4));

        $q->orthogonalize();
        self::bitsliceEncryptBlock($skey, $q);
        $q->orthogonalize();

        return self::store32_le($q[0]) .
            self::store32_le($q[2]) .
            self::store32_le($q[4]) .
            self::store32_le($q[6]);
    }

    /**
     * Mutates $q
     *
     * @param ParagonIE_Sodium_Core_AES_Expanded $skey
     * @param ParagonIE_Sodium_Core_AES_Block $q
     * @return void
     */
    public static function bitsliceEncryptBlock(
        ParagonIE_Sodium_Core_AES_Expanded $skey,
        ParagonIE_Sodium_Core_AES_Block $q
    ) {
        self::addRoundKey($q, $skey);
        for ($u = 1; $u < $skey->getNumRounds(); ++$u) {
            self::sbox($q);
            $q->shiftRows();
            $q->mixColumns();
            self::addRoundKey($q, $skey, ($u << 3));
        }
        self::sbox($q);
        $q->shiftRows();
        self::addRoundKey($q, $skey, ($skey->getNumRounds() << 3));
    }

    /**
     * @param string $x
     * @param string $y
     * @return string
     */
    public static function aesRound($x, $y)
    {
        $q = ParagonIE_Sodium_Core_AES_Block::init();
        $q[0] = self::load_4(self::substr($x, 0, 4));
        $q[2] = self::load_4(self::substr($x, 4, 4));
        $q[4] = self::load_4(self::substr($x, 8, 4));
        $q[6] = self::load_4(self::substr($x, 12, 4));

        $rk = ParagonIE_Sodium_Core_AES_Block::init();
        $rk[0] = $rk[1] = self::load_4(self::substr($y, 0, 4));
        $rk[2] = $rk[3] = self::load_4(self::substr($y, 4, 4));
        $rk[4] = $rk[5] = self::load_4(self::substr($y, 8, 4));
        $rk[6] = $rk[7] = self::load_4(self::substr($y, 12, 4));

        $q->orthogonalize();
        self::sbox($q);
        $q->shiftRows();
        $q->mixColumns();
        $q->orthogonalize();
        // add round key without key schedule:
        for ($i = 0; $i < 8; ++$i) {
            $q[$i] ^= $rk[$i];
        }
        return self::store32_le($q[0]) .
            self::store32_le($q[2]) .
            self::store32_le($q[4]) .
            self::store32_le($q[6]);
    }

    /**
     * Process two AES blocks in one shot.
     *
     * @param string $b0  First AES block
     * @param string $rk0 First round key
     * @param string $b1  Second AES block
     * @param string $rk1 Second round key
     * @return string[]
     */
    public static function doubleRound($b0, $rk0, $b1, $rk1)
    {
        $q = ParagonIE_Sodium_Core_AES_Block::init();
        // First block
        $q[0] = self::load_4(self::substr($b0, 0, 4));
        $q[2] = self::load_4(self::substr($b0, 4, 4));
        $q[4] = self::load_4(self::substr($b0, 8, 4));
        $q[6] = self::load_4(self::substr($b0, 12, 4));
        // Second block
        $q[1] = self::load_4(self::substr($b1, 0, 4));
        $q[3] = self::load_4(self::substr($b1, 4, 4));
        $q[5] = self::load_4(self::substr($b1, 8, 4));
        $q[7] = self::load_4(self::substr($b1, 12, 4));;

        $rk = ParagonIE_Sodium_Core_AES_Block::init();
        // First round key
        $rk[0] = self::load_4(self::substr($rk0, 0, 4));
        $rk[2] = self::load_4(self::substr($rk0, 4, 4));
        $rk[4] = self::load_4(self::substr($rk0, 8, 4));
        $rk[6] = self::load_4(self::substr($rk0, 12, 4));
        // Second round key
        $rk[1] = self::load_4(self::substr($rk1, 0, 4));
        $rk[3] = self::load_4(self::substr($rk1, 4, 4));
        $rk[5] = self::load_4(self::substr($rk1, 8, 4));
        $rk[7] = self::load_4(self::substr($rk1, 12, 4));

        $q->orthogonalize();
        self::sbox($q);
        $q->shiftRows();
        $q->mixColumns();
        $q->orthogonalize();
        // add round key without key schedule:
        for ($i = 0; $i < 8; ++$i) {
            $q[$i] ^= $rk[$i];
        }
        return array(
            self::store32_le($q[0]) . self::store32_le($q[2]) . self::store32_le($q[4]) . self::store32_le($q[6]),
            self::store32_le($q[1]) . self::store32_le($q[3]) . self::store32_le($q[5]) . self::store32_le($q[7]),
        );
    }

    /**
     * @param ParagonIE_Sodium_Core_AES_Expanded $skey
     * @param ParagonIE_Sodium_Core_AES_Block $q
     * @return void
     */
    public static function bitsliceDecryptBlock(
        ParagonIE_Sodium_Core_AES_Expanded $skey,
        ParagonIE_Sodium_Core_AES_Block $q
    ) {
        self::addRoundKey($q, $skey, ($skey->getNumRounds() << 3));
        for ($u = $skey->getNumRounds() - 1; $u > 0; --$u) {
            $q->inverseShiftRows();
            self::invSbox($q);
            self::addRoundKey($q, $skey, ($u << 3));
            $q->inverseMixColumns();
        }
        $q->inverseShiftRows();
        self::invSbox($q);
        self::addRoundKey($q, $skey, ($u << 3));
    }
}
