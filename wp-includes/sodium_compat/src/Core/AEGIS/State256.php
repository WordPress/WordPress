<?php

if (class_exists('ParagonIE_Sodium_Core_AEGIS_State256', false)) {
    return;
}

if (!defined('SODIUM_COMPAT_AEGIS_C0')) {
    define('SODIUM_COMPAT_AEGIS_C0', "\x00\x01\x01\x02\x03\x05\x08\x0d\x15\x22\x37\x59\x90\xe9\x79\x62");
}
if (!defined('SODIUM_COMPAT_AEGIS_C1')) {
    define('SODIUM_COMPAT_AEGIS_C1', "\xdb\x3d\x18\x55\x6d\xc2\x2f\xf1\x20\x11\x31\x42\x73\xb5\x28\xdd");
}

class ParagonIE_Sodium_Core_AEGIS_State256
{
    /** @var array<int, string> $state */
    protected $state;
    public function __construct()
    {
        $this->state = array_fill(0, 6, '');
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
        if (count($input) < 6) {
            throw new SodiumException('invalid input');
        }
        $state = new self();
        for ($i = 0; $i < 6; ++$i) {
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
        $k0 = ParagonIE_Sodium_Core_Util::substr($key, 0, 16);
        $k1 = ParagonIE_Sodium_Core_Util::substr($key, 16, 16);
        $n0 = ParagonIE_Sodium_Core_Util::substr($nonce, 0, 16);
        $n1 = ParagonIE_Sodium_Core_Util::substr($nonce, 16, 16);

        // S0 = k0 ^ n0
        // S1 = k1 ^ n1
        // S2 = C1
        // S3 = C0
        // S4 = k0 ^ C0
        // S5 = k1 ^ C1
        $k0_n0 = $k0 ^ $n0;
        $k1_n1 = $k1 ^ $n1;
        $state->state[0] = $k0_n0;
        $state->state[1] = $k1_n1;
        $state->state[2] = SODIUM_COMPAT_AEGIS_C1;
        $state->state[3] = SODIUM_COMPAT_AEGIS_C0;
        $state->state[4] = $k0 ^ SODIUM_COMPAT_AEGIS_C0;
        $state->state[5] = $k1 ^ SODIUM_COMPAT_AEGIS_C1;

        // Repeat(4,
        //   Update(k0)
        //   Update(k1)
        //   Update(k0 ^ n0)
        //   Update(k1 ^ n1)
        // )
        for ($i = 0; $i < 4; ++$i) {
            $state->update($k0);
            $state->update($k1);
            $state->update($k0 ^ $n0);
            $state->update($k1 ^ $n1);
        }
        return $state;
    }

    /**
     * @param string $ai
     * @return self
     * @throws SodiumException
     */
    public function absorb($ai)
    {
        if (ParagonIE_Sodium_Core_Util::strlen($ai) !== 16) {
            throw new SodiumException('Input must be an AES block in size');
        }
        return $this->update($ai);
    }

    /**
     * @param string $ci
     * @return string
     * @throws SodiumException
     */
    public function dec($ci)
    {
        if (ParagonIE_Sodium_Core_Util::strlen($ci) !== 16) {
            throw new SodiumException('Input must be an AES block in size');
        }
        // z = S1 ^ S4 ^ S5 ^ (S2 & S3)
        $z = $this->state[1]
            ^ $this->state[4]
            ^ $this->state[5]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[2], $this->state[3]);
        $xi = $ci ^ $z;
        $this->update($xi);
        return $xi;
    }

    /**
     * @param string $cn
     * @return string
     */
    public function decPartial($cn)
    {
        $len = ParagonIE_Sodium_Core_Util::strlen($cn);
        // z = S1 ^ S4 ^ S5 ^ (S2 & S3)
        $z = $this->state[1]
            ^ $this->state[4]
            ^ $this->state[5]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[2], $this->state[3]);

        // t = ZeroPad(cn, 128)
        $t = str_pad($cn, 16, "\0", STR_PAD_RIGHT);

        // out = t ^ z
        $out = $t ^ $z;

        // xn = Truncate(out, |cn|)
        $xn = ParagonIE_Sodium_Core_Util::substr($out, 0, $len);

        // v = ZeroPad(xn, 128)
        $v = str_pad($xn, 16, "\0", STR_PAD_RIGHT);
        // Update(v)
        $this->update($v);

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
        if (ParagonIE_Sodium_Core_Util::strlen($xi) !== 16) {
            throw new SodiumException('Input must be an AES block in size');
        }
        // z = S1 ^ S4 ^ S5 ^ (S2 & S3)
        $z = $this->state[1]
            ^ $this->state[4]
            ^ $this->state[5]
            ^ ParagonIE_Sodium_Core_Util::andStrings($this->state[2], $this->state[3]);
        $this->update($xi);
        return $xi ^ $z;
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
        $t = $this->state[3] ^ $encoded;

        for ($i = 0; $i < 7; ++$i) {
            $this->update($t);
        }

        return ($this->state[0] ^ $this->state[1] ^ $this->state[2]) .
            ($this->state[3] ^ $this->state[4] ^ $this->state[5]);
    }

    /**
     * @param string $m
     * @return self
     */
    public function update($m)
    {
        /*
            S'0 = AESRound(S5, S0 ^ M)
            S'1 = AESRound(S0, S1)
            S'2 = AESRound(S1, S2)
            S'3 = AESRound(S2, S3)
            S'4 = AESRound(S3, S4)
            S'5 = AESRound(S4, S5)
         */
        list($s_0, $s_1) = ParagonIE_Sodium_Core_AES::doubleRound(
            $this->state[5],$this->state[0] ^ $m,
            $this->state[0], $this->state[1]
        );

        list($s_2, $s_3) = ParagonIE_Sodium_Core_AES::doubleRound(
            $this->state[1], $this->state[2],
            $this->state[2], $this->state[3]
        );
        list($s_4, $s_5) = ParagonIE_Sodium_Core_AES::doubleRound(
            $this->state[3], $this->state[4],
            $this->state[4], $this->state[5]
        );

        /*
            S0  = S'0
            S1  = S'1
            S2  = S'2
            S3  = S'3
            S4  = S'4
            S5  = S'5
         */
        $this->state[0] = $s_0;
        $this->state[1] = $s_1;
        $this->state[2] = $s_2;
        $this->state[3] = $s_3;
        $this->state[4] = $s_4;
        $this->state[5] = $s_5;
        return $this;
    }
}
