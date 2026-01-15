<?php

if (class_exists('ParagonIE_Sodium_Core32_Poly1305_State', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core32_Poly1305_State
 */
class ParagonIE_Sodium_Core32_Poly1305_State extends ParagonIE_Sodium_Core32_Util
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
     * @var array<int, ParagonIE_Sodium_Core32_Int32>
     */
    public $h;

    /**
     * @var int
     */
    protected $leftover = 0;

    /**
     * @var array<int, ParagonIE_Sodium_Core32_Int32>
     */
    public $r;

    /**
     * @var array<int, ParagonIE_Sodium_Core32_Int64>
     */
    public $pad;

    /**
     * ParagonIE_Sodium_Core32_Poly1305_State constructor.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $key
     * @throws InvalidArgumentException
     * @throws SodiumException
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
            // st->r[0] = ...
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 0, 4))
                ->setUnsignedInt(true)
                ->mask(0x3ffffff),
            // st->r[1] = ...
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 3, 4))
                ->setUnsignedInt(true)
                ->shiftRight(2)
                ->mask(0x3ffff03),
            // st->r[2] = ...
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 6, 4))
                ->setUnsignedInt(true)
                ->shiftRight(4)
                ->mask(0x3ffc0ff),
            // st->r[3] = ...
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 9, 4))
                ->setUnsignedInt(true)
                ->shiftRight(6)
                ->mask(0x3f03fff),
            // st->r[4] = ...
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 12, 4))
                ->setUnsignedInt(true)
                ->shiftRight(8)
                ->mask(0x00fffff)
        );

        /* h = 0 */
        $this->h = array(
            new ParagonIE_Sodium_Core32_Int32(array(0, 0), true),
            new ParagonIE_Sodium_Core32_Int32(array(0, 0), true),
            new ParagonIE_Sodium_Core32_Int32(array(0, 0), true),
            new ParagonIE_Sodium_Core32_Int32(array(0, 0), true),
            new ParagonIE_Sodium_Core32_Int32(array(0, 0), true)
        );

        /* save pad for later */
        $this->pad = array(
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 16, 4))
                ->setUnsignedInt(true)->toInt64(),
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 20, 4))
                ->setUnsignedInt(true)->toInt64(),
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 24, 4))
                ->setUnsignedInt(true)->toInt64(),
            ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($key, 28, 4))
                ->setUnsignedInt(true)->toInt64(),
        );

        $this->leftover = 0;
        $this->final = false;
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

        /* handle leftover */
        if ($this->leftover) {
            /** @var int $want */
            $want = ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE - $this->leftover;
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
            if ($this->leftover < ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE) {
                // We still don't have enough to run $this->blocks()
                return $this;
            }

            $this->blocks(
                self::intArrayToString($this->buffer),
                ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE
            );
            $this->leftover = 0;
        }

        /* process full blocks */
        if ($bytes >= ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE) {
            /** @var int $want */
            $want = $bytes & ~(ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE - 1);
            if ($want >= ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE) {
                /** @var string $block */
                $block = self::substr($message, 0, $want);
                if (self::strlen($block) >= ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE) {
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
     * @throws SodiumException
     * @throws TypeError
     */
    public function blocks($message, $bytes)
    {
        if (self::strlen($message) < 16) {
            $message = str_pad($message, 16, "\x00", STR_PAD_RIGHT);
        }
        $hibit = ParagonIE_Sodium_Core32_Int32::fromInt((int) ($this->final ? 0 : 1 << 24)); /* 1 << 128 */
        $hibit->setUnsignedInt(true);
        $zero = new ParagonIE_Sodium_Core32_Int64(array(0, 0, 0, 0), true);
        /**
         * @var ParagonIE_Sodium_Core32_Int64 $d0
         * @var ParagonIE_Sodium_Core32_Int64 $d1
         * @var ParagonIE_Sodium_Core32_Int64 $d2
         * @var ParagonIE_Sodium_Core32_Int64 $d3
         * @var ParagonIE_Sodium_Core32_Int64 $d4
         * @var ParagonIE_Sodium_Core32_Int64 $r0
         * @var ParagonIE_Sodium_Core32_Int64 $r1
         * @var ParagonIE_Sodium_Core32_Int64 $r2
         * @var ParagonIE_Sodium_Core32_Int64 $r3
         * @var ParagonIE_Sodium_Core32_Int64 $r4
         *
         * @var ParagonIE_Sodium_Core32_Int32 $h0
         * @var ParagonIE_Sodium_Core32_Int32 $h1
         * @var ParagonIE_Sodium_Core32_Int32 $h2
         * @var ParagonIE_Sodium_Core32_Int32 $h3
         * @var ParagonIE_Sodium_Core32_Int32 $h4
         */
        $r0 = $this->r[0]->toInt64();
        $r1 = $this->r[1]->toInt64();
        $r2 = $this->r[2]->toInt64();
        $r3 = $this->r[3]->toInt64();
        $r4 = $this->r[4]->toInt64();

        $s1 = $r1->toInt64()->mulInt(5, 3);
        $s2 = $r2->toInt64()->mulInt(5, 3);
        $s3 = $r3->toInt64()->mulInt(5, 3);
        $s4 = $r4->toInt64()->mulInt(5, 3);

        $h0 = $this->h[0];
        $h1 = $this->h[1];
        $h2 = $this->h[2];
        $h3 = $this->h[3];
        $h4 = $this->h[4];

        while ($bytes >= ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE) {
            /* h += m[i] */
            $h0 = $h0->addInt32(
                ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 0, 4))
                    ->mask(0x3ffffff)
            )->toInt64();
            $h1 = $h1->addInt32(
                ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 3, 4))
                    ->shiftRight(2)
                    ->mask(0x3ffffff)
            )->toInt64();
            $h2 = $h2->addInt32(
                ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 6, 4))
                    ->shiftRight(4)
                    ->mask(0x3ffffff)
            )->toInt64();
            $h3 = $h3->addInt32(
                ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 9, 4))
                    ->shiftRight(6)
                    ->mask(0x3ffffff)
            )->toInt64();
            $h4 = $h4->addInt32(
                ParagonIE_Sodium_Core32_Int32::fromReverseString(self::substr($message, 12, 4))
                    ->shiftRight(8)
                    ->orInt32($hibit)
            )->toInt64();

            /* h *= r */
            $d0 = $zero
                ->addInt64($h0->mulInt64($r0, 27))
                ->addInt64($s4->mulInt64($h1, 27))
                ->addInt64($s3->mulInt64($h2, 27))
                ->addInt64($s2->mulInt64($h3, 27))
                ->addInt64($s1->mulInt64($h4, 27));

            $d1 = $zero
                ->addInt64($h0->mulInt64($r1, 27))
                ->addInt64($h1->mulInt64($r0, 27))
                ->addInt64($s4->mulInt64($h2, 27))
                ->addInt64($s3->mulInt64($h3, 27))
                ->addInt64($s2->mulInt64($h4, 27));

            $d2 = $zero
                ->addInt64($h0->mulInt64($r2, 27))
                ->addInt64($h1->mulInt64($r1, 27))
                ->addInt64($h2->mulInt64($r0, 27))
                ->addInt64($s4->mulInt64($h3, 27))
                ->addInt64($s3->mulInt64($h4, 27));

            $d3 = $zero
                ->addInt64($h0->mulInt64($r3, 27))
                ->addInt64($h1->mulInt64($r2, 27))
                ->addInt64($h2->mulInt64($r1, 27))
                ->addInt64($h3->mulInt64($r0, 27))
                ->addInt64($s4->mulInt64($h4, 27));

            $d4 = $zero
                ->addInt64($h0->mulInt64($r4, 27))
                ->addInt64($h1->mulInt64($r3, 27))
                ->addInt64($h2->mulInt64($r2, 27))
                ->addInt64($h3->mulInt64($r1, 27))
                ->addInt64($h4->mulInt64($r0, 27));

            /* (partial) h %= p */
            $c = $d0->shiftRight(26);
            $h0 = $d0->toInt32()->mask(0x3ffffff);
            $d1 = $d1->addInt64($c);

            $c = $d1->shiftRight(26);
            $h1 = $d1->toInt32()->mask(0x3ffffff);
            $d2 = $d2->addInt64($c);

            $c = $d2->shiftRight(26);
            $h2 = $d2->toInt32()->mask(0x3ffffff);
            $d3 = $d3->addInt64($c);

            $c = $d3->shiftRight(26);
            $h3 = $d3->toInt32()->mask(0x3ffffff);
            $d4 = $d4->addInt64($c);

            $c = $d4->shiftRight(26);
            $h4 = $d4->toInt32()->mask(0x3ffffff);
            $h0 = $h0->addInt32($c->toInt32()->mulInt(5, 3));

            $c = $h0->shiftRight(26);
            $h0 = $h0->mask(0x3ffffff);
            $h1 = $h1->addInt32($c);

            // Chop off the left 32 bytes.
            $message = self::substr(
                $message,
                ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE
            );
            $bytes -= ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE;
        }

        /** @var array<int, ParagonIE_Sodium_Core32_Int32> $h */
        $this->h = array($h0, $h1, $h2, $h3, $h4);
        return $this;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    public function finish()
    {
        /* process the remaining block */
        if ($this->leftover) {
            $i = $this->leftover;
            $this->buffer[$i++] = 1;
            for (; $i < ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE; ++$i) {
                $this->buffer[$i] = 0;
            }
            $this->final = true;
            $this->blocks(
                self::substr(
                    self::intArrayToString($this->buffer),
                    0,
                    ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE
                ),
                $b = ParagonIE_Sodium_Core32_Poly1305::BLOCK_SIZE
            );
        }

        /**
         * @var ParagonIE_Sodium_Core32_Int32 $f
         * @var ParagonIE_Sodium_Core32_Int32 $g0
         * @var ParagonIE_Sodium_Core32_Int32 $g1
         * @var ParagonIE_Sodium_Core32_Int32 $g2
         * @var ParagonIE_Sodium_Core32_Int32 $g3
         * @var ParagonIE_Sodium_Core32_Int32 $g4
         * @var ParagonIE_Sodium_Core32_Int32 $h0
         * @var ParagonIE_Sodium_Core32_Int32 $h1
         * @var ParagonIE_Sodium_Core32_Int32 $h2
         * @var ParagonIE_Sodium_Core32_Int32 $h3
         * @var ParagonIE_Sodium_Core32_Int32 $h4
         */
        $h0 = $this->h[0];
        $h1 = $this->h[1];
        $h2 = $this->h[2];
        $h3 = $this->h[3];
        $h4 = $this->h[4];

        $c = $h1->shiftRight(26);           # $c = $h1 >> 26;
        $h1 = $h1->mask(0x3ffffff);         # $h1 &= 0x3ffffff;

        $h2 = $h2->addInt32($c);            # $h2 += $c;
        $c = $h2->shiftRight(26);           # $c = $h2 >> 26;
        $h2 = $h2->mask(0x3ffffff);         # $h2 &= 0x3ffffff;

        $h3 = $h3->addInt32($c);            # $h3 += $c;
        $c = $h3->shiftRight(26);           # $c = $h3 >> 26;
        $h3 = $h3->mask(0x3ffffff);         # $h3 &= 0x3ffffff;

        $h4 = $h4->addInt32($c);            # $h4 += $c;
        $c = $h4->shiftRight(26);           # $c = $h4 >> 26;
        $h4 = $h4->mask(0x3ffffff);         # $h4 &= 0x3ffffff;

        $h0 = $h0->addInt32($c->mulInt(5, 3)); # $h0 += self::mul($c, 5);
        $c = $h0->shiftRight(26);           # $c = $h0 >> 26;
        $h0 = $h0->mask(0x3ffffff);         # $h0 &= 0x3ffffff;
        $h1 = $h1->addInt32($c);            # $h1 += $c;

        /* compute h + -p */
        $g0 = $h0->addInt(5);
        $c  = $g0->shiftRight(26);
        $g0 = $g0->mask(0x3ffffff);
        $g1 = $h1->addInt32($c);
        $c  = $g1->shiftRight(26);
        $g1 = $g1->mask(0x3ffffff);
        $g2 = $h2->addInt32($c);
        $c  = $g2->shiftRight(26);
        $g2 = $g2->mask(0x3ffffff);
        $g3 = $h3->addInt32($c);
        $c  = $g3->shiftRight(26);
        $g3 = $g3->mask(0x3ffffff);
        $g4 = $h4->addInt32($c)->subInt(1 << 26);

        # $mask = ($g4 >> 31) - 1;
        /* select h if h < p, or h + -p if h >= p */
        $mask = (int) (($g4->toInt() >> 31) + 1);

        $g0 = $g0->mask($mask);
        $g1 = $g1->mask($mask);
        $g2 = $g2->mask($mask);
        $g3 = $g3->mask($mask);
        $g4 = $g4->mask($mask);

        /** @var int $mask */
        $mask = ~$mask;

        $h0 = $h0->mask($mask)->orInt32($g0);
        $h1 = $h1->mask($mask)->orInt32($g1);
        $h2 = $h2->mask($mask)->orInt32($g2);
        $h3 = $h3->mask($mask)->orInt32($g3);
        $h4 = $h4->mask($mask)->orInt32($g4);

        /* h = h % (2^128) */
        $h0 = $h0->orInt32($h1->shiftLeft(26));
        $h1 = $h1->shiftRight(6)->orInt32($h2->shiftLeft(20));
        $h2 = $h2->shiftRight(12)->orInt32($h3->shiftLeft(14));
        $h3 = $h3->shiftRight(18)->orInt32($h4->shiftLeft(8));

        /* mac = (h + pad) % (2^128) */
        $f = $h0->toInt64()->addInt64($this->pad[0]);
        $h0 = $f->toInt32();
        $f = $h1->toInt64()->addInt64($this->pad[1])->addInt($h0->overflow);
        $h1 = $f->toInt32();
        $f = $h2->toInt64()->addInt64($this->pad[2])->addInt($h1->overflow);
        $h2 = $f->toInt32();
        $f = $h3->toInt64()->addInt64($this->pad[3])->addInt($h2->overflow);
        $h3 = $f->toInt32();

        return $h0->toReverseString() .
            $h1->toReverseString() .
            $h2->toReverseString() .
            $h3->toReverseString();
    }
}
