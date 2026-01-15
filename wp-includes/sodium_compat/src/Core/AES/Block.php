<?php

if (class_exists('ParagonIE_Sodium_Core_AES_Block', false)) {
    return;
}

/**
 * @internal This should only be used by sodium_compat
 */
class ParagonIE_Sodium_Core_AES_Block extends SplFixedArray
{
    /**
     * @var array<int, int>
     */
    protected $values = array();

    /**
     * @var int
     */
    protected $size;

    /**
     * @param int $size
     */
    public function __construct($size = 8)
    {
        parent::__construct($size);
        $this->size = $size;
        $this->values = array_fill(0, $size, 0);
    }

    /**
     * @return self
     */
    public static function init()
    {
        return new self(8);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param array<int, int> $array
     * @param bool $save_indexes
     * @return self
     *
     * @psalm-suppress MethodSignatureMismatch
     */
    #[ReturnTypeWillChange]
    public static function fromArray($array, $save_indexes = null)
    {
        $count = count($array);
        if ($save_indexes) {
            $keys = array_keys($array);
        } else {
            $keys = range(0, $count - 1);
        }
        $array = array_values($array);
        /** @var array<int, int> $keys */

        $obj = new ParagonIE_Sodium_Core_AES_Block();
        if ($save_indexes) {
            for ($i = 0; $i < $count; ++$i) {
                $obj->offsetSet($keys[$i], $array[$i]);
            }
        } else {
            for ($i = 0; $i < $count; ++$i) {
                $obj->offsetSet($i, $array[$i]);
            }
        }
        return $obj;
    }


    /**
     * @internal You should not use this directly from another application
     *
     * @param int|null $offset
     * @param int $value
     * @return void
     *
     * @psalm-suppress MethodSignatureMismatch
     * @psalm-suppress MixedArrayOffset
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException('Expected an integer');
        }
        if (is_null($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @return bool
     *
     * @psalm-suppress MethodSignatureMismatch
     * @psalm-suppress MixedArrayOffset
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @return void
     *
     * @psalm-suppress MethodSignatureMismatch
     * @psalm-suppress MixedArrayOffset
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param int $offset
     * @return int
     *
     * @psalm-suppress MethodSignatureMismatch
     * @psalm-suppress MixedArrayOffset
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        if (!isset($this->values[$offset])) {
            $this->values[$offset] = 0;
        }
        return (int) ($this->values[$offset]);
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @return array
     */
    public function __debugInfo()
    {
        $out = array();
        foreach ($this->values as $v) {
            $out[] = str_pad(dechex($v), 8, '0', STR_PAD_LEFT);
        }
        return array(implode(', ', $out));
        /*
         return array(implode(', ', $this->values));
         */
    }

    /**
     * @param int $cl low bit mask
     * @param int $ch high bit mask
     * @param int $s shift
     * @param int $x index 1
     * @param int $y index 2
     * @return self
     */
    public function swapN($cl, $ch, $s, $x, $y)
    {
        static $u32mask = ParagonIE_Sodium_Core_Util::U32_MAX;
        $a = $this->values[$x] & $u32mask;
        $b = $this->values[$y] & $u32mask;
        // (x) = (a & cl) | ((b & cl) << (s));
        $this->values[$x] = ($a & $cl) | ((($b & $cl) << $s) & $u32mask);
        // (y) = ((a & ch) >> (s)) | (b & ch);
        $this->values[$y] = ((($a & $ch) & $u32mask) >> $s) | ($b & $ch);
        return $this;
    }

    /**
     * @param int $x index 1
     * @param int $y index 2
     * @return self
     */
    public function swap2($x, $y)
    {
        return $this->swapN(0x55555555, 0xAAAAAAAA, 1, $x, $y);
    }

    /**
     * @param int $x index 1
     * @param int $y index 2
     * @return self
     */
    public function swap4($x, $y)
    {
        return $this->swapN(0x33333333, 0xCCCCCCCC, 2, $x, $y);
    }

    /**
     * @param int $x index 1
     * @param int $y index 2
     * @return self
     */
    public function swap8($x, $y)
    {
        return $this->swapN(0x0F0F0F0F, 0xF0F0F0F0, 4, $x, $y);
    }

    /**
     * @return self
     */
    public function orthogonalize()
    {
        return $this
            ->swap2(0, 1)
            ->swap2(2, 3)
            ->swap2(4, 5)
            ->swap2(6, 7)

            ->swap4(0, 2)
            ->swap4(1, 3)
            ->swap4(4, 6)
            ->swap4(5, 7)

            ->swap8(0, 4)
            ->swap8(1, 5)
            ->swap8(2, 6)
            ->swap8(3, 7);
    }

    /**
     * @return self
     */
    public function shiftRows()
    {
        for ($i = 0; $i < 8; ++$i) {
            $x = $this->values[$i] & ParagonIE_Sodium_Core_Util::U32_MAX;
            $this->values[$i] = (
                ($x & 0x000000FF)
                    | (($x & 0x0000FC00) >> 2) | (($x & 0x00000300) << 6)
                    | (($x & 0x00F00000) >> 4) | (($x & 0x000F0000) << 4)
                    | (($x & 0xC0000000) >> 6) | (($x & 0x3F000000) << 2)
            ) & ParagonIE_Sodium_Core_Util::U32_MAX;
        }
        return $this;
    }

    /**
     * @param int $x
     * @return int
     */
    public static function rotr16($x)
    {
        return (($x << 16) & ParagonIE_Sodium_Core_Util::U32_MAX) | ($x >> 16);
    }

    /**
     * @return self
     */
    public function mixColumns()
    {
        $q0 = $this->values[0];
        $q1 = $this->values[1];
        $q2 = $this->values[2];
        $q3 = $this->values[3];
        $q4 = $this->values[4];
        $q5 = $this->values[5];
        $q6 = $this->values[6];
        $q7 = $this->values[7];
        $r0 = (($q0 >> 8) | ($q0 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r1 = (($q1 >> 8) | ($q1 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r2 = (($q2 >> 8) | ($q2 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r3 = (($q3 >> 8) | ($q3 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r4 = (($q4 >> 8) | ($q4 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r5 = (($q5 >> 8) | ($q5 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r6 = (($q6 >> 8) | ($q6 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r7 = (($q7 >> 8) | ($q7 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;

        $this->values[0] = $q7 ^ $r7 ^ $r0 ^ self::rotr16($q0 ^ $r0);
        $this->values[1] = $q0 ^ $r0 ^ $q7 ^ $r7 ^ $r1 ^ self::rotr16($q1 ^ $r1);
        $this->values[2] = $q1 ^ $r1 ^ $r2 ^ self::rotr16($q2 ^ $r2);
        $this->values[3] = $q2 ^ $r2 ^ $q7 ^ $r7 ^ $r3 ^ self::rotr16($q3 ^ $r3);
        $this->values[4] = $q3 ^ $r3 ^ $q7 ^ $r7 ^ $r4 ^ self::rotr16($q4 ^ $r4);
        $this->values[5] = $q4 ^ $r4 ^ $r5 ^ self::rotr16($q5 ^ $r5);
        $this->values[6] = $q5 ^ $r5 ^ $r6 ^ self::rotr16($q6 ^ $r6);
        $this->values[7] = $q6 ^ $r6 ^ $r7 ^ self::rotr16($q7 ^ $r7);
        return $this;
    }

    /**
     * @return self
     */
    public function inverseMixColumns()
    {
        $q0 = $this->values[0];
        $q1 = $this->values[1];
        $q2 = $this->values[2];
        $q3 = $this->values[3];
        $q4 = $this->values[4];
        $q5 = $this->values[5];
        $q6 = $this->values[6];
        $q7 = $this->values[7];
        $r0 = (($q0 >> 8) | ($q0 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r1 = (($q1 >> 8) | ($q1 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r2 = (($q2 >> 8) | ($q2 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r3 = (($q3 >> 8) | ($q3 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r4 = (($q4 >> 8) | ($q4 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r5 = (($q5 >> 8) | ($q5 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r6 = (($q6 >> 8) | ($q6 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;
        $r7 = (($q7 >> 8) | ($q7 << 24)) & ParagonIE_Sodium_Core_Util::U32_MAX;

        $this->values[0] = $q5 ^ $q6 ^ $q7 ^ $r0 ^ $r5 ^ $r7 ^ self::rotr16($q0 ^ $q5 ^ $q6 ^ $r0 ^ $r5);
        $this->values[1] = $q0 ^ $q5 ^ $r0 ^ $r1 ^ $r5 ^ $r6 ^ $r7 ^ self::rotr16($q1 ^ $q5 ^ $q7 ^ $r1 ^ $r5 ^ $r6);
        $this->values[2] = $q0 ^ $q1 ^ $q6 ^ $r1 ^ $r2 ^ $r6 ^ $r7 ^ self::rotr16($q0 ^ $q2 ^ $q6 ^ $r2 ^ $r6 ^ $r7);
        $this->values[3] = $q0 ^ $q1 ^ $q2 ^ $q5 ^ $q6 ^ $r0 ^ $r2 ^ $r3 ^ $r5 ^ self::rotr16($q0 ^ $q1 ^ $q3 ^ $q5 ^ $q6 ^ $q7 ^ $r0 ^ $r3 ^ $r5 ^ $r7);
        $this->values[4] = $q1 ^ $q2 ^ $q3 ^ $q5 ^ $r1 ^ $r3 ^ $r4 ^ $r5 ^ $r6 ^ $r7 ^ self::rotr16($q1 ^ $q2 ^ $q4 ^ $q5 ^ $q7 ^ $r1 ^ $r4 ^ $r5 ^ $r6);
        $this->values[5] = $q2 ^ $q3 ^ $q4 ^ $q6 ^ $r2 ^ $r4 ^ $r5 ^ $r6 ^ $r7 ^ self::rotr16($q2 ^ $q3 ^ $q5 ^ $q6 ^ $r2 ^ $r5 ^ $r6 ^ $r7);
        $this->values[6] = $q3 ^ $q4 ^ $q5 ^ $q7 ^ $r3 ^ $r5 ^ $r6 ^ $r7 ^ self::rotr16($q3 ^ $q4 ^ $q6 ^ $q7 ^ $r3 ^ $r6 ^ $r7);
        $this->values[7] = $q4 ^ $q5 ^ $q6 ^ $r4 ^ $r6 ^ $r7 ^ self::rotr16($q4 ^ $q5 ^ $q7 ^ $r4 ^ $r7);
        return $this;
    }

    /**
     * @return self
     */
    public function inverseShiftRows()
    {
        for ($i = 0; $i < 8; ++$i) {
            $x = $this->values[$i];
            $this->values[$i] = ParagonIE_Sodium_Core_Util::U32_MAX & (
                ($x & 0x000000FF)
                    | (($x & 0x00003F00) << 2) | (($x & 0x0000C000) >> 6)
                    | (($x & 0x000F0000) << 4) | (($x & 0x00F00000) >> 4)
                    | (($x & 0x03000000) << 6) | (($x & 0xFC000000) >> 2)
            );
        }
        return $this;
    }
}
