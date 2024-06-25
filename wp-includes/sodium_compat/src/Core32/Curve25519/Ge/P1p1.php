<?php

if (class_exists('ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1', false)) {
    return;
}
/**
 * Class ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
 */
class ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1
{
    /**
     * @var ParagonIE_Sodium_Core32_Curve25519_Fe
     */
    public $X;

    /**
     * @var ParagonIE_Sodium_Core32_Curve25519_Fe
     */
    public $Y;

    /**
     * @var ParagonIE_Sodium_Core32_Curve25519_Fe
     */
    public $Z;

    /**
     * @var ParagonIE_Sodium_Core32_Curve25519_Fe
     */
    public $T;

    /**
     * ParagonIE_Sodium_Core32_Curve25519_Ge_P1p1 constructor.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe|null $x
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe|null $y
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe|null $z
     * @param ParagonIE_Sodium_Core32_Curve25519_Fe|null $t
     *
     * @throws SodiumException
     * @throws TypeError
     */
    public function __construct(
        ParagonIE_Sodium_Core32_Curve25519_Fe $x = null,
        ParagonIE_Sodium_Core32_Curve25519_Fe $y = null,
        ParagonIE_Sodium_Core32_Curve25519_Fe $z = null,
        ParagonIE_Sodium_Core32_Curve25519_Fe $t = null
    ) {
        if ($x === null) {
            $x = ParagonIE_Sodium_Core32_Curve25519::fe_0();
        }
        $this->X = $x;
        if ($y === null) {
            $y = ParagonIE_Sodium_Core32_Curve25519::fe_0();
        }
        $this->Y = $y;
        if ($z === null) {
            $z = ParagonIE_Sodium_Core32_Curve25519::fe_0();
        }
        $this->Z = $z;
        if ($t === null) {
            $t = ParagonIE_Sodium_Core32_Curve25519::fe_0();
        }
        $this->T = $t;
    }
}
