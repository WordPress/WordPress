<?php
/**
 * $Id: rpc.php 354 2007-11-05 20:48:49Z spocke $
 *
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