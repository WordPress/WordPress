<?php
class wfCrypt {
	private static function getPubKey(){
		#Command to generate our keypair was: openssl req -x509 -newkey rsa:2048 -keyout mycert.key -out mycert.pem -nodes -subj "/C=US/ST=Washington/L=Seattle/O=Wordfence/OU=IT/CN=wordfence.com" -days 7300
		#This is a 2048 bit key using SHA256 with RSA. 
		$key = <<<ENDKEY
-----BEGIN CERTIFICATE-----
MIIDrTCCApWgAwIBAgIJAIg6Va5tcvwyMA0GCSqGSIb3DQEBCwUAMG0xCzAJBgNV
BAYTAlVTMRMwEQYDVQQIDApXYXNoaW5ndG9uMRAwDgYDVQQHDAdTZWF0dGxlMRIw
EAYDVQQKDAlXb3JkZmVuY2UxCzAJBgNVBAsMAklUMRYwFAYDVQQDDA13b3JkZmVu
Y2UuY29tMB4XDTE1MDMxMjA1NTIzMFoXDTM1MDMwNzA1NTIzMFowbTELMAkGA1UE
BhMCVVMxEzARBgNVBAgMCldhc2hpbmd0b24xEDAOBgNVBAcMB1NlYXR0bGUxEjAQ
BgNVBAoMCVdvcmRmZW5jZTELMAkGA1UECwwCSVQxFjAUBgNVBAMMDXdvcmRmZW5j
ZS5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQC/9Ogj1PIQsuZu
dTUNWlG0zaDNWpeY1ZiB/6oBS/YXkGFuG8R/nZ/kYsRmBm6yRp/3jC/HiPjg+7Zc
bA/CKoHdUlNjFZ+10DmS369wVX+c0oV9f720b/a0xN0qeKxJTiN2NsAl5szYv2CQ
Bvzjeb5VfKgrfV9tgYr38swudxvexponYaK0OlDL3u/Xca4SLRKmB+ZYCcZJttoG
SNFsQMlLHWWmM0FJH9qZ3x8MtRM5KsNEWO+/op511Rr36ZnLJdzUnETsaxHKwuCv
0+D9b0mwk8K/c67l63v4+zywXNkdYIslgo7Aeeyb6t0lyyfruXutEyMinmApACT2
sDMAbYk7AgMBAAGjUDBOMB0GA1UdDgQWBBTstr/AoPQyLLIt4/peFSjj0FFXHzAf
BgNVHSMEGDAWgBTstr/AoPQyLLIt4/peFSjj0FFXHzAMBgNVHRMEBTADAQH/MA0G
CSqGSIb3DQEBCwUAA4IBAQA9HsK+XdZh2MGP2SDdggA+MxkNBCCFBtcsmQrpiLUW
67xt59FPRMwTgSA9Lt8uqcWaXoHXiaTnXTRtN/BKZR0F71HQfiV6zy511blIRlk2
nV+vYzwLUENCZ31hQEZsY+uYqBSTiHecUKohn8A9pOOEpis2YEn2zVo4cobdyGa1
zCnaAN99KT8s9lOO0UW0J52qZhvv4y8YhELtrXKBsFatGEsVIM0NFI+ZDsNpMnSQ
cmUtLiIJtk5hxNbOaIz2vzbOkbzJ3ehzODJ1X5rya7X0v2akLLhwP9jqz5ua6ttP
duLv4Q6v3LY6pwDoyKQMDqNNxVjaFmx5HyFWRPofpu/T
-----END CERTIFICATE-----
ENDKEY;
		return $key;
	}
	public static function makeSymHexKey($length){
		return bin2hex(wfWAFUtils::random_bytes($length / 2));
	}
	public static function pubCrypt($symKey){ //encrypts a symmetric key and returns it base64
		openssl_public_encrypt($symKey, $encSymKey, self::getPubKey(), OPENSSL_PKCS1_OAEP_PADDING); //The default OPENSSL_PKCS1_PADDING is deprecated.
		return base64_encode($encSymKey);
	}
}
