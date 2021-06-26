<?php

if (class_exists('ParagonIE_Sodium_Core_Curve25519_Ge_P3', false)) {
    return;
}

/**
 * Class ParagonIE_Sodium_Core_Curve25519_Ge_P3
 */
class ParagonIE_Sodium_Core_Curve25519_Ge_P3
{
    /**
     * @var ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public $X;

    /**
     * @var ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public $Y;

    /**
     * @var ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public $Z;

    /**
     * @var ParagonIE_Sodium_Core_Curve25519_Fe
     */
    public $T;

    /**
     * ParagonIE_Sodium_Core_Curve25519_Ge_P3 constructor.
     *
     * @internal You should not use this directly from another application
     *
     * @param ParagonIE_Sodium_Core_Curve25519_Fe|null $x
     * @param ParagonIE_Sodium_Core_Curve25519_Fe|null $y
     * @param ParagonIE_Sodium_Core_Curve25519_Fe|null $z
     * @param ParagonIE_Sodium_Core_Curve25519_Fe|null $t
     */
    public function __construct(
        ParagonIE_Sodium_Core_Curve25519_Fe $x = null,
        ParagonIE_Sodium_Core_Curve25519_Fe $y = null,
        ParagonIE_Sodium_Core_Curve25519_Fe $z = null,
        ParagonIE_Sodium_Core_Curve25519_Fe $t = null
    ) {
        if ($x === null) {
            $x = new ParagonIE_Sodium_Core_Curve25519_Fe();
        }
        $this->X = $x;
        if ($y === null) {
            $y = new ParagonIE_Sodium_Core_Curve25519_Fe();
        }
        $this->Y = $y;
        if ($z === null) {
            $z = new ParagonIE_Sodium_Core_Curve25519_Fe();
        }
        $this->Z = $z;
        if ($t === null) {
            $t = new ParagonIE_Sodium_Core_Curve25519_Fe();
        }
        $this->T = $t;
    }
}
