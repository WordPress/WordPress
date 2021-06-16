<?php

if (class_exists('ParagonIE_Sodium_Core_BLAKE2b', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_BLAKE2b
 *
 * Based on the work of Devi Mandiri in devi/salt.
 */
abstract class ParagonIE_Sodium_Core_BLAKE2b extends ParagonIE_Sodium_Core_Util
{
    /**
     * @var SplFixedArray
     */
    protected static $iv;

    /**
     * @var array<int, array<int, int>>
     */
    protected static $sigma = array(
        array(  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14, 15),
        array( 14, 10,  4,  8,  9, 15, 13,  6,  1, 12,  0,  2, 11,  7,  5,  3),
        array( 11,  8, 12,  0,  5,  2, 15, 13, 10, 14,  3,  6,  7,  1,  9,  4),
        array(  7,  9,  3,  1, 13, 12, 11, 14,  2,  6,  5, 10,  4,  0, 15,  8),
        array(  9,  0,  5,  7,  2,  4, 10, 15, 14,  1, 11, 12,  6,  8,  3, 13),
        array(  2, 12,  6, 10,  0, 11,  8,  3,  4, 13,  7,  5, 15, 14,  1,  9),
        array( 12,  5,  1, 15, 14, 13,  4, 10,  0,  7,  6,  3,  9,  2,  8, 11),
        array( 13, 11,  7, 14, 12,  1,  3,  9,  5,  0, 15,  4,  8,  6,  2, 10),
        array(  6, 15, 14,  9, 11,  3,  0,  8, 12,  2, 13,  7,  1,  4, 10,  5),
        array( 10,  2,  8,  4,  7,  6,  1,  5, 15, 11,  9, 14,  3, 12, 13 , 0),
        array(  0,  1,  2,  3,  4,  5,  6,  7,  8,  9, 10, 11, 12, 13, 14, 15),
        array( 14, 10,  4,  8,  9, 15, 13,  6,  1, 12,  0,  2, 11,  7,  5,  3)
    );

    const BLOCKBYTES = 128;
    const OUTBYTES   = 64;
    const KEYBYTES   = 64;

    /**
     * Turn two 32-bit integers into a fixed array representing a 64-bit integer.
     *
     * @internal You should not use this directly from another application
     *
     * @param int $high
     * @param int $low
     * @return SplFixedArray
     * @psalm-suppress MixedAssignment
     */
    public static function new64($high, $low)
    {
        $i64 = new SplFixedArray(2);
        $i64[0] = $high & 0xffffffff;
        $i64[1] = $low & 0xffffffff;
        return $i64;
    }

    /**
     * Convert an arbitrary number into an SplFixedArray of two 32-bit integers
     * that represents a 64-bit integer.
     *
     * @internal You should not use this directly from another application
     *
     * @param int $num
     * @return SplFixedArray
     */
    protected static function to64($num)
    {
        list($hi, $lo) = self::numericTo64BitInteger($num);
        return self::new64($hi, $lo);
    }

    /**
     * Adds two 64-bit integers together, returning their sum as a SplFixedArray
     * containing two 32-bit integers (representing a 64-bit integer).
     *
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $x
     * @param SplFixedArray $y
     * @return SplFixedArray
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedOperand
     */
    protected static function add64($x, $y)
    {
        $l = ($x[1] + $y[1]) & 0xffffffff;
        return self::new64(
            (int) ($x[0] + $y[0] + (
                ($l < $x[1]) ? 1 : 0
            )),
            (int) $l
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $x
     * @param SplFixedArray $y
     * @param SplFixedArray $z
     * @return SplFixedArray
     */
    protected static function add364($x, $y, $z)
    {
        return self::add64($x, self::add64($y, $z));
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $x
     * @param SplFixedArray $y
     * @return SplFixedArray
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function xor64(SplFixedArray $x, SplFixedArray $y)
    {
        if (!is_numeric($x[0])) {
            throw new SodiumException('x[0] is not an integer');
        }
        if (!is_numeric($x[1])) {
            throw new SodiumException('x[1] is not an integer');
        }
        if (!is_numeric($y[0])) {
            throw new SodiumException('y[0] is not an integer');
        }
        if (!is_numeric($y[1])) {
            throw new SodiumException('y[1] is not an integer');
        }
        return self::new64(
            (int) (($x[0] ^ $y[0]) & 0xffffffff),
            (int) (($x[1] ^ $y[1]) & 0xffffffff)
        );
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $x
     * @param int $c
     * @return SplFixedArray
     * @psalm-suppress MixedAssignment
     */
    public static function rotr64($x, $c)
    {
        if ($c >= 64) {
            $c %= 64;
        }
        if ($c >= 32) {
            /** @var int $tmp */
            $tmp = $x[0];
            $x[0] = $x[1];
            $x[1] = $tmp;
            $c -= 32;
        }
        if ($c === 0) {
            return $x;
        }

        $l0 = 0;
        $c = 64 - $c;

        if ($c < 32) {
            /** @var int $h0 */
            $h0 = ((int) ($x[0]) << $c) | (
                (
                    (int) ($x[1]) & ((1 << $c) - 1)
                        <<
                    (32 - $c)
                ) >> (32 - $c)
            );
            /** @var int $l0 */
            $l0 = (int) ($x[1]) << $c;
        } else {
            /** @var int $h0 */
            $h0 = (int) ($x[1]) << ($c - 32);
        }

        $h1 = 0;
        $c1 = 64 - $c;

        if ($c1 < 32) {
            /** @var int $h1 */
            $h1 = (int) ($x[0]) >> $c1;
            /** @var int $l1 */
            $l1 = ((int) ($x[1]) >> $c1) | ((int) ($x[0]) & ((1 << $c1) - 1)) << (32 - $c1);
        } else {
            /** @var int $l1 */
            $l1 = (int) ($x[0]) >> ($c1 - 32);
        }

        return self::new64($h0 | $h1, $l0 | $l1);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $x
     * @return int
     * @psalm-suppress MixedOperand
     */
    protected static function flatten64($x)
    {
        return (int) ($x[0] * 4294967296 + $x[1]);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $x
     * @param int $i
     * @return SplFixedArray
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayOffset
     */
    protected static function load64(SplFixedArray $x, $i)
    {
        /** @var int $l */
        $l = (int) ($x[$i])
             | ((int) ($x[$i+1]) << 8)
             | ((int) ($x[$i+2]) << 16)
             | ((int) ($x[$i+3]) << 24);
        /** @var int $h */
        $h = (int) ($x[$i+4])
             | ((int) ($x[$i+5]) << 8)
             | ((int) ($x[$i+6]) << 16)
             | ((int) ($x[$i+7]) << 24);
        return self::new64($h, $l);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $x
     * @param int $i
     * @param SplFixedArray $u
     * @return void
     * @psalm-suppress MixedAssignment
     */
    protected static function store64(SplFixedArray $x, $i, SplFixedArray $u)
    {
        $maxLength = $x->getSize() - 1;
        for ($j = 0; $j < 8; ++$j) {
            /*
               [0, 1, 2, 3, 4, 5, 6, 7]
                    ... becomes ...
               [0, 0, 0, 0, 1, 1, 1, 1]
            */
            /** @var int $uIdx */
            $uIdx = ((7 - $j) & 4) >> 2;
            $x[$i]   = ((int) ($u[$uIdx]) & 0xff);
            if (++$i > $maxLength) {
                return;
            }
            /** @psalm-suppress MixedOperand */
            $u[$uIdx] >>= 8;
        }
    }

    /**
     * This just sets the $iv static variable.
     *
     * @internal You should not use this directly from another application
     *
     * @return void
     */
    public static function pseudoConstructor()
    {
        static $called = false;
        if ($called) {
            return;
        }
        self::$iv = new SplFixedArray(8);
        self::$iv[0] = self::new64(0x6a09e667, 0xf3bcc908);
        self::$iv[1] = self::new64(0xbb67ae85, 0x84caa73b);
        self::$iv[2] = self::new64(0x3c6ef372, 0xfe94f82b);
        self::$iv[3] = self::new64(0xa54ff53a, 0x5f1d36f1);
        self::$iv[4] = self::new64(0x510e527f, 0xade682d1);
        self::$iv[5] = self::new64(0x9b05688c, 0x2b3e6c1f);
        self::$iv[6] = self::new64(0x1f83d9ab, 0xfb41bd6b);
        self::$iv[7] = self::new64(0x5be0cd19, 0x137e2179);

        $called = true;
    }

    /**
     * Returns a fresh BLAKE2 context.
     *
     * @internal You should not use this directly from another application
     *
     * @return SplFixedArray
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     */
    protected static function context()
    {
        $ctx    = new SplFixedArray(6);
        $ctx[0] = new SplFixedArray(8);   // h
        $ctx[1] = new SplFixedArray(2);   // t
        $ctx[2] = new SplFixedArray(2);   // f
        $ctx[3] = new SplFixedArray(256); // buf
        $ctx[4] = 0;                      // buflen
        $ctx[5] = 0;                      // last_node (uint8_t)

        for ($i = 8; $i--;) {
            $ctx[0][$i] = self::$iv[$i];
        }
        for ($i = 256; $i--;) {
            $ctx[3][$i] = 0;
        }

        $zero = self::new64(0, 0);
        $ctx[1][0] = $zero;
        $ctx[1][1] = $zero;
        $ctx[2][0] = $zero;
        $ctx[2][1] = $zero;

        return $ctx;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $ctx
     * @param SplFixedArray $buf
     * @return void
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedArrayOffset
     */
    protected static function compress(SplFixedArray $ctx, SplFixedArray $buf)
    {
        $m = new SplFixedArray(16);
        $v = new SplFixedArray(16);

        for ($i = 16; $i--;) {
            $m[$i] = self::load64($buf, $i << 3);
        }

        for ($i = 8; $i--;) {
            $v[$i] = $ctx[0][$i];
        }

        $v[ 8] = self::$iv[0];
        $v[ 9] = self::$iv[1];
        $v[10] = self::$iv[2];
        $v[11] = self::$iv[3];

        $v[12] = self::xor64($ctx[1][0], self::$iv[4]);
        $v[13] = self::xor64($ctx[1][1], self::$iv[5]);
        $v[14] = self::xor64($ctx[2][0], self::$iv[6]);
        $v[15] = self::xor64($ctx[2][1], self::$iv[7]);

        for ($r = 0; $r < 12; ++$r) {
            $v = self::G($r, 0, 0, 4, 8, 12, $v, $m);
            $v = self::G($r, 1, 1, 5, 9, 13, $v, $m);
            $v = self::G($r, 2, 2, 6, 10, 14, $v, $m);
            $v = self::G($r, 3, 3, 7, 11, 15, $v, $m);
            $v = self::G($r, 4, 0, 5, 10, 15, $v, $m);
            $v = self::G($r, 5, 1, 6, 11, 12, $v, $m);
            $v = self::G($r, 6, 2, 7, 8, 13, $v, $m);
            $v = self::G($r, 7, 3, 4, 9, 14, $v, $m);
        }

        for ($i = 8; $i--;) {
            $ctx[0][$i] = self::xor64(
                $ctx[0][$i], self::xor64($v[$i], $v[$i+8])
            );
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $r
     * @param int $i
     * @param int $a
     * @param int $b
     * @param int $c
     * @param int $d
     * @param SplFixedArray $v
     * @param SplFixedArray $m
     * @return SplFixedArray
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayOffset
     */
    public static function G($r, $i, $a, $b, $c, $d, SplFixedArray $v, SplFixedArray $m)
    {
        $v[$a] = self::add364($v[$a], $v[$b], $m[self::$sigma[$r][$i << 1]]);
        $v[$d] = self::rotr64(self::xor64($v[$d], $v[$a]), 32);
        $v[$c] = self::add64($v[$c], $v[$d]);
        $v[$b] = self::rotr64(self::xor64($v[$b], $v[$c]), 24);
        $v[$a] = self::add364($v[$a], $v[$b], $m[self::$sigma[$r][($i << 1) + 1]]);
        $v[$d] = self::rotr64(self::xor64($v[$d], $v[$a]), 16);
        $v[$c] = self::add64($v[$c], $v[$d]);
        $v[$b] = self::rotr64(self::xor64($v[$b], $v[$c]), 63);
        return $v;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $ctx
     * @param int $inc
     * @return void
     * @throws SodiumException
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     */
    public static function increment_counter($ctx, $inc)
    {
        if ($inc < 0) {
            throw new SodiumException('Increasing by a negative number makes no sense.');
        }
        $t = self::to64($inc);
        # S->t is $ctx[1] in our implementation

        # S->t[0] = ( uint64_t )( t >> 0 );
        $ctx[1][0] = self::add64($ctx[1][0], $t);

        # S->t[1] += ( S->t[0] < inc );
        if (self::flatten64($ctx[1][0]) < $inc) {
            $ctx[1][1] = self::add64($ctx[1][1], self::to64(1));
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $ctx
     * @param SplFixedArray $p
     * @param int $plen
     * @return void
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedArrayOffset
     * @psalm-suppress MixedOperand
     */
    public static function update(SplFixedArray $ctx, SplFixedArray $p, $plen)
    {
        self::pseudoConstructor();

        $offset = 0;
        while ($plen > 0) {
            $left = $ctx[4];
            $fill = 256 - $left;

            if ($plen > $fill) {
                # memcpy( S->buf + left, in, fill ); /* Fill buffer */
                for ($i = $fill; $i--;) {
                    $ctx[3][$i + $left] = $p[$i + $offset];
                }

                # S->buflen += fill;
                $ctx[4] += $fill;

                # blake2b_increment_counter( S, BLAKE2B_BLOCKBYTES );
                self::increment_counter($ctx, 128);

                # blake2b_compress( S, S->buf ); /* Compress */
                self::compress($ctx, $ctx[3]);

                # memcpy( S->buf, S->buf + BLAKE2B_BLOCKBYTES, BLAKE2B_BLOCKBYTES ); /* Shift buffer left */
                for ($i = 128; $i--;) {
                    $ctx[3][$i] = $ctx[3][$i + 128];
                }

                # S->buflen -= BLAKE2B_BLOCKBYTES;
                $ctx[4] -= 128;

                # in += fill;
                $offset += $fill;

                # inlen -= fill;
                $plen -= $fill;
            } else {
                for ($i = $plen; $i--;) {
                    $ctx[3][$i + $left] = $p[$i + $offset];
                }
                $ctx[4] += $plen;
                $offset += $plen;
                $plen -= $plen;
            }
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $ctx
     * @param SplFixedArray $out
     * @return SplFixedArray
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedArrayOffset
     * @psalm-suppress MixedOperand
     */
    public static function finish(SplFixedArray $ctx, SplFixedArray $out)
    {
        self::pseudoConstructor();
        if ($ctx[4] > 128) {
            self::increment_counter($ctx, 128);
            self::compress($ctx, $ctx[3]);
            $ctx[4] -= 128;
            if ($ctx[4] > 128) {
                throw new SodiumException('Failed to assert that buflen <= 128 bytes');
            }
            for ($i = $ctx[4]; $i--;) {
                $ctx[3][$i] = $ctx[3][$i + 128];
            }
        }

        self::increment_counter($ctx, $ctx[4]);
        $ctx[2][0] = self::new64(0xffffffff, 0xffffffff);

        for ($i = 256 - $ctx[4]; $i--;) {
            $ctx[3][$i+$ctx[4]] = 0;
        }

        self::compress($ctx, $ctx[3]);

        $i = (int) (($out->getSize() - 1) / 8);
        for (; $i >= 0; --$i) {
            self::store64($out, $i << 3, $ctx[0][$i]);
        }
        return $out;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray|null $key
     * @param int $outlen
     * @param SplFixedArray|null $salt
     * @param SplFixedArray|null $personal
     * @return SplFixedArray
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedArrayOffset
     */
    public static function init(
        $key = null,
        $outlen = 64,
        $salt = null,
        $personal = null
    ) {
        self::pseudoConstructor();
        $klen = 0;

        if ($key !== null) {
            if (count($key) > 64) {
                throw new SodiumException('Invalid key size');
            }
            $klen = count($key);
        }

        if ($outlen > 64) {
            throw new SodiumException('Invalid output size');
        }

        $ctx = self::context();

        $p = new SplFixedArray(64);
        // Zero our param buffer...
        for ($i = 64; --$i;) {
            $p[$i] = 0;
        }

        $p[0] = $outlen; // digest_length
        $p[1] = $klen;   // key_length
        $p[2] = 1;       // fanout
        $p[3] = 1;       // depth

        if ($salt instanceof SplFixedArray) {
            // salt: [32] through [47]
            for ($i = 0; $i < 16; ++$i) {
                $p[32 + $i] = (int) $salt[$i];
            }
        }
        if ($personal instanceof SplFixedArray) {
            // personal: [48] through [63]
            for ($i = 0; $i < 16; ++$i) {
                $p[48 + $i] = (int) $personal[$i];
            }
        }

        $ctx[0][0] = self::xor64(
            $ctx[0][0],
            self::load64($p, 0)
        );
        if ($salt instanceof SplFixedArray || $personal instanceof SplFixedArray) {
            // We need to do what blake2b_init_param() does:
            for ($i = 1; $i < 8; ++$i) {
                $ctx[0][$i] = self::xor64(
                    $ctx[0][$i],
                    self::load64($p, $i << 3)
                );
            }
        }

        if ($klen > 0 && $key instanceof SplFixedArray) {
            $block = new SplFixedArray(128);
            for ($i = 128; $i--;) {
                $block[$i] = 0;
            }
            for ($i = $klen; $i--;) {
                $block[$i] = $key[$i];
            }
            self::update($ctx, $block, 128);
            $ctx[4] = 128;
        }

        return $ctx;
    }

    /**
     * Convert a string into an SplFixedArray of integers
     *
     * @internal You should not use this directly from another application
     *
     * @param string $str
     * @return SplFixedArray
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public static function stringToSplFixedArray($str = '')
    {
        $values = unpack('C*', $str);
        return SplFixedArray::fromArray(array_values($values));
    }

    /**
     * Convert an SplFixedArray of integers into a string
     *
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $a
     * @return string
     * @throws TypeError
     */
    public static function SplFixedArrayToString(SplFixedArray $a)
    {
        /**
         * @var array<int, int|string> $arr
         */
        $arr = $a->toArray();
        $c = $a->count();
        array_unshift($arr, str_repeat('C', $c));
        return (string) (call_user_func_array('pack', $arr));
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param SplFixedArray $ctx
     * @return string
     * @throws TypeError
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedArrayOffset
     * @psalm-suppress MixedMethodCall
     */
    public static function contextToString(SplFixedArray $ctx)
    {
        $str = '';
        /** @var array<int, array<int, int>> $ctxA */
        $ctxA = $ctx[0]->toArray();

        # uint64_t h[8];
        for ($i = 0; $i < 8; ++$i) {
            $str .= self::store32_le($ctxA[$i][1]);
            $str .= self::store32_le($ctxA[$i][0]);
        }

        # uint64_t t[2];
        # uint64_t f[2];
        for ($i = 1; $i < 3; ++$i) {
            $ctxA = $ctx[$i]->toArray();
            $str .= self::store32_le($ctxA[0][1]);
            $str .= self::store32_le($ctxA[0][0]);
            $str .= self::store32_le($ctxA[1][1]);
            $str .= self::store32_le($ctxA[1][0]);
        }

        # uint8_t buf[2 * 128];
        $str .= self::SplFixedArrayToString($ctx[3]);

        /** @var int $ctx4 */
        $ctx4 = (int) $ctx[4];

        # size_t buflen;
        $str .= implode('', array(
            self::intToChr($ctx4 & 0xff),
            self::intToChr(($ctx4 >> 8) & 0xff),
            self::intToChr(($ctx4 >> 16) & 0xff),
            self::intToChr(($ctx4 >> 24) & 0xff),
            self::intToChr(($ctx4 >> 32) & 0xff),
            self::intToChr(($ctx4 >> 40) & 0xff),
            self::intToChr(($ctx4 >> 48) & 0xff),
            self::intToChr(($ctx4 >> 56) & 0xff)
        ));
        # uint8_t last_node;
        return $str . self::intToChr($ctx[5]) . str_repeat("\x00", 23);
    }

    /**
     * Creates an SplFixedArray containing other SplFixedArray elements, from
     * a string (compatible with \Sodium\crypto_generichash_{init, update, final})
     *
     * @internal You should not use this directly from another application
     *
     * @param string $string
     * @return SplFixedArray
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArrayAssignment
     */
    public static function stringToContext($string)
    {
        $ctx = self::context();

        # uint64_t h[8];
        for ($i = 0; $i < 8; ++$i) {
            $ctx[0][$i] = SplFixedArray::fromArray(
                array(
                    self::load_4(
                        self::substr($string, (($i << 3) + 4), 4)
                    ),
                    self::load_4(
                        self::substr($string, (($i << 3) + 0), 4)
                    )
                )
            );
        }

        # uint64_t t[2];
        # uint64_t f[2];
        for ($i = 1; $i < 3; ++$i) {
            $ctx[$i][1] = SplFixedArray::fromArray(
                array(
                    self::load_4(self::substr($string, 76 + (($i - 1) << 4), 4)),
                    self::load_4(self::substr($string, 72 + (($i - 1) << 4), 4))
                )
            );
            $ctx[$i][0] = SplFixedArray::fromArray(
                array(
                    self::load_4(self::substr($string, 68 + (($i - 1) << 4), 4)),
                    self::load_4(self::substr($string, 64 + (($i - 1) << 4), 4))
                )
            );
        }

        # uint8_t buf[2 * 128];
        $ctx[3] = self::stringToSplFixedArray(self::substr($string, 96, 256));

        # uint8_t buf[2 * 128];
        $int = 0;
        for ($i = 0; $i < 8; ++$i) {
            $int |= self::chrToInt($string[352 + $i]) << ($i << 3);
        }
        $ctx[4] = $int;

        return $ctx;
    }
}
