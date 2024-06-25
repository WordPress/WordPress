<?php

/**
 * Class ParagonIE_Sodium_Core_Base64UrlSafe
 *
 *  Copyright (c) 2016 - 2018 Paragon Initiative Enterprises.
 *  Copyright (c) 2014 Steve "Sc00bz" Thomas (steve at tobtu dot com)
 */
class ParagonIE_Sodium_Core_Base64_UrlSafe
{
    // COPY ParagonIE_Sodium_Core_Base64_Common STARTING HERE
    /**
     * Encode into Base64
     *
     * Base64 character set "[A-Z][a-z][0-9]+/"
     *
     * @param string $src
     * @return string
     * @throws TypeError
     */
    public static function encode($src)
    {
        return self::doEncode($src, true);
    }

    /**
     * Encode into Base64, no = padding
     *
     * Base64 character set "[A-Z][a-z][0-9]+/"
     *
     * @param string $src
     * @return string
     * @throws TypeError
     */
    public static function encodeUnpadded($src)
    {
        return self::doEncode($src, false);
    }

    /**
     * @param string $src
     * @param bool $pad   Include = padding?
     * @return string
     * @throws TypeError
     */
    protected static function doEncode($src, $pad = true)
    {
        $dest = '';
        $srcLen = ParagonIE_Sodium_Core_Util::strlen($src);
        // Main loop (no padding):
        for ($i = 0; $i + 3 <= $srcLen; $i += 3) {
            /** @var array<int, int> $chunk */
            $chunk = unpack('C*', ParagonIE_Sodium_Core_Util::substr($src, $i, 3));
            $b0 = $chunk[1];
            $b1 = $chunk[2];
            $b2 = $chunk[3];

            $dest .=
                self::encode6Bits(               $b0 >> 2       ) .
                self::encode6Bits((($b0 << 4) | ($b1 >> 4)) & 63) .
                self::encode6Bits((($b1 << 2) | ($b2 >> 6)) & 63) .
                self::encode6Bits(  $b2                     & 63);
        }
        // The last chunk, which may have padding:
        if ($i < $srcLen) {
            /** @var array<int, int> $chunk */
            $chunk = unpack('C*', ParagonIE_Sodium_Core_Util::substr($src, $i, $srcLen - $i));
            $b0 = $chunk[1];
            if ($i + 1 < $srcLen) {
                $b1 = $chunk[2];
                $dest .=
                    self::encode6Bits($b0 >> 2) .
                    self::encode6Bits((($b0 << 4) | ($b1 >> 4)) & 63) .
                    self::encode6Bits(($b1 << 2) & 63);
                if ($pad) {
                    $dest .= '=';
                }
            } else {
                $dest .=
                    self::encode6Bits( $b0 >> 2) .
                    self::encode6Bits(($b0 << 4) & 63);
                if ($pad) {
                    $dest .= '==';
                }
            }
        }
        return $dest;
    }

    /**
     * decode from base64 into binary
     *
     * Base64 character set "./[A-Z][a-z][0-9]"
     *
     * @param string $src
     * @param bool $strictPadding
     * @return string
     * @throws RangeException
     * @throws TypeError
     * @psalm-suppress RedundantCondition
     */
    public static function decode($src, $strictPadding = false)
    {
        // Remove padding
        $srcLen = ParagonIE_Sodium_Core_Util::strlen($src);
        if ($srcLen === 0) {
            return '';
        }

        if ($strictPadding) {
            if (($srcLen & 3) === 0) {
                if ($src[$srcLen - 1] === '=') {
                    $srcLen--;
                    if ($src[$srcLen - 1] === '=') {
                        $srcLen--;
                    }
                }
            }
            if (($srcLen & 3) === 1) {
                throw new RangeException(
                    'Incorrect padding'
                );
            }
            if ($src[$srcLen - 1] === '=') {
                throw new RangeException(
                    'Incorrect padding'
                );
            }
        } else {
            $src = rtrim($src, '=');
            $srcLen =  ParagonIE_Sodium_Core_Util::strlen($src);
        }

        $err = 0;
        $dest = '';
        // Main loop (no padding):
        for ($i = 0; $i + 4 <= $srcLen; $i += 4) {
            /** @var array<int, int> $chunk */
            $chunk = unpack('C*', ParagonIE_Sodium_Core_Util::substr($src, $i, 4));
            $c0 = self::decode6Bits($chunk[1]);
            $c1 = self::decode6Bits($chunk[2]);
            $c2 = self::decode6Bits($chunk[3]);
            $c3 = self::decode6Bits($chunk[4]);

            $dest .= pack(
                'CCC',
                ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                ((($c1 << 4) | ($c2 >> 2)) & 0xff),
                ((($c2 << 6) | $c3) & 0xff)
            );
            $err |= ($c0 | $c1 | $c2 | $c3) >> 8;
        }
        // The last chunk, which may have padding:
        if ($i < $srcLen) {
            /** @var array<int, int> $chunk */
            $chunk = unpack('C*', ParagonIE_Sodium_Core_Util::substr($src, $i, $srcLen - $i));
            $c0 = self::decode6Bits($chunk[1]);

            if ($i + 2 < $srcLen) {
                $c1 = self::decode6Bits($chunk[2]);
                $c2 = self::decode6Bits($chunk[3]);
                $dest .= pack(
                    'CC',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff),
                    ((($c1 << 4) | ($c2 >> 2)) & 0xff)
                );
                $err |= ($c0 | $c1 | $c2) >> 8;
            } elseif ($i + 1 < $srcLen) {
                $c1 = self::decode6Bits($chunk[2]);
                $dest .= pack(
                    'C',
                    ((($c0 << 2) | ($c1 >> 4)) & 0xff)
                );
                $err |= ($c0 | $c1) >> 8;
            } elseif ($i < $srcLen && $strictPadding) {
                $err |= 1;
            }
        }
        /** @var bool $check */
        $check = ($err === 0);
        if (!$check) {
            throw new RangeException(
                'Base64::decode() only expects characters in the correct base64 alphabet'
            );
        }
        return $dest;
    }
    // COPY ParagonIE_Sodium_Core_Base64_Common ENDING HERE
    /**
     * Uses bitwise operators instead of table-lookups to turn 6-bit integers
     * into 8-bit integers.
     *
     * Base64 character set:
     * [A-Z]      [a-z]      [0-9]      +     /
     * 0x41-0x5a, 0x61-0x7a, 0x30-0x39, 0x2b, 0x2f
     *
     * @param int $src
     * @return int
     */
    protected static function decode6Bits($src)
    {
        $ret = -1;

        // if ($src > 0x40 && $src < 0x5b) $ret += $src - 0x41 + 1; // -64
        $ret += (((0x40 - $src) & ($src - 0x5b)) >> 8) & ($src - 64);

        // if ($src > 0x60 && $src < 0x7b) $ret += $src - 0x61 + 26 + 1; // -70
        $ret += (((0x60 - $src) & ($src - 0x7b)) >> 8) & ($src - 70);

        // if ($src > 0x2f && $src < 0x3a) $ret += $src - 0x30 + 52 + 1; // 5
        $ret += (((0x2f - $src) & ($src - 0x3a)) >> 8) & ($src + 5);

        // if ($src == 0x2c) $ret += 62 + 1;
        $ret += (((0x2c - $src) & ($src - 0x2e)) >> 8) & 63;

        // if ($src == 0x5f) ret += 63 + 1;
        $ret += (((0x5e - $src) & ($src - 0x60)) >> 8) & 64;

        return $ret;
    }

    /**
     * Uses bitwise operators instead of table-lookups to turn 8-bit integers
     * into 6-bit integers.
     *
     * @param int $src
     * @return string
     */
    protected static function encode6Bits($src)
    {
        $diff = 0x41;

        // if ($src > 25) $diff += 0x61 - 0x41 - 26; // 6
        $diff += ((25 - $src) >> 8) & 6;

        // if ($src > 51) $diff += 0x30 - 0x61 - 26; // -75
        $diff -= ((51 - $src) >> 8) & 75;

        // if ($src > 61) $diff += 0x2d - 0x30 - 10; // -13
        $diff -= ((61 - $src) >> 8) & 13;

        // if ($src > 62) $diff += 0x5f - 0x2b - 1; // 3
        $diff += ((62 - $src) >> 8) & 49;

        return pack('C', $src + $diff);
    }
}
