<?php

if (class_exists('ParagonIE_Sodium_Core_Util', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_Util
 */
abstract class ParagonIE_Sodium_Core_Util
{
    /**
     * @param int $integer
     * @param int $size (16, 32, 64)
     * @return int
     */
    public static function abs($integer, $size = 0)
    {
        /** @var int $realSize */
        $realSize = (PHP_INT_SIZE << 3) - 1;
        if ($size) {
            --$size;
        } else {
            /** @var int $size */
            $size = $realSize;
        }

        $negative = -(($integer >> $size) & 1);
        return (int) (
            ($integer ^ $negative)
                +
            (($negative >> $realSize) & 1)
        );
    }

    /**
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks
     *
     * @internal You should not use this directly from another application
     *
     * @param string $binaryString (raw binary)
     * @return string
     * @throws TypeError
     */
    public static function bin2hex($binaryString)
    {
        /* Type checks: */
        if (!is_string($binaryString)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($binaryString) . ' given.');
        }

        $hex = '';
        $len = self::strlen($binaryString);
        for ($i = 0; $i < $len; ++$i) {
            /** @var array<int, int> $chunk */
            $chunk = unpack('C', $binaryString[$i]);
            /** @var int $c */
            $c = $chunk[1] & 0xf;
            /** @var int $b */
            $b = $chunk[1] >> 4;
            $hex .= pack(
                'CC',
                (87 + $b + ((($b - 10) >> 8) & ~38)),
                (87 + $c + ((($c - 10) >> 8) & ~38))
            );
        }
        return $hex;
    }

    /**
     * Convert a binary string into a hexadecimal string without cache-timing
     * leaks, returning uppercase letters (as per RFC 4648)
     *
     * @internal You should not use this directly from another application
     *
     * @param string $bin_string (raw binary)
     * @return string
     * @throws TypeError
     */
    public static function bin2hexUpper($bin_string)
    {
        $hex = '';
        $len = self::strlen($bin_string);
        for ($i = 0; $i < $len; ++$i) {
            /** @var array<int, int> $chunk */
            $chunk = unpack('C', $bin_string[$i]);
            /**
             * Lower 16 bits
             *
             * @var int $c
             */
            $c = $chunk[1] & 0xf;

            /**
             * Upper 16 bits
             * @var int $b
             */
            $b = $chunk[1] >> 4;

            /**
             * Use pack() and binary operators to turn the two integers
             * into hexadecimal characters. We don't use chr() here, because
             * it uses a lookup table internally and we want to avoid
             * cache-timing side-channels.
             */
            $hex .= pack(
                'CC',
                (55 + $b + ((($b - 10) >> 8) & ~6)),
                (55 + $c + ((($c - 10) >> 8) & ~6))
            );
        }
        return $hex;
    }

    /**
     * Cache-timing-safe variant of ord()
     *
     * @internal You should not use this directly from another application
     *
     * @param string $chr
     * @return int
     * @throws SodiumException
     * @throws TypeError
     */
    public static function chrToInt($chr)
    {
        /* Type checks: */
        if (!is_string($chr)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($chr) . ' given.');
        }
        if (self::strlen($chr) !== 1) {
            throw new SodiumException('chrToInt() expects a string that is exactly 1 character long');
        }
        /** @var array<int, int> $chunk */
        $chunk = unpack('C', $chr);
        return (int) ($chunk[1]);
    }

    /**
     * Compares two strings.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $left
     * @param string $right
     * @param int $len
     * @return int
     * @throws SodiumException
     * @throws TypeError
     */
    public static function compare($left, $right, $len = null)
    {
        $leftLen = self::strlen($left);
        $rightLen = self::strlen($right);
        if ($len === null) {
            $len = max($leftLen, $rightLen);
            $left = str_pad($left, $len, "\x00", STR_PAD_RIGHT);
            $right = str_pad($right, $len, "\x00", STR_PAD_RIGHT);
        }

        $gt = 0;
        $eq = 1;
        $i = $len;
        while ($i !== 0) {
            --$i;
            $gt |= ((self::chrToInt($right[$i]) - self::chrToInt($left[$i])) >> 8) & $eq;
            $eq &= ((self::chrToInt($right[$i]) ^ self::chrToInt($left[$i])) - 1) >> 8;
        }
        return ($gt + $gt + $eq) - 1;
    }

    /**
     * If a variable does not match a given type, throw a TypeError.
     *
     * @param mixed $mixedVar
     * @param string $type
     * @param int $argumentIndex
     * @throws TypeError
     * @throws SodiumException
     * @return void
     */
    public static function declareScalarType(&$mixedVar = null, $type = 'void', $argumentIndex = 0)
    {
        if (func_num_args() === 0) {
            /* Tautology, by default */
            return;
        }
        if (func_num_args() === 1) {
            throw new TypeError('Declared void, but passed a variable');
        }
        $realType = strtolower(gettype($mixedVar));
        $type = strtolower($type);
        switch ($type) {
            case 'null':
                if ($mixedVar !== null) {
                    throw new TypeError('Argument ' . $argumentIndex . ' must be null, ' . $realType . ' given.');
                }
                break;
            case 'integer':
            case 'int':
                $allow = array('int', 'integer');
                if (!in_array($type, $allow)) {
                    throw new TypeError('Argument ' . $argumentIndex . ' must be an integer, ' . $realType . ' given.');
                }
                $mixedVar = (int) $mixedVar;
                break;
            case 'boolean':
            case 'bool':
                $allow = array('bool', 'boolean');
                if (!in_array($type, $allow)) {
                    throw new TypeError('Argument ' . $argumentIndex . ' must be a boolean, ' . $realType . ' given.');
                }
                $mixedVar = (bool) $mixedVar;
                break;
            case 'string':
                if (!is_string($mixedVar)) {
                    throw new TypeError('Argument ' . $argumentIndex . ' must be a string, ' . $realType . ' given.');
                }
                $mixedVar = (string) $mixedVar;
                break;
            case 'decimal':
            case 'double':
            case 'float':
                $allow = array('decimal', 'double', 'float');
                if (!in_array($type, $allow)) {
                    throw new TypeError('Argument ' . $argumentIndex . ' must be a float, ' . $realType . ' given.');
                }
                $mixedVar = (float) $mixedVar;
                break;
            case 'object':
                if (!is_object($mixedVar)) {
                    throw new TypeError('Argument ' . $argumentIndex . ' must be an object, ' . $realType . ' given.');
                }
                break;
            case 'array':
                if (!is_array($mixedVar)) {
                    if (is_object($mixedVar)) {
                        if ($mixedVar instanceof ArrayAccess) {
                            return;
                        }
                    }
                    throw new TypeError('Argument ' . $argumentIndex . ' must be an array, ' . $realType . ' given.');
                }
                break;
            default:
                throw new SodiumException('Unknown type (' . $realType .') does not match expect type (' . $type . ')');
        }
    }

    /**
     * Evaluate whether or not two strings are equal (in constant-time)
     *
     * @param string $left
     * @param string $right
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function hashEquals($left, $right)
    {
        /* Type checks: */
        if (!is_string($left)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($left) . ' given.');
        }
        if (!is_string($right)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($right) . ' given.');
        }

        if (is_callable('hash_equals')) {
            return hash_equals($left, $right);
        }
        $d = 0;
        /** @var int $len */
        $len = self::strlen($left);
        if ($len !== self::strlen($right)) {
            return false;
        }
        for ($i = 0; $i < $len; ++$i) {
            $d |= self::chrToInt($left[$i]) ^ self::chrToInt($right[$i]);
        }

        if ($d !== 0) {
            return false;
        }
        return $left === $right;
    }

    /**
     * Convert a hexadecimal string into a binary string without cache-timing
     * leaks
     *
     * @internal You should not use this directly from another application
     *
     * @param string $hexString
     * @param bool $strictPadding
     * @return string (raw binary)
     * @throws RangeException
     * @throws TypeError
     */
    public static function hex2bin($hexString, $strictPadding = false)
    {
        /* Type checks: */
        if (!is_string($hexString)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($hexString) . ' given.');
        }

        /** @var int $hex_pos */
        $hex_pos = 0;
        /** @var string $bin */
        $bin = '';
        /** @var int $c_acc */
        $c_acc = 0;
        /** @var int $hex_len */
        $hex_len = self::strlen($hexString);
        /** @var int $state */
        $state = 0;
        if (($hex_len & 1) !== 0) {
            if ($strictPadding) {
                throw new RangeException(
                    'Expected an even number of hexadecimal characters'
                );
            } else {
                $hexString = '0' . $hexString;
                ++$hex_len;
            }
        }

        $chunk = unpack('C*', $hexString);
        while ($hex_pos < $hex_len) {
            ++$hex_pos;
            /** @var int $c */
            $c = $chunk[$hex_pos];
            /** @var int $c_num */
            $c_num = $c ^ 48;
            /** @var int $c_num0 */
            $c_num0 = ($c_num - 10) >> 8;
            /** @var int $c_alpha */
            $c_alpha = ($c & ~32) - 55;
            /** @var int $c_alpha0 */
            $c_alpha0 = (($c_alpha - 10) ^ ($c_alpha - 16)) >> 8;
            if (($c_num0 | $c_alpha0) === 0) {
                throw new RangeException(
                    'hex2bin() only expects hexadecimal characters'
                );
            }
            /** @var int $c_val */
            $c_val = ($c_num0 & $c_num) | ($c_alpha & $c_alpha0);
            if ($state === 0) {
                $c_acc = $c_val * 16;
            } else {
                $bin .= pack('C', $c_acc | $c_val);
            }
            $state ^= 1;
        }
        return $bin;
    }

    /**
     * Turn an array of integers into a string
     *
     * @internal You should not use this directly from another application
     *
     * @param array<int, int> $ints
     * @return string
     */
    public static function intArrayToString(array $ints)
    {
        /** @var array<int, int> $args */
        $args = $ints;
        foreach ($args as $i => $v) {
            $args[$i] = (int) ($v & 0xff);
        }
        array_unshift($args, str_repeat('C', count($ints)));
        return (string) (call_user_func_array('pack', $args));
    }

    /**
     * Cache-timing-safe variant of ord()
     *
     * @internal You should not use this directly from another application
     *
     * @param int $int
     * @return string
     * @throws TypeError
     */
    public static function intToChr($int)
    {
        return pack('C', $int);
    }

    /**
     * Load a 3 character substring into an integer
     *
     * @internal You should not use this directly from another application
     *
     * @param string $string
     * @return int
     * @throws RangeException
     * @throws TypeError
     */
    public static function load_3($string)
    {
        /* Type checks: */
        if (!is_string($string)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($string) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($string) < 3) {
            throw new RangeException(
                'String must be 3 bytes or more; ' . self::strlen($string) . ' given.'
            );
        }
        /** @var array<int, int> $unpacked */
        $unpacked = unpack('V', $string . "\0");
        return (int) ($unpacked[1] & 0xffffff);
    }

    /**
     * Load a 4 character substring into an integer
     *
     * @internal You should not use this directly from another application
     *
     * @param string $string
     * @return int
     * @throws RangeException
     * @throws TypeError
     */
    public static function load_4($string)
    {
        /* Type checks: */
        if (!is_string($string)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($string) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($string) < 4) {
            throw new RangeException(
                'String must be 4 bytes or more; ' . self::strlen($string) . ' given.'
            );
        }
        /** @var array<int, int> $unpacked */
        $unpacked = unpack('V', $string);
        return (int) ($unpacked[1] & 0xffffffff);
    }

    /**
     * Load a 8 character substring into an integer
     *
     * @internal You should not use this directly from another application
     *
     * @param string $string
     * @return int
     * @throws RangeException
     * @throws SodiumException
     * @throws TypeError
     */
    public static function load64_le($string)
    {
        /* Type checks: */
        if (!is_string($string)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($string) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($string) < 4) {
            throw new RangeException(
                'String must be 4 bytes or more; ' . self::strlen($string) . ' given.'
            );
        }
        if (PHP_VERSION_ID >= 50603 && PHP_INT_SIZE === 8) {
            /** @var array<int, int> $unpacked */
            $unpacked = unpack('P', $string);
            return (int) $unpacked[1];
        }

        /** @var int $result */
        $result  = (self::chrToInt($string[0]) & 0xff);
        $result |= (self::chrToInt($string[1]) & 0xff) <<  8;
        $result |= (self::chrToInt($string[2]) & 0xff) << 16;
        $result |= (self::chrToInt($string[3]) & 0xff) << 24;
        $result |= (self::chrToInt($string[4]) & 0xff) << 32;
        $result |= (self::chrToInt($string[5]) & 0xff) << 40;
        $result |= (self::chrToInt($string[6]) & 0xff) << 48;
        $result |= (self::chrToInt($string[7]) & 0xff) << 56;
        return (int) $result;
    }

    /**
     * @internal You should not use this directly from another application
     *
     * @param string $left
     * @param string $right
     * @return int
     * @throws SodiumException
     * @throws TypeError
     */
    public static function memcmp($left, $right)
    {
        if (self::hashEquals($left, $right)) {
            return 0;
        }
        return -1;
    }

    /**
     * Multiply two integers in constant-time
     *
     * Micro-architecture timing side-channels caused by how your CPU
     * implements multiplication are best prevented by never using the
     * multiplication operators and ensuring that our code always takes
     * the same number of operations to complete, regardless of the values
     * of $a and $b.
     *
     * @internal You should not use this directly from another application
     *
     * @param int $a
     * @param int $b
     * @param int $size Limits the number of operations (useful for small,
     *                  constant operands)
     * @return int
     */
    public static function mul($a, $b, $size = 0)
    {
        if (ParagonIE_Sodium_Compat::$fastMult) {
            return (int) ($a * $b);
        }

        static $defaultSize = null;
        /** @var int $defaultSize */
        if (!$defaultSize) {
            /** @var int $defaultSize */
            $defaultSize = (PHP_INT_SIZE << 3) - 1;
        }
        if ($size < 1) {
            /** @var int $size */
            $size = $defaultSize;
        }
        /** @var int $size */

        $c = 0;

        /**
         * Mask is either -1 or 0.
         *
         * -1 in binary looks like 0x1111 ... 1111
         *  0 in binary looks like 0x0000 ... 0000
         *
         * @var int
         */
        $mask = -(($b >> ((int) $defaultSize)) & 1);

        /**
         * Ensure $b is a positive integer, without creating
         * a branching side-channel
         *
         * @var int $b
         */
        $b = ($b & ~$mask) | ($mask & -$b);

        /**
         * Unless $size is provided:
         *
         * This loop always runs 32 times when PHP_INT_SIZE is 4.
         * This loop always runs 64 times when PHP_INT_SIZE is 8.
         */
        for ($i = $size; $i >= 0; --$i) {
            $c += (int) ($a & -($b & 1));
            $a <<= 1;
            $b >>= 1;
        }

        /**
         * If $b was negative, we then apply the same value to $c here.
         * It doesn't matter much if $a was negative; the $c += above would
         * have produced a negative integer to begin with. But a negative $b
         * makes $b >>= 1 never return 0, so we would end up with incorrect
         * results.
         *
         * The end result is what we'd expect from integer multiplication.
         */
        return (int) (($c & ~$mask) | ($mask & -$c));
    }

    /**
     * Convert any arbitrary numbers into two 32-bit integers that represent
     * a 64-bit integer.
     *
     * @internal You should not use this directly from another application
     *
     * @param int|float $num
     * @return array<int, int>
     */
    public static function numericTo64BitInteger($num)
    {
        $high = 0;
        /** @var int $low */
        $low = $num & 0xffffffff;

        if ((+(abs($num))) >= 1) {
            if ($num > 0) {
                /** @var int $high */
                $high = min((+(floor($num/4294967296))), 4294967295);
            } else {
                /** @var int $high */
                $high = ~~((+(ceil(($num - (+((~~($num)))))/4294967296))));
            }
        }
        return array((int) $high, (int) $low);
    }

    /**
     * Store a 24-bit integer into a string, treating it as big-endian.
     *
     * @internal You should not use this directly from another application
     *
     * @param int $int
     * @return string
     * @throws TypeError
     */
    public static function store_3($int)
    {
        /* Type checks: */
        if (!is_int($int)) {
            if (is_numeric($int)) {
                $int = (int) $int;
            } else {
                throw new TypeError('Argument 1 must be an integer, ' . gettype($int) . ' given.');
            }
        }
        /** @var string $packed */
        $packed = pack('N', $int);
        return self::substr($packed, 1, 3);
    }

    /**
     * Store a 32-bit integer into a string, treating it as little-endian.
     *
     * @internal You should not use this directly from another application
     *
     * @param int $int
     * @return string
     * @throws TypeError
     */
    public static function store32_le($int)
    {
        /* Type checks: */
        if (!is_int($int)) {
            if (is_numeric($int)) {
                $int = (int) $int;
            } else {
                throw new TypeError('Argument 1 must be an integer, ' . gettype($int) . ' given.');
            }
        }

        /** @var string $packed */
        $packed = pack('V', $int);
        return $packed;
    }

    /**
     * Store a 32-bit integer into a string, treating it as big-endian.
     *
     * @internal You should not use this directly from another application
     *
     * @param int $int
     * @return string
     * @throws TypeError
     */
    public static function store_4($int)
    {
        /* Type checks: */
        if (!is_int($int)) {
            if (is_numeric($int)) {
                $int = (int) $int;
            } else {
                throw new TypeError('Argument 1 must be an integer, ' . gettype($int) . ' given.');
            }
        }

        /** @var string $packed */
        $packed = pack('N', $int);
        return $packed;
    }

    /**
     * Stores a 64-bit integer as an string, treating it as little-endian.
     *
     * @internal You should not use this directly from another application
     *
     * @param int $int
     * @return string
     * @throws TypeError
     */
    public static function store64_le($int)
    {
        /* Type checks: */
        if (!is_int($int)) {
            if (is_numeric($int)) {
                $int = (int) $int;
            } else {
                throw new TypeError('Argument 1 must be an integer, ' . gettype($int) . ' given.');
            }
        }

        if (PHP_INT_SIZE === 8) {
            if (PHP_VERSION_ID >= 50603) {
                /** @var string $packed */
                $packed = pack('P', $int);
                return $packed;
            }
            return self::intToChr($int & 0xff) .
                self::intToChr(($int >>  8) & 0xff) .
                self::intToChr(($int >> 16) & 0xff) .
                self::intToChr(($int >> 24) & 0xff) .
                self::intToChr(($int >> 32) & 0xff) .
                self::intToChr(($int >> 40) & 0xff) .
                self::intToChr(($int >> 48) & 0xff) .
                self::intToChr(($int >> 56) & 0xff);
        }
        if ($int > PHP_INT_MAX) {
            list($hiB, $int) = self::numericTo64BitInteger($int);
        } else {
            $hiB = 0;
        }
        return
            self::intToChr(($int      ) & 0xff) .
            self::intToChr(($int >>  8) & 0xff) .
            self::intToChr(($int >> 16) & 0xff) .
            self::intToChr(($int >> 24) & 0xff) .
            self::intToChr($hiB & 0xff) .
            self::intToChr(($hiB >>  8) & 0xff) .
            self::intToChr(($hiB >> 16) & 0xff) .
            self::intToChr(($hiB >> 24) & 0xff);
    }

    /**
     * Safe string length
     *
     * @internal You should not use this directly from another application
     *
     * @ref mbstring.func_overload
     *
     * @param string $str
     * @return int
     * @throws TypeError
     */
    public static function strlen($str)
    {
        /* Type checks: */
        if (!is_string($str)) {
            throw new TypeError('String expected');
        }

        return (int) (
        self::isMbStringOverride()
            ? mb_strlen($str, '8bit')
            : strlen($str)
        );
    }

    /**
     * Turn a string into an array of integers
     *
     * @internal You should not use this directly from another application
     *
     * @param string $string
     * @return array<int, int>
     * @throws TypeError
     */
    public static function stringToIntArray($string)
    {
        if (!is_string($string)) {
            throw new TypeError('String expected');
        }
        /**
         * @var array<int, int>
         */
        $values = array_values(
            unpack('C*', $string)
        );
        return $values;
    }

    /**
     * Safe substring
     *
     * @internal You should not use this directly from another application
     *
     * @ref mbstring.func_overload
     *
     * @param string $str
     * @param int $start
     * @param int $length
     * @return string
     * @throws TypeError
     */
    public static function substr($str, $start = 0, $length = null)
    {
        /* Type checks: */
        if (!is_string($str)) {
            throw new TypeError('String expected');
        }

        if ($length === 0) {
            return '';
        }

        if (self::isMbStringOverride()) {
            if (PHP_VERSION_ID < 50400 && $length === null) {
                $length = self::strlen($str);
            }
            $sub = (string) mb_substr($str, $start, $length, '8bit');
        } elseif ($length === null) {
            $sub = (string) substr($str, $start);
        } else {
            $sub = (string) substr($str, $start, $length);
        }
        if ($sub !== '') {
            return $sub;
        }
        return '';
    }

    /**
     * Compare a 16-character byte string in constant time.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $a
     * @param string $b
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function verify_16($a, $b)
    {
        /* Type checks: */
        if (!is_string($a)) {
            throw new TypeError('String expected');
        }
        if (!is_string($b)) {
            throw new TypeError('String expected');
        }
        return self::hashEquals(
            self::substr($a, 0, 16),
            self::substr($b, 0, 16)
        );
    }

    /**
     * Compare a 32-character byte string in constant time.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $a
     * @param string $b
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function verify_32($a, $b)
    {
        /* Type checks: */
        if (!is_string($a)) {
            throw new TypeError('String expected');
        }
        if (!is_string($b)) {
            throw new TypeError('String expected');
        }
        return self::hashEquals(
            self::substr($a, 0, 32),
            self::substr($b, 0, 32)
        );
    }

    /**
     * Calculate $a ^ $b for two strings.
     *
     * @internal You should not use this directly from another application
     *
     * @param string $a
     * @param string $b
     * @return string
     * @throws TypeError
     */
    public static function xorStrings($a, $b)
    {
        /* Type checks: */
        if (!is_string($a)) {
            throw new TypeError('Argument 1 must be a string');
        }
        if (!is_string($b)) {
            throw new TypeError('Argument 2 must be a string');
        }

        return (string) ($a ^ $b);
    }

    /**
     * Returns whether or not mbstring.func_overload is in effect.
     *
     * @internal You should not use this directly from another application
     *
     * Note: MB_OVERLOAD_STRING === 2, but we don't reference the constant
     * (for nuisance-free PHP 8 support)
     *
     * @return bool
     */
    protected static function isMbStringOverride()
    {
        static $mbstring = null;

        if ($mbstring === null) {
            $mbstring = extension_loaded('mbstring')
                && defined('MB_OVERLOAD_STRING')
                &&
            ((int) (ini_get('mbstring.func_overload')) & 2);
            // MB_OVERLOAD_STRING === 2
        }
        /** @var bool $mbstring */

        return $mbstring;
    }
}
