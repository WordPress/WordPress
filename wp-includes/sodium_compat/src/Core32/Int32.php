<?php

/**
 * Class ParagonIE_Sodium_Core32_Int32
 *
 * Encapsulates a 32-bit integer.
 *
 * These are immutable. It always returns a new instance.
 */
class ParagonIE_Sodium_Core32_Int32
{
    /**
     * @var array<int, int> - two 16-bit integers
     *
     * 0 is the higher 16 bits
     * 1 is the lower 16 bits
     */
    public $limbs = array(0, 0);

    /**
     * @var int
     */
    public $overflow = 0;

    /**
     * @var bool
     */
    public $unsignedInt = false;

    /**
     * ParagonIE_Sodium_Core32_Int32 constructor.
     * @param array $array
     * @param bool $unsignedInt
     */
    public function __construct($array = array(0, 0), $unsignedInt = false)
    {
        $this->limbs = array(
            (int) $array[0],
            (int) $array[1]
        );
        $this->overflow = 0;
        $this->unsignedInt = $unsignedInt;
    }

    /**
     * Adds two int32 objects
     *
     * @param ParagonIE_Sodium_Core32_Int32 $addend
     * @return ParagonIE_Sodium_Core32_Int32
     */
    public function addInt32(ParagonIE_Sodium_Core32_Int32 $addend)
    {
        $i0 = $this->limbs[0];
        $i1 = $this->limbs[1];
        $j0 = $addend->limbs[0];
        $j1 = $addend->limbs[1];

        $r1 = $i1 + ($j1 & 0xffff);
        $carry = $r1 >> 16;

        $r0 = $i0 + ($j0 & 0xffff) + $carry;
        $carry = $r0 >> 16;

        $r0 &= 0xffff;
        $r1 &= 0xffff;

        $return = new ParagonIE_Sodium_Core32_Int32(
            array($r0, $r1)
        );
        $return->overflow = $carry;
        $return->unsignedInt = $this->unsignedInt;
        return $return;
    }

    /**
     * Adds a normal integer to an int32 object
     *
     * @param int $int
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     */
    public function addInt($int)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($int, 'int', 1);
        /** @var int $int */
        $int = (int) $int;

        $int = (int) $int;

        $i0 = $this->limbs[0];
        $i1 = $this->limbs[1];

        $r1 = $i1 + ($int & 0xffff);
        $carry = $r1 >> 16;

        $r0 = $i0 + (($int >> 16) & 0xffff) + $carry;
        $carry = $r0 >> 16;
        $r0 &= 0xffff;
        $r1 &= 0xffff;
        $return = new ParagonIE_Sodium_Core32_Int32(
            array($r0, $r1)
        );
        $return->overflow = $carry;
        $return->unsignedInt = $this->unsignedInt;
        return $return;
    }

    /**
     * @param int $b
     * @return int
     */
    public function compareInt($b = 0)
    {
        $gt = 0;
        $eq = 1;

        $i = 2;
        $j = 0;
        while ($i > 0) {
            --$i;
            /** @var int $x1 */
            $x1 = $this->limbs[$i];
            /** @var int $x2 */
            $x2 = ($b >> ($j << 4)) & 0xffff;
            /** @var int $gt */
            $gt |= (($x2 - $x1) >> 8) & $eq;
            /** @var int $eq */
            $eq &= (($x2 ^ $x1) - 1) >> 8;
        }
        return ($gt + $gt - $eq) + 1;
    }

    /**
     * @param int $m
     * @return ParagonIE_Sodium_Core32_Int32
     */
    public function mask($m = 0)
    {
        /** @var int $hi */
        $hi = ($m >> 16) & 0xffff;
        /** @var int $lo */
        $lo = ($m & 0xffff);
        return new ParagonIE_Sodium_Core32_Int32(
            array(
                (int) ($this->limbs[0] & $hi),
                (int) ($this->limbs[1] & $lo)
            ),
            $this->unsignedInt
        );
    }

    /**
     * @param int $int
     * @param int $size
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     */
    public function mulInt($int = 0, $size = 0)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($int, 'int', 1);
        ParagonIE_Sodium_Core32_Util::declareScalarType($size, 'int', 2);
        /** @var int $int */
        $int = (int) $int;
        /** @var int $size */
        $size = (int) $size;

        if (!$size) {
            $size = 31;
        }
        /** @var int $size */

        $a = clone $this;
        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;

        // Initialize:
        $ret0 = 0;
        $ret1 = 0;
        $a0 = $a->limbs[0];
        $a1 = $a->limbs[1];

        /** @var int $size */
        /** @var int $i */
        for ($i = $size; $i >= 0; --$i) {
            $m = (int) (-($int & 1));
            $x0 = $a0 & $m;
            $x1 = $a1 & $m;

            $ret1 += $x1;
            $c = $ret1 >> 16;

            $ret0 += $x0 + $c;

            $ret0 &= 0xffff;
            $ret1 &= 0xffff;

            $a1 = ($a1 << 1);
            $x1 = $a1 >> 16;
            $a0 = ($a0 << 1) | $x1;
            $a0 &= 0xffff;
            $a1 &= 0xffff;
            $int >>= 1;
        }
        $return->limbs[0] = $ret0;
        $return->limbs[1] = $ret1;
        return $return;
    }

    /**
     * @param ParagonIE_Sodium_Core32_Int32 $int
     * @param int $size
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     */
    public function mulInt32(ParagonIE_Sodium_Core32_Int32 $int, $size = 0)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($size, 'int', 2);
        if (!$size) {
            $size = 31;
        }
        /** @var int $size */

        $a = clone $this;
        $b = clone $int;
        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;

        // Initialize:
        $ret0 = 0;
        $ret1 = 0;
        $a0 = $a->limbs[0];
        $a1 = $a->limbs[1];
        $b0 = $b->limbs[0];
        $b1 = $b->limbs[1];

        /** @var int $size */
        /** @var int $i */
        for ($i = $size; $i >= 0; --$i) {
            $m = (int) (-($b1 & 1));
            $x0 = $a0 & $m;
            $x1 = $a1 & $m;

            $ret1 += $x1;
            $c = $ret1 >> 16;

            $ret0 += $x0 + $c;

            $ret0 &= 0xffff;
            $ret1 &= 0xffff;

            $a1 = ($a1 << 1);
            $x1 = $a1 >> 16;
            $a0 = ($a0 << 1) | $x1;
            $a0 &= 0xffff;
            $a1 &= 0xffff;

            $x0 = ($b0 & 1) << 16;
            $b0 = ($b0 >> 1);
            $b1 = (($b1 | $x0) >> 1);

            $b0 &= 0xffff;
            $b1 &= 0xffff;

        }
        $return->limbs[0] = $ret0;
        $return->limbs[1] = $ret1;

        return $return;
    }

    /**
     * OR this 32-bit integer with another.
     *
     * @param ParagonIE_Sodium_Core32_Int32 $b
     * @return ParagonIE_Sodium_Core32_Int32
     */
    public function orInt32(ParagonIE_Sodium_Core32_Int32 $b)
    {
        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;
        $return->limbs = array(
            (int) ($this->limbs[0] | $b->limbs[0]),
            (int) ($this->limbs[1] | $b->limbs[1])
        );
        /** @var int overflow */
        $return->overflow = $this->overflow | $b->overflow;
        return $return;
    }

    /**
     * @param int $b
     * @return bool
     */
    public function isGreaterThan($b = 0)
    {
        return $this->compareInt($b) > 0;
    }

    /**
     * @param int $b
     * @return bool
     */
    public function isLessThanInt($b = 0)
    {
        return $this->compareInt($b) < 0;
    }

    /**
     * @param int $c
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArrayAccess
     */
    public function rotateLeft($c = 0)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($c, 'int', 1);
        /** @var int $c */
        $c = (int) $c;

        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;
        $c &= 31;
        if ($c === 0) {
            // NOP, but we want a copy.
            $return->limbs = $this->limbs;
        } else {
            /** @var int $c */

            /** @var int $idx_shift */
            $idx_shift = ($c >> 4) & 1;

            /** @var int $sub_shift */
            $sub_shift = $c & 15;

            /** @var array<int, int> $limbs */
            $limbs =& $return->limbs;

            /** @var array<int, int> $myLimbs */
            $myLimbs =& $this->limbs;

            for ($i = 1; $i >= 0; --$i) {
                /** @var int $j */
                $j = ($i + $idx_shift) & 1;
                /** @var int $k */
                $k = ($i + $idx_shift + 1) & 1;
                $limbs[$i] = (int) (
                    (
                        ((int) ($myLimbs[$j]) << $sub_shift)
                            |
                        ((int) ($myLimbs[$k]) >> (16 - $sub_shift))
                    ) & 0xffff
                );
            }
        }
        return $return;
    }

    /**
     * Rotate to the right
     *
     * @param int $c
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedArrayAccess
     */
    public function rotateRight($c = 0)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($c, 'int', 1);
        /** @var int $c */
        $c = (int) $c;

        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;
        $c &= 31;
        /** @var int $c */
        if ($c === 0) {
            // NOP, but we want a copy.
            $return->limbs = $this->limbs;
        } else {
            /** @var int $c */

            /** @var int $idx_shift */
            $idx_shift = ($c >> 4) & 1;

            /** @var int $sub_shift */
            $sub_shift = $c & 15;

            /** @var array<int, int> $limbs */
            $limbs =& $return->limbs;

            /** @var array<int, int> $myLimbs */
            $myLimbs =& $this->limbs;

            for ($i = 1; $i >= 0; --$i) {
                /** @var int $j */
                $j = ($i - $idx_shift) & 1;
                /** @var int $k */
                $k = ($i - $idx_shift - 1) & 1;
                $limbs[$i] = (int) (
                    (
                        ((int) ($myLimbs[$j]) >> (int) ($sub_shift))
                            |
                        ((int) ($myLimbs[$k]) << (16 - (int) ($sub_shift)))
                    ) & 0xffff
                );
            }
        }
        return $return;
    }

    /**
     * @param bool $bool
     * @return self
     */
    public function setUnsignedInt($bool = false)
    {
        $this->unsignedInt = !empty($bool);
        return $this;
    }

    /**
     * @param int $c
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     */
    public function shiftLeft($c = 0)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($c, 'int', 1);
        /** @var int $c */
        $c = (int) $c;

        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;
        $c &= 63;
        /** @var int $c */
        if ($c === 0) {
            $return->limbs = $this->limbs;
        } elseif ($c < 0) {
            /** @var int $c */
            return $this->shiftRight(-$c);
        } else {
            /** @var int $c */
            /** @var int $tmp */
            $tmp = $this->limbs[1] << $c;
            $return->limbs[1] = (int)($tmp & 0xffff);
            /** @var int $carry */
            $carry = $tmp >> 16;

            /** @var int $tmp */
            $tmp = ($this->limbs[0] << $c) | ($carry & 0xffff);
            $return->limbs[0] = (int) ($tmp & 0xffff);
        }
        return $return;
    }

    /**
     * @param int $c
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedOperand
     */
    public function shiftRight($c = 0)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($c, 'int', 1);
        /** @var int $c */
        $c = (int) $c;

        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;
        $c &= 63;
        /** @var int $c */
        if ($c >= 16) {
            $return->limbs = array(
                (int) ($this->overflow & 0xffff),
                (int) ($this->limbs[0])
            );
            $return->overflow = $this->overflow >> 16;
            return $return->shiftRight($c & 15);
        }
        if ($c === 0) {
            $return->limbs = $this->limbs;
        } elseif ($c < 0) {
            /** @var int $c */
            return $this->shiftLeft(-$c);
        } else {
            if (is_null($c)) {
                throw new TypeError();
            }
            /** @var int $c */
            // $return->limbs[0] = (int) (($this->limbs[0] >> $c) & 0xffff);
            $carryLeft = (int) ($this->overflow & ((1 << ($c + 1)) - 1));
            $return->limbs[0] = (int) ((($this->limbs[0] >> $c) | ($carryLeft << (16 - $c))) & 0xffff);
            $carryRight = (int) ($this->limbs[0] & ((1 << ($c + 1)) - 1));
            $return->limbs[1] = (int) ((($this->limbs[1] >> $c) | ($carryRight << (16 - $c))) & 0xffff);
            $return->overflow >>= $c;
        }
        return $return;
    }

    /**
     * Subtract a normal integer from an int32 object.
     *
     * @param int $int
     * @return ParagonIE_Sodium_Core32_Int32
     * @throws SodiumException
     * @throws TypeError
     */
    public function subInt($int)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($int, 'int', 1);
        /** @var int $int */
        $int = (int) $int;

        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;

        /** @var int $tmp */
        $tmp = $this->limbs[1] - ($int & 0xffff);
        /** @var int $carry */
        $carry = $tmp >> 16;
        $return->limbs[1] = (int) ($tmp & 0xffff);

        /** @var int $tmp */
        $tmp = $this->limbs[0] - (($int >> 16) & 0xffff) + $carry;
        $return->limbs[0] = (int) ($tmp & 0xffff);
        return $return;
    }

    /**
     * Subtract two int32 objects from each other
     *
     * @param ParagonIE_Sodium_Core32_Int32 $b
     * @return ParagonIE_Sodium_Core32_Int32
     */
    public function subInt32(ParagonIE_Sodium_Core32_Int32 $b)
    {
        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;

        /** @var int $tmp */
        $tmp = $this->limbs[1] - ($b->limbs[1] & 0xffff);
        /** @var int $carry */
        $carry = $tmp >> 16;
        $return->limbs[1] = (int) ($tmp & 0xffff);

        /** @var int $tmp */
        $tmp = $this->limbs[0] - ($b->limbs[0] & 0xffff) + $carry;
        $return->limbs[0] = (int) ($tmp & 0xffff);
        return $return;
    }

    /**
     * XOR this 32-bit integer with another.
     *
     * @param ParagonIE_Sodium_Core32_Int32 $b
     * @return ParagonIE_Sodium_Core32_Int32
     */
    public function xorInt32(ParagonIE_Sodium_Core32_Int32 $b)
    {
        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->unsignedInt = $this->unsignedInt;
        $return->limbs = array(
            (int) ($this->limbs[0] ^ $b->limbs[0]),
            (int) ($this->limbs[1] ^ $b->limbs[1])
        );
        return $return;
    }

    /**
     * @param int $signed
     * @return self
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fromInt($signed)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($signed, 'int', 1);;
        /** @var int $signed */
        $signed = (int) $signed;

        return new ParagonIE_Sodium_Core32_Int32(
            array(
                (int) (($signed >> 16) & 0xffff),
                (int) ($signed & 0xffff)
            )
        );
    }

    /**
     * @param string $string
     * @return self
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fromString($string)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($string, 'string', 1);
        $string = (string) $string;
        if (ParagonIE_Sodium_Core32_Util::strlen($string) !== 4) {
            throw new RangeException(
                'String must be 4 bytes; ' . ParagonIE_Sodium_Core32_Util::strlen($string) . ' given.'
            );
        }
        $return = new ParagonIE_Sodium_Core32_Int32();

        $return->limbs[0]  = (int) ((ParagonIE_Sodium_Core32_Util::chrToInt($string[0]) & 0xff) << 8);
        $return->limbs[0] |= (ParagonIE_Sodium_Core32_Util::chrToInt($string[1]) & 0xff);
        $return->limbs[1]  = (int) ((ParagonIE_Sodium_Core32_Util::chrToInt($string[2]) & 0xff) << 8);
        $return->limbs[1] |= (ParagonIE_Sodium_Core32_Util::chrToInt($string[3]) & 0xff);
        return $return;
    }

    /**
     * @param string $string
     * @return self
     * @throws SodiumException
     * @throws TypeError
     */
    public static function fromReverseString($string)
    {
        ParagonIE_Sodium_Core32_Util::declareScalarType($string, 'string', 1);
        $string = (string) $string;
        if (ParagonIE_Sodium_Core32_Util::strlen($string) !== 4) {
            throw new RangeException(
                'String must be 4 bytes; ' . ParagonIE_Sodium_Core32_Util::strlen($string) . ' given.'
            );
        }
        $return = new ParagonIE_Sodium_Core32_Int32();

        $return->limbs[0]  = (int) ((ParagonIE_Sodium_Core32_Util::chrToInt($string[3]) & 0xff) << 8);
        $return->limbs[0] |= (ParagonIE_Sodium_Core32_Util::chrToInt($string[2]) & 0xff);
        $return->limbs[1]  = (int) ((ParagonIE_Sodium_Core32_Util::chrToInt($string[1]) & 0xff) << 8);
        $return->limbs[1] |= (ParagonIE_Sodium_Core32_Util::chrToInt($string[0]) & 0xff);
        return $return;
    }

    /**
     * @return array<int, int>
     */
    public function toArray()
    {
        return array((int) ($this->limbs[0] << 16 | $this->limbs[1]));
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function toString()
    {
        return
            ParagonIE_Sodium_Core32_Util::intToChr(($this->limbs[0] >> 8) & 0xff) .
            ParagonIE_Sodium_Core32_Util::intToChr($this->limbs[0] & 0xff) .
            ParagonIE_Sodium_Core32_Util::intToChr(($this->limbs[1] >> 8) & 0xff) .
            ParagonIE_Sodium_Core32_Util::intToChr($this->limbs[1] & 0xff);
    }

    /**
     * @return int
     */
    public function toInt()
    {
        return (int) (
            (($this->limbs[0] & 0xffff) << 16)
                |
            ($this->limbs[1] & 0xffff)
        );
    }

    /**
     * @return ParagonIE_Sodium_Core32_Int32
     */
    public function toInt32()
    {
        $return = new ParagonIE_Sodium_Core32_Int32();
        $return->limbs[0] = (int) ($this->limbs[0] & 0xffff);
        $return->limbs[1] = (int) ($this->limbs[1] & 0xffff);
        $return->unsignedInt = $this->unsignedInt;
        $return->overflow = (int) ($this->overflow & 0x7fffffff);
        return $return;
    }

    /**
     * @return ParagonIE_Sodium_Core32_Int64
     */
    public function toInt64()
    {
        $return = new ParagonIE_Sodium_Core32_Int64();
        $return->unsignedInt = $this->unsignedInt;
        if ($this->unsignedInt) {
            $return->limbs[0] += (($this->overflow >> 16) & 0xffff);
            $return->limbs[1] += (($this->overflow) & 0xffff);
        } else {
            $neg = -(($this->limbs[0] >> 15) & 1);
            $return->limbs[0] = (int)($neg & 0xffff);
            $return->limbs[1] = (int)($neg & 0xffff);
        }
        $return->limbs[2] = (int) ($this->limbs[0] & 0xffff);
        $return->limbs[3] = (int) ($this->limbs[1] & 0xffff);
        return $return;
    }

    /**
     * @return string
     * @throws TypeError
     */
    public function toReverseString()
    {
        return ParagonIE_Sodium_Core32_Util::intToChr($this->limbs[1] & 0xff) .
            ParagonIE_Sodium_Core32_Util::intToChr(($this->limbs[1] >> 8) & 0xff) .
            ParagonIE_Sodium_Core32_Util::intToChr($this->limbs[0] & 0xff) .
            ParagonIE_Sodium_Core32_Util::intToChr(($this->limbs[0] >> 8) & 0xff);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->toString();
        } catch (TypeError $ex) {
            // PHP engine can't handle exceptions from __toString()
            return '';
        }
    }
}
