<?php

if (class_exists('ParagonIE_Sodium_Core_AEGIS_State128L', false)) {
    return;
}

if (!defined('SODIUM_COMPAT_AEGIS_C0')) {
    define('SODIUM_COMPAT_AEGIS_C0', "\x00\x01\x01\x02\x03\x05\x08\x0d\x15\x22\x37\x59\x90\xe9\x79\x62");
}
if (!defined('SODIUM_COMPAT_AEGIS_C1')) {
    define('SODIUM_COMPAT_AEGIS_C1', "\xdb\x3d\x18\x55\x6d\xc2\x2f\xf1\x20\x11\x31\x42\x73\xb5\x28\xdd");
}

class ParagonIE_Sodium_Core_AEGIS_State128L
{
    /** @var array<int, string> $state */
    protected $state;
    public function __construct()
    {
        $this->state = array_fill(0, 8, '');
    }

    /**
     * @internal Only use this for unit tests!
     * @return string[]
     */
    public function getState()
    {
        return array_values($this->state);
    }

    /**
     * @param array $input
     * @return self
     * @throws SodiumException
     *
     * @internal Only for unit tests
     */
    public static function initForUnitTests(array $input)
    {
        if (count($input) < 8) {
            throw new SodiumException('invalid input');
        }
        $state = new self();
        for ($i = 0; $i < 8; ++$i) {
            $state->state[$i] = $input[$i];
        }
        return $state;
    }

    /**
     * @param string $key
     * @param string $nonce
     * @return self
     */
    public static function init($key, $nonce)
    {
        $state = new self();

        // S0 = key ^ nonce
        $state->state[0] = $key ^ $nonce;
        // S1 = C1
        $state->state[1] = SODIUM_COMPAT_AEGIS_C1;
        // S2 = C0
        $state->state[2] = SODIUM_COMPAT_AEGIS_C0;
        // S3 = C1
        $state->state[3] = SODIUM_COMPAT_AEGIS_C1;
        // S4 = key ^ nonce
        $state->state[4] = $key ^ $nonce;
        // S5 = key ^ C0
        $state->state[5] = $key ^ SODIUM_COMPAT_AEGIS_C0;
        // S6 = key ^ C1
        $state->state[6] = $key ^ SODIUM_COMPAT_AEGIS_C1;
        // S7 = key ^ C0
        $state->state[7] = $key ^ SODIUM_COMPAT_AEGIS_C0;

        // Repeat(10, Update(nonce, key))
        for ($i = 0; $i < 10; ++$i) {
            $state->update($nonce, $key);
        }
        return $state;
    }

    /**
     * @param string $ai
     * @return self
     */
    public function absorb($ai)
    {
        if (ParagonIE_Sodium_Core_Util::strlen($ai) !== 32) {
            throw new SodiumException('Input must be two AES blocks in size');
        }
        $t0 = ParagonIE_Sodium_Core_Util::substr($ai, 0, 16);
        $t1 = ParagonIE_Sodium_Core_Util::substr($ai, 16, 16);
        return $this->update($t0, $t1);
    }


    /**
     * @param string $ci
     * @return string
     * @throws SodiumException
     */
    public function dec($ci)
    {
        if (ParagonIE_Sodium_Core_Util::strlen($ci) !== 32) {
            throw new SodiumException('Input must be two AES blocks in size');
        }

        // z0 = S6 ^ S1 ^ (S2 & S3)
        $z0 = $this->state[6]
            ^ $this->state[1]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[2], $this->state[3]);
        // z1 = S2 ^ S5 ^ (S6 & S7)
        $z1 = $this->state[2]
            ^ $this->state[5]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[6], $this->state[7]);

        // t0, t1 = Split(xi, 128)
        $t0 = ParagonIE_Sodium_Core_Util::substr($ci, 0, 16);
        $t1 = ParagonIE_Sodium_Core_Util::substr($ci, 16, 16);

        // out0 = t0 ^ z0
        // out1 = t1 ^ z1
        $out0 = $t0 ^ $z0;
        $out1 = $t1 ^ $z1;

        // Update(out0, out1)
        // xi = out0 || out1
        $this->update($out0, $out1);
        return $out0 . $out1;
    }

    /**
     * @param string $cn
     * @return string
     */
    public function decPartial($cn)
    {
        $len = ParagonIE_Sodium_Core_Util::strlen($cn);

        // z0 = S6 ^ S1 ^ (S2 & S3)
        $z0 = $this->state[6]
            ^ $this->state[1]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[2], $this->state[3]);
        // z1 = S2 ^ S5 ^ (S6 & S7)
        $z1 = $this->state[2]
            ^ $this->state[5]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[6], $this->state[7]);

        // t0, t1 = Split(ZeroPad(cn, 256), 128)
        $cn = str_pad($cn, 32, "\0", STR_PAD_RIGHT);
        $t0 = ParagonIE_Sodium_Core_Util::substr($cn, 0, 16);
        $t1 = ParagonIE_Sodium_Core_Util::substr($cn, 16, 16);
        // out0 = t0 ^ z0
        // out1 = t1 ^ z1
        $out0 = $t0 ^ $z0;
        $out1 = $t1 ^ $z1;

        // xn = Truncate(out0 || out1, |cn|)
        $xn = ParagonIE_Sodium_Core_Util::substr($out0 . $out1, 0, $len);

        // v0, v1 = Split(ZeroPad(xn, 256), 128)
        $padded = str_pad($xn, 32, "\0", STR_PAD_RIGHT);
        $v0 = ParagonIE_Sodium_Core_Util::substr($padded, 0, 16);
        $v1 = ParagonIE_Sodium_Core_Util::substr($padded, 16, 16);
        // Update(v0, v1)
        $this->update($v0, $v1);

        // return xn
        return $xn;
    }

    /**
     * @param string $xi
     * @return string
     * @throws SodiumException
     */
    public function enc($xi)
    {
        if (ParagonIE_Sodium_Core_Util::strlen($xi) !== 32) {
            throw new SodiumException('Input must be two AES blocks in size');
        }

        // z0 = S6 ^ S1 ^ (S2 & S3)
        $z0 = $this->state[6]
            ^ $this->state[1]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[2], $this->state[3]);
        // z1 = S2 ^ S5 ^ (S6 & S7)
        $z1 = $this->state[2]
            ^ $this->state[5]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[6], $this->state[7]);

        // t0, t1 = Split(xi, 128)
        $t0 = ParagonIE_Sodium_Core_Util::substr($xi, 0, 16);
        $t1 = ParagonIE_Sodium_Core_Util::substr($xi, 16, 16);

        // out0 = t0 ^ z0
        // out1 = t1 ^ z1
        $out0 = $t0 ^ $z0;
        $out1 = $t1 ^ $z1;

        // Update(t0, t1)
        // ci = out0 || out1
        $this->update($t0, $t1);

        // return ci
        return $out0 . $out1;
    }

    /**
     * @param int $ad_len_bits
     * @param int $msg_len_bits
     * @return string
     */
    public function finalize($ad_len_bits, $msg_len_bits)
    {
        $encoded = ParagonIE_Sodium_Core_Util::store64_le($ad_len_bits) .
            ParagonIE_Sodium_Core_Util::store64_le($msg_len_bits);
        $t = $this->state[2] ^ $encoded;
        for ($i = 0; $i < 7; ++$i) {
            $this->update($t, $t);
        }
        return ($this->state[0] ^ $this->state[1] ^ $this->state[2] ^ $this->state[3]) .
            ($this->state[4] ^ $this->state[5] ^ $this->state[6] ^ $this->state[7]);
    }

    /**
     * @param string $m0
     * @param string $m1
     * @return self
     */
    public function update($m0, $m1)
    {
        /*
           S'0 = AESRound(S7, S0 ^ M0)
           S'1 = AESRound(S0, S1)
           S'2 = AESRound(S1, S2)
           S'3 = AESRound(S2, S3)
           S'4 = AESRound(S3, S4 ^ M1)
           S'5 = AESRound(S4, S5)
           S'6 = AESRound(S5, S6)
           S'7 = AESRound(S6, S7)
         */
        list($s_0, $s_1) = ParagonIE_Sodium_Core_AES::doubleRound(
            $this->state[7], $this->state[0] ^ $m0,
            $this->state[0], $this->state[1]
        );

        list($s_2, $s_3) = ParagonIE_Sodium_Core_AES::doubleRound(
            $this->state[1], $this->state[2],
            $this->state[2], $this->state[3]
        );

        list($s_4, $s_5) = ParagonIE_Sodium_Core_AES::doubleRound(
            $this->state[3], $this->state[4] ^ $m1,
            $this->state[4], $this->state[5]
        );
        list($s_6, $s_7) = ParagonIE_Sodium_Core_AES::doubleRound(
            $this->state[5], $this->state[6],
            $this->state[6], $this->state[7]
        );

        /*
           S0  = S'0
           S1  = S'1
           S2  = S'2
           S3  = S'3
           S4  = S'4
           S5  = S'5
           S6  = S'6
           S7  = S'7
         */
        $this->state[0] = $s_0;
        $this->state[1] = $s_1;
        $this->state[2] = $s_2;
        $this->state[3] = $s_3;
        $this->state[4] = $s_4;
        $this->state[5] = $s_5;
        $this->state[6] = $s_6;
        $this->state[7] = $s_7;
        return $this;
    }
}