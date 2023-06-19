<?php exit;

//Check everything exists before using it
if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']))
	$_SERVER['HTTP_ACCEPT_ENCODING'] = '';
if(!isset($_SERVER['HTTP_USER_AGENT']))
	$_SERVER['HTTP_USER_AGENT'] = '';

// Determine supported compression method
$gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
$deflate = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');

// Determine used compression method
$encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');

// Check for buggy versions of Internet Explorer
if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') &&
	preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches))
{
	$version = floatval($matches[1]);

	if ($version < 6)
		$encoding = 'none';

	if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1'))
		$encoding = 'none';
}

//Some servers compress the output of PHP - Don't break in those cases
if(ini_get('output_handler') == 'ob_gzhandler' || ini_get('zlib.output_compression') == 1)
	$encoding = 'none';

$iscompressed = file_exists(__FILE__.'.'.$encoding);
if($encoding != 'none' && $iscompressed == false)
{
	$flag = ($encoding == 'gzip' ? FORCE_GZIP : FORCE_DEFLATE);
	$code = file_get_contents(__FILE__.'.none');
	$contents = gzencode($code,9,$flag);
}else{
	//Get data
	$contents = file_get_contents(__FILE__.'.'.$encoding);
}

// first check if we have to send 304
// inspired by http://www.jonasjohn.de/snippets/php/caching.htm

$eTag=md5($contents);
$modTime=filemtime(__FILE__.'.none');

date_default_timezone_set("UTC");
$eTagMatch = (isset($_SERVER['HTTP_IF_NONE_MATCH']) && strpos($_SERVER['HTTP_IF_NONE_MATCH'],$eTag));
$modTimeMatch = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $modTime);

if (($modTimeMatch)||($eTagMatch)) {
	header('HTTP/1.1 304 Not Modified');
	header('Connection: close');
} else {
	// send all sorts of headers
	$expireTime=60*60*24*355; // 1y max according to RFC
	if ($encoding != 'none') {
		header('Content-Encoding: '.$encoding);
	}
	header('Vary: Accept-Encoding');
	header('Content-Length: '.strlen($contents));
	header('Content-type: %%CONTENT%%; charset=utf-8');
	header('Cache-Control: max-age='.$expireTime.', public, must-revalidate');
	header('Cache-Control: max-age='.$expireTime.', public, immutable');
	header('Expires: '.gmdate('D, d M Y H:i:s', time() + $expireTime).' GMT');
	header('ETag: ' . $eTag);
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', $modTime).' GMT');

	// send output
	echo $contents;

	//And write to filesystem cache if not done yet
	if($encoding != 'none' && $iscompressed == false)
	{
		//Write the content we sent
		file_put_contents(__FILE__.'.'.$encoding,$contents);

		//And write the new content
		$flag = ($encoding == 'gzip' ? FORCE_DEFLATE : FORCE_GZIP);
		$ext = ($encoding == 'gzip' ? 'deflate' : 'gzip');
		$contents = gzencode($code,9,$flag);
		file_put_contents(__FILE__.'.'.$ext,$contents);
	}
}
