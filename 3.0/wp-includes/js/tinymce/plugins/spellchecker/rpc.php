<?php
/**
 * $Id: rpc.php 822 2008-04-28 13:45:03Z spocke $
 *
 * @package MCManager.includes
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

require_once("./includes/general.php");

// Set RPC response headers
header('Content-Type: text/plain');
header('Content-Encoding: UTF-8');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$raw = "";

// Try param
if (isset($_POST["json_data"]))
	$raw = getRequestParam("json_data");

// Try globals array
if (!$raw && isset($_GLOBALS) && isset($_GLOBALS["HTTP_RAW_POST_DATA"]))
	$raw = $_GLOBALS["HTTP_RAW_POST_DATA"];

// Try globals variable
if (!$raw && isset($HTTP_RAW_POST_DATA))
	$raw = $HTTP_RAW_POST_DATA;

// Try stream
if (!$raw) {
	if (!function_exists('file_get_contents')) {
		$fp = fopen("php://input", "r");
		if ($fp) {
			$raw = "";

			while (!feof($fp))
				$raw = fread($fp, 1024);

			fclose($fp);
		}
	} else
		$raw = "" . file_get_contents("php://input");
}

// No input data
if (!$raw)
	die('{"result":null,"id":null,"error":{"errstr":"Could not get raw post data.","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');

// Passthrough request to remote server
if (isset($config['general.remote_rpc_url'])) {
	$url = parse_url($config['general.remote_rpc_url']);

	// Setup request
	$req = "POST " . $url["path"] . " HTTP/1.0\r\n";
	$req .= "Connection: close\r\n";
	$req .= "Host: " . $url['host'] . "\r\n";
	$req .= "Content-Length: " . strlen($raw) . "\r\n";
	$req .= "\r\n" . $raw;

	if (!isset($url['port']) || !$url['port'])
		$url['port'] = 80;

	$errno = $errstr = "";

	$socket = fsockopen($url['host'], intval($url['port']), $errno, $errstr, 30);
	if ($socket) {
		// Send request headers
		fputs($socket, $req);

		// Read response headers and data
		$resp = "";
		while (!feof($socket))
				$resp .= fgets($socket, 4096);

		fclose($socket);

		// Split response header/data
		$resp = explode("\r\n\r\n", $resp);
		echo $resp[1]; // Output body
	}

	die();
}

// Get JSON data
$json = new Moxiecode_JSON();
$input = $json->decode($raw);

// Execute RPC
if (isset($config['general.engine'])) {
	$spellchecker = new $config['general.engine']($config);
	$result = call_user_func_array(array($spellchecker, $input['method']), $input['params']);
} else
	die('{"result":null,"id":null,"error":{"errstr":"You must choose an spellchecker engine in the config.php file.","errfile":"","errline":null,"errcontext":"","level":"FATAL"}}');

// Request and response id should always be the same
$output = array(
	"id" => $input->id,
	"result" => $result,
	"error" => null
);

// Return JSON encoded string
echo $json->encode($output);

?>