<?php
/**
 * GOTMLS Brute-Force protections
 * @package GOTMLS
*/

if (!(isset($GLOBALS["GOTMLS"]["detected_attacks"]) && $GLOBALS["GOTMLS"]["detected_attacks"])) {
	$file = (isset($_SERVER["SCRIPT_FILENAME"])?$_SERVER["SCRIPT_FILENAME"]:__FILE__);
	$GLOBALS["GOTMLS"]["detected_attacks"] = '&attack[]='.strtolower((isset($_SERVER["DOCUMENT_ROOT"]) && strlen($_SERVER["DOCUMENT_ROOT"]) < strlen($file))?substr($file, strlen($_SERVER["DOCUMENT_ROOT"])):basename($file));
}
foreach (array("REMOTE_ADDR", "HTTP_HOST", "REQUEST_URI", "HTTP_REFERER", "HTTP_USER_AGENT") as $var)
	$GLOBALS["GOTMLS"]["detected_attacks"] .= (isset($_SERVER[$var])?"&SERVER_$var=".urlencode($_SERVER[$var]):"");
foreach (array("log", "session_id") as $var)
	$GLOBALS["GOTMLS"]["detected_attacks"] .= (isset($_POST[$var])?"&POST_$var=".urlencode($_POST[$var]).(isset($_POST["sess".$_POST[$var]])?"&TIME=".time()."&POST_sess$var=".urlencode($_POST["sess".$_POST[$var]]):""):"");
$ver = "Unknown";
if ($file = str_replace(basename(dirname(__FILE__)), basename(__FILE__), dirname(__FILE__)))
	if (is_file($file) && $contents = @file_get_contents($file))
		if (preg_match('/\nversion:\s*([0-9\.]+)/i', $contents, $match))
			$ver = $match[1];
header("location: http://safe-load.gotmls.net/report.php?ver=$ver".$GLOBALS["GOTMLS"]["detected_attacks"]);
die();