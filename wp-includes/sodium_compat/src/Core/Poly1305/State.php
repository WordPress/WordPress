<?php

if (class_exists('ParagonIE_Sodium_Core_Poly1305_State', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_Poly1305_State
 */
class ParagonIE_Sodium_Core_Poly1305_State extends ParagonIE_Sodium_Core_Util
{
    /**
     * @var array<int, int>
     */
    protected $buffer = array();

    /**
     * @var bool
     */
    protected $final = false;

    /**
     * @var array<int, int>
     */
    public $h;

    /**
     * @var int
     */
    protected $leftover = 0;

    /**
     * @var int[]
     */
    public $r;

    /**
     * @var int[]
     */
    public $pad;

    /**
     * ParagonIE_Sodium_Core_Poly1305_State constructor.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $key
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    public function __construct($key = '')
    {
        if (self::strlen($key) < 32) {
            throw new InvalidArgumentException(
                'Poly1305 requires a 32-byte key'
            );
        }
        /* r &= 0xffffffc0ffffffc0ffffffc0fffffff */
        $this->r = array(
            (int) ((self::load_4(self::substr($key, 0, 4))) & 0x3ffffff),
            (int) ((self::load_4(self::substr($key, 3, 4)) >> 2) & 0x3ffff03),
            (int) ((self::load_4(self::substr($key, 6, 4)) >> 4) & 0x3ffc0ff),
            (int) ((self::load_4(self::substr($key, 9, 4)) >> 6) & 0x3f03fff),
            (int) ((self::load_4(self::substr($key, 12, 4)) >> 8) & 0x00fffff)
        );

        /* h = 0 */
        $this->h = array(0, 0, 0, 0, 0);

        /* save pad for later */
        $this->pad = array(
            self::load_4(self::substr($key, 16, 4)),
            self::load_4(self::substr($key, 20, 4)),
            self::load_4(self::substr($key, 24, 4)),
            self::load_4(self::substr($key, 28, 4)),
        );

        $this->leftover = 0;
        $this->final = false;
    }

    /**
     * Zero internal buffer upon destruction
     */
    public function __destruct()
    {
        $this->r[0] ^= $this->r[0];
        $this->r[1] ^= $this->r[1];
        $this->r[2] ^= $this->r[2];
        $this->r[3] ^= $this->r[3];
        $this->r[4] ^= $this->r[4];
        $this->h[0] ^= $this->h[0];
        $this->h[1] ^= $this->h[1];
        $this->h[2] ^= $this->h[2];
        $this->h[3] ^= $this->h[3];
        $this->h[4] ^= $this->h[4];
        $this->pad[0] ^= $this->pad[0];
        $this->pad[1] ^= $this->pad[1];
        $this->pad[2] ^= $this->pad[2];
        $this->pad[3] ^= $this->pad[3];
        $this->leftover = 0;
        $this->final = true;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $message
     * @return self
     * @throws SodiumException
     * @throws TypeError
     */
    public function update($message = '')
    {
        $bytes = self::strlen($message);
        if ($bytes < 1) {
            return $this;
        }

        /* handle leftover */
        if ($this->leftover) {
            $want = ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE - $this->leftover;
            if ($want > $bytes) {
                $want = $bytes;
            }
            for ($i = 0; $i < $want; ++$i) {
                $mi = self::chrToInt($message[$i]);
                $this->buffer[$this->leftover + $i] = $mi;
            }
            // We snip off the leftmost bytes.
            $message = self::substr($message, $want);
            $bytes = self::strlen($message);
            $this->leftover += $want;
            if ($this->leftover < ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE) {
                // We still don't have enough to run $this->blocks()
                return $this;
            }

            $this->blocks(
                self::intArrayToString($this->buffer),
                ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE
            );
            $this->leftover = 0;
        }

        /* process full blocks */
        if ($bytes >= ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE) {
            /** @var int $want */
            $want = $bytes & ~(ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE - 1);
            if ($want >= ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE) {
                $block = self::substr($message, 0, $want);
                if (self::strlen($block) >= ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE) {
                    $this->blocks($block, $want);
                    $message = self::substr($message, $want);
                    $bytes = self::strlen($message);
                }
            }
        }

        /* store leftover */
        if ($bytes) {
            for ($i = 0; $i < $bytes; ++$i) {
                $mi = self::chrToInt($message[$i]);
                $this->buffer[$this->leftover + $i] = $mi;
            }
            $this->leftover = (int) $this->leftover + $bytes;
        }
        return $this;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $message
     * @param int $bytes
     * @return self
     * @throws TypeError
     */
    public function blocks($message, $bytes)
    {
        if (self::strlen($message) < 16) {
            $message = str_pad($message, 16, "\x00", STR_PAD_RIGHT);
        }
        /** @var int $hibit */
        $hibit = $this->final ? 0 : 1 << 24; /* 1 << 128 */
        $r0 = (int) $this->r[0];
        $r1 = (int) $this->r[1];
        $r2 = (int) $this->r[2];
        $r3 = (int) $this->r[3];
        $r4 = (int) $this->r[4];

        $s1 = self::mul($r1, 5, 3);
        $s2 = self::mul($r2, 5, 3);
        $s3 = self::mul($r3, 5, 3);
        $s4 = self::mul($r4, 5, 3);

        $h0 = $this->h[0];
        $h1 = $this->h[1];
        $h2 = $this->h[2];
        $h3 = $this->h[3];
        $h4 = $this->h[4];

        while ($bytes >= ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE) {
            /* h += m[i] */
            $h0 +=  self::load_4(self::substr($message, 0, 4))       & 0x3ffffff;
            $h1 += (self::load_4(self::substr($message, 3, 4)) >> 2) & 0x3ffffff;
            $h2 += (self::load_4(self::substr($message, 6, 4)) >> 4) & 0x3ffffff;
            $h3 += (self::load_4(self::substr($message, 9, 4)) >> 6) & 0x3ffffff;
            $h4 += (self::load_4(self::substr($message, 12, 4)) >> 8) | $hibit;

            /* h *= r */
            $d0 = (
                self::mul($h0, $r0, 27) +
                self::mul($s4, $h1, 27) +
                self::mul($s3, $h2, 27) +
                self::mul($s2, $h3, 27) +
                self::mul($s1, $h4, 27)
            );

            $d1 = (
                self::mul($h0, $r1, 27) +
                self::mul($h1, $r0, 27) +
                self::mul($s4, $h2, 27) +
                self::mul($s3, $h3, 27) +
                self::mul($s2, $h4, 27)
            );

            $d2 = (
                self::mul($h0, $r2, 27) +
                self::mul($h1, $r1, 27) +
                self::mul($h2, $r0, 27) +
                self::mul($s4, $h3, 27) +
                self::mul($s3, $h4, 27)
            );

            $d3 = (
                self::mul($h0, $r3, 27) +
                self::mul($h1, $r2, 27) +
                self::mul($h2, $r1, 27) +
                self::mul($h3, $r0, 27) +
                self::mul($s4, $h4, 27)
            );

            $d4 = (
                self::mul($h0, $r4, 27) +
                self::mul($h1, $r3, 27) +
                self::mul($h2, $r2, 27) +
                self::mul($h3, $r1, 27) +
                self::mul($h4, $r0, 27)
            );

            /* (partial) h %= p */
            /** @var int $c */
            $c = $d0 >> 26;
            /** @var int $h0 */
            $h0 = $d0 & 0x3ffffff;
            $d1 += $c;

            /** @var int $c */
            $c = $d1 >> 26;
            /** @var int $h1 */
            $h1 = $d1 & 0x3ffffff;
            $d2 += $c;

            /** @var int $c */
            $c = $d2 >> 26;
            /** @var int $h2  */
            $h2 = $d2 & 0x3ffffff;
            $d3 += $c;

            /** @var int $c */
            $c = $d3 >> 26;
            /** @var int $h3 */
            $h3 = $d3 & 0x3ffffff;
            $d4 += $c;

            /** @var int $c */
            $c = $d4 >> 26;
            /** @var int $h4 */
            $h4 = $d4 & 0x3ffffff;
            $h0 += (int) self::mul($c, 5, 3);

            /** @var int $c */
            $c = $h0 >> 26;
            /** @var int $h0 */
            $h0 &= 0x3ffffff;
            $h1 += $c;

            // Chop off the left 32 bytes.
            $message = self::substr(
                $message,
                ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE
            );
            $bytes -= ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE;
        }

        $this->h = array(
            (int) ($h0 & 0xffffffff),
            (int) ($h1 & 0xffffffff),
            (int) ($h2 & 0xffffffff),
            (int) ($h3 & 0xffffffff),
            (int) ($h4 & 0xffffffff)
        );
        return $this;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return string
     * @throws TypeError
     */
    public function finish()
    {
        /* process the remaining block */
        if ($this->leftover) {
            $i = $this->leftover;
            $this->buffer[$i++] = 1;
            for (; $i < ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE; ++$i) {
                $this->buffer[$i] = 0;
            }
            $this->final = true;
            $this->blocks(
                self::substr(
                    self::intArrayToString($this->buffer),
                    0,
                    ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE
                ),
                ParagonIE_Sodium_Core_Poly1305::BLOCK_SIZE
            );
        }

        $h0 = (int) $this->h[0];
        $h1 = (int) $this->h[1];
        $h2 = (int) $this->h[2];
        $h3 = (int) $this->h[3];
        $h4 = (int) $this->h[4];

        /** @var int $c */
        $c = $h1 >> 26;
        /** @var int $h1 */
        $h1 &= 0x3ffffff;
        /** @var int $h2 */
        $h2 += $c;
        /** @var int $c */
        $c = $h2 >> 26;
        /** @var int $h2 */
        $h2 &= 0x3ffffff;
        $h3 += $c;
        /** @var int $c */
        $c = $h3 >> 26;
        $h3 &= 0x3ffffff;
        $h4 += $c;
        /** @var int $c */
        $c = $h4 >> 26;
        $h4 &= 0x3ffffff;
        /** @var int $h0 */
        $h0 += self::mul($c, 5, 3);
        /** @var int $c */
        $c = $h0 >> 26;
        /** @var int $h0 */
        $h0 &= 0x3ffffff;
        /** @var int $h1 */
        $h1 += $c;

        /* compute h + -p */
        /** @var int $g0 */
        $g0 = $h0 + 5;
        /** @var int $c */
        $c = $g0 >> 26;
        /** @var int $g0 */
        $g0 &= 0x3ffffff;

        /** @var int $g1 */
        $g1 = $h1 + $c;
        /** @var int $c */
        $c = $g1 >> 26;
        $g1 &= 0x3ffffff;

        /** @var int $g2 */
        $g2 = $h2 + $c;
        /** @var int $c */
        $c = $g2 >> 26;
        /** @var int $g2 */
        $g2 &= 0x3ffffff;

        /** @var int $g3 */
        $g3 = $h3 + $c;
        /** @var int $c */
        $c = $g3 >> 26;
        /** @var int $g3 */
        $g3 &= 0x3ffffff;

        /** @var int $g4 */
        $g4 = ($h4 + $c - (1 << 26)) & 0xffffffff;

        /* select h if h < p, or h + -p if h >= p */
        /** @var int $mask */
        $mask = ($g4 >> 31) - 1;

        $g0 &= $mask;
        $g1 &= $mask;
        $g2 &= $mask;
        $g3 &= $mask;
        $g4 &= $mask;

        /** @var int $mask */
        $mask = ~$mask & 0xffffffff;
        /** @var int $h0 */
        $h0 = ($h0 & $mask) | $g0;
        /** @var int $h1 */
        $h1 = ($h1 & $mask) | $g1;
        /** @var int $h2 */
        $h2 = ($h2 & $mask) | $g2;
        /** @var int $h3 */
        $h3 = ($h3 & $mask) | $g3;
        /** @var int $h4 */
        $h4 = ($h4 & $mask) | $g4;

        /* h = h % (2^128) */
        /** @var int $h0 */
        $h0 = (($h0) | ($h1 << 26)) & 0xffffffff;
        /** @var int $h1 */
        $h1 = (($h1 >>  6) | ($h2 << 20)) & 0xffffffff;
        /** @var int $h2 */
        $h2 = (($h2 >> 12) | ($h3 << 14)) & 0xffffffff;
        /** @var int $h3 */
        $h3 = (($h3 >> 18) | ($h4 <<  8)) & 0xffffffff;

        /* mac = (h + pad) % (2^128) */
        $f = (int) ($h0 + $this->pad[0]);
        $h0 = (int) $f;
        $f = (int) ($h1 + $this->pad[1] + ($f >> 32));
        $h1 = (int) $f;
        $f = (int) ($h2 + $this->pad[2] + ($f >> 32));
        $h2 = (int) $f;
        $f = (int) ($h3 + $this->pad[3] + ($f >> 32));
        $h3 = (int) $f;

        return self::store32_le($h0 & 0xffffffff) .
            self::store32_le($h1 & 0xffffffff) .
            self::store32_le($h2 & 0xffffffff) .
            self::store32_le($h3 & 0xffffffff);
    }
}
