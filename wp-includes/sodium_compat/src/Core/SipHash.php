<?php

if (class_exists('ParagonIE_Sodium_Core_SipHash', false)) {
    return;
}

/**
 * Class ParagonIE_SodiumCompat_Core_SipHash
 *
 * Only uses 32-bit arithmetic, while the original SipHash used 64-bit integers
 */
class ParagonIE_Sodium_Core_SipHash extends ParagonIE_Sodium_Core_Util
{
    /**
     * @internal You should not use this directly from another application
     *
     * @param int[] $v
     * @return int[]
     *
     */
    public static function sipRound(array $v)
    {
        # v0 += v1;
        list($v[0], $v[1]) = self::add(
            array($v[0], $v[1]),
            array($v[2], $v[3])
        );

        #  v1=ROTL(v1,13);
        list($v[2], $v[3]) = self::rotl_64((int) $v[2], (int) $v[3], 13);

        #  v1 ^= v0;
        $v[2] = (int) $v[2] ^ (int) $v[0];
        $v[3] = (int) $v[3] ^ (int) $v[1];

        #  v0=ROTL(v0,32);
        list($v[0], $v[1]) = self::rotl_64((int) $v[0], (int) $v[1], 32);

        # v2 += v3;
        list($v[4], $v[5]) = self::add(
            array((int) $v[4], (int) $v[5]),
            array((int) $v[6], (int) $v[7])
        );

        # v3=ROTL(v3,16);
        list($v[6], $v[7]) = self::rotl_64((int) $v[6], (int) $v[7], 16);

        #  v3 ^= v2;
        $v[6] = (int) $v[6] ^ (int) $v[4];
        $v[7] = (int) $v[7] ^ (int) $v[5];

        # v0 += v3;
        list($v[0], $v[1]) = self::add(
            array((int) $v[0], (int) $v[1]),
            array((int) $v[6], (int) $v[7])
        );

        # v3=ROTL(v3,21);
        list($v[6], $v[7]) = self::rotl_64((int) $v[6], (int) $v[7], 21);

        # v3 ^= v0;
        $v[6] = (int) $v[6] ^ (int) $v[0];
        $v[7] = (int) $v[7] ^ (int) $v[1];

        # v2 += v1;
        list($v[4], $v[5]) = self::add(
            array((int) $v[4], (int) $v[5]),
            array((int) $v[2], (int) $v[3])
        );

        # v1=ROTL(v1,17);
        list($v[2], $v[3]) = self::rotl_64((int) $v[2], (int) $v[3], 17);

        #  v1 ^= v2;;
        $v[2] = (int) $v[2] ^ (int) $v[4];
        $v[3] = (int) $v[3] ^ (int) $v[5];

        # v2=ROTL(v2,32)
        list($v[4], $v[5]) = self::rotl_64((int) $v[4], (int) $v[5], 32);

        return $v;
    }

    /**
     * Add two 32 bit integers representing a 64-bit integer.
     *
     * @internal You should not use this directly from another application
     *
     * @param int[] $a
     * @param int[] $b
     * @return array<int, mixed>
     */
    public static function add(array $a, array $b)
    {
        /** @var int $x1 */
        $x1 = $a[1] + $b[1];
        /** @var int $c */
        $c = $x1 >> 32; // Carry if ($a + $b) > 0xffffffff
        /** @var int $x0 */
        $x0 = $a[0] + $b[0] + $c;
        return array(
            $x0 & 0xffffffff,
            $x1 & 0xffffffff
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $int0
     * @param int $int1
     * @param int $c
     * @return array<int, mixed>
     */
    public static function rotl_64($int0, $int1, $c)
    {
        $int0 &= 0xffffffff;
        $int1 &= 0xffffffff;
        $c &= 63;
        if ($c === 32) {
            return array($int1, $int0);
        }
        if ($c > 31) {
            $tmp = $int1;
            $int1 = $int0;
            $int0 = $tmp;
            $c &= 31;
        }
        if ($c === 0) {
            return array($int0, $int1);
        }
        return array(
            0xffffffff & (
                ($int0 << $c)
                    |
                ($int1 >> (32 - $c))
            ),
            0xffffffff & (
                ($int1 << $c)
                    |
                ($int0 >> (32 - $c))
            ),
        );
    }

    /**
     * Implements Siphash-2-4 using only 32-bit numbers.
     *
     * When we split an int into two, the higher bits go to the lower index.
     * e.g. 0xDEADBEEFAB10C92D becomes [
     *     0 => 0xDEADBEEF,
     *     1 => 0xAB10C92D
     * ].
     *
     * @internal You should not use this directly from another application
     *
     * @param string $in
     * @param string $key
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public static function sipHash24($in, $key)
    {
        $inlen = self::strlen($in);

        # /* "somepseudorandomlygeneratedbytes" */
        # u64 v0 = 0x736f6d6570736575ULL;
        # u64 v1 = 0x646f72616e646f6dULL;
        # u64 v2 = 0x6c7967656e657261ULL;
        # u64 v3 = 0x7465646279746573ULL;
        $v = array(
            0x736f6d65, // 0
            0x70736575, // 1
            0x646f7261, // 2
            0x6e646f6d, // 3
            0x6c796765, // 4
            0x6e657261, // 5
            0x74656462, // 6
            0x79746573  // 7
        );
        // v0 => $v[0], $v[1]
        // v1 => $v[2], $v[3]
        // v2 => $v[4], $v[5]
        // v3 => $v[6], $v[7]

        # u64 k0 = LOAD64_LE( k );
        # u64 k1 = LOAD64_LE( k + 8 );
        $k = array(
            self::load_4(self::substr($key, 4, 4)),
            self::load_4(self::substr($key, 0, 4)),
            self::load_4(self::substr($key, 12, 4)),
            self::load_4(self::substr($key, 8, 4))
        );
        // k0 => $k[0], $k[1]
        // k1 => $k[2], $k[3]

        # b = ( ( u64 )inlen ) << 56;
        $b = array(
            $inlen << 24,
            0
        );
        // See docblock for why the 0th index gets the higher bits.

        # v3 ^= k1;
        $v[6] ^= $k[2];
        $v[7] ^= $k[3];
        # v2 ^= k0;
        $v[4] ^= $k[0];
        $v[5] ^= $k[1];
        # v1 ^= k1;
        $v[2] ^= $k[2];
        $v[3] ^= $k[3];
        # v0 ^= k0;
        $v[0] ^= $k[0];
        $v[1] ^= $k[1];

        $left = $inlen;
        # for ( ; in != end; in += 8 )
        while ($left >= 8) {
            # m = LOAD64_LE( in );
            $m = array(
                self::load_4(self::substr($in, 4, 4)),
                self::load_4(self::substr($in, 0, 4))
            );

            # v3 ^= m;
            $v[6] ^= $m[0];
            $v[7] ^= $m[1];

            # SIPROUND;
            # SIPROUND;
            $v = self::sipRound($v);
            $v = self::sipRound($v);

            # v0 ^= m;
            $v[0] ^= $m[0];
            $v[1] ^= $m[1];

            $in = self::substr($in, 8);
            $left -= 8;
        }

        # switch( left )
        #  {
        #     case 7: b |= ( ( u64 )in[ 6] )  << 48;
        #     case 6: b |= ( ( u64 )in[ 5] )  << 40;
        #     case 5: b |= ( ( u64 )in[ 4] )  << 32;
        #     case 4: b |= ( ( u64 )in[ 3] )  << 24;
        #     case 3: b |= ( ( u64 )in[ 2] )  << 16;
        #     case 2: b |= ( ( u64 )in[ 1] )  <<  8;
        #     case 1: b |= ( ( u64 )in[ 0] ); break;
        #     case 0: break;
        # }
        switch ($left) {
            case 7:
                $b[0] |= self::chrToInt($in[6]) << 16;
            case 6:
                $b[0] |= self::chrToInt($in[5]) << 8;
            case 5:
                $b[0] |= self::chrToInt($in[4]);
            case 4:
                $b[1] |= self::chrToInt($in[3]) << 24;
            case 3:
                $b[1] |= self::chrToInt($in[2]) << 16;
            case 2:
                $b[1] |= self::chrToInt($in[1]) << 8;
            case 1:
                $b[1] |= self::chrToInt($in[0]);
            case 0:
                break;
        }
        // See docblock for why the 0th index gets the higher bits.

        # v3 ^= b;
        $v[6] ^= $b[0];
        $v[7] ^= $b[1];

        # SIPROUND;
        # SIPROUND;
        $v = self::sipRound($v);
        $v = self::sipRound($v);

        # v0 ^= b;
        $v[0] ^= $b[0];
        $v[1] ^= $b[1];

        // Flip the lower 8 bits of v2 which is ($v[4], $v[5]) in our implementation
        # v2 ^= 0xff;
        $v[5] ^= 0xff;

        # SIPROUND;
        # SIPROUND;
        # SIPROUND;
        # SIPROUND;
        $v = self::sipRound($v);
        $v = self::sipRound($v);
        $v = self::sipRound($v);
        $v = self::sipRound($v);

        # b = v0 ^ v1 ^ v2 ^ v3;
        # STORE64_LE( out, b );
        return  self::store32_le($v[1] ^ $v[3] ^ $v[5] ^ $v[7]) .
            self::store32_le($v[0] ^ $v[2] ^ $v[4] ^ $v[6]);
    }
}
