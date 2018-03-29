<?php

/** @var wfWAF $waf */
/** @var wfWAFView $this */

/*
 * IMPORTANT:
 * 
 * If the form variables below change name or format, admin.ajaxWatcher.js in the main plugin also needs changed. It
 * processes these to generate its whitelist button.
 */

$request = $waf->getRequest();
$headerString = '';
if (is_array($request->getHeaders())) {
	foreach ($request->getHeaders() as $header => $value) {
		switch (wfWAFUtils::strtolower($header)) {
			case 'cookie':
				$headerString .= 'Cookie: ' . trim($request->getCookieString()) . "\n";
				break;
			
			case 'host':
				$headerString .= 'Host: ' . $request->getHost() . "\n";
				break;
			
			case 'authorization':
				$hasAuth = true;
				if ($request->getAuth()) {
					$headerString .= 'Authorization: Basic <redacted>' . "\n";
				}
				break;
			
			default:
				$headerString .= $header . ': ' . $value . "\n";
				break;
		}
	}
}

$payload = array('ip' => $request->getIP(), 'timestamp' => $request->getTimestamp(), 'headers' => $headerString, 'url' => $request->getProtocol() . '://' . $request->getHost() . $request->getPath(), 'home_url' => $waf->getStorageEngine()->getConfig('homeURL', ''));
$payloadJSON = wfWAFUtils::json_encode($payload);
$shouldEncrypt = false;
if (function_exists('openssl_get_publickey') && function_exists('openssl_get_cipher_methods')) {
	$ciphers = openssl_get_cipher_methods();
	$shouldEncrypt = array_search('aes-256-cbc', $ciphers) !== false;
}

if ($shouldEncrypt) {
	$keyData = file_get_contents(dirname(__FILE__) . '/../falsepositive.key');
	$key = @openssl_get_publickey($keyData);
	if ($key !== false) {
		$symmetricKey = wfWAFUtils::random_bytes(32);
		$iv = wfWAFUtils::random_bytes(16);
		$encrypted = @openssl_encrypt($payloadJSON, 'aes-256-cbc', $symmetricKey, OPENSSL_RAW_DATA, $iv);
		if ($encrypted !== false) {
			$success = openssl_public_encrypt($symmetricKey, $symmetricKeyEncrypted, $key, OPENSSL_PKCS1_OAEP_PADDING);
			if ($success) {
				$message = $iv . $symmetricKeyEncrypted . $encrypted;
				$signatureRaw = hash('sha256', $message, true);
				$success = openssl_public_encrypt($signatureRaw, $signature, $key, OPENSSL_PKCS1_OAEP_PADDING);
				if ($success) {
					$payload = array('message' => bin2hex($message), 'signature' => bin2hex($signature));
					$payloadJSON = wfWAFUtils::json_encode($payload);
				}
			}
		}
	}
}

$message = base64_encode($payloadJSON);
$payload = "-----BEGIN REPORT-----\n" . implode("\n", str_split($message, 60)) . "\n-----END REPORT-----";

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>403 Forbidden</title>
	<style>
		html {
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 14px;
			line-height: 1.42857143;
			color: #333;
			background-color: #fff;
		}
		
		h1, h2, h3, h4, h45, h6 {
			font-weight: 500;
			line-height: 1.1;
		}
		
		h1 { font-size: 36px; }
		h2 { font-size: 30px; }
		h3 { font-size: 24px; }
		h4 { font-size: 18px; }
		h5 { font-size: 14px; }
		h6 { font-size: 12px; }
		
		h1, h2, h3 {
			margin-top: 20px;
			margin-bottom: 10px;
		}
		h4, h5, h6 {
			margin-top: 10px;
			margin-bottom: 10px;
		}
		
		.btn {
			background-color: #00709e;
			border: 1px solid #09486C;
			border-radius: 4px;
			box-sizing: border-box;
			color: #ffffff;
			cursor: pointer;
			display: inline-block;
			font-size: 14px;
			font-weight: normal;
			letter-spacing: normal;
			line-height: 20px;
			margin: 5px 0px;
			padding: 12px 6px;
			text-align: center;
			text-decoration: none;
			vertical-align: middle;
			white-space: nowrap;
			word-spacing: 0px;
		}
		
		textarea {
			display: block;
			height: 48px;
			padding: 6px 12px;
			font-size: 14px;
			line-height: 1.42857143;
			color: #555;
			background-color: #fff;
			background-image: none;
			border: 1px solid #ccc;
			border-radius: 4px;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
			-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
			-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			font-family: monospace;
		}
		
		textarea:focus {
			border-color: #66afe9;
			outline: 0;
			-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075), 0 0 8px rgba(102, 175, 233, .6);
		}
		
		hr {
			margin-top: 20px;
			margin-bottom: 20px;
			border: 0;
			border-top: 1px solid #eee
		}
		
		.btn.disabled, .btn[disabled] {
			background-color: #9f9fa0;
			border: 1px solid #7E7E7F;
			cursor: not-allowed;
			filter: alpha(opacity=65);
			-webkit-box-shadow: none;
			box-shadow: none;
			opacity: .65;
			pointer-events: none;
		}
	</style>
</head>
<body>

<h1>403 Forbidden</h1>

<h3>WHAT? Why am I seeing this?</h3>

<p>Your access to this site was blocked by Wordfence, a security provider, who protects sites from malicious activity.</p>

<p>If you believe Wordfence should be allowing you access to this site, please let them know using the steps below so they can investigate why this is happening.</p>

<hr>

<h3>Reporting a Problem</h3>

<h4>1. Please copy this text. You need to paste it into a form later.</h4>

<p><textarea id="payload" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" cols="65"><?php echo htmlspecialchars($payload); ?></textarea></p>
<script type="application/javascript">
	(function() {
		var textarea = document.getElementById('payload');
		var cs = window.getComputedStyle(textarea);
		var lines = textarea.value.split('\n');
		var height = 1 + lines.length;
		var pixelHeight = Math.min(height * parseInt(cs.getPropertyValue('line-height')), 600);
		textarea.style.height = pixelHeight + 'px';
		
		textarea.addEventListener('focus', function() {
			document.getElementById('reportButton').className = document.getElementById('reportButton').className.replace(new RegExp('(?:^|\\s)'+ 'disabled' + '(?:\\s|$)'), ' ');
			document.getElementById('reportButton').href = 'ht' + 'tp:/' + '/user-reports.wordfence' + '.com';
		});
	})();
</script>

<h4>2. Click this button and you will be prompted to paste the text above.</h4>

<p><a href="#" id="reportButton" class="btn disabled" target="_blank" rel="noopener noreferrer">Report Problem</a></p>

<p style="color: #999999;margin-top: 2rem;"><em>Generated by Wordfence at <?php echo gmdate('D, j M Y G:i:s T', wfWAFUtils::normalizedTime()); ?>.<br>Your computer's time: <script type="application/javascript">document.write(new Date().toUTCString());</script>.</em></p>

</body>
</html>