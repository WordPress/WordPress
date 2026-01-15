<?php

if (!defined('SODIUM_COMPAT_AEGIS_C0')) {
    define('SODIUM_COMPAT_AEGIS_C0', "\x00\x01\x01\x02\x03\x05\x08\x0d\x15\x22\x37\x59\x90\xe9\x79\x62");
}
if (!defined('SODIUM_COMPAT_AEGIS_C1')) {
    define('SODIUM_COMPAT_AEGIS_C1', "\xdb\x3d\x18\x55\x6d\xc2\x2f\xf1\x20\x11\x31\x42\x73\xb5\x28\xdd");
}

class ParagonIE_Sodium_Core_AEGIS128L extends ParagonIE_Sodium_Core_AES
{
    /**
     * @param string $ct
     * @param string $tag
     * @param string $ad
     * @param string $key
     * @param string $nonce
     * @return string
     * @throws SodiumException
     */
    public static function decrypt($ct, $tag, $ad, $key, $nonce)
    {
        $state = self::init($key, $nonce);
        $ad_blocks = (self::strlen($ad) + 31) >> 5;
        for ($i = 0; $i < $ad_blocks; ++$i) {
            $ai = self::substr($ad, $i << 5, 32);
            if (self::strlen($ai) < 32) {
                $ai = str_pad($ai, 32, "\0", STR_PAD_RIGHT);
            }
            $state->absorb($ai);
        }

        $msg = '';
        $cn = self::strlen($ct) & 31;
        $ct_blocks = self::strlen($ct) >> 5;
        for ($i = 0; $i < $ct_blocks; ++$i) {
            $msg .= $state->dec(self::substr($ct, $i << 5, 32));
        }
        if ($cn) {
            $start = $ct_blocks << 5;
            $msg .= $state->decPartial(self::substr($ct, $start, $cn));
        }
        $expected_tag = $state->finalize(
            self::strlen($ad) << 3,
            self::strlen($msg) << 3
        );
        if (!self::hashEquals($expected_tag, $tag)) {
            try {
                // The RFC says to erase msg, so we shall try:
                ParagonIE_Sodium_Compat::memzero($msg);
            } catch (SodiumException $ex) {
                // Do nothing if we cannot memzero
            }
            throw new SodiumException('verification failed');
        }
        return $msg;
    }

    /**
     * @param string $msg
     * @param string $ad
     * @param string $key
     * @param string $nonce
     * @return array
     *
     * @throws SodiumException
     */
    public static function encrypt($msg, $ad, $key, $nonce)
    {
        $state = self::init($key, $nonce);
        // ad_blocks = Split(ZeroPad(ad, 256), 256)
        // for ai in ad_blocks:
        //     Absorb(ai)
        $ad_len = self::strlen($ad);
        $msg_len = self::strlen($msg);
        $ad_blocks = ($ad_len + 31) >> 5;
        for ($i = 0; $i < $ad_blocks; ++$i) {
            $ai = self::substr($ad, $i << 5, 32);
            if (self::strlen($ai) < 32) {
                $ai = str_pad($ai, 32, "\0", STR_PAD_RIGHT);
            }
            $state->absorb($ai);
        }

        // msg_blocks = Split(ZeroPad(msg, 256), 256)
        // for xi in msg_blocks:
        //     ct = ct || Enc(xi)
        $ct = '';
        $msg_blocks = ($msg_len + 31) >> 5;
        for ($i = 0; $i < $msg_blocks; ++$i) {
            $xi = self::substr($msg, $i << 5, 32);
            if (self::strlen($xi) < 32) {
                $xi = str_pad($xi, 32, "\0", STR_PAD_RIGHT);
            }
            $ct .= $state->enc($xi);
        }
        // tag = Finalize(|ad|, |msg|)
        // ct = Truncate(ct, |msg|)
        $tag = $state->finalize(
            $ad_len << 3,
            $msg_len << 3
        );
        // return ct and tag
        return array(
            self::substr($ct, 0, $msg_len),
            $tag
        );
    }

    /**
     * @param string $key
     * @param string $nonce
     * @return ParagonIE_Sodium_Core_AEGIS_State128L
     */
    public static function init($key, $nonce)
    {
        return ParagonIE_Sodium_Core_AEGIS_State128L::init($key, $nonce);
    }
}
