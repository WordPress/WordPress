<?php

require_once('../b2config.php');

/* connecting the db */
$connexion = @mysql_connect($server,$loginsql,$passsql) or die("Can't connect to the database<br>".mysql_error());
mysql_select_db("$base");

/* checking login & pass in the database */
function veriflog() {
	global $HTTP_COOKIE_VARS;
	global $tableusers,$tablesettings,$tablecategories,$tableposts,$tablecomments;

	if (!empty($HTTP_COOKIE_VARS["wordpressuser"])) {
		$user_login = $HTTP_COOKIE_VARS["wordpressuser"];
		$user_pass_md5 = $HTTP_COOKIE_VARS["wordpresspass"];
	} else {
		return false;
	}

	if (!($user_login != ""))
		return false;
	if (!$user_pass_md5)
		return false;

	$query =  " SELECT user_login, user_pass FROM $tableusers WHERE user_login = '$user_login' ";
	$result = @mysql_query($query) or die("Query: $query<br /><br />Error: ".mysql_error());

	$lines = mysql_num_rows($result);
	if ($lines<1) {
		return false;
	} else {
		$res=mysql_fetch_row($result);
		if ($res[0] == $user_login && md5($res[1]) == $user_pass_md5) {
			return true;
		} else {
			return false;
		}
	}
}
//if ( $user_login!="" && $user_pass!="" && $id_session!="" && $adresse_ip==$REMOTE_ADDR) {
//	if ( !(veriflog()) AND !(verifcookielog()) ) {
	if (!(veriflog())) {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		if (!empty($HTTP_COOKIE_VARS["wordpressuser"])) {
			$error="<b>Error</b>: wrong login or password";
		}
		header("Location: $path/b2login.php");
		exit();
	}
//}
?>