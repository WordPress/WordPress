<?php

if (class_exists('ParagonIE_Sodium_File', false)) {
    return;
}
/**
 * Class ParagonIE_Sodium_File
 */
class ParagonIE_Sodium_File extends ParagonIE_Sodium_Core_Util
{
    /* PHP's default buffer size is 8192 for fread()/fwrite(). */
    const BUFFER_SIZE = 8192;

    /**
     * Box a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_box(), but produces
     * the same result.
     *
     * @param string $inputFile  Absolute path to a file on the filesystem
     * @param string $outputFile Absolute path to a file on the filesystem
     * @param string $nonce      Number to be used only once
     * @param string $keyPair    ECDH secret key and ECDH public key concatenated
     *
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function box(
        $inputFile,
        $outputFile,
        $nonce,
        #[\SensitiveParameter]
        $keyPair
    ) {
        /* Type checks: */
        if (!is_string($inputFile)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($inputFile) . ' given.');
        }
        if (!is_string($outputFile)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($outputFile) . ' given.');
        }
        if (!is_string($nonce)) {
            throw new TypeError('Argument 3 must be a string, ' . gettype($nonce) . ' given.');
        }

        /* Input validation: */
        if (!is_string($keyPair)) {
            throw new TypeError('Argument 4 must be a string, ' . gettype($keyPair) . ' given.');
        }
        if (self::strlen($nonce) !== ParagonIE_Sodium_Compat::CRYPTO_BOX_NONCEBYTES) {
            throw new TypeError('Argument 3 must be CRYPTO_BOX_NONCEBYTES bytes');
        }
        if (self::strlen($keyPair) !== ParagonIE_Sodium_Compat::CRYPTO_BOX_KEYPAIRBYTES) {
            throw new TypeError('Argument 4 must be CRYPTO_BOX_KEYPAIRBYTES bytes');
        }

        /** @var int $size */
        $size = filesize($inputFile);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $ifp */
        $ifp = fopen($inputFile, 'rb');
        if (!is_resource($ifp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var resource $ofp */
        $ofp = fopen($outputFile, 'wb');
        if (!is_resource($ofp)) {
            fclose($ifp);
            throw new SodiumException('Could not open output file for writing');
        }

        $res = self::box_encrypt($ifp, $ofp, $size, $nonce, $keyPair);
        fclose($ifp);
        fclose($ofp);
        return $res;
    }

    /**
     * Open a boxed file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_box_open(), but produces
     * the same result.
     *
     * Warning: Does not protect against TOCTOU attacks. You should
     * just load the file into memory and use crypto_box_open() if
     * you are worried about those.
     *
     * @param string $inputFile
     * @param string $outputFile
     * @param string $nonce
     * @param string $keypair
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function box_open(
        $inputFile,
        $outputFile,
        $nonce,
        #[\SensitiveParameter]
        $keypair
    ) {
        /* Type checks: */
        if (!is_string($inputFile)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($inputFile) . ' given.');
        }
        if (!is_string($outputFile)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($outputFile) . ' given.');
        }
        if (!is_string($nonce)) {
            throw new TypeError('Argument 3 must be a string, ' . gettype($nonce) . ' given.');
        }
        if (!is_string($keypair)) {
            throw new TypeError('Argument 4 must be a string, ' . gettype($keypair) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($nonce) !== ParagonIE_Sodium_Compat::CRYPTO_BOX_NONCEBYTES) {
            throw new TypeError('Argument 4 must be CRYPTO_BOX_NONCEBYTES bytes');
        }
        if (self::strlen($keypair) !== ParagonIE_Sodium_Compat::CRYPTO_BOX_KEYPAIRBYTES) {
            throw new TypeError('Argument 4 must be CRYPTO_BOX_KEYPAIRBYTES bytes');
        }

        if (!file_exists($inputFile)) {
            throw new SodiumException('Input file does not exist');
        }
        /** @var int $size */
        $size = filesize($inputFile);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $ifp */
        $ifp = fopen($inputFile, 'rb');
        if (!is_resource($ifp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var resource $ofp */
        $ofp = @fopen($outputFile, 'wb');
        if (!is_resource($ofp)) {
            fclose($ifp);
            throw new SodiumException('Could not open output file for writing');
        }

        $res = self::box_decrypt($ifp, $ofp, $size, $nonce, $keypair);
        fclose($ifp);
        fclose($ofp);
        try {
            ParagonIE_Sodium_Compat::memzero($nonce);
            ParagonIE_Sodium_Compat::memzero($ephKeypair);
        } catch (SodiumException $ex) {
            if (isset($ephKeypair)) {
                unset($ephKeypair);
            }
        }
        return $res;
    }

    /**
     * Seal a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_box_seal(), but produces
     * the same result.
     *
     * @param string $inputFile  Absolute path to a file on the filesystem
     * @param string $outputFile Absolute path to a file on the filesystem
     * @param string $publicKey  ECDH public key
     *
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function box_seal(
        $inputFile,
        $outputFile,
        #[\SensitiveParameter]
        $publicKey
    ) {
        /* Type checks: */
        if (!is_string($inputFile)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($inputFile) . ' given.');
        }
        if (!is_string($outputFile)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($outputFile) . ' given.');
        }
        if (!is_string($publicKey)) {
            throw new TypeError('Argument 3 must be a string, ' . gettype($publicKey) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($publicKey) !== ParagonIE_Sodium_Compat::CRYPTO_BOX_PUBLICKEYBYTES) {
            throw new TypeError('Argument 3 must be CRYPTO_BOX_PUBLICKEYBYTES bytes');
        }

        if (!file_exists($inputFile)) {
            throw new SodiumException('Input file does not exist');
        }
        /** @var int $size */
        $size = filesize($inputFile);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $ifp */
        $ifp = fopen($inputFile, 'rb');
        if (!is_resource($ifp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var resource $ofp */
        $ofp = @fopen($outputFile, 'wb');
        if (!is_resource($ofp)) {
            fclose($ifp);
            throw new SodiumException('Could not open output file for writing');
        }

        /** @var string $ephKeypair */
        $ephKeypair = ParagonIE_Sodium_Compat::crypto_box_keypair();

        /** @var string $msgKeypair */
        $msgKeypair = ParagonIE_Sodium_Compat::crypto_box_keypair_from_secretkey_and_publickey(
            ParagonIE_Sodium_Compat::crypto_box_secretkey($ephKeypair),
            $publicKey
        );

        /** @var string $ephemeralPK */
        $ephemeralPK = ParagonIE_Sodium_Compat::crypto_box_publickey($ephKeypair);

        /** @var string $nonce */
        $nonce = ParagonIE_Sodium_Compat::crypto_generichash(
            $ephemeralPK . $publicKey,
            '',
            24
        );

        /** @var int $firstWrite */
        $firstWrite = fwrite(
            $ofp,
            $ephemeralPK,
            ParagonIE_Sodium_Compat::CRYPTO_BOX_PUBLICKEYBYTES
        );
        if (!is_int($firstWrite)) {
            fclose($ifp);
            fclose($ofp);
            ParagonIE_Sodium_Compat::memzero($ephKeypair);
            throw new SodiumException('Could not write to output file');
        }
        if ($firstWrite !== ParagonIE_Sodium_Compat::CRYPTO_BOX_PUBLICKEYBYTES) {
            ParagonIE_Sodium_Compat::memzero($ephKeypair);
            fclose($ifp);
            fclose($ofp);
            throw new SodiumException('Error writing public key to output file');
        }

        $res = self::box_encrypt($ifp, $ofp, $size, $nonce, $msgKeypair);
        fclose($ifp);
        fclose($ofp);
        try {
            ParagonIE_Sodium_Compat::memzero($nonce);
            ParagonIE_Sodium_Compat::memzero($ephKeypair);
        } catch (SodiumException $ex) {
            /** @psalm-suppress PossiblyUndefinedVariable */
            unset($ephKeypair);
        }
        return $res;
    }

    /**
     * Open a sealed file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_box_seal_open(), but produces
     * the same result.
     *
     * Warning: Does not protect against TOCTOU attacks. You should
     * just load the file into memory and use crypto_box_seal_open() if
     * you are worried about those.
     *
     * @param string $inputFile
     * @param string $outputFile
     * @param string $ecdhKeypair
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function box_seal_open(
        $inputFile,
        $outputFile,
        #[\SensitiveParameter]
        $ecdhKeypair
    ) {
        /* Type checks: */
        if (!is_string($inputFile)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($inputFile) . ' given.');
        }
        if (!is_string($outputFile)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($outputFile) . ' given.');
        }
        if (!is_string($ecdhKeypair)) {
            throw new TypeError('Argument 3 must be a string, ' . gettype($ecdhKeypair) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($ecdhKeypair) !== ParagonIE_Sodium_Compat::CRYPTO_BOX_KEYPAIRBYTES) {
            throw new TypeError('Argument 3 must be CRYPTO_BOX_KEYPAIRBYTES bytes');
        }

        $publicKey = ParagonIE_Sodium_Compat::crypto_box_publickey($ecdhKeypair);

        if (!file_exists($inputFile)) {
            throw new SodiumException('Input file does not exist');
        }
        /** @var int $size */
        $size = filesize($inputFile);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $ifp */
        $ifp = fopen($inputFile, 'rb');
        if (!is_resource($ifp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var resource $ofp */
        $ofp = @fopen($outputFile, 'wb');
        if (!is_resource($ofp)) {
            fclose($ifp);
            throw new SodiumException('Could not open output file for writing');
        }

        $ephemeralPK = fread($ifp, ParagonIE_Sodium_Compat::CRYPTO_BOX_PUBLICKEYBYTES);
        if (!is_string($ephemeralPK)) {
            throw new SodiumException('Could not read input file');
        }
        if (self::strlen($ephemeralPK) !== ParagonIE_Sodium_Compat::CRYPTO_BOX_PUBLICKEYBYTES) {
            fclose($ifp);
            fclose($ofp);
            throw new SodiumException('Could not read public key from sealed file');
        }

        $nonce = ParagonIE_Sodium_Compat::crypto_generichash(
            $ephemeralPK . $publicKey,
            '',
            24
        );
        $msgKeypair = ParagonIE_Sodium_Compat::crypto_box_keypair_from_secretkey_and_publickey(
            ParagonIE_Sodium_Compat::crypto_box_secretkey($ecdhKeypair),
            $ephemeralPK
        );

        $res = self::box_decrypt($ifp, $ofp, $size, $nonce, $msgKeypair);
        fclose($ifp);
        fclose($ofp);
        try {
            ParagonIE_Sodium_Compat::memzero($nonce);
            ParagonIE_Sodium_Compat::memzero($ephKeypair);
        } catch (SodiumException $ex) {
            if (isset($ephKeypair)) {
                unset($ephKeypair);
            }
        }
        return $res;
    }

    /**
     * Calculate the BLAKE2b hash of a file.
     *
     * @param string      $filePath     Absolute path to a file on the filesystem
     * @param string|null $key          BLAKE2b key
     * @param int         $outputLength Length of hash output
     *
     * @return string                   BLAKE2b hash
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress FailedTypeResolution
     */
    public static function generichash(
        $filePath,
        #[\SensitiveParameter]
        $key = '',
        $outputLength = 32
    ) {
        /* Type checks: */
        if (!is_string($filePath)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($filePath) . ' given.');
        }
        if (!is_string($key)) {
            if (is_null($key)) {
                $key = '';
            } else {
                throw new TypeError('Argument 2 must be a string, ' . gettype($key) . ' given.');
            }
        }
        if (!is_int($outputLength)) {
            if (!is_numeric($outputLength)) {
                throw new TypeError('Argument 3 must be an integer, ' . gettype($outputLength) . ' given.');
            }
            $outputLength = (int) $outputLength;
        }

        /* Input validation: */
        if (!empty($key)) {
            if (self::strlen($key) < ParagonIE_Sodium_Compat::CRYPTO_GENERICHASH_KEYBYTES_MIN) {
                throw new TypeError('Argument 2 must be at least CRYPTO_GENERICHASH_KEYBYTES_MIN bytes');
            }
            if (self::strlen($key) > ParagonIE_Sodium_Compat::CRYPTO_GENERICHASH_KEYBYTES_MAX) {
                throw new TypeError('Argument 2 must be at most CRYPTO_GENERICHASH_KEYBYTES_MAX bytes');
            }
        }
        if ($outputLength < ParagonIE_Sodium_Compat::CRYPTO_GENERICHASH_BYTES_MIN) {
            throw new SodiumException('Argument 3 must be at least CRYPTO_GENERICHASH_BYTES_MIN');
        }
        if ($outputLength > ParagonIE_Sodium_Compat::CRYPTO_GENERICHASH_BYTES_MAX) {
            throw new SodiumException('Argument 3 must be at least CRYPTO_GENERICHASH_BYTES_MAX');
        }

        if (!file_exists($filePath)) {
            throw new SodiumException('File does not exist');
        }
        /** @var int $size */
        $size = filesize($filePath);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $fp */
        $fp = fopen($filePath, 'rb');
        if (!is_resource($fp)) {
            throw new SodiumException('Could not open input file for reading');
        }
        $ctx = ParagonIE_Sodium_Compat::crypto_generichash_init($key, $outputLength);
        while ($size > 0) {
            $blockSize = $size > 64
                ? 64
                : $size;
            $read = fread($fp, $blockSize);
            if (!is_string($read)) {
                throw new SodiumException('Could not read input file');
            }
            ParagonIE_Sodium_Compat::crypto_generichash_update($ctx, $read);
            $size -= $blockSize;
        }

        fclose($fp);
        return ParagonIE_Sodium_Compat::crypto_generichash_final($ctx, $outputLength);
    }

    /**
     * Encrypt a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_secretbox(), but produces
     * the same result.
     *
     * @param string $inputFile  Absolute path to a file on the filesystem
     * @param string $outputFile Absolute path to a file on the filesystem
     * @param string $nonce      Number to be used only once
     * @param string $key        Encryption key
     *
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function secretbox(
        $inputFile,
        $outputFile,
        $nonce,
        #[\SensitiveParameter]
        $key
    ) {
        /* Type checks: */
        if (!is_string($inputFile)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($inputFile) . ' given..');
        }
        if (!is_string($outputFile)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($outputFile) . ' given.');
        }
        if (!is_string($nonce)) {
            throw new TypeError('Argument 3 must be a string, ' . gettype($nonce) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($nonce) !== ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new TypeError('Argument 3 must be CRYPTO_SECRETBOX_NONCEBYTES bytes');
        }
        if (!is_string($key)) {
            throw new TypeError('Argument 4 must be a string, ' . gettype($key) . ' given.');
        }
        if (self::strlen($key) !== ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_KEYBYTES) {
            throw new TypeError('Argument 4 must be CRYPTO_SECRETBOX_KEYBYTES bytes');
        }

        if (!file_exists($inputFile)) {
            throw new SodiumException('Input file does not exist');
        }
        /** @var int $size */
        $size = filesize($inputFile);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $ifp */
        $ifp = @fopen($inputFile, 'rb');
        if (!is_resource($ifp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var resource $ofp */
        $ofp = fopen($outputFile, 'wb');
        if (!is_resource($ofp)) {
            fclose($ifp);
            throw new SodiumException('Could not open output file for writing');
        }

        $res = self::secretbox_encrypt($ifp, $ofp, $size, $nonce, $key);
        fclose($ifp);
        fclose($ofp);
        return $res;
    }
    /**
     * Seal a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_secretbox_open(), but produces
     * the same result.
     *
     * Warning: Does not protect against TOCTOU attacks. You should
     * just load the file into memory and use crypto_secretbox_open() if
     * you are worried about those.
     *
     * @param string $inputFile
     * @param string $outputFile
     * @param string $nonce
     * @param string $key
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    public static function secretbox_open(
        $inputFile,
        $outputFile,
        $nonce,
        #[\SensitiveParameter]
        $key
    ) {
        /* Type checks: */
        if (!is_string($inputFile)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($inputFile) . ' given.');
        }
        if (!is_string($outputFile)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($outputFile) . ' given.');
        }
        if (!is_string($nonce)) {
            throw new TypeError('Argument 3 must be a string, ' . gettype($nonce) . ' given.');
        }
        if (!is_string($key)) {
            throw new TypeError('Argument 4 must be a string, ' . gettype($key) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($nonce) !== ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new TypeError('Argument 3 must be CRYPTO_SECRETBOX_NONCEBYTES bytes');
        }
        if (self::strlen($key) !== ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_KEYBYTES) {
            throw new TypeError('Argument 4 must be CRYPTO_SECRETBOX_KEYBYTES bytes');
        }

        if (!file_exists($inputFile)) {
            throw new SodiumException('Input file does not exist');
        }
        /** @var int $size */
        $size = filesize($inputFile);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $ifp */
        $ifp = fopen($inputFile, 'rb');
        if (!is_resource($ifp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var resource $ofp */
        $ofp = @fopen($outputFile, 'wb');
        if (!is_resource($ofp)) {
            fclose($ifp);
            throw new SodiumException('Could not open output file for writing');
        }

        $res = self::secretbox_decrypt($ifp, $ofp, $size, $nonce, $key);
        fclose($ifp);
        fclose($ofp);
        try {
            ParagonIE_Sodium_Compat::memzero($key);
        } catch (SodiumException $ex) {
            /** @psalm-suppress PossiblyUndefinedVariable */
            unset($key);
        }
        return $res;
    }

    /**
     * Sign a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_sign_detached(), but produces
     * the same result.
     *
     * @param string $filePath  Absolute path to a file on the filesystem
     * @param string $secretKey Secret signing key
     *
     * @return string           Ed25519 signature
     * @throws SodiumException
     * @throws TypeError
     */
    public static function sign(
        $filePath,
        #[\SensitiveParameter]
        $secretKey
    ) {
        /* Type checks: */
        if (!is_string($filePath)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($filePath) . ' given.');
        }
        if (!is_string($secretKey)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($secretKey) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($secretKey) !== ParagonIE_Sodium_Compat::CRYPTO_SIGN_SECRETKEYBYTES) {
            throw new TypeError('Argument 2 must be CRYPTO_SIGN_SECRETKEYBYTES bytes');
        }
        if (PHP_INT_SIZE === 4) {
            return self::sign_core32($filePath, $secretKey);
        }

        if (!file_exists($filePath)) {
            throw new SodiumException('File does not exist');
        }
        /** @var int $size */
        $size = filesize($filePath);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $fp */
        $fp = fopen($filePath, 'rb');
        if (!is_resource($fp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var string $az */
        $az = hash('sha512', self::substr($secretKey, 0, 32), true);

        $az[0] = self::intToChr(self::chrToInt($az[0]) & 248);
        $az[31] = self::intToChr((self::chrToInt($az[31]) & 63) | 64);

        $hs = hash_init('sha512');
        self::hash_update($hs, self::substr($az, 32, 32));
        /** @var resource $hs */
        $hs = self::updateHashWithFile($hs, $fp, $size);

        /** @var string $nonceHash */
        $nonceHash = hash_final($hs, true);

        /** @var string $pk */
        $pk = self::substr($secretKey, 32, 32);

        /** @var string $nonce */
        $nonce = ParagonIE_Sodium_Core_Ed25519::sc_reduce($nonceHash) . self::substr($nonceHash, 32);

        /** @var string $sig */
        $sig = ParagonIE_Sodium_Core_Ed25519::ge_p3_tobytes(
            ParagonIE_Sodium_Core_Ed25519::ge_scalarmult_base($nonce)
        );

        $hs = hash_init('sha512');
        self::hash_update($hs, self::substr($sig, 0, 32));
        self::hash_update($hs, self::substr($pk, 0, 32));
        /** @var resource $hs */
        $hs = self::updateHashWithFile($hs, $fp, $size);

        /** @var string $hramHash */
        $hramHash = hash_final($hs, true);

        /** @var string $hram */
        $hram = ParagonIE_Sodium_Core_Ed25519::sc_reduce($hramHash);

        /** @var string $sigAfter */
        $sigAfter = ParagonIE_Sodium_Core_Ed25519::sc_muladd($hram, $az, $nonce);

        /** @var string $sig */
        $sig = self::substr($sig, 0, 32) . self::substr($sigAfter, 0, 32);

        try {
            ParagonIE_Sodium_Compat::memzero($az);
        } catch (SodiumException $ex) {
            $az = null;
        }
        fclose($fp);
        return $sig;
    }

    /**
     * Verify a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_sign_verify_detached(), but
     * produces the same result.
     *
     * @param string $sig       Ed25519 signature
     * @param string $filePath  Absolute path to a file on the filesystem
     * @param string $publicKey Signing public key
     *
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     * @throws Exception
     */
    public static function verify(
        $sig,
        $filePath,
        $publicKey
    ) {
        /* Type checks: */
        if (!is_string($sig)) {
            throw new TypeError('Argument 1 must be a string, ' . gettype($sig) . ' given.');
        }
        if (!is_string($filePath)) {
            throw new TypeError('Argument 2 must be a string, ' . gettype($filePath) . ' given.');
        }
        if (!is_string($publicKey)) {
            throw new TypeError('Argument 3 must be a string, ' . gettype($publicKey) . ' given.');
        }

        /* Input validation: */
        if (self::strlen($sig) !== ParagonIE_Sodium_Compat::CRYPTO_SIGN_BYTES) {
            throw new TypeError('Argument 1 must be CRYPTO_SIGN_BYTES bytes');
        }
        if (self::strlen($publicKey) !== ParagonIE_Sodium_Compat::CRYPTO_SIGN_PUBLICKEYBYTES) {
            throw new TypeError('Argument 3 must be CRYPTO_SIGN_PUBLICKEYBYTES bytes');
        }
        if (self::strlen($sig) < 64) {
            throw new SodiumException('Signature is too short');
        }

        if (PHP_INT_SIZE === 4) {
            return self::verify_core32($sig, $filePath, $publicKey);
        }

        /* Security checks */
        if (
            (ParagonIE_Sodium_Core_Ed25519::chrToInt($sig[63]) & 240)
                &&
            ParagonIE_Sodium_Core_Ed25519::check_S_lt_L(self::substr($sig, 32, 32))
        ) {
            throw new SodiumException('S < L - Invalid signature');
        }
        if (ParagonIE_Sodium_Core_Ed25519::small_order($sig)) {
            throw new SodiumException('Signature is on too small of an order');
        }
        if ((self::chrToInt($sig[63]) & 224) !== 0) {
            throw new SodiumException('Invalid signature');
        }
        $d = 0;
        for ($i = 0; $i < 32; ++$i) {
            $d |= self::chrToInt($publicKey[$i]);
        }
        if ($d === 0) {
            throw new SodiumException('All zero public key');
        }

        if (!file_exists($filePath)) {
            throw new SodiumException('File does not exist');
        }
        /** @var int $size */
        $size = filesize($filePath);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        /** @var resource $fp */
        $fp = fopen($filePath, 'rb');
        if (!is_resource($fp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var bool The original value of ParagonIE_Sodium_Compat::$fastMult */
        $orig = ParagonIE_Sodium_Compat::$fastMult;

        // Set ParagonIE_Sodium_Compat::$fastMult to true to speed up verification.
        ParagonIE_Sodium_Compat::$fastMult = true;

        if (ParagonIE_Sodium_Core_Ed25519::small_order($publicKey)) {
            throw new SodiumException('Public key has small order');
        }
        /** @var ParagonIE_Sodium_Core_Curve25519_Ge_P3 $A */
        $A = ParagonIE_Sodium_Core_Ed25519::ge_frombytes_negate_vartime($publicKey);
        if (!ParagonIE_Sodium_Core_Ed25519::is_on_main_subgroup($A)) {
            throw new SodiumException('Public key is not on a member of the main subgroup');
        }

        $hs = hash_init('sha512');
        self::hash_update($hs, self::substr($sig, 0, 32));
        self::hash_update($hs, self::substr($publicKey, 0, 32));
        /** @var resource $hs */
        $hs = self::updateHashWithFile($hs, $fp, $size);
        /** @var string $hDigest */
        $hDigest = hash_final($hs, true);

        /** @var string $h */
        $h = ParagonIE_Sodium_Core_Ed25519::sc_reduce($hDigest) . self::substr($hDigest, 32);

        /** @var ParagonIE_Sodium_Core_Curve25519_Ge_P2 $R */
        $R = ParagonIE_Sodium_Core_Ed25519::ge_double_scalarmult_vartime(
            $h,
            $A,
            self::substr($sig, 32)
        );

        /** @var string $rcheck */
        $rcheck = ParagonIE_Sodium_Core_Ed25519::ge_tobytes($R);

        // Close the file handle
        fclose($fp);

        // Reset ParagonIE_Sodium_Compat::$fastMult to what it was before.
        ParagonIE_Sodium_Compat::$fastMult = $orig;
        return self::verify_32($rcheck, self::substr($sig, 0, 32));
    }

    /**
     * @param resource $ifp
     * @param resource $ofp
     * @param int      $mlen
     * @param string   $nonce
     * @param string   $boxKeypair
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function box_encrypt($ifp, $ofp, $mlen, $nonce, $boxKeypair)
    {
        if (PHP_INT_SIZE === 4) {
            return self::secretbox_encrypt(
                $ifp,
                $ofp,
                $mlen,
                $nonce,
                ParagonIE_Sodium_Crypto32::box_beforenm(
                    ParagonIE_Sodium_Crypto32::box_secretkey($boxKeypair),
                    ParagonIE_Sodium_Crypto32::box_publickey($boxKeypair)
                )
            );
        }
        return self::secretbox_encrypt(
            $ifp,
            $ofp,
            $mlen,
            $nonce,
            ParagonIE_Sodium_Crypto::box_beforenm(
                ParagonIE_Sodium_Crypto::box_secretkey($boxKeypair),
                ParagonIE_Sodium_Crypto::box_publickey($boxKeypair)
            )
        );
    }


    /**
     * @param resource $ifp
     * @param resource $ofp
     * @param int      $mlen
     * @param string   $nonce
     * @param string   $boxKeypair
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function box_decrypt($ifp, $ofp, $mlen, $nonce, $boxKeypair)
    {
        if (PHP_INT_SIZE === 4) {
            return self::secretbox_decrypt(
                $ifp,
                $ofp,
                $mlen,
                $nonce,
                ParagonIE_Sodium_Crypto32::box_beforenm(
                    ParagonIE_Sodium_Crypto32::box_secretkey($boxKeypair),
                    ParagonIE_Sodium_Crypto32::box_publickey($boxKeypair)
                )
            );
        }
        return self::secretbox_decrypt(
            $ifp,
            $ofp,
            $mlen,
            $nonce,
            ParagonIE_Sodium_Crypto::box_beforenm(
                ParagonIE_Sodium_Crypto::box_secretkey($boxKeypair),
                ParagonIE_Sodium_Crypto::box_publickey($boxKeypair)
            )
        );
    }

    /**
     * Encrypt a file
     *
     * @param resource $ifp
     * @param resource $ofp
     * @param int $mlen
     * @param string $nonce
     * @param string $key
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function secretbox_encrypt($ifp, $ofp, $mlen, $nonce, $key)
    {
        if (PHP_INT_SIZE === 4) {
            return self::secretbox_encrypt_core32($ifp, $ofp, $mlen, $nonce, $key);
        }

        $plaintext = fread($ifp, 32);
        if (!is_string($plaintext)) {
            throw new SodiumException('Could not read input file');
        }
        $first32 = self::ftell($ifp);

        /** @var string $subkey */
        $subkey = ParagonIE_Sodium_Core_HSalsa20::hsalsa20($nonce, $key);

        /** @var string $realNonce */
        $realNonce = ParagonIE_Sodium_Core_Util::substr($nonce, 16, 8);

        /** @var string $block0 */
        $block0 = str_repeat("\x00", 32);

        /** @var int $mlen - Length of the plaintext message */
        $mlen0 = $mlen;
        if ($mlen0 > 64 - ParagonIE_Sodium_Crypto::secretbox_xsalsa20poly1305_ZEROBYTES) {
            $mlen0 = 64 - ParagonIE_Sodium_Crypto::secretbox_xsalsa20poly1305_ZEROBYTES;
        }
        $block0 .= ParagonIE_Sodium_Core_Util::substr($plaintext, 0, $mlen0);

        /** @var string $block0 */
        $block0 = ParagonIE_Sodium_Core_Salsa20::salsa20_xor(
            $block0,
            $realNonce,
            $subkey
        );

        $state = new ParagonIE_Sodium_Core_Poly1305_State(
            ParagonIE_Sodium_Core_Util::substr(
                $block0,
                0,
                ParagonIE_Sodium_Crypto::onetimeauth_poly1305_KEYBYTES
            )
        );

        // Pre-write 16 blank bytes for the Poly1305 tag
        $start = self::ftell($ofp);
        fwrite($ofp, str_repeat("\x00", 16));

        /** @var string $c */
        $cBlock = ParagonIE_Sodium_Core_Util::substr(
            $block0,
            ParagonIE_Sodium_Crypto::secretbox_xsalsa20poly1305_ZEROBYTES
        );
        $state->update($cBlock);
        fwrite($ofp, $cBlock);
        $mlen -= 32;

        /** @var int $iter */
        $iter = 1;

        /** @var int $incr */
        $incr = self::BUFFER_SIZE >> 6;

        /*
         * Set the cursor to the end of the first half-block. All future bytes will
         * generated from salsa20_xor_ic, starting from 1 (second block).
         */
        fseek($ifp, $first32, SEEK_SET);

        while ($mlen > 0) {
            $blockSize = $mlen > self::BUFFER_SIZE
                ? self::BUFFER_SIZE
                : $mlen;
            $plaintext = fread($ifp, $blockSize);
            if (!is_string($plaintext)) {
                throw new SodiumException('Could not read input file');
            }
            $cBlock = ParagonIE_Sodium_Core_Salsa20::salsa20_xor_ic(
                $plaintext,
                $realNonce,
                $iter,
                $subkey
            );
            fwrite($ofp, $cBlock, $blockSize);
            $state->update($cBlock);

            $mlen -= $blockSize;
            $iter += $incr;
        }
        try {
            ParagonIE_Sodium_Compat::memzero($block0);
            ParagonIE_Sodium_Compat::memzero($subkey);
        } catch (SodiumException $ex) {
            $block0 = null;
            $subkey = null;
        }
        $end = self::ftell($ofp);

        /*
         * Write the Poly1305 authentication tag that provides integrity
         * over the ciphertext (encrypt-then-MAC)
         */
        fseek($ofp, $start, SEEK_SET);
        fwrite($ofp, $state->finish(), ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_MACBYTES);
        fseek($ofp, $end, SEEK_SET);
        unset($state);

        return true;
    }

    /**
     * Decrypt a file
     *
     * @param resource $ifp
     * @param resource $ofp
     * @param int $mlen
     * @param string $nonce
     * @param string $key
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function secretbox_decrypt($ifp, $ofp, $mlen, $nonce, $key)
    {
        if (PHP_INT_SIZE === 4) {
            return self::secretbox_decrypt_core32($ifp, $ofp, $mlen, $nonce, $key);
        }
        $tag = fread($ifp, 16);
        if (!is_string($tag)) {
            throw new SodiumException('Could not read input file');
        }

        /** @var string $subkey */
        $subkey = ParagonIE_Sodium_Core_HSalsa20::hsalsa20($nonce, $key);

        /** @var string $realNonce */
        $realNonce = ParagonIE_Sodium_Core_Util::substr($nonce, 16, 8);

        /** @var string $block0 */
        $block0 = ParagonIE_Sodium_Core_Salsa20::salsa20(
            64,
            ParagonIE_Sodium_Core_Util::substr($nonce, 16, 8),
            $subkey
        );

        /* Verify the Poly1305 MAC -before- attempting to decrypt! */
        $state = new ParagonIE_Sodium_Core_Poly1305_State(self::substr($block0, 0, 32));
        if (!self::onetimeauth_verify($state, $ifp, $tag, $mlen)) {
            throw new SodiumException('Invalid MAC');
        }

        /*
         * Set the cursor to the end of the first half-block. All future bytes will
         * generated from salsa20_xor_ic, starting from 1 (second block).
         */
        $first32 = fread($ifp, 32);
        if (!is_string($first32)) {
            throw new SodiumException('Could not read input file');
        }
        $first32len = self::strlen($first32);
        fwrite(
            $ofp,
            self::xorStrings(
                self::substr($block0, 32, $first32len),
                self::substr($first32, 0, $first32len)
            )
        );
        $mlen -= 32;

        /** @var int $iter */
        $iter = 1;

        /** @var int $incr */
        $incr = self::BUFFER_SIZE >> 6;

        /* Decrypts ciphertext, writes to output file. */
        while ($mlen > 0) {
            $blockSize = $mlen > self::BUFFER_SIZE
                ? self::BUFFER_SIZE
                : $mlen;
            $ciphertext = fread($ifp, $blockSize);
            if (!is_string($ciphertext)) {
                throw new SodiumException('Could not read input file');
            }
            $pBlock = ParagonIE_Sodium_Core_Salsa20::salsa20_xor_ic(
                $ciphertext,
                $realNonce,
                $iter,
                $subkey
            );
            fwrite($ofp, $pBlock, $blockSize);
            $mlen -= $blockSize;
            $iter += $incr;
        }
        return true;
    }

    /**
     * @param ParagonIE_Sodium_Core_Poly1305_State $state
     * @param resource $ifp
     * @param string $tag
     * @param int $mlen
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function onetimeauth_verify(
        ParagonIE_Sodium_Core_Poly1305_State $state,
        $ifp,
        $tag = '',
        $mlen = 0
    ) {
        /** @var int $pos */
        $pos = self::ftell($ifp);

        /** @var int $iter */
        $iter = 1;

        /** @var int $incr */
        $incr = self::BUFFER_SIZE >> 6;

        while ($mlen > 0) {
            $blockSize = $mlen > self::BUFFER_SIZE
                ? self::BUFFER_SIZE
                : $mlen;
            $ciphertext = fread($ifp, $blockSize);
            if (!is_string($ciphertext)) {
                throw new SodiumException('Could not read input file');
            }
            $state->update($ciphertext);
            $mlen -= $blockSize;
            $iter += $incr;
        }
        $res = ParagonIE_Sodium_Core_Util::verify_16($tag, $state->finish());

        fseek($ifp, $pos, SEEK_SET);
        return $res;
    }

    /**
     * Update a hash context with the contents of a file, without
     * loading the entire file into memory.
     *
     * @param resource|HashContext $hash
     * @param resource $fp
     * @param int $size
     * @return resource|object Resource on PHP < 7.2, HashContext object on PHP >= 7.2
     * @throws SodiumException
     * @throws TypeError
     * @psalm-suppress PossiblyInvalidArgument
     *                 PHP 7.2 changes from a resource to an object,
     *                 which causes Psalm to complain about an error.
     * @psalm-suppress TypeCoercion
     *                 Ditto.
     */
    public static function updateHashWithFile($hash, $fp, $size = 0)
    {
        /* Type checks: */
        if (PHP_VERSION_ID < 70200) {
            if (!is_resource($hash)) {
                throw new TypeError('Argument 1 must be a resource, ' . gettype($hash) . ' given.');
            }
        } else {
            if (!is_object($hash)) {
                throw new TypeError('Argument 1 must be an object (PHP 7.2+), ' . gettype($hash) . ' given.');
            }
        }

        if (!is_resource($fp)) {
            throw new TypeError('Argument 2 must be a resource, ' . gettype($fp) . ' given.');
        }
        if (!is_int($size)) {
            throw new TypeError('Argument 3 must be an integer, ' . gettype($size) . ' given.');
        }

        /** @var int $originalPosition */
        $originalPosition = self::ftell($fp);

        // Move file pointer to beginning of file
        fseek($fp, 0, SEEK_SET);
        for ($i = 0; $i < $size; $i += self::BUFFER_SIZE) {
            /** @var string|bool $message */
            $message = fread(
                $fp,
                ($size - $i) > self::BUFFER_SIZE
                    ? $size - $i
                    : self::BUFFER_SIZE
            );
            if (!is_string($message)) {
                throw new SodiumException('Unexpected error reading from file.');
            }
            /** @var string $message */
            /** @psalm-suppress InvalidArgument */
            self::hash_update($hash, $message);
        }
        // Reset file pointer's position
        fseek($fp, $originalPosition, SEEK_SET);
        return $hash;
    }

    /**
     * Sign a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_sign_detached(), but produces
     * the same result. (32-bit)
     *
     * @param string $filePath  Absolute path to a file on the filesystem
     * @param string $secretKey Secret signing key
     *
     * @return string           Ed25519 signature
     * @throws SodiumException
     * @throws TypeError
     */
    private static function sign_core32($filePath, $secretKey)
    {
        $size = filesize($filePath);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }

        $fp = fopen($filePath, 'rb');
        if (!is_resource($fp)) {
            throw new SodiumException('Could not open input file for reading');
        }

        /** @var string $az */
        $az = hash('sha512', self::substr($secretKey, 0, 32), true);

        $az[0] = self::intToChr(self::chrToInt($az[0]) & 248);
        $az[31] = self::intToChr((self::chrToInt($az[31]) & 63) | 64);

        $hs = hash_init('sha512');
        self::hash_update($hs, self::substr($az, 32, 32));
        /** @var resource $hs */
        $hs = self::updateHashWithFile($hs, $fp, $size);

        $nonceHash = hash_final($hs, true);
        $pk = self::substr($secretKey, 32, 32);
        $nonce = ParagonIE_Sodium_Core32_Ed25519::sc_reduce($nonceHash) . self::substr($nonceHash, 32);
        $sig = ParagonIE_Sodium_Core32_Ed25519::ge_p3_tobytes(
            ParagonIE_Sodium_Core32_Ed25519::ge_scalarmult_base($nonce)
        );

        $hs = hash_init('sha512');
        self::hash_update($hs, self::substr($sig, 0, 32));
        self::hash_update($hs, self::substr($pk, 0, 32));
        /** @var resource $hs */
        $hs = self::updateHashWithFile($hs, $fp, $size);

        $hramHash = hash_final($hs, true);

        $hram = ParagonIE_Sodium_Core32_Ed25519::sc_reduce($hramHash);

        $sigAfter = ParagonIE_Sodium_Core32_Ed25519::sc_muladd($hram, $az, $nonce);

        /** @var string $sig */
        $sig = self::substr($sig, 0, 32) . self::substr($sigAfter, 0, 32);

        try {
            ParagonIE_Sodium_Compat::memzero($az);
        } catch (SodiumException $ex) {
            $az = null;
        }
        fclose($fp);
        return $sig;
    }

    /**
     *
     * Verify a file (rather than a string). Uses less memory than
     * ParagonIE_Sodium_Compat::crypto_sign_verify_detached(), but
     * produces the same result. (32-bit)
     *
     * @param string $sig       Ed25519 signature
     * @param string $filePath  Absolute path to a file on the filesystem
     * @param string $publicKey Signing public key
     *
     * @return bool
     * @throws SodiumException
     * @throws Exception
     */
    public static function verify_core32($sig, $filePath, $publicKey)
    {
        /* Security checks */
        if (ParagonIE_Sodium_Core32_Ed25519::check_S_lt_L(self::substr($sig, 32, 32))) {
            throw new SodiumException('S < L - Invalid signature');
        }
        if (ParagonIE_Sodium_Core32_Ed25519::small_order($sig)) {
            throw new SodiumException('Signature is on too small of an order');
        }

        if ((self::chrToInt($sig[63]) & 224) !== 0) {
            throw new SodiumException('Invalid signature');
        }
        $d = 0;
        for ($i = 0; $i < 32; ++$i) {
            $d |= self::chrToInt($publicKey[$i]);
        }
        if ($d === 0) {
            throw new SodiumException('All zero public key');
        }

        /** @var int|bool $size */
        $size = filesize($filePath);
        if (!is_int($size)) {
            throw new SodiumException('Could not obtain the file size');
        }
        /** @var int $size */

        /** @var resource|bool $fp */
        $fp = fopen($filePath, 'rb');
        if (!is_resource($fp)) {
            throw new SodiumException('Could not open input file for reading');
        }
        /** @var resource $fp */

        /** @var bool The original value of ParagonIE_Sodium_Compat::$fastMult */
        $orig = ParagonIE_Sodium_Compat::$fastMult;

        // Set ParagonIE_Sodium_Compat::$fastMult to true to speed up verification.
        ParagonIE_Sodium_Compat::$fastMult = true;

        /** @var ParagonIE_Sodium_Core32_Curve25519_Ge_P3 $A */
        $A = ParagonIE_Sodium_Core32_Ed25519::ge_frombytes_negate_vartime($publicKey);

        $hs = hash_init('sha512');
        self::hash_update($hs, self::substr($sig, 0, 32));
        self::hash_update($hs, self::substr($publicKey, 0, 32));
        /** @var resource $hs */
        $hs = self::updateHashWithFile($hs, $fp, $size);
        /** @var string $hDigest */
        $hDigest = hash_final($hs, true);

        /** @var string $h */
        $h = ParagonIE_Sodium_Core32_Ed25519::sc_reduce($hDigest) . self::substr($hDigest, 32);

        /** @var ParagonIE_Sodium_Core32_Curve25519_Ge_P2 $R */
        $R = ParagonIE_Sodium_Core32_Ed25519::ge_double_scalarmult_vartime(
            $h,
            $A,
            self::substr($sig, 32)
        );

        /** @var string $rcheck */
        $rcheck = ParagonIE_Sodium_Core32_Ed25519::ge_tobytes($R);

        // Close the file handle
        fclose($fp);

        // Reset ParagonIE_Sodium_Compat::$fastMult to what it was before.
        ParagonIE_Sodium_Compat::$fastMult = $orig;
        return self::verify_32($rcheck, self::substr($sig, 0, 32));
    }

    /**
     * Encrypt a file (32-bit)
     *
     * @param resource $ifp
     * @param resource $ofp
     * @param int $mlen
     * @param string $nonce
     * @param string $key
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function secretbox_encrypt_core32($ifp, $ofp, $mlen, $nonce, $key)
    {
        $plaintext = fread($ifp, 32);
        if (!is_string($plaintext)) {
            throw new SodiumException('Could not read input file');
        }
        $first32 = self::ftell($ifp);

        /** @var string $subkey */
        $subkey = ParagonIE_Sodium_Core32_HSalsa20::hsalsa20($nonce, $key);

        /** @var string $realNonce */
        $realNonce = ParagonIE_Sodium_Core32_Util::substr($nonce, 16, 8);

        /** @var string $block0 */
        $block0 = str_repeat("\x00", 32);

        /** @var int $mlen - Length of the plaintext message */
        $mlen0 = $mlen;
        if ($mlen0 > 64 - ParagonIE_Sodium_Crypto::secretbox_xsalsa20poly1305_ZEROBYTES) {
            $mlen0 = 64 - ParagonIE_Sodium_Crypto::secretbox_xsalsa20poly1305_ZEROBYTES;
        }
        $block0 .= ParagonIE_Sodium_Core32_Util::substr($plaintext, 0, $mlen0);

        /** @var string $block0 */
        $block0 = ParagonIE_Sodium_Core32_Salsa20::salsa20_xor(
            $block0,
            $realNonce,
            $subkey
        );

        $state = new ParagonIE_Sodium_Core32_Poly1305_State(
            ParagonIE_Sodium_Core32_Util::substr(
                $block0,
                0,
                ParagonIE_Sodium_Crypto::onetimeauth_poly1305_KEYBYTES
            )
        );

        // Pre-write 16 blank bytes for the Poly1305 tag
        $start = self::ftell($ofp);
        fwrite($ofp, str_repeat("\x00", 16));

        /** @var string $c */
        $cBlock = ParagonIE_Sodium_Core32_Util::substr(
            $block0,
            ParagonIE_Sodium_Crypto::secretbox_xsalsa20poly1305_ZEROBYTES
        );
        $state->update($cBlock);
        fwrite($ofp, $cBlock);
        $mlen -= 32;

        /** @var int $iter */
        $iter = 1;

        /** @var int $incr */
        $incr = self::BUFFER_SIZE >> 6;

        /*
         * Set the cursor to the end of the first half-block. All future bytes will
         * generated from salsa20_xor_ic, starting from 1 (second block).
         */
        fseek($ifp, $first32, SEEK_SET);

        while ($mlen > 0) {
            $blockSize = $mlen > self::BUFFER_SIZE
                ? self::BUFFER_SIZE
                : $mlen;
            $plaintext = fread($ifp, $blockSize);
            if (!is_string($plaintext)) {
                throw new SodiumException('Could not read input file');
            }
            $cBlock = ParagonIE_Sodium_Core32_Salsa20::salsa20_xor_ic(
                $plaintext,
                $realNonce,
                $iter,
                $subkey
            );
            fwrite($ofp, $cBlock, $blockSize);
            $state->update($cBlock);

            $mlen -= $blockSize;
            $iter += $incr;
        }
        try {
            ParagonIE_Sodium_Compat::memzero($block0);
            ParagonIE_Sodium_Compat::memzero($subkey);
        } catch (SodiumException $ex) {
            $block0 = null;
            $subkey = null;
        }
        $end = self::ftell($ofp);

        /*
         * Write the Poly1305 authentication tag that provides integrity
         * over the ciphertext (encrypt-then-MAC)
         */
        fseek($ofp, $start, SEEK_SET);
        fwrite($ofp, $state->finish(), ParagonIE_Sodium_Compat::CRYPTO_SECRETBOX_MACBYTES);
        fseek($ofp, $end, SEEK_SET);
        unset($state);

        return true;
    }

    /**
     * Decrypt a file (32-bit)
     *
     * @param resource $ifp
     * @param resource $ofp
     * @param int $mlen
     * @param string $nonce
     * @param string $key
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function secretbox_decrypt_core32($ifp, $ofp, $mlen, $nonce, $key)
    {
        $tag = fread($ifp, 16);
        if (!is_string($tag)) {
            throw new SodiumException('Could not read input file');
        }

        /** @var string $subkey */
        $subkey = ParagonIE_Sodium_Core32_HSalsa20::hsalsa20($nonce, $key);

        /** @var string $realNonce */
        $realNonce = ParagonIE_Sodium_Core32_Util::substr($nonce, 16, 8);

        /** @var string $block0 */
        $block0 = ParagonIE_Sodium_Core32_Salsa20::salsa20(
            64,
            ParagonIE_Sodium_Core32_Util::substr($nonce, 16, 8),
            $subkey
        );

        /* Verify the Poly1305 MAC -before- attempting to decrypt! */
        $state = new ParagonIE_Sodium_Core32_Poly1305_State(self::substr($block0, 0, 32));
        if (!self::onetimeauth_verify_core32($state, $ifp, $tag, $mlen)) {
            throw new SodiumException('Invalid MAC');
        }

        /*
         * Set the cursor to the end of the first half-block. All future bytes will
         * generated from salsa20_xor_ic, starting from 1 (second block).
         */
        $first32 = fread($ifp, 32);
        if (!is_string($first32)) {
            throw new SodiumException('Could not read input file');
        }
        $first32len = self::strlen($first32);
        fwrite(
            $ofp,
            self::xorStrings(
                self::substr($block0, 32, $first32len),
                self::substr($first32, 0, $first32len)
            )
        );
        $mlen -= 32;

        /** @var int $iter */
        $iter = 1;

        /** @var int $incr */
        $incr = self::BUFFER_SIZE >> 6;

        /* Decrypts ciphertext, writes to output file. */
        while ($mlen > 0) {
            $blockSize = $mlen > self::BUFFER_SIZE
                ? self::BUFFER_SIZE
                : $mlen;
            $ciphertext = fread($ifp, $blockSize);
            if (!is_string($ciphertext)) {
                throw new SodiumException('Could not read input file');
            }
            $pBlock = ParagonIE_Sodium_Core32_Salsa20::salsa20_xor_ic(
                $ciphertext,
                $realNonce,
                $iter,
                $subkey
            );
            fwrite($ofp, $pBlock, $blockSize);
            $mlen -= $blockSize;
            $iter += $incr;
        }
        return true;
    }

    /**
     * One-time message authentication for 32-bit systems
     *
     * @param ParagonIE_Sodium_Core32_Poly1305_State $state
     * @param resource $ifp
     * @param string $tag
     * @param int $mlen
     * @return bool
     * @throws SodiumException
     * @throws TypeError
     */
    protected static function onetimeauth_verify_core32(
        ParagonIE_Sodium_Core32_Poly1305_State $state,
        $ifp,
        $tag = '',
        $mlen = 0
    ) {
        /** @var int $pos */
        $pos = self::ftell($ifp);

        while ($mlen > 0) {
            $blockSize = $mlen > self::BUFFER_SIZE
                ? self::BUFFER_SIZE
                : $mlen;
            $ciphertext = fread($ifp, $blockSize);
            if (!is_string($ciphertext)) {
                throw new SodiumException('Could not read input file');
            }
            $state->update($ciphertext);
            $mlen -= $blockSize;
        }
        $res = ParagonIE_Sodium_Core32_Util::verify_16($tag, $state->finish());

        fseek($ifp, $pos, SEEK_SET);
        return $res;
    }

    /**
     * @param resource $resource
     * @return int
     * @throws SodiumException
     */
    private static function ftell($resource)
    {
        $return = ftell($resource);
        if (!is_int($return)) {
            throw new SodiumException('ftell() returned false');
        }
        return (int) $return;
    }
}
