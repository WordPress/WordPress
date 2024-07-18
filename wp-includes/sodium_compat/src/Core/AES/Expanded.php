<?php

if (class_exists('ParagonIE_Sodium_Core_AES_Expanded', false)) {
    return;
}

/**
 * @internal This should only be used by sodium_compat
 */
class ParagonIE_Sodium_Core_AES_Expanded extends ParagonIE_Sodium_Core_AES_KeySchedule
{
    /** @var bool $expanded */
    protected $expanded = true;
}
