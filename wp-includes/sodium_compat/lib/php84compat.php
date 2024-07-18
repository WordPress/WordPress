<?php

require_once dirname(dirname(__FILE__)) . '/autoload.php';

/**
 * This file will monkey patch the pure-PHP implementation in place of the
 * PECL functions and constants, but only if they do not already exist.
 *
 * Thus, the functions or constants just proxy to the appropriate
 * ParagonIE_Sodium_Compat method or class constant, respectively.
 */
foreach (array(
    'CRYPTO_AEAD_AESGIS128L_KEYBYTES',
    'CRYPTO_AEAD_AESGIS128L_NSECBYTES',
    'CRYPTO_AEAD_AESGIS128L_NPUBBYTES',
    'CRYPTO_AEAD_AESGIS128L_ABYTES',
    'CRYPTO_AEAD_AESGIS256_KEYBYTES',
    'CRYPTO_AEAD_AESGIS256_NSECBYTES',
    'CRYPTO_AEAD_AESGIS256_NPUBBYTES',
    'CRYPTO_AEAD_AESGIS256_ABYTES',
    ) as $constant
) {
    if (!defined("SODIUM_$constant") && defined("ParagonIE_Sodium_Compat::$constant")) {
        define("SODIUM_$constant", constant("ParagonIE_Sodium_Compat::$constant"));
    }
}
if (!is_callable('sodium_crypto_aead_aegis128l_decrypt')) {
    /**
     * @see ParagonIE_Sodium_Compat::crypto_aead_aegis128l_decrypt()
     * @param string $ciphertext
     * @param string $additional_data
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     */
    function sodium_crypto_aead_aegis128l_decrypt(
        $ciphertext,
        $additional_data,
        $nonce,
        #[\SensitiveParameter]
        $key
    ) {
        return ParagonIE_Sodium_Compat::crypto_aead_aegis128l_decrypt(
            $ciphertext,
            $additional_data,
            $nonce,
            $key
        );
    }
}
if (!is_callable('sodium_crypto_aead_aegis128l_encrypt')) {
    /**
     * @see ParagonIE_Sodium_Compat::crypto_aead_aegis128l_encrypt()
     * @param string $message
     * @param string $additional_data
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    function sodium_crypto_aead_aegis128l_encrypt(
        #[\SensitiveParameter]
        $message,
        $additional_data,
        $nonce,
        #[\SensitiveParameter]
        $key
    ) {
        return ParagonIE_Sodium_Compat::crypto_aead_aegis128l_encrypt(
            $message,
            $additional_data,
            $nonce,
            $key
        );
    }
}
if (!is_callable('sodium_crypto_aead_aegis256_decrypt')) {
    /**
     * @see ParagonIE_Sodium_Compat::crypto_aead_aegis256_encrypt()
     * @param string $ciphertext
     * @param string $additional_data
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     */
    function sodium_crypto_aead_aegis256_decrypt(
        $ciphertext,
        $additional_data,
        $nonce,
        #[\SensitiveParameter]
        $key
    ) {
        return ParagonIE_Sodium_Compat::crypto_aead_aegis256_decrypt(
            $ciphertext,
            $additional_data,
            $nonce,
            $key
        );
    }
}
if (!is_callable('sodium_crypto_aead_aegis256_encrypt')) {
    /**
     * @see ParagonIE_Sodium_Compat::crypto_aead_aegis256_encrypt()
     * @param string $message
     * @param string $additional_data
     * @param string $nonce
     * @param string $key
     * @return string
     * @throws SodiumException
     * @throws TypeError
     */
    function sodium_crypto_aead_aegis256_encrypt(
        #[\SensitiveParameter]
        $message,
        $additional_data,
        $nonce,
        #[\SensitiveParameter]
        $key
    ) {
        return ParagonIE_Sodium_Compat::crypto_aead_aegis256_encrypt(
            $message,
            $additional_data,
            $nonce,
            $key
        );
    }
}
