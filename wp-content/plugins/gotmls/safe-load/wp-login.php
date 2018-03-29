<?php
/**
 * GOTMLS wp-login protection
 * @package GOTMLS
*/

if (!defined("GOTMLS_REQUEST_METHOD"))
	define("GOTMLS_REQUEST_METHOD", (isset($_SERVER["REQUEST_METHOD"])?strtoupper($_SERVER["REQUEST_METHOD"]):"none"));
if ((GOTMLS_REQUEST_METHOD == "POST") && isset($_POST["log"]) && isset($_POST["pwd"]) && isset($_POST["session_id"]) && isset($_POST["sess".$_POST["session_id"]]) && is_numeric($_POST["sess".$_POST["session_id"]])) {
	$sess = round($_POST["sess".$_POST["session_id"]] / 60000);
	$time = round(time() / 60);
	if ((($time - $sess) > 2) || (($sess - $time) > 2)) {
		$GLOBALS["GOTMLS"]["detected_attacks"] = '&attack[]=NO_JS';
		include(dirname(__FILE__)."/index.php");
	}
} else {
	include(dirname(__FILE__)."/session.php");
	if (!function_exists("GOTMLS_update_log_file")) {
		function GOTMLS_update_log_file($dont_force_write = true) {
			if (!defined("GOTMLS_SESSION_FILE"))
				define("GOTMLS_SESSION_FILE", dirname(__FILE__)."/_SESSION/index.php");
			if (is_file(GOTMLS_SESSION_FILE))
				include(GOTMLS_SESSION_FILE);
			else {
				if (!is_dir(dirname(GOTMLS_SESSION_FILE)))
					@mkdir(dirname(GOTMLS_SESSION_FILE));
				if (is_dir(dirname(GOTMLS_SESSION_FILE)))
					if (!is_file(GOTMLS_SESSION_FILE))
						if (file_put_contents(GOTMLS_SESSION_FILE, "<?php if (!defined('GOTMLS_INSTALL_TIME')) define('GOTMLS_INSTALL_TIME', '".GOTMLS_SESSION_TIME."');"))
							include(GOTMLS_SESSION_FILE);
			}
			if (!defined("GOTMLS_INSTALL_TIME"))
				return false;
			else {
				$GOTMLS_LOGIN_ARRAY = array("ADDR"=>(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"REMOTE_ADDR"), "AGENT"=>(isset($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:"HTTP_USER_AGENT"), "TIME"=>GOTMLS_INSTALL_TIME);
				$GOTMLS_LOGIN_KEY = md5(serialize($GOTMLS_LOGIN_ARRAY));
				if (!defined("GOTMLS_LOG_FILE"))
					define("GOTMLS_LOG_FILE", dirname(GOTMLS_SESSION_FILE)."/.GOTMLS.$GOTMLS_LOGIN_KEY.php");
				if (is_file(GOTMLS_LOG_FILE))
					include(GOTMLS_LOG_FILE);
				if (GOTMLS_REQUEST_METHOD == "POST")
					$GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY][GOTMLS_REQUEST_METHOD][GOTMLS_INSTALL_TIME] = $GOTMLS_LOGIN_ARRAY;
				else
					$GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY][GOTMLS_REQUEST_METHOD] = GOTMLS_INSTALL_TIME;
				@file_put_contents(GOTMLS_LOG_FILE, '<?php $GLOBALS["GOTMLS"]["logins"]["'.$GOTMLS_LOGIN_KEY.'"]=unserialize(base64_decode("'.base64_encode(serialize($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY])).'"));');
				if (isset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]) && is_array($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]))
					return $GOTMLS_LOGIN_KEY;
				else
					return 0;
			}
		}
	}
	if ((GOTMLS_REQUEST_METHOD == "POST") && isset($_POST["log"]) && isset($_POST["pwd"]) && !(isset($GOTMLS_LOGIN_KEY) && isset($GOTMLS_logins[$GOTMLS_LOGIN_KEY]["whitelist"]))) {
		if (!(isset($_SESSION["GOTMLS_detected_attacks"]) && $_SESSION["GOTMLS_SESSION_LAST"]))
			$GLOBALS["GOTMLS"]["detected_attacks"] = '&attack[]=NO_SESSION';
		if (!isset($_SERVER["REMOTE_ADDR"]))
			$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_REMOTE_ADDR';
		if (!isset($_SERVER["HTTP_USER_AGENT"]))
			$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_HTTP_USER_AGENT';
		if (!isset($_SERVER["HTTP_REFERER"]))
			$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_HTTP_REFERER';
		if (!$GLOBALS["GOTMLS"]["detected_attacks"]) {
			if (isset($_SESSION["GOTMLS_login_attempts"]) && is_numeric($_SESSION["GOTMLS_login_attempts"]) && strlen($_SESSION["GOTMLS_login_attempts"]."") > 0)
				$_SESSION["GOTMLS_login_attempts"]++;
			else {
				if ($GOTMLS_LOGIN_KEY = GOTMLS_update_log_file()) {
					if (!(isset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"]) && is_array($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"])))
						$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_LOGIN_ATTEMPTS';
					elseif (!isset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["GET"]))
						$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_LOGIN_GETS';
					else {
						$_SESSION["GOTMLS_login_attempts"] = 0;
						foreach ($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"] as $LOGIN_TIME=>$LOGIN_ARRAY) {
							if ($LOGIN_TIME > $GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["GET"])
								$_SESSION["GOTMLS_login_attempts"]++;
							else
								unset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"][$LOGIN_TIME]);
						}
					}
				} else
					$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_LOG_FILE';
			}
			if (!(isset($_SESSION["GOTMLS_login_attempts"]) && is_numeric($_SESSION["GOTMLS_login_attempts"]) && ($_SESSION["GOTMLS_login_attempts"] < 6) && $_SESSION["GOTMLS_login_attempts"]))
				$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=TOO_MANY_login_attempts';
		}
		if ($GLOBALS["GOTMLS"]["detected_attacks"])
			include(dirname(__FILE__)."/index.php");
	} else {
		if (isset($_SERVER["SCRIPT_FILENAME"]) && basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]))
			GOTMLS_update_log_file();
		$_SESSION["GOTMLS_detected_attacks"] = '';
		$_SESSION["GOTMLS_login_attempts"] = 0;
	}
}