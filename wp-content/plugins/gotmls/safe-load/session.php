<?php
/**
 * GOTMLS SESSION Start
 * @package GOTMLS
*/

if (!defined("GOTMLS_SESSION_TIME"))
	define("GOTMLS_SESSION_TIME", microtime(true));
if (!@session_id())
	@session_start();
if (isset($_SESSION["GOTMLS_SESSION_TIME"]))
	$_SESSION["GOTMLS_SESSION_LAST"] = $_SESSION["GOTMLS_SESSION_TIME"];
else
	$_SESSION["GOTMLS_SESSION_LAST"] = 0;
$_SESSION["GOTMLS_SESSION_TIME"] = GOTMLS_SESSION_TIME;
