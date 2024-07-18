<?php

if (!defined('SODIUM_COMPAT_AEGIS_C0')) {
    define('SODIUM_COMPAT_AEGIS_C0', "\x00\x01\x01\x02\x03\x05\x08\x0d\x15\x22\x37\x59\x90\xe9\x79\x62");
}
if (!defined('SODIUM_COMPAT_AEGIS_C1')) {
    define('SODIUM_COMPAT_AEGIS_C1', "\xdb\x3d\x18\x55\x6d\xc2\x2f\xf1\x20\x11\x31\x42\x73\xb5\x28\xdd");
}

class ParagonIE_Sodium_Core_AEGIS256 extends ParagonIE_Sodium_Core_AES
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

        // ad_blocks = Split(ZeroPad(ad, 128), 128)
        $ad_blocks = (self::strlen($ad) + 15) >> 4;
        // for ai in ad_blocks:
        //     Absorb(ai)
        for ($i = 0; $i < $ad_blocks; ++$i) {
            $ai = self::substr($ad, $i << 4, 16);
            if (self::strlen($ai) < 16) {
                $ai = str_pad($ai, 16, "\0", STR_PAD_RIGHT);
            }
            $state->absorb($ai);
        }

        $msg = '';
        $cn = self::strlen($ct) & 15;
        $ct_blocks = self::strlen($ct) >> 4;
        // ct_blocks = Split(ZeroPad(ct, 128), 128)
        // cn = Tail(ct, |ct| mod 128)
        for ($i = 0; $i < $ct_blocks; ++$i) {
            $msg .= $state->dec(self::substr($ct, $i << 4, 16));
        }
        // if cn is not empty:
        //   msg = msg || DecPartial(cn)
        if ($cn) {
            $start = $ct_blocks << 4;
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
     * @throws SodiumException
     */
    public static function encrypt($msg, $ad, $key, $nonce)
    {
        $state = self::init($key, $nonce);
        $ad_len = self::strlen($ad);
        $msg_len = self::strlen($msg);
        $ad_blocks = ($ad_len + 15) >> 4;
        for ($i = 0; $i < $ad_blocks; ++$i) {
            $ai = self::substr($ad, $i << 4, 16);
            if (self::strlen($ai) < 16) {
                $ai = str_pad($ai, 16, "\0", STR_PAD_RIGHT);
            }
            $state->absorb($ai);
        }

        $ct = '';
        $msg_blocks = ($msg_len + 15) >> 4;
        for ($i = 0; $i < $msg_blocks; ++$i) {
            $xi = self::substr($msg, $i << 4, 16);
            if (self::strlen($xi) < 16) {
                $xi = str_pad($xi, 16, "\0", STR_PAD_RIGHT);
            }
            $ct .= $state->enc($xi);
        }
        $tag = $state->finalize(
            $ad_len << 3,
            $msg_len << 3
        );
        return array(
            self::substr($ct, 0, $msg_len),
            $tag
        );

    }

    /**
     * @param string $key
     * @param string $nonce
     * @return ParagonIE_Sodium_Core_AEGIS_State256
     */
    public static function init($key, $nonce)
    {
        return ParagonIE_Sodium_Core_AEGIS_State256::init($key, $nonce);
    }
}
