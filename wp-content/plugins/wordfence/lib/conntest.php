<?php if (!defined('WORDFENCE_VERSION')) { exit; } ?>
<?php if(! wfUtils::isAdmin()){ exit(); } ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<title>Wordfence Connectivity Tester</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<body>
<h1>Wordfence connectivity tester</h1>
<br /><br />
DNS lookup for noc1.wordfence.com returns: <?php echo gethostbyname('noc1.wordfence.com'); ?><br /><br />
<?php
$curlContent = "";
function curlWrite($h, $d){
	global $curlContent;
	$curlContent .= $d;
	return strlen($d);
}
function doWPostTest($protocol){
	echo "<br /><b>Starting wp_remote_post() test</b><br />\n";
	$cronURL = admin_url('admin-ajax.php');
	$cronURL = preg_replace('/^(https?:\/\/)/i', '://noc1.wordfence.com/scanptest/', $cronURL);
	$cronURL .= '?action=wordfence_doScan&isFork=0&cronKey=47e9d1fa6a675b5999999333';
	$cronURL = $protocol . $cronURL;
	$result = wp_remote_post($cronURL, array(
		'timeout' => 10, //Must be less than max execution time or more than 2 HTTP children will be occupied by scan
		'blocking' => true, //Non-blocking seems to block anyway, so we use blocking
		// This causes cURL to throw errors in some versions since WordPress uses its own certificate bundle ('CA certificate set, but certificate verification is disabled')
		// 'sslverify' => false,
		'headers' => array()
		));
	if( (! is_wp_error($result)) && $result['response']['code'] == 200 && strpos($result['body'], "scanptestok") !== false){
		echo "wp_remote_post() test to noc1.wordfence.com passed!<br />\n";
	} else if(is_wp_error($result)){
		echo "wp_remote_post() test to noc1.wordfence.com failed! Response was: " . $result->get_error_message() . "<br />\n";
	} else {
		echo "wp_remote_post() test to noc1.wordfence.com failed! Response was: " . $result['response']['code'] . " " . $result['response']['message'] . "<br />\n";
		echo "This likely means that your hosting provider is blocking requests to noc1.wordfence.com or has set up a proxy that is not behaving itself.<br />\n";
		echo "This additional info may help you diagnose the issue. The response headers we received were:<br />\n";
		foreach($result['headers'] as $key => $value){
			echo "$key => $value<br />\n";
		}
	}
}
function doCurlTest($protocol){
	if(! function_exists('curl_init')){
		echo "<br /><b style='color: #F00;'>CURL is not installed</b>. Asking your hosting provider to install and enable CURL may improve any connection problems.</b><br />\n";
		return;
	}
	echo "<br /><b>STARTING CURL $protocol CONNECTION TEST....</b><br />\n";
	global $curlContent;
	$curlContent = "";
	$curl = curl_init($protocol . '://noc1.wordfence.com/');
	if(defined('WP_PROXY_HOST') && defined('WP_PROXY_PORT') && wfUtils::hostNotExcludedFromProxy('noc1.wordfence.com') ){
		curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 0);
		curl_setopt($curl, CURLOPT_PROXY, WP_PROXY_HOST . ':' . WP_PROXY_PORT);
		if(defined('WP_PROXY_USERNAME') && defined('WP_PROXY_PASSWORD')){
			curl_setopt($curl, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME . ':' . WP_PROXY_PASSWORD);
		}
	}

	curl_setopt ($curl, CURLOPT_TIMEOUT, 900);
	curl_setopt ($curl, CURLOPT_USERAGENT, "Wordfence.com UA " . (defined('WORDFENCE_VERSION') ? WORDFENCE_VERSION : '[Unknown version]') );
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt ($curl, CURLOPT_HEADER, 0);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($curl, CURLOPT_WRITEFUNCTION, 'curlWrite');
	curl_exec($curl);
	$httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	if(strpos($curlContent, 'Your site did not send an API key') !== false){
		echo "Curl connectivity test passed.<br /><br />\n";
	} else {
		$curlErrorNo = curl_errno($curl);
		$curlError = curl_error($curl);
		echo "Curl connectivity test failed with response: <pre>$curlContent</pre>";
		echo "<br />Curl HTTP status: $httpStatus<br />Curl error code: $curlErrorNo<br />Curl Error: $curlError<br /><br />\n";
	}
}
doCurlTest('http');
doCurlTest('https');
doWPostTest('http');
doWPostTest('https');
?>
</body>
</html>

