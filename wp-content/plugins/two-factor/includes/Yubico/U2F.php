<?php
/* Copyright (c) 2014 Yubico AB
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above
 *     copyright notice, this list of conditions and the following
 *     disclaimer in the documentation and/or other materials provided
 *     with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace u2flib_server;

/** Constant for the version of the u2f protocol */
const U2F_VERSION = "U2F_V2";

/** Error for the authentication message not matching any outstanding
 * authentication request */
const ERR_NO_MATCHING_REQUEST = 1;

/** Error for the authentication message not matching any registration */
const ERR_NO_MATCHING_REGISTRATION = 2;

/** Error for the signature on the authentication message not verifying with
 * the correct key */
const ERR_AUTHENTICATION_FAILURE = 3;

/** Error for the challenge in the registration message not matching the
 * registration challenge */
const ERR_UNMATCHED_CHALLENGE = 4;

/** Error for the attestation signature on the registration message not
 * verifying */
const ERR_ATTESTATION_SIGNATURE = 5;

/** Error for the attestation verification not verifying */
const ERR_ATTESTATION_VERIFICATION = 6;

/** Error for not getting good random from the system */
const ERR_BAD_RANDOM = 7;

/** Error when the counter is lower than expected */
const ERR_COUNTER_TOO_LOW = 8;

/** Error decoding public key */
const ERR_PUBKEY_DECODE = 9;

/** Error user-agent returned error */
const ERR_BAD_UA_RETURNING = 10;

/** Error old OpenSSL version */
const ERR_OLD_OPENSSL = 11;

/** @internal */
const PUBKEY_LEN = 65;

class U2F
{
    /** @var string  */
    private $appId;

    /** @var null|string */
    private $attestDir;

    /** @internal */
    private $FIXCERTS = array(
        '349bca1031f8c82c4ceca38b9cebf1a69df9fb3b94eed99eb3fb9aa3822d26e8',
        'dd574527df608e47ae45fbba75a2afdd5c20fd94a02419381813cd55a2a3398f',
        '1d8764f0f7cd1352df6150045c8f638e517270e8b5dda1c63ade9c2280240cae',
        'd0edc9a91a1677435a953390865d208c55b3183c6759c9b5a7ff494c322558eb',
        '6073c436dcd064a48127ddbf6032ac1a66fd59a0c24434f070d4e564c124c897',
        'ca993121846c464d666096d35f13bf44c1b05af205f9b4a1e00cf6cc10c5e511'
    );

    /**
     * @param string $appId Application id for the running application
     * @param string|null $attestDir Directory where trusted attestation roots may be found
     * @throws Error If OpenSSL older than 1.0.0 is used
     */
    public function __construct($appId, $attestDir = null)
    {
        if(OPENSSL_VERSION_NUMBER < 0x10000000) {
            throw new Error('OpenSSL has to be at least version 1.0.0, this is ' . OPENSSL_VERSION_TEXT, ERR_OLD_OPENSSL);
        }
        $this->appId = $appId;
        $this->attestDir = $attestDir;
    }

    /**
     * Called to get a registration request to send to a user.
     * Returns an array of one registration request and a array of sign requests.
     *
     * @param array $registrations List of current registrations for this
     * user, to prevent the user from registering the same authenticator several
     * times.
     * @return array An array of two elements, the first containing a
     * RegisterRequest the second being an array of SignRequest
     * @throws Error
     */
    public function getRegisterData(array $registrations = array())
    {
        $challenge = $this->createChallenge();
        $request = new RegisterRequest($challenge, $this->appId);
        $signs = $this->getAuthenticateData($registrations);
        return array($request, $signs);
    }

    /**
     * Called to verify and unpack a registration message.
     *
     * @param RegisterRequest $request this is a reply to
     * @param object $response response from a user
     * @param bool $includeCert set to true if the attestation certificate should be
     * included in the returned Registration object
     * @return Registration
     * @throws Error
     */
    public function doRegister($request, $response, $includeCert = true)
    {
        if( !is_object( $request ) ) {
            throw new \InvalidArgumentException('$request of doRegister() method only accepts object.');
        }

        if( !is_object( $response ) ) {
            throw new \InvalidArgumentException('$response of doRegister() method only accepts object.');
        }

        if( property_exists( $response, 'errorCode') && $response->errorCode !== 0 ) {
            throw new Error('User-agent returned error. Error code: ' . $response->errorCode, ERR_BAD_UA_RETURNING );
        }

        if( !is_bool( $includeCert ) ) {
            throw new \InvalidArgumentException('$include_cert of doRegister() method only accepts boolean.');
        }

        $rawReg = $this->base64u_decode($response->registrationData);
        $regData = array_values(unpack('C*', $rawReg));
        $clientData = $this->base64u_decode($response->clientData);
        $cli = json_decode($clientData);

        if($cli->challenge !== $request->challenge) {
            throw new Error('Registration challenge does not match', ERR_UNMATCHED_CHALLENGE );
        }

        $registration = new Registration();
        $offs = 1;
        $pubKey = substr($rawReg, $offs, PUBKEY_LEN);
        $offs += PUBKEY_LEN;
        // Decode the pubKey to make sure it's good.
        $tmpKey = $this->pubkey_to_pem($pubKey);
        if($tmpKey === null) {
            throw new Error('Decoding of public key failed', ERR_PUBKEY_DECODE );
        }
        $registration->publicKey = base64_encode($pubKey);
        $khLen = $regData[$offs++];
        $kh = substr($rawReg, $offs, $khLen);
        $offs += $khLen;
        $registration->keyHandle = $this->base64u_encode($kh);

        // length of certificate is stored in byte 3 and 4 (excluding the first 4 bytes).
        $certLen = 4;
        $certLen += ($regData[$offs + 2] << 8);
        $certLen += $regData[$offs + 3];

        $rawCert = $this->fixSignatureUnusedBits(substr($rawReg, $offs, $certLen));
        $offs += $certLen;
        $pemCert  = "-----BEGIN CERTIFICATE-----\r\n";
        $pemCert .= chunk_split(base64_encode($rawCert), 64);
        $pemCert .= "-----END CERTIFICATE-----";
        if($includeCert) {
            $registration->certificate = base64_encode($rawCert);
        }
        if($this->attestDir) {
            if(openssl_x509_checkpurpose($pemCert, -1, $this->get_certs()) !== true) {
                throw new Error('Attestation certificate can not be validated', ERR_ATTESTATION_VERIFICATION );
            }
        }

        if(!openssl_pkey_get_public($pemCert)) {
            throw new Error('Decoding of public key failed', ERR_PUBKEY_DECODE );
        }
        $signature = substr($rawReg, $offs);

        $dataToVerify  = chr(0);
        $dataToVerify .= hash('sha256', $request->appId, true);
        $dataToVerify .= hash('sha256', $clientData, true);
        $dataToVerify .= $kh;
        $dataToVerify .= $pubKey;

        if(openssl_verify($dataToVerify, $signature, $pemCert, 'sha256') === 1) {
            return $registration;
        } else {
            throw new Error('Attestation signature does not match', ERR_ATTESTATION_SIGNATURE );
        }
    }

    /**
     * Called to get an authentication request.
     *
     * @param array $registrations An array of the registrations to create authentication requests for.
     * @return array An array of SignRequest
     * @throws Error
     */
    public function getAuthenticateData(array $registrations)
    {
        $sigs = array();
        $challenge = $this->createChallenge();
        foreach ($registrations as $reg) {
            if( !is_object( $reg ) ) {
                throw new \InvalidArgumentException('$registrations of getAuthenticateData() method only accepts array of object.');
            }

            $sig = new SignRequest();
            $sig->appId = $this->appId;
            $sig->keyHandle = $reg->keyHandle;
            $sig->challenge = $challenge;
            $sigs[] = $sig;
        }
        return $sigs;
    }

    /**
     * Called to verify an authentication response
     *
     * @param array $requests An array of outstanding authentication requests
     * @param array $registrations An array of current registrations
     * @param object $response A response from the authenticator
     * @return Registration
     * @throws Error
     *
     * The Registration object returned on success contains an updated counter
     * that should be saved for future authentications.
     * If the Error returned is ERR_COUNTER_TOO_LOW this is an indication of
     * token cloning or similar and appropriate action should be taken.
     */
    public function doAuthenticate(array $requests, array $registrations, $response)
    {
        if( !is_object( $response ) ) {
            throw new \InvalidArgumentException('$response of doAuthenticate() method only accepts object.');
        }

        if( property_exists( $response, 'errorCode') && $response->errorCode !== 0 ) {
            throw new Error('User-agent returned error. Error code: ' . $response->errorCode, ERR_BAD_UA_RETURNING );
        }

        /** @var object|null $req */
        $req = null;

        /** @var object|null $reg */
        $reg = null;

        $clientData = $this->base64u_decode($response->clientData);
        $decodedClient = json_decode($clientData);
        foreach ($requests as $req) {
            if( !is_object( $req ) ) {
                throw new \InvalidArgumentException('$requests of doAuthenticate() method only accepts array of object.');
            }

            if($req->keyHandle === $response->keyHandle && $req->challenge === $decodedClient->challenge) {
                break;
            }

            $req = null;
        }
        if($req === null) {
            throw new Error('No matching request found', ERR_NO_MATCHING_REQUEST );
        }
        foreach ($registrations as $reg) {
            if( !is_object( $reg ) ) {
                throw new \InvalidArgumentException('$registrations of doAuthenticate() method only accepts array of object.');
            }

            if($reg->keyHandle === $response->keyHandle) {
                break;
            }
            $reg = null;
        }
        if($reg === null) {
            throw new Error('No matching registration found', ERR_NO_MATCHING_REGISTRATION );
        }
        $pemKey = $this->pubkey_to_pem($this->base64u_decode($reg->publicKey));
        if($pemKey === null) {
            throw new Error('Decoding of public key failed', ERR_PUBKEY_DECODE );
        }

        $signData = $this->base64u_decode($response->signatureData);
        $dataToVerify  = hash('sha256', $req->appId, true);
        $dataToVerify .= substr($signData, 0, 5);
        $dataToVerify .= hash('sha256', $clientData, true);
        $signature = substr($signData, 5);

        if(openssl_verify($dataToVerify, $signature, $pemKey, 'sha256') === 1) {
            $ctr = unpack("Nctr", substr($signData, 1, 4));
            $counter = $ctr['ctr'];
            /* TODO: wrap-around should be handled somehow.. */
            if($counter > $reg->counter) {
                $reg->counter = $counter;
                return $reg;
            } else {
                throw new Error('Counter too low.', ERR_COUNTER_TOO_LOW );
            }
        } else {
            throw new Error('Authentication failed', ERR_AUTHENTICATION_FAILURE );
        }
    }

    /**
     * @return array
     */
    private function get_certs()
    {
        $files = array();
        $dir = $this->attestDir;
        if($dir && $handle = opendir($dir)) {
            while(false !== ($entry = readdir($handle))) {
                if(is_file("$dir/$entry")) {
                    $files[] = "$dir/$entry";
                }
            }
            closedir($handle);
        }
        return $files;
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64u_encode($data)
    {
        return trim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @param string $data
     * @return string
     */
    private function base64u_decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * @param string $key
     * @return null|string
     */
    private function pubkey_to_pem($key)
    {
        if(strlen($key) !== PUBKEY_LEN || $key[0] !== "\x04") {
            return null;
        }

        /*
         * Convert the public key to binary DER format first
         * Using the ECC SubjectPublicKeyInfo OIDs from RFC 5480
         *
         *  SEQUENCE(2 elem)                        30 59
         *   SEQUENCE(2 elem)                       30 13
         *    OID1.2.840.10045.2.1 (id-ecPublicKey) 06 07 2a 86 48 ce 3d 02 01
         *    OID1.2.840.10045.3.1.7 (secp256r1)    06 08 2a 86 48 ce 3d 03 01 07
         *   BIT STRING(520 bit)                    03 42 ..key..
         */
        $der  = "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01";
        $der .= "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42";
        $der .= "\0".$key;

        $pem  = "-----BEGIN PUBLIC KEY-----\r\n";
        $pem .= chunk_split(base64_encode($der), 64);
        $pem .= "-----END PUBLIC KEY-----";

        return $pem;
    }

    /**
     * @return string
     * @throws Error
     */
    private function createChallenge()
    {
        $challenge = openssl_random_pseudo_bytes(32, $crypto_strong );
        if( $crypto_strong !== true ) {
            throw new Error('Unable to obtain a good source of randomness', ERR_BAD_RANDOM);
        }

        $challenge = $this->base64u_encode( $challenge );

        return $challenge;
    }

    /**
     * Fixes a certificate where the signature contains unused bits.
     *
     * @param string $cert
     * @return mixed
     */
    private function fixSignatureUnusedBits($cert)
    {
        if(in_array(hash('sha256', $cert), $this->FIXCERTS)) {
            $cert[strlen($cert) - 257] = "\0";
        }
        return $cert;
    }
}

/**
 * Class for building a registration request
 *
 * @package u2flib_server
 */
class RegisterRequest
{
    /** Protocol version */
    public $version = U2F_VERSION;

    /** Registration challenge */
    public $challenge;

    /** Application id */
    public $appId;

    /**
     * @param string $challenge
     * @param string $appId
     * @internal
     */
    public function __construct($challenge, $appId)
    {
        $this->challenge = $challenge;
        $this->appId = $appId;
    }
}

/**
 * Class for building up an authentication request
 *
 * @package u2flib_server
 */
class SignRequest
{
    /** Protocol version */
    public $version = U2F_VERSION;

    /** Authentication challenge */
    public $challenge;

    /** Key handle of a registered authenticator */
    public $keyHandle;

    /** Application id */
    public $appId;
}

/**
 * Class returned for successful registrations
 *
 * @package u2flib_server
 */
class Registration
{
    /** The key handle of the registered authenticator */
    public $keyHandle;

    /** The public key of the registered authenticator */
    public $publicKey;

    /** The attestation certificate of the registered authenticator */
    public $certificate;

    /** The counter associated with this registration */
    public $counter = -1;
}

/**
 * Error class, returned on errors
 *
 * @package u2flib_server
 */
class Error extends \Exception
{
    /**
     * Override constructor and make message and code mandatory
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($message, $code, ?\Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
