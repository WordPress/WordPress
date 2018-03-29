<?php
/*
Plugin Name: Anti-Malware Security and Brute-Force Firewall
Plugin URI: http://gotmls.net/
Author: Eli Scheetz
Text Domain: gotmls
Author URI: http://wordpress.ieonly.com/category/my-plugins/anti-malware/
Contributors: scheeeli, gotmls
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QZHD8QHZ2E7PE
Description: This Anti-Virus/Anti-Malware plugin searches for Malware and other Virus like threats and vulnerabilities on your server and helps you remove them. It's always growing and changing to adapt to new threats so let me know if it's not working for you.
Version: 4.17.58
*/
if (isset($_SERVER["DOCUMENT_ROOT"]) && ($SCRIPT_FILE = str_replace($_SERVER["DOCUMENT_ROOT"], "", isset($_SERVER["SCRIPT_FILENAME"])?$_SERVER["SCRIPT_FILENAME"]:isset($_SERVER["SCRIPT_NAME"])?$_SERVER["SCRIPT_NAME"]:"")) && strlen($SCRIPT_FILE) > strlen("/".basename(__FILE__)) && substr(__FILE__, -1 * strlen($SCRIPT_FILE)) == substr($SCRIPT_FILE, -1 * strlen(__FILE__)))
	include(dirname(__FILE__)."/safe-load/index.php");
else
	require_once(dirname(__FILE__)."/images/index.php");
/*            ___
 *           /  /\     GOTMLS Main Plugin File
 *          /  /:/     @package GOTMLS
 *         /__/::\
 Copyright \__\/\:\__  © 2012-2018 Eli Scheetz (email: eli@gotmls.net)
 *            \  \:\/\
 *             \__\::/ This program is free software; you can redistribute it
 *     ___     /__/:/ and/or modify it under the terms of the GNU General Public
 *    /__/\   _\__\/ License as published by the Free Software Foundation;
 *    \  \:\ /  /\  either version 2 of the License, or (at your option) any
 *  ___\  \:\  /:/ later version.
 * /  /\\  \:\/:/
  /  /:/ \  \::/ This program is distributed in the hope that it will be useful,
 /  /:/_  \__\/ but WITHOUT ANY WARRANTY; without even the implied warranty
/__/:/ /\__    of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
\  \:\/:/ /\  See the GNU General Public License for more details.
 \  \::/ /:/
  \  \:\/:/ You should have received a copy of the GNU General Public License
 * \  \::/ with this program; if not, write to the Free Software Foundation,    
 *  \__\/ Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA        */

load_plugin_textdomain('gotmls', false, basename(GOTMLS_plugin_path).'/languages');
require_once(GOTMLS_plugin_path.'images/index.php');

function GOTMLS_install() {
	global $wp_version;
	if (version_compare($wp_version, GOTMLS_require_version, "<"))
		die(GOTMLS_require_version_LANGUAGE);
}
register_activation_hook(__FILE__, "GOTMLS_install");

function GOTMLS_user_can() {
	if (is_multisite())
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] = "manage_network";
	elseif (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"]) || $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] == "manage_network")
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] = "activate_plugins";
	if (current_user_can($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"]))
		return true;
	else
		return false;
}

function GOTMLS_menu() {
	$GOTMLS_Full_plugin_logo_URL = GOTMLS_images_path.'GOTMLS-16x16.gif';
	$base_page = "GOTMLS-settings";
	$base_function = "GOTMLS_settings";
	$pluginTitle = "Anti-Malware";
	$pageTitle = "$pluginTitle ".GOTMLS_Scan_Settings_LANGUAGE;
	if (GOTMLS_user_can()) {
		$my_admin_page = add_menu_page($pageTitle, $pluginTitle, $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"], $base_page, $base_function, $GOTMLS_Full_plugin_logo_URL);
		add_action('load-'.$my_admin_page, 'GOTMLS_admin_add_help_tab');
		add_submenu_page($base_page, "$pluginTitle ".GOTMLS_Scan_Settings_LANGUAGE, GOTMLS_Scan_Settings_LANGUAGE, $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"], $base_page, $base_function);
		add_submenu_page($base_page, "$pluginTitle Firewall Options", "Firewall Options", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"], "GOTMLS-Firewall-Options", "GOTMLS_Firewall_Options");
		add_submenu_page($base_page, "$pluginTitle ".GOTMLS_View_Quarantine_LANGUAGE, GOTMLS_View_Quarantine_LANGUAGE.(($Qs = GOTMLS_get_quarantine(true))?' <span class="awaiting-mod count-'.$Qs.'"><span class="awaiting-mod">'.$Qs.'</span></span>':""), $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"], "GOTMLS-View-Quarantine", "GOTMLS_View_Quarantine");
	}
}

function GOTMLS_admin_add_help_tab() {
	$screen = get_current_screen();
	$screen->add_help_tab(array(
		'id'	=> "GOTMLS_Getting_Started",
		'title'	=> __("Getting Started", 'gotmls'),
		'content'	=> '<p>'.__("Make sure the Definition Updates are current and Run a Complete Scan.").'</p><p>'.sprintf(__("If Known Threats are found and displayed in red then there will be a button to '%s'. If only Potentional Threats are found then there is no automatic fix because those are probably not malicious."), GOTMLS_Automatically_Fix_LANGUAGE).'</p><p>'.__("A backup of the original infected files are placed in the Quarantine in case you need to restore them or just want to look at them later. You can delete these files if you don't want to save more.").'</p>'
	));
	$FAQMarker = '== Frequently Asked Questions ==';
 	if (is_file(dirname(__FILE__).'/readme.txt') && ($readme = explode($FAQMarker, @file_get_contents(dirname(__FILE__).'/readme.txt').$FAQMarker)) && strlen($readme[1]) && ($readme = explode("==", $readme[1]."==")) && strlen($readme[0])) {
		$screen->add_help_tab(array(
			'id'	=> "GOTMLS_FAQs",
			'title'	=> __("FAQs", 'gotmls'),
			'content'	=> '<p>'.preg_replace('/\[(.+?)\]\((.+?)\)/', "<a target=\"_blank\" href=\"\\2\">\\1</a>", preg_replace('/[\r\n]+= /', "</p><b>", preg_replace('/ =[\r\n]+/', "</b><p>", $readme[0]))).'</p>'
		));
	}
}

function GOTMLS_close_button($box_id, $margin = '6px') {
	return '<a href="javascript:void(0);" style="float: right; color: #F00; overflow: hidden; width: 20px; height: 20px; text-decoration: none; margin: '.$margin.'" onclick="showhide(\''.$box_id.'\');"><span class="dashicons dashicons-dismiss"></span>X</a>';
}

function GOTMLS_enqueue_scripts() {
	wp_enqueue_style('dashicons');
}
add_action('admin_enqueue_scripts', 'GOTMLS_enqueue_scripts');

function GOTMLS_display_header($optional_box = "") {
	global $wp_version, $current_user, $wpdb;
	wp_get_current_user();
	$GOTMLS_url_parts = explode('/', GOTMLS_siteurl);
	if (isset($_GET["check_site"]) && $_GET["check_site"])
		echo '<div id="check_site" style="z-index: 1234567;"><img src="'.GOTMLS_images_path.'checked.gif" height=16 width=16 alt="&#x2714;"> '.__("Tested your site. It appears we didn't break anything",'gotmls').' ;-)</div><script type="text/javascript">window.parent.document.getElementById("check_site_warning").style.backgroundColor=\'#0C0\';</script><li>Please <a target="_blank" href="https://wordpress.org/plugins/gotmls/stats/?compatibility%5Bversion%5D='.$wp_version.'&compatibility%5Btopic_version%5D='.GOTMLS_Version.'&compatibility%5Bcompatible%5D=1#compatibility-works">Vote "Works"</a> or <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/gotmls#postform">write a "Five-Star" Review</a> on WordPress.org if you like this plugin.</li><style>#footer, #GOTMLS-metabox-container, #GOTMLS-right-sidebar, #admin-page-container, #wpadminbar, #adminmenuback, #adminmenuwrap, #adminmenu, .error, .updated, .update-nag {display: none !important;} #wpbody-content {padding-bottom: 0;} #wpbody, html.wp-toolbar {padding-top: 0 !important;} #wpcontent, #footer {margin-left: 5px !important;}';
	else
		echo '<style>#GOTMLS-right-sidebar {float: right; margin-right: 0px;}';
	$Update_Definitions = array(GOTMLS_update_home.'definitions.js'.$GLOBALS["GOTMLS"]["tmp"]["Definition"]["Updates"].'&ver='.GOTMLS_Version.'&wp='.$wp_version.'&'.GOTMLS_set_nonce(__FUNCTION__."108").'&d='.ur1encode(GOTMLS_siteurl));
	if (isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["auto_UPDATE_definitions"]) && $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["auto_UPDATE_definitions"])
		array_unshift($Update_Definitions, admin_url('admin-ajax.php?action=GOTMLS_auto_update&'.GOTMLS_set_nonce(__FUNCTION__."109").'&UPDATE_definitions_array=1'));
	else
		$Update_Definitions[] = str_replace("//", "//www.", $Update_Definitions[0]);
	$Update_Link = '<div style="text-align: center;"><a href="';
	$new_version = "";
	$file = basename(GOTMLS_plugin_path).'/index.php';
	$current = get_site_transient("update_plugins");
	if (isset($current->response[$file]->new_version) && version_compare(GOTMLS_Version, $current->response[$file]->new_version, "<")) {
		$new_version = sprintf(__("Upgrade to %s now!",'gotmls'), $current->response[$file]->new_version).'<br /><br />';
		$Update_Link .= wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=').$file, 'upgrade-plugin_'.$file);
	}
	$Update_Link .= "\">$new_version</a></div>";
	$defLatest = (is_numeric($Latest = preg_replace('/[^0-9]/', "", GOTMLS_sexagesimal($GLOBALS["GOTMLS"]["tmp"]["Definition"]["Latest"]))) && is_numeric($Default = preg_replace('/[^0-9]/', "", GOTMLS_sexagesimal($GLOBALS["GOTMLS"]["tmp"]["Definition"]["Default"]))) && $Latest > $Default)?1:0;
	if (is_array($keys = maybe_unserialize(get_option('GOTMLS_Installation_Keys', array()))) && array_key_exists(GOTMLS_installation_key, $keys))
		$isRegistered = $keys[GOTMLS_installation_key];
	else
		$isRegistered = "";
	$Update_Div ='<div id="findUpdates" style="display: none;"><center>'.__("Searching for updates ...",'gotmls').'<br /><img src="'.GOTMLS_images_path.'wait.gif" height=16 width=16 alt="Wait..." /><br /><input type="button" value="Cancel" onclick="cancelserver(\'findUpdates\');" /></center></div>';
	$php_version = "<li>PHP: <span class='GOTMLS_date'>".phpversion()."</span></li>\n";
	if (isset($_SERVER["SERVER_SOFTWARE"]) && preg_match('/Apache\/([0-9\.]+)/i', $_SERVER["SERVER_SOFTWARE"], $GLOBALS["GOTMLS"]["tmp"]["apache"]) && count($GLOBALS["GOTMLS"]["tmp"]["apache"]) > 1)
		$php_version .= "<li>Apache: <span class='GOTMLS_date'>".$GLOBALS["GOTMLS"]["tmp"]["apache"][1]."</span></li>\n";
	elseif  (isset($_SERVER["SERVER_SOFTWARE"]) && strlen($_SERVER["SERVER_SOFTWARE"]))
		$php_version .= "<li>".$_SERVER["SERVER_SOFTWARE"]."</li>\n";
	if ((isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["you"]["user_email"]) && strlen($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["you"]["user_email"]) == 32)) {
		$reg_email_key = $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["you"]["user_email"];
		if ($reg_email_key == md5($current_user->user_email))
			$isRegistered = $current_user->user_email;
		elseif (!($isRegistered = $wpdb->get_var("SELECT `user_nicename` FROM $wpdb->users WHERE MD5(`user_email`) = '".$reg_email_key."'")))
			$isRegistered = '<img src="//gravatar.com/avatar/'.$user_info["user_email"].'?s=32" border="0" alt="Unknown User">Unknown User';
	} else
		$reg_email_key = "";
	$head_nonce = GOTMLS_set_nonce(__FUNCTION__."141");
	echo '
span.GOTMLS_date {float: right; width: 130px; white-space: nowrap;}
.GOTMLS_page {float: left; border-radius: 10px; padding: 0 5px;}
.GOTMLS_quarantine_item {margin: 4px 12px;}
.rounded-corners {margin: 10px; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border: 1px solid #000;}
.shadowed-box {box-shadow: -3px 3px 3px #666; -moz-box-shadow: -3px 3px 3px #666; -webkit-box-shadow: -3px 3px 3px #666;}
.sidebar-box {background-color: #CCC;}
.GOTMLS-scanlog li a {display: none;}
.GOTMLS-scanlog li:hover a {display: block;}
.GOTMLS-sidebar-links {list-style: none;}
.GOTMLS-sidebar-links li img {margin: 3px; height: 16px; vertical-align: middle;}
.GOTMLS-sidebar-links li {margin-bottom: 0 !important;}
.popup-box {background-color: #FFC; display: none; position: absolute; left: 0px; z-index: 10;}
.shadowed-text {text-shadow: #00F -1px 1px 1px;}
.sub-option {float: left; margin: 3px 5px;}
.inside p {margin: 10px;}
.GOTMLS_li, .GOTMLS_plugin li {list-style: none;}
.GOTMLS_plugin {margin: 5px; background: #cfc; border: 1px solid #0C0; padding: 0 5px; border-radius: 3px;}
.GOTMLS_plugin.known, .GOTMLS_plugin.backdoor, .GOTMLS_plugin.htaccess, .GOTMLS_plugin.timthumb, .GOTMLS_plugin.errors {background: #f99; border: 1px solid #f00;}
.GOTMLS_plugin.potential, .GOTMLS_plugin.wp_core, .GOTMLS_plugin.skipdirs, .GOTMLS_plugin.skipped {background: #ffc; border: 1px solid #fc6;}
.GOTMLS ul li {margin-left: 12px;}
.GOTMLS h2 {margin: 0 0 10px;}
.postbox {margin-right: 10px;}
#pastDonations li {list-style: none;}
#quarantine_buttons {position: absolute; right: 0px; top: -54px; margin: 0px; padding: 0px;}
#quarantine_buttons input.button-primary {margin-right: 20px;}
#delete_button {
	background-color: #C33;
	color: #FFF;
	background-image: linear-gradient(to bottom, #C22, #933);
	border-color: #933 #933 #900;
	box-shadow: 0 1px 0 rgba(230, 120, 120, 0.5) inset;
	text-decoration: none; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
	margin-top: 10px;
}
#main-page-title {
	background: url("'.$GLOBALS["GOTMLS"]["tmp"]["protocol"].'//gravatar.com/avatar/5feb789dd3a292d563fea3b885f786d6?s=64") no-repeat scroll 0 0 transparent;
	height: 64px;
	line-height: 58px;
	margin: 10px 0 0 0;
	max-width: 600px;
	padding: 0 110px 0 84px;
}
#main-page-title h1 {
	background: url("'.$GLOBALS["GOTMLS"]["tmp"]["protocol"].'//gravatar.com/avatar/8151cac22b3fc543d099241fd573d176?s=64") no-repeat scroll top right transparent;
	height: 64px;
	line-height: 32px;
	margin: 0;
	padding: 0 84px 0 0;
	display: table-cell;
    text-align: center;
    vertical-align: middle;
}
</style>
<div id="div_file" class="shadowed-box rounded-corners sidebar-box" style="padding: 0; display: none; position: fixed; top: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1].'; left: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0].'; width: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3].'; height: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2].'; border: solid #c00; z-index: 112358;"><table style="width: 100%; height: 100%;" cellspacing="0" cellpadding="0"><tr><td style="border-bottom: 1px solid #EEE; height: 32px;" colspan="2">'.GOTMLS_close_button("div_file").'<h3 onmousedown="grabDiv();" onmouseup="releaseDiv();" id="windowTitle" style="cursor: move; border-bottom: 0px none; z-index: 2345677; position: absolute; left: 0px; top: 0px; margin: 0px; padding: 6px; width: 90%; height: 20px;">'.GOTMLS_Loading_LANGUAGE.'</h3></td></tr><tr><td colspan="2" style="height: 100%"><div style="width: 100%; height: 100%; position: relative; padding: 0; margin: 0;" class="inside"><br /><br /><center><img src="'.GOTMLS_images_path.'wait.gif" height=16 width=16 alt="..."> '.GOTMLS_Loading_LANGUAGE.'<br /><br /><input type="button" onclick="showhide(\'GOTMLS_iFrame\', true);" value="'.__("If this is taking too long, click here.",'gotmls').'" class="button-primary" /></center><iframe id="GOTMLS_iFrame" name="GOTMLS_iFrame" style="top: 0px; left: 0px; position: absolute; width: 100%; height: 100%; background-color: #CCC;"></iframe></td></tr><tr><td style="height: 20px;"><iframe id="GOTMLS_statusFrame" name="GOTMLS_statusFrame" style="width: 100%; height: 20px; background-color: #CCC;"></iframe></div></td><td style="height: 20px; width: 20px;"><h3 id="cornerGrab" onmousedown="grabCorner();" onmouseup="releaseCorner();" style="cursor: move; height: 24px; width: 24px; margin: 0; padding: 0; z-index: 2345678; overflow: hidden; position: absolute; right: 0px; bottom: 0px;"><span class="dashicons dashicons-editor-expand"></span>&#8690;</h3></td></tr></table></div>
<script type="text/javascript">
function showhide(id) {
	divx = document.getElementById(id);
	if (divx) {
		if (divx.style.display == "none" || arguments[1]) {
			divx.style.display = "block";
			divx.parentNode.className = (divx.parentNode.className+"close").replace(/close/gi,"");
			return true;
		} else {
			divx.style.display = "none";
			return false;
		}
	}
}
function checkAllFiles(check) {
	var checkboxes = new Array(); 
	checkboxes = document["GOTMLS_Form_clean"].getElementsByTagName("input");
	for (var i=0; i<checkboxes.length; i++)
		if (checkboxes[i].type == "checkbox")
			checkboxes[i].checked = check;
}
function setvalAllFiles(val) {
	var checkboxes = document.getElementById("GOTMLS_fixing");
	if (checkboxes)
		checkboxes.value = val;
}
function getWindowWidth(min) {
	if (typeof window.innerWidth != "undefined" && window.innerWidth > min)
		min = window.innerWidth;
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientWidth != "undefined" && document.documentElement.clientWidth > min)
		min = document.documentElement.clientWidth;
	else if (typeof document.getElementsByTagName("body")[0].clientWidth != "undefined" && document.getElementsByTagName("body")[0].clientWidth > min)
		min = document.getElementsByTagName("body")[0].clientWidth;
	return min;
}
function getWindowHeight(min) {
	if (typeof window.innerHeight != "undefined" && window.innerHeight > min)
		min = window.innerHeight;
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientHeight != "undefined" && document.documentElement.clientHeight > min)
		min = document.documentElement.clientHeight;
	else if (typeof document.getElementsByTagName("body")[0].clientHeight != "undefined" && document.getElementsByTagName("body")[0].clientHeight > min)
		min = document.getElementsByTagName("body")[0].clientHeight;
	return min;
}
function loadIframe(title) {
	showhide("GOTMLS_iFrame", true);
	showhide("GOTMLS_iFrame");
	document.getElementById("windowTitle").innerHTML = title;
	if (curDiv) {
		windowW = getWindowWidth(200);
		windowH = getWindowHeight(200);
		if (windowW > 200)
			windowW -= 30;
		if (windowH > 200)
			windowH -= 20;
		if (px2num(curDiv.style.width) > windowW) {
			curDiv.style.width = windowW + "px";
			curDiv.style.left = "0px";
		} else if ((px2num(curDiv.style.left) + px2num(curDiv.style.width)) > windowW) {
			curDiv.style.left = (windowW - px2num(curDiv.style.width)) + "px";
		}
		if (px2num(curDiv.style.height) > windowH) {
			curDiv.style.height = windowH + "px";
			curDiv.style.top = "0px";
		} else if ((px2num(curDiv.style.top) + px2num(curDiv.style.height)) > windowH) {
			curDiv.style.top = (windowH - px2num(curDiv.style.height)) + "px";
		}
		if (px2num(curDiv.style.left) < 0)
			curDiv.style.left = "0px";
		if (px2num(curDiv.style.top)< 0)
			curDiv.style.top = "0px";
	}
	showhide("div_file", true);
	if (IE)
		curDiv.scrollIntoView(true);
}
function cancelserver(divid) {
	document.getElementById(divid).innerHTML = "<div class=\'error\'>'. __("No response from server!",'gotmls').'</div>";
}
function checkupdateserver(server, divid) {
	var updatescript = document.createElement("script");
	updatescript.setAttribute("src", server);
	divx = document.getElementById(divid);
	if (divx) {
		divx.appendChild(updatescript);
		if (arguments[2])
			return setTimeout("stopCheckingDefinitions = checkupdateserver(\'"+arguments[2]+"\',\'"+divid+"\')",15000);
		else
			return setTimeout("cancelserver(\'"+divid+"\')",'.($GLOBALS["GOTMLS"]["tmp"]['execution_time']+1).'000+3000);
	}
}
var IE = document.all?true:false;
if (!IE) document.captureEvents(Event.MOUSEMOVE)
document.onmousemove = getMouseXY;
var offsetX = 0;
var offsetY = 0;
var offsetW = 0;
var offsetH = 0;
var curX = 0;
var curY = 0;
var curDiv;
function getMouseXY(e) {
	if (IE) { // grab the mouse pos if browser is IE
		curX = event.clientX + document.body.scrollLeft;
		curY = event.clientY + document.body.scrollTop;
	} else {  // grab the mouse pos if browser is Not IE
		curX = e.pageX - document.body.scrollLeft;
		curY = e.pageY - document.body.scrollTop;
	}
	if (curX < 0) {curX = 0;}
	if (curY < 0) {curY = 0;}
	if (offsetX && curX > 10) {curDiv.style.left = (curX - offsetX)+"px";}
	if (offsetY && (curY - offsetY) > 0) {curDiv.style.top = (curY - offsetY)+"px";}
	if (offsetW && (curX - offsetW) > 360) {curDiv.style.width = (curX - offsetW)+"px";}
	if (offsetH && (curY - offsetH) > 200) {curDiv.style.height = (curY - offsetH)+"px";}
	return true;
}
function px2num(px) {
	return parseInt(px.substring(0, px.length - 2), 10);
}
function setDiv(DivID) {
	if (curDiv = document.getElementById(DivID)) {
		if (IE)
			curDiv.style.position = "absolute";
		curDiv.style.left = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0].'";
		curDiv.style.top = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1].'";
		curDiv.style.height = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2].'";
		curDiv.style.width = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3].'";
	}
}
function grabDiv() {
	corner = document.getElementById("windowTitle");
	if (corner) {
		corner.style.width="100%";
		corner.style.height="100%";
	}
	offsetX=curX-px2num(curDiv.style.left); 
	offsetY=curY-px2num(curDiv.style.top);
}
function releaseDiv() {
	corner = document.getElementById("windowTitle");
	if (corner) {
		corner.style.width="90%";
		corner.style.height="20px";
	}
	document.getElementById("GOTMLS_statusFrame").src = "'.admin_url('admin-ajax.php?action=GOTMLS_position&'.$head_nonce.'&GOTMLS_x=').'"+curDiv.style.left+"&GOTMLS_y="+curDiv.style.top;
	offsetX=0; 
	offsetY=0;
}
function grabCorner() {
	corner = document.getElementById("cornerGrab");
	if (corner) {
		corner.style.width="100%";
		corner.style.height="100%";
	}
	offsetW=curX-px2num(curDiv.style.width); 
	offsetH=curY-px2num(curDiv.style.height);
}
function releaseCorner() {
	corner = document.getElementById("cornerGrab");
	if (corner) {
		corner.style.width="20px";
		corner.style.height="20px";
	}
	document.getElementById("GOTMLS_statusFrame").src = "'.admin_url('admin-ajax.php?action=GOTMLS_position&'.$head_nonce.'&GOTMLS_w=').'"+curDiv.style.width+"&GOTMLS_h="+curDiv.style.height;
	offsetW=0; 
	offsetH=0;
}
setDiv("div_file");
</script>
<div id="main-page-title"><h1 style="vertical-align: middle;">Anti-Malware from&nbsp;GOTMLS.NET</h1></div>
<div id="admin-page-container">
<div id="GOTMLS-right-sidebar" style="width: 300px;" class="metabox-holder">
	'.GOTMLS_box(__("Updates & Registration",'gotmls'), "<ul>$php_version<li>WordPress: <span class='GOTMLS_date'>$wp_version</span></li>\n<li>Plugin: <span class='GOTMLS_date'>".GOTMLS_Version.'</span></li>
<li><div id="GOTMLS_Key" style="margin: 0;'.((!$defLatest && !$isRegistered)?' display: none;">Key: <span style="float: right;">'.GOTMLS_installation_key.'</span></div><div style="':'">Key: <span style="float: right;">'.GOTMLS_installation_key.'</span></div><div style="display: none;').'"><form method="POST" action="'.admin_url('admin-ajax.php?'.$head_nonce).'" target="GOTMLS_iFrame" name="GOTMLS_Form_lognewkey"><input type="hidden" name="GOTMLS_installation_key" value="'.GOTMLS_installation_key.'"><input type="hidden" name="action" value="GOTMLS_lognewkey"><span style="color: #F00;" id="GOTMLS_No_Key">No Key! <input type="submit" style="float: right;" value="'.__("Get FREE Key!",'gotmls').'" class="button-primary" onclick="showhide(\'GOTMLS_No_Key\');showhide(\'GOTMLS_Key\', true);check_for_updates(\'Definition_Updates\');" /></span></form></div>'.($isRegistered?'</li><li>Registered to: '.$isRegistered:"").'</li>
<li>Definitions: <span id="GOTMLS_definitions_date" class="GOTMLS_date">'.$GLOBALS["GOTMLS"]["tmp"]["Definition"]["Latest"].'</span></li></ul>
	<form id="updateform" method="post" name="updateform" action="'.str_replace("GOTMLS_mt=", "GOTMLS_last_mt=", GOTMLS_script_URI).'&'.$head_nonce.'">
		<img style="display: none; float: left; margin-right: 4px;" src="'.GOTMLS_images_path.'checked.gif" height=16 width=16 alt="definitions updated" id="autoUpdateDownload" onclick="showhide(\'autoUpdateForm\', true); showhide(\'registerKeyForm\', true); showhide(\'clear_updates\', true); getElementById(\'registerFormMessage\').innerHTML = \'<p>You can change your registered email here if you want.</p>\';">
		'.str_replace('findUpdates', 'Definition_Updates', $Update_Div).'
		<div id="autoUpdateForm" style="display: none;">
		<input type="submit" style="width: 100%;" name="auto_update" value="'.__("Download new definitions!",'gotmls').'"> 
		</div>
	</form>
	<form id="clearupdateform" method="post" name="updateform" action="'.str_replace("GOTMLS_mt=", "GOTMLS_last_mt=", GOTMLS_script_URI).'&'.$head_nonce.'">
		<input name="UPDATE_definitions_array" value="0" type="hidden">
		<input type="submit" style="display: none; width: 100%; color: #ff0; background-color: #c33" id="clear_updates" value="'.__("Delete ALL definitions!",'gotmls').'"> 
	</form>
		<div id="registerKeyForm" style="display: none;"><span id="registerFormMessage" style="color: #F00">'.__("<p>Get instant access to definition updates.</p>",'gotmls').'</span><p>
'.__("If you have not already registered your Key then register now using the form below.<br />* All registration fields are required<br />** I will NOT share your information.",'gotmls').'</p>
<form id="registerform" onsubmit="return sinupFormValidate(this);" action="'.GOTMLS_plugin_home.'wp-login.php?action=register" method="post" name="registerform" target="GOTMLS_iFrame"><input type="hidden" name="redirect_to" id="register_redirect_to" value="/donate/"><input type="hidden" name="user_login" id="register_user_login" value=""><input type="hidden" name="old_user_email" id="old_user_email" value="'.$reg_email_key.'">
<div>'.__("Your Full Name:",'gotmls').'</div>
<div style="float: left; width: 50%;"><input style="width: 100%;" id="first_name" type="text" name="first_name" value="'.$current_user->user_firstname.'" /></div>
<div style="float: left; width: 50%;"><input style="width: 100%;" id="last_name" type="text" name="last_name" value="'.$current_user->user_lastname.'" /></div>
<div style="clear: left; width: 100%;">
<div>'.__("A password will be e-mailed to this address:",'gotmls').'</div>
<input style="width: 100%;" id="user_email" type="text" name="user_email" value="'.$current_user->user_email.'" /></div>
<div>
<div>'.__("Your WordPress Site URL:",'gotmls').'</div>
<input style="width: 100%;" id="user_url" type="text" name="user_url" value="'.GOTMLS_siteurl.'" readonly /></div>
<div>
<div>'.__("Plugin Installation Key:",'gotmls').'</div>
<input style="width: 100%;" id="installation_key" type="text" name="installation_key" value="'.GOTMLS_installation_key.'" readonly /><input id="old_key" type="hidden" name="old_key" value="'.md5($GOTMLS_url_parts[2]).'" /></div>
<input style="width: 100%;" id="wp-submit" type="submit" name="wp-submit" value="Register Now!" /></form></div>'.$Update_Link, "stuffbox").'
	<script type="text/javascript">
		var alt_addr = "'.$Update_Definitions[1].'";
		function check_for_updates(update_type) {
			showhide(update_type, true);
			stopCheckingDefinitions = checkupdateserver("'.$Update_Definitions[0].'", update_type, alt_addr);
		}
		function updates_complete(chk) {
			if (auto_img = document.getElementById("autoUpdateDownload")) {
				auto_img.style.display="block";
				check_for_donation(chk);
			}
		}
		function check_for_donation(chk) {
			if (document.getElementById("autoUpdateDownload").src.replace(/^.+\?/,"")=="0")
				if (chk.substr(0, 8) != "Changed " || chk.substr(8, 1) != "0")
					chk += "\\n\\n'.__("Please make a donation for the use of this wonderful feature!",'gotmls').'";
			alert(chk);
		}
		function sinupFormValidate(form) {
			var error = "";
			if(form["first_name"].value == "")	
				error += "'.__("First Name is a required field!",'gotmls').'\n";
			if(form["last_name"].value == "")		
				error += "'.__("Last Name is a required field!",'gotmls').'\n";
			if(form["user_email"].value == "")
				error += "'.__("Email Address is a required field!",'gotmls').'\n";
			else {
				if (uem = document.getElementById("register_user_login"))
					uem.value = form["user_email"].value;
				if (uem = document.getElementById("register_redirect_to"))
					uem.value = "/donate/?email="+form["user_email"].value.replace("@", "%40");
			}
			if(form["user_url"].value == "")
				error += "'.__("Your WordPress Site URL is a required field!",'gotmls').'\n";
			if(form["installation_key"].value == "")
				error += "'.__("Plugin Installation Key is a required field!",'gotmls').'\n";
			if(error != "") {
				alert(error);
				return false;
			} else {
				document.getElementById("Definition_Updates").innerHTML = \'<img src="'.GOTMLS_images_path.'wait.gif">'.__("Submitting Registration ...",'gotmls').'\';
				showhide("Definition_Updates", true);
				setTimeout(\'stopCheckingDefinitions = checkupdateserver("'.$Update_Definitions[0].'", "Definition_Updates", "'.$Update_Definitions[1].'")\', 3000);
				showhide("registerKeyForm");
				return true;
			}
		}
		var divNAtext = false;
		function loadGOTMLS() {
			clearTimeout(divNAtext);
			setDivNAtext();
			'.$GLOBALS["GOTMLS"]["tmp"]["onLoad"].'
		}
		if ('.($defLatest+strlen($isRegistered)).')
			check_for_updates("Definition_Updates");
/*		else
			showhide("registerKeyForm", true);*/
		if (divNAtext)
			loadGOTMLS();
		else
			divNAtext=true;
	</script>
	'.GOTMLS_box(__("Resources & Links",'gotmls'), '
			<div id="pastDonations"></div>
<form name="ppdform" id="ppdform" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="NKANR75NUL9WY">
<input type="hidden" name="on0" value="Contribution Level">
<center>
<input type="radio" name="os0" value="Basic">$15
<input type="radio" name="os0" value="Full" checked>$29
<input type="radio" name="os0" value="Double">$52
<input type="radio" name="os0" value="Elite">$100
<input type="radio" name="os0" value="Ninja">$200
</center>
<input type="hidden" name="currency_code" value="USD">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="no_note" value="1">
			<input type="hidden" name="tax" value="0">
			<input type="hidden" name="lc" value="US">
			<input type="hidden" name="item_name" value="Donation to Eli\'s Anti-Malware Plugin">
			<input type="hidden" name="item_number" value="GOTMLS-key-'.GOTMLS_installation_key.'">
			<input type="hidden" name="custom" value="key-'.GOTMLS_installation_key.'">
			<input type="hidden" name="notify_url" value="'.GOTMLS_plugin_home.GOTMLS_installation_key.'/ipn">
			<input type="hidden" name="page_style" value="GOTMLS">
			<input type="hidden" name="return" value="'.GOTMLS_plugin_home.'donate/?donation-source=paid">
			<input type="hidden" name="cancel_return" value="'.GOTMLS_plugin_home.'donate/?donation-source=cancel">
			<input type="image" id="pp_button" src="'.GOTMLS_images_path.'btn_donateCC_WIDE.gif" border="0" name="submitc" alt="'.__("Make a Donation with PayPal",'gotmls').'">
			<div>
				<ul class="GOTMLS-sidebar-links">
					<li style="float: right;"><b>on <a target="_blank" href="https://profiles.wordpress.org/scheeeli#content-plugins">WordPress.org</a></b><ul class="GOTMLS-sidebar-links">
						<li><a target="_blank" href="https://wordpress.org/plugins/gotmls/faq/">Plugin FAQs</a></li>
						<li><a target="_blank" href="https://wordpress.org/support/plugin/gotmls">Forum Posts</a></li>
						<li><a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/gotmls">Plugin Reviews</a></li>
					</ul></li>
					<li><img src="//gravatar.com/avatar/5feb789dd3a292d563fea3b885f786d6?s=16" border="0" alt="Plugin site:"><b><a target="_blank" href="'.GOTMLS_plugin_home.'">GOTMLS.NET</a></b></li>
					<li><img src="//gravatar.com/avatar/8151cac22b3fc543d099241fd573d176?s=16" border="0" alt="Developer site:"><b><a target="_blank" href="http://wordpress.ieonly.com/category/my-plugins/anti-malware/">Eli\'s Blog</a></b></li>
					<li><img src="https://ssl.gstatic.com/ui/v1/icons/mail/favicon.ico" border="0" alt="mail:"><b><a target="_blank" href="mailto:eli@gotmls.net">Email Eli</a></b></li>
					<li><iframe allowtransparency="true" frameborder="0" scrolling="no" src="https://platform.twitter.com/widgets/follow_button.html?screen_name=GOTMLS&amp;show_count=false" style="width:125px; height:20px;"></iframe></li>
				</ul>
			</div>
			</form>
			<a target="_blank" href="https://www.google.com/transparencyreport/safebrowsing/diagnostic/index.html#url='.urlencode(GOTMLS_siteurl).'">Google Safe Browsing Diagnostic</a>', "stuffbox").//GOTMLS_box(__("Last Scan Status",'gotmls'), GOTMLS_scan_log(), "stuffbox").
	$optional_box.'
</div>';
	if (isset($GLOBALS["GOTMLS"]["tmp"]["stuffbox"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["stuffbox"])) {
		echo '
<script type="text/javascript">
function stuffbox_showhide(id) {
	divx = document.getElementById(id);
	if (divx) {
		if (divx.style.display == "none" || arguments[1]) {';
		$else = '
			if (divx = document.getElementById("GOTMLS-right-sidebar"))
				divx.style.width = "30px";
			if (divx = document.getElementById("GOTMLS-main-section"))
				divx.style.marginRight = "30px";';
		foreach ($GLOBALS["GOTMLS"]["tmp"]["stuffbox"] as $md5 => $bTitle) {
			echo "\nif (divx = document.getElementById('inside_$md5'))\n\tdivx.style.display = 'block';\nif (divx = document.getElementById('title_$md5'))\n\tdivx.innerHTML = '".GOTMLS_strip4java($bTitle, true)."';";
			$else .= "\nif (divx = document.getElementById('inside_$md5'))\n\tdivx.style.display = 'none';\nif (divx = document.getElementById('title_$md5'))\n\tdivx.innerHTML = '".substr($bTitle, 0, 1)."';";
		}
		echo '
			if (divx = document.getElementById("GOTMLS-right-sidebar"))
				divx.style.width = "300px";
			if (divx = document.getElementById("GOTMLS-main-section"))
				divx.style.marginRight = "300px";
			return true;
		} else {'.$else.'
			return false;
		}
	}
}
if (getWindowWidth(780) == 780) 
	setTimeout("stuffbox_showhide(\'inside_'.$md5.'\')", 200);
</script>';
	}
	echo '
	<div id="GOTMLS-main-section" style="margin-right: 300px;">
		<div class="metabox-holder GOTMLS" style="width: 100%;" id="GOTMLS-metabox-container">';
}

function GOTMLS_box($bTitle, $bContents, $bType = "postbox") {
	$md5 = md5($bTitle);
	if (isset($GLOBALS["GOTMLS"]["tmp"]["$bType"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["$bType"]))
		$GLOBALS["GOTMLS"]["tmp"]["$bType"]["$md5"] = "$bTitle";
	else
		$GLOBALS["GOTMLS"]["tmp"]["$bType"] = array("$md5"=>"$bTitle");
	return '
	<div id="box_'.$md5.'" class="'.$bType.'"><h3 title="Click to toggle" onclick="if (typeof '.$bType.'_showhide == \'function\'){'.$bType.'_showhide(\'inside_'.$md5.'\');}else{showhide(\'inside_'.$md5.'\');}" style="cursor: pointer;" class="hndle"><span id="title_'.$md5.'">'.$bTitle.'</span></h3>
		<div id="inside_'.$md5.'" class="inside">
'.$bContents.'
		</div>
	</div>';
}

function GOTMLS_get_scanlog() {
	global $wpdb;
	$LastScan = '';
	if (isset($_GET["GOTMLS_cl"]) && GOTMLS_get_nonce()) {
		$SQL = $wpdb->prepare("DELETE FROM `$wpdb->options` WHERE option_name LIKE %s AND substring_index(option_name, '/', -1) < %s", 'GOTMLS_scan_log/%', $_GET["GOTMLS_cl"]);
		if ($cleared = $wpdb->query($SQL))
			$LastScan .= sprintf(__("Cleared %s records from this log.",'gotmls'), $cleared);
//		else $LastScan .= $wpdb->last_error."<li>$SQL</li>";
	}
	$SQL = "SELECT substring_index(option_name, '/', -1) AS `mt`, option_name, option_value FROM `$wpdb->options` WHERE option_name LIKE 'GOTMLS_scan_log/%' ORDER BY mt DESC";
	if ($rs = $wpdb->get_results($SQL, ARRAY_A)) {
		$units = array("seconds"=>60,"minutes"=>60,"hours"=>24,"days"=>365,"years"=>10);
		$LastScan .= '<ul class="GOTMLS-scanlog GOTMLS-sidebar-links">';
		foreach ($rs as $row) {
			$LastScan .= "\n<li>";
			$GOTMLS_scan_log = (isset($row["option_name"])?get_option($row["option_name"], array()):array());
			if (isset($GOTMLS_scan_log["scan"]["type"]) && strlen($GOTMLS_scan_log["scan"]["type"]))
				$LastScan .= htmlentities($GOTMLS_scan_log["scan"]["type"]);
			else
				$LastScan .= "Unknown scan type";
			if (isset($GOTMLS_scan_log["scan"]["dir"]) && is_dir($GOTMLS_scan_log["scan"]["dir"]))
				$LastScan .= " of ".basename($GOTMLS_scan_log["scan"]["dir"]);
			if (isset($GOTMLS_scan_log["scan"]["start"]) && is_numeric($GOTMLS_scan_log["scan"]["start"])) {
				$time = (time() - $GOTMLS_scan_log["scan"]["start"]);
				$ukeys = array_keys($units);
				for ($unit = $ukeys[0], $key=0; (isset($units[$ukeys[$key]]) && $key < (count($ukeys) - 1) && $time >= $units[$ukeys[$key]]); $unit = $ukeys[++$key])
					$time = floor($time/$units[$ukeys[$key]]);
				if (1 == $time)
					$unit = substr($unit, 0, -1);
				$LastScan .= " started $time $unit ago";
				if (isset($GOTMLS_scan_log["scan"]["finish"]) && is_numeric($GOTMLS_scan_log["scan"]["finish"]) && ($GOTMLS_scan_log["scan"]["finish"] >= $GOTMLS_scan_log["scan"]["start"])) {
					$time = ($GOTMLS_scan_log["scan"]["finish"] - $GOTMLS_scan_log["scan"]["start"]);
					for ($unit = $ukeys[0], $key=0; (isset($units[$ukeys[$key]]) && $key < (count($ukeys) - 1) && $time >= $units[$ukeys[$key]]); $unit = $ukeys[++$key])
						$time = floor($time/$units[$ukeys[$key]]);
					if (1 == $time)
						$unit = substr($unit, 0, -1);
					if ($time)
						$LastScan .= " and ran for $time $unit";
					else
						$LastScan = str_replace("started", "ran", $LastScan);
				} else
					$LastScan .= " and has not finish";
			} else
				$LastScan .= " failed to started";
			$LastScan .= '<a href="'.GOTMLS_script_URI.'&GOTMLS_cl='.$row["mt"].'&'.GOTMLS_set_nonce(__FUNCTION__."600").'">[clear log below this entry]</a></li>';
		}
		$LastScan .= '</ul>';
	} else
		$LastScan .= '<h3>'.__("No Scans have been logged",'gotmls').'</h3>';
	return "$LastScan\n";
}

function GOTMLS_get_whitelists() {
	$Q_Page = '';
	if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"])) {
		$Q_Page .= '<ul name="found_Quarantine" id="found_Quarantine" class="GOTMLS_plugin known" style="background-color: #ccc; padding: 0;"><h3>'.__("Globally White-listed files",'gotmls').'<span class="GOTMLS_date">'.__("# of patterns",'gotmls').'</span><span class="GOTMLS_date">'.__("Date Updated",'gotmls').'</span></h3>';
		foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"] as $file => $non_threats) {
			if (isset($non_threats[0])) {
				$updated = GOTMLS_sexagesimal($non_threats[0]);
				unset($non_threats[0]);
			} else
				$updated = "Unknown";
			$Q_Page .= '<li style="margin: 4px 12px;"><span class="GOTMLS_date">'.count($non_threats).'</span><span class="GOTMLS_date">'.$updated."</span>$file</li>\n";
		}
		if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"])) {
			$Q_Page .= '<h3>'.__("WordPress Core files",'gotmls').'<span class="GOTMLS_date">'.__("# of files",'gotmls').'</span></h3>';
			foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"] as $ver => $files) {
				$Q_Page .= '<li style="margin: 4px 12px;"><span class="GOTMLS_date">'.count($files)."</span>Version $ver</li>\n";
			}
		}
		$Q_Page .= "</ul>";
	}
	return "$Q_Page\n";
}

function GOTMLS_get_quarantine($only = false) {
	global $wpdb, $post;
	if (is_numeric($only))
		return get_post($only, ARRAY_A);
	elseif ($only)
		return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` = 'private'");
	else
		$args = array('posts_per_page' => (isset($_GET['posts_per_page'])&&is_numeric($_GET['posts_per_page'])&&$_GET['posts_per_page']>0?$_GET['posts_per_page']:200), 'orderby' => 'date', 'post_type' => 'GOTMLS_quarantine', "post_status" => "private");
	if (isset($_POST["paged"]))
		$args["paged"] = $_POST["paged"];
	$my_query = new WP_Query($args);
	$Q_Paged = '<form method="POST" name="GOTMLS_Form_page"><input type="hidden" id="GOTMLS_paged" name="paged" value="1"><div style="float: left;">Page:</div>';
	$Q_Page = '
	<form method="POST" action="'.admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."700")).(isset($_SERVER["QUERY_STRING"])&&strlen($_SERVER["QUERY_STRING"])?"&".$_SERVER["QUERY_STRING"]:"").'" target="GOTMLS_iFrame" name="GOTMLS_Form_clean"><input type="hidden" id="GOTMLS_fixing" name="GOTMLS_fixing" value="1"><input type="hidden" name="action" value="GOTMLS_fix">';
	if ($my_query->have_posts()) {
		$Q_Page .= '<p id="quarantine_buttons" style="display: none;"><input id="repair_button" type="submit" value="'.__("Restore selected files",'gotmls').'" class="button-primary" onclick="if (confirm(\''.__("Are you sure you want to overwrite the previously cleaned files with the selected files in the Quarantine?",'gotmls').'\')) { setvalAllFiles(1); loadIframe(\'File Restoration Results\'); } else return false;" /><input id="delete_button" type="submit" class="button-primary" value="'.__("Delete selected files",'gotmls').'" onclick="if (confirm(\''.__("Are you sure you want to permanently delete the selected files in the Quarantine?",'gotmls').'\')) { setvalAllFiles(2); loadIframe(\'File Deletion Results\'); } else return false;" /></p><p><b>'.__("The following items have been found to contain malicious code, they have been cleaned, and the original infected file contents have been saved here in the Quarantine. The code is safe here and you do not need to do anything further with these files.",'gotmls').'</b></p>
		<ul name="found_Quarantine" id="found_Quarantine" class="GOTMLS_plugin known" style="background-color: #ccc; padding: 0;"><h3 style="margin: 8px 12px;">'.($my_query->post_count>1?'<input type="checkbox" onchange="checkAllFiles(this.checked); document.getElementById(\'quarantine_buttons\').style.display = \'block\';"> '.sprintf(__("Check all %d",'gotmls'),$my_query->post_count):"").__(" Items in Quarantine",'gotmls').'<span class="GOTMLS_date">'.__("Quarantined",'gotmls').'</span><span class="GOTMLS_date">'.__("Date Infected",'gotmls').'</span></h3>';
		$root_path = implode(GOTMLS_slash(), array_slice(GOTMLS_explode_dir(__FILE__), 0, (2 + intval($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"])) * -1));
		while ($my_query->have_posts()) {
			$my_query->the_post();
			$Q_Page .= '
			<li id="GOTMLS_quarantine_'.$post->ID.'" class="GOTMLS_quarantine_item"><span class="GOTMLS_date">'.$post->post_date_gmt.'</span><span class="GOTMLS_date">'.$post->post_modified_gmt.'</span><input type="checkbox" name="GOTMLS_fix[]" value="'.$post->ID.'" id="check_'.$post->ID.'" onchange="document.getElementById(\'quarantine_buttons\').style.display = \'block\';" /><img src="'.GOTMLS_images_path.'blocked.gif" height=16 width=16 alt="Q">'.GOTMLS_error_link(__("View Quarantined File",'gotmls'), $post->ID).str_replace($root_path, "...", $post->post_title)."</a></li>\n";
		}
		$Q_Page .= "\n</ul>";
		for ($p = 1; $p <= $my_query->max_num_pages; $p++) {
			$Q_Paged .= '<input class="GOTMLS_page" type="submit" value="'.$p.'"'.((isset($_POST["paged"]) && $_POST["paged"] == $p) || (!isset($_POST["paged"]) && 1 == $p)?" DISABLED":"").' onclick="document.getElementById(\'GOTMLS_paged\').value = \''.$p.'\';">';
		}
	} else
		$Q_Page .= '<h3>'.__("No Items in Quarantine",'gotmls').'</h3>';
	wp_reset_query();
	$return = "$Q_Paged\n</form><br style=\"clear: left;\" />\n$Q_Page\n</form>\n$Q_Paged\n</form><br style=\"clear: left;\" />\n";
	if (($trashed = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` != 'private'")) > 1)
		$return = '<a href="'.admin_url('admin-ajax.php?action=GOTMLS_empty_trash&'.GOTMLS_set_nonce(__FUNCTION__."720")).'" id="empty_trash_link" style="float: right;" target="GOTMLS_statusFrame">['.sprintf(__("Clear %s Deleted Files from the Trash",'gotmls'), $trashed)."]</a>$return";
	return $return;
}

function GOTMLS_View_Quarantine() {
	GOTMLS_update_definitions();
	$echo = GOTMLS_box($Q_Page = __("White-lists",'gotmls'), GOTMLS_get_whitelists());
	if (!isset($_GET['Whitelists']))
		$echo .= "\n<script>\nshowhide('inside_".md5($Q_Page)."');\n</script>\n";
	$echo .= GOTMLS_box($Q_Page = __("Quarantine",'gotmls'), GOTMLS_get_quarantine());
	GOTMLS_display_header();
	echo $echo."\n</div></div></div>";
}

function GOTMLS_Firewall_Options() {
	global $current_user, $wpdb, $table_prefix;
	GOTMLS_update_definitions();
	GOTMLS_display_header();
	$GOTMLS_nonce_found = GOTMLS_get_nonce();
	$gt = ">";
	$lt = "<";
	$save_action = "";
	$patch_attr = array(
		array(
			"icon" => "blocked",
			"language" => __("Your WordPress Login page is susceptible to a brute-force attack (just like any other login page). These types of attacks are becoming more prevalent these days and can sometimes cause your server to become slow or unresponsive, even if the attacks do not succeed in gaining access to your site. Applying this patch will block access to the WordPress Login page whenever this type of attack is detected."),
			"status" => 'Not Installed',
			"action" => 'Install Patch'
		),
		array(
			"language" => __("Your WordPress site has the current version of my brute-force Login protection installed."),
			"action" => 'Uninstall Patch',
			"status" => 'Enabled',
			"icon" => "checked"
		),
		array(
			"language" => __("Your WordPress Login page has the old version of my brute-force protection installed. Upgrade this patch to improve the protection on the WordPress Login page and preserve the integrity of your WordPress core files."),
			"action" => 'Upgrade Patch',
			"status" => 'Out of Date',
			"icon" => "threat"
		)
	);
	$find = '|<Files[^>]+xmlrpc.php>(.+?)</Files>\s*(# END GOTMLS Patch to Block XMLRPC Access\s*)*|is';
	$deny = "\n<IfModule !mod_authz_core.c>\norder deny,allow\ndeny from all";
	$allow = "";
	if (isset($_SERVER["REMOTE_ADDR"])) {
		$deny .= "\nallow from ".$_SERVER["REMOTE_ADDR"];
		$allow .= " ".$_SERVER["REMOTE_ADDR"];
	}
	if (isset($_SERVER["SERVER_ADDR"])) {
		$deny .= "\nallow from ".$_SERVER["SERVER_ADDR"];
		$allow .= " ".$_SERVER["SERVER_ADDR"];
	}
	$deny .= "\n</IfModule>\n<IfModule mod_authz_core.c>\nRequire";
	if (strlen(trim($allow)) > 0)
		$deny .= " ip$allow";
	else
		$deny .= " all denied";
	$deny .= "\n</IfModule>";
	if (count($GLOBALS["GOTMLS"]["tmp"]["apache"]) > 1)
		$errdiv = "<!-- ".$GLOBALS["GOTMLS"]["tmp"]["apache"][0]." -->";
	else
		$errdiv = "<div class='error'>Unable to read Apache Version, this patch may not work!</div>";
	$patch_action = $lt.'form method="POST" name="GOTMLS_Form_XMLRPC_patch"'.$gt.$lt.'input type="hidden" name="'.str_replace('=', '" value="', GOTMLS_set_nonce(__FUNCTION__."1159")).'"'.$gt.$lt.'script'.$gt."\nfunction setFirewall(opt, val) {\n\tif (autoUpdateDownloadGIF = document.getElementById('fw_opt'))\n\t\tautoUpdateDownloadGIF.value = opt;\n\tif (autoUpdateDownloadGIF = document.getElementById('fw_val'))\n\t\tautoUpdateDownloadGIF.value = val;\n}\nfunction testComplete() {\nif (autoUpdateDownloadGIF = document.getElementById('autoUpdateDownload'))\n\tdonationAmount = autoUpdateDownloadGIF.src.replace(/^.+\?/,'');\nif ((autoUpdateDownloadGIF.src == donationAmount) || donationAmount=='0') {\n\tif (patch_searching_div = document.getElementById('GOTMLS_XMLRPC_patch_searching')) {\n\t\tif (autoUpdateDownloadGIF.src == donationAmount)\n\t\t\tpatch_searching_div.innerHTML = '<span style=\"color: #F00;\">".__("You must register and donate to use this feature!",'gotmls')."</span>';\n\t\telse\n\t\t\tpatch_searching_div.innerHTML = '<span style=\"color: #F00;\">".__("This feature is available to those who have donated!",'gotmls')."</span>';\n\t}\n} else {\n\tshowhide('GOTMLS_XMLRPC_patch_searching');\n\tshowhide('GOTMLS_XMLRPC_patch_button', true);\n}\n}\nwindow.onload=testComplete;\n$lt/script$gt$lt".'div style="padding: 0 30px;"'.$gt.$lt.'input type="hidden" name="GOTMLS_XMLRPC_patching" value="';
	$patch_found = false;
	$head = str_replace(array('|<Files[^>]+', '(.+?)', '\\s*(', '\\s*)*|is'), array("<Files ", "$deny\n", "\n", "\n"), $find);
	$htaccess = "";
	if (is_file(ABSPATH.'.htaccess'))
		if (($htaccess = @file_get_contents(ABSPATH.'.htaccess')) && strlen($htaccess))
			$patch_found = preg_match($find, $htaccess);
	if ($patch_found) {
		$errdiv = "";
		if ($GOTMLS_nonce_found && isset($_POST["GOTMLS_XMLRPC_patching"]) && ($_POST["GOTMLS_XMLRPC_patching"] < 0) && GOTMLS_file_put_contents(ABSPATH.'.htaccess', preg_replace($find, "", $htaccess)))
			$patch_action .= '1"'.$gt.$lt.'input style="float: right;" type="submit" value="Block XMLRPC Access" /'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'question.gif"'.$gt.$lt.'b'.$gt.'Block XMLRPC Access (Now Allowing Access';
		elseif ($GOTMLS_nonce_found && isset($_POST["GOTMLS_XMLRPC_patching"]) && ($_POST["GOTMLS_XMLRPC_patching"] < 0))
			$patch_action .= '-1"'.$gt.$lt.'input style="float: right;" type="submit" value="Unblock XMLRPC Access" /'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'threat.gif"'.$gt.$lt.'b'.$gt.'Block XMLRPC Access (Still Blocking: '.sprintf(__("Failed to remove XMLRPC Protection [.htaccess %s]",'gotmls'),(is_readable(ABSPATH.'.htaccess')?'read-'.(is_writable(ABSPATH.'.htaccess')?'write?':'only!'):"unreadable!").": ".strlen($htaccess).GOTMLS_fileperms(ABSPATH.'.htaccess'));
		else
			$patch_action .= '-1"'.$gt.$lt.'input style="float: right;" type="submit" value="Unblock XMLRPC Access" /'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'checked.gif"'.$gt.$lt.'b'.$gt.'Block XMLRPC Access (Currently Blocked';
	} else {
		if ($GOTMLS_nonce_found && isset($_POST["GOTMLS_XMLRPC_patching"]) && ($_POST["GOTMLS_XMLRPC_patching"] > 0) && GOTMLS_file_put_contents(ABSPATH.'.htaccess', "$head$htaccess")) {
			$patch_action .= '-1"'.$gt.$lt.'input style="float: right;" type="submit" value="Unblock XMLRPC Access" /'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'checked.gif"'.$gt.$lt.'b'.$gt.'Block XMLRPC Access (Now Blocked';
			$errdiv = "";
		} elseif ($GOTMLS_nonce_found && isset($_POST["GOTMLS_XMLRPC_patching"]) && ($_POST["GOTMLS_XMLRPC_patching"] > 0))
			$patch_action .= '1"'.$gt.$lt.'input style="float: right;" type="submit" value="Block XMLRPC Access" /'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'threat.gif"'.$gt.$lt.'b'.$gt.'Block XMLRPC Access (Still Allowing Access: '.sprintf(__("Failed to install XMLRPC Protection [.htaccess %s]",'gotmls'),(is_readable(ABSPATH.'.htaccess')?'read-'.(is_writable(ABSPATH.'.htaccess')?'write?':'only!'):"unreadable!").": ".strlen($htaccess).GOTMLS_fileperms(ABSPATH.'.htaccess'));
		else
			$patch_action .= '1"'.$gt.$lt.'input style="float: right;" type="submit" value="Block XMLRPC Access" /'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'question.gif"'.$gt.$lt.'b'.$gt.'Block XMLRPC Access (Currently Allowing Access';
	}
	$patch_action .= ")$errdiv$lt/b$gt$lt/p$gt".__("Most WordPress sites do not use the XMLRPC features and hack attempts on the xmlrpc.php file are more common then ever before. Even if there are no vulnerabilities for hackers to exploit, these attempts can cause slowness or downtime similar to a DDoS attack. This patch automatically blocks all external access to the xmlrpc.php file.",'gotmls').$lt.'/div'.$gt.$lt.'/form'.$gt.$lt.'hr /'.$gt;
	$patch_status = 0;
	$patch_found = -1;
	$find = "#if\s*\(([^\&]+\&\&)?\s*file_exists\((.+?)(safe-load|wp-login)\.php'\)\)\s*require(_once)?\((.+?)(safe-load|wp-login)\.php'\);#";
	$head = str_replace(array('#', '\\(', '\\)', '(_once)?', ')\\.', '\\s*', '(.+?)(', '|', '([^\\&]+\\&\\&)?'), array(' ', '(', ')', '_once', '.', ' ', '\''.dirname(__FILE__).'/', '/', '!in_array($_SERVER["REMOTE_ADDR"], array("'.$_SERVER["REMOTE_ADDR"].'")) &&'), $find);
	if (is_file(ABSPATH.'../wp-config.php') && !is_file(ABSPATH.'wp-config.php'))
		$wp_config = '../wp-config.php';
	else
		$wp_config = 'wp-config.php';
	if (is_file(ABSPATH.$wp_config)) {
		if (($config = @file_get_contents(ABSPATH.$wp_config)) && strlen($config)) {
			if ($patch_found = preg_match($find, $config)) {
				if (strpos($config, substr($head, strpos($head, "file_exists")))) {
					if ($GOTMLS_nonce_found && isset($_POST["GOTMLS_patching"]) && GOTMLS_file_put_contents(ABSPATH.$wp_config, preg_replace('#'.$lt.'\?[ph\s]+(//.*\s*)*\?'.$gt.'#i', "", preg_replace($find, "", $config))))
						$patch_action .= $lt.'div class="error"'.$gt.__("Removed Brute-Force Protection",'gotmls').$lt.'/div'.$gt;
					else
						$patch_status = 1;
				} else {
					if ($GOTMLS_nonce_found && isset($_POST["GOTMLS_patching"]) && GOTMLS_file_put_contents(ABSPATH.$wp_config, preg_replace($find, "$head", $config))) {
						$patch_action .= $lt.'div class="updated"'.$gt.__("Upgraded Brute-Force Protection",'gotmls').$lt.'/div'.$gt;
						$patch_status = 1;
					} else
						$patch_status = 2;
				}
			} elseif ($GOTMLS_nonce_found && isset($_POST["GOTMLS_patching"]) && strlen($config) && ($patch_found == 0) && GOTMLS_file_put_contents(ABSPATH.$wp_config, "$lt?php$head// Load Brute-Force Protection by GOTMLS.NET before the WordPress bootstrap. ?$gt$config")) {
				$patch_action .= $lt.'div class="updated"'.$gt.__("Installed Brute-Force Protection",'gotmls').$lt.'/div'.$gt;
				$patch_status = 1;
			} elseif ($GOTMLS_nonce_found && isset($_POST["GOTMLS_patching"]))
				$patch_action .= $lt.'div class="updated"'.$gt.sprintf(__("Failed to install Brute-Force Protection (wp-config.php %s)",'gotmls'),(is_readable(ABSPATH.$wp_config)?'read-'.(is_writable(ABSPATH.$wp_config)?'write':'only'):"unreadable").": ".strlen($config).GOTMLS_fileperms(ABSPATH.$wp_config)).$lt.'/div'.$gt;
		} else
			$patch_action .= $lt.'div class="error"'.$gt.__("wp-config.php Not Readable!",'gotmls').$lt.'/div'.$gt;
	} else
		$patch_action .= $lt.'div class="error"'.$gt.__("wp-config.php Not Found!",'gotmls').$lt.'/div'.$gt;
	if ($GOTMLS_nonce_found && file_exists(ABSPATH.'wp-login.php') && ($login = @file_get_contents(ABSPATH.'wp-login.php')) && strlen($login) && (preg_match($find, $login))) {
		if (isset($_POST["GOTMLS_patching"]) && ($source = GOTMLS_get_URL("http://core.svn.wordpress.org/tags/".$wp_version.'/wp-login.php')) && (strlen($source) > 500) && GOTMLS_file_put_contents(ABSPATH.'wp-login.php', $source))
			$patch_action .= $lt.'div class="updated"'.$gt.__("Removed Old Brute-Force Login Patch",'gotmls').$lt.'/div'.$gt;
		else
			$patch_status = 2;
	}
	if ($GOTMLS_nonce_found && isset($_POST["GOTMLS_firewall_option"]) && strlen($_POST["GOTMLS_firewall_option"]) && isset($_POST["GOTMLS_firewall_value"]) && strlen($_POST["GOTMLS_firewall_value"])) {
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["firewall"][$_POST["GOTMLS_firewall_option"]] = $_POST["GOTMLS_firewall_value"];
		if (update_option("GOTMLS_settings_array", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]))
			$save_action = "\n{$lt}div onclick=\"this.style.display='none';\" style='position: relative; top: -40px; margin: 0 300px 0 130px;' class='updated'$gt\nSettings Saved!$lt/div$gt\n";
		else
			$save_action = "\n{$lt}div onclick=\"this.style.display='none';\" style='position: relative; top: -40px; margin: 0 300px 0 130px;' class='updated'$gt\nSave Failed!$lt/div$gt\n";
	}
	$sec_opts = $lt.'form method="POST" name="GOTMLS_Form_firewall"'.$gt.$lt.'input type="hidden" id="fw_opt" name="GOTMLS_firewall_option" value="traversal"'.$gt.$lt.'input type="hidden" name="GOTMLS_firewall_value" id="fw_val" value="0"'.$gt.$lt.'input type="hidden" name="'.str_replace('=', '" value="', GOTMLS_set_nonce(__FUNCTION__."805")).'"'.$gt;
	if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]) && array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]))
		foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"] as $TP => $VA)
			if (is_array($VA) && count($VA) > 3 && strlen($VA[1]) && strlen($VA[2]))
				$sec_opts .= $lt.'div style="padding: 0 30px;"'.$gt.$lt.'input type="submit" style="float: right;" value="'.(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["firewall"]["$TP"]) && $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["firewall"]["$TP"]?"Enable Protection\" onclick=\"setFirewall('$TP', 0);\"$gt$lt".'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'threat.gif"'.$gt.$lt."b$gt$VA[1] (Currently Disabled)":"Disable Protection\" onclick=\"setFirewall('$TP', 1);\"$gt$lt".'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'checked.gif"'.$gt.$lt."b$gt$VA[1] (Automatically Enabled)")."$lt/b$gt$lt/p$gt$VA[2]$lt/div$gt$lt".'hr /'.$gt;
	$sec_opts .= "$lt/form$gt\n$patch_action\n$lt".'form method="POST" name="GOTMLS_Form_patch"'.$gt.$lt.'div style="padding: 0 30px;"'.$gt.$lt.'input type="hidden" name="'.str_replace('=', '" value="', GOTMLS_set_nonce(__FUNCTION__."807")).'"'.$gt.$lt.'input type="submit" value="'.$patch_attr[$patch_status]["action"].'" style="float: right;'.($patch_status?'"'.$gt:' display: none;" id="GOTMLS_patch_button"'.$gt.$lt.'div id="GOTMLS_patch_searching" style="float: right;"'.$gt.__("Checking for session compatibility ...",'gotmls').' '.$lt.'img src="'.GOTMLS_images_path.'wait.gif" height=16 width=16 alt="Wait..." /'.$gt.$lt.'/div'.$gt).$lt.'input type="hidden" name="GOTMLS_patching" value="1"'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.$patch_attr[$patch_status]["icon"].'.gif"'.$gt.$lt.'b'.$gt.'Brute-force Protection '.$patch_attr[$patch_status]["status"].$lt.'/b'.$gt.$lt.'/p'.$gt.$patch_attr[$patch_status]["language"].__(" For more information on Brute-Force attack prevention and the WordPress wp-login-php file ",'gotmls').' '.$lt.'a target="_blank" href="http://gotmls.net/tag/wp-login-php/"'.$gt.__("read my blog",'gotmls')."$lt/a$gt.$lt/div$gt$lt/form$gt\n$lt"."script type='text/javascript'$gt\nfunction search_patch_onload() {\n\tstopCheckingSession = checkupdateserver('".GOTMLS_images_path."gotmls.js?SESSION=0', 'GOTMLS_patch_searching');\n}\nif (window.addEventListener)\n\twindow.addEventListener('load', search_patch_onload)\nelse\n\tdocument.attachEvent('onload', search_patch_onload);\n$lt/script$gt";
	$admin_notice = "";
	if ($current_user->user_login == "admin") {
		$admin_notice .= $lt.'hr /'.$gt;
		if ($GOTMLS_nonce_found && isset($_POST["GOTMLS_admin_username"]) && ($current_user->user_login != trim($_POST["GOTMLS_admin_username"])) && strlen(trim($_POST["GOTMLS_admin_username"])) && preg_match('/^\s*[a-z_0-9\@\.\-]{3,}\s*$/i', $_POST["GOTMLS_admin_username"])) {
				if ($wpdb->update($wpdb->users, array("user_login" => trim($_POST["GOTMLS_admin_username"])), array("user_login" => $current_user->user_login))) {
					$wpdb->query("UPDATE `{$table_prefix}sitemeta` SET `meta_value` = REPLACE(`meta_value`, 's:5:\"admin\";', 's:".strlen(trim($_POST["GOTMLS_admin_username"])).":\"".trim($_POST["GOTMLS_admin_username"])."\";') WHERE `meta_key` = 'site_admins' AND `meta_value` like '%s:5:\"admin\";%'");
					$admin_notice .= $lt.'div class="updated"'.$gt.sprintf(__("You username has been change to %s. Don't forget to use your new username when you login again.",'gotmls'), $_POST["GOTMLS_admin_username"]).$lt.'/div'.$gt;
				} else
					$admin_notice .= $lt.'div class="error"'.$gt.sprintf(__("SQL Error changing username: %s. Please try again later.",'gotmls'), $wpdb->last_error).$lt.'/div'.$gt;
		} else {
			if (isset($_POST["GOTMLS_admin_username"]))
				$admin_notice .= $lt.'div class="updated"'.$gt.sprintf(__("Your new username must be at least 3 characters and can only contain &quot;%s&quot;. Please try again.",'gotmls'), "a-z0-9_.-@").$lt.'/div'.$gt;
			$admin_notice .= $lt.'form method="POST" name="GOTMLS_Form_admin"'.$gt.$lt.'div style="float: right;"'.$gt.$lt.'div style="float: left;"'.$gt.__("Change your username:",'gotmls').$lt.'/div'.$gt.$lt.'input type="hidden" name="'.str_replace('=', '" value="', GOTMLS_set_nonce(__FUNCTION__."1235")).'"'.$gt.$lt.'input style="float: left;" type="text" id="GOTMLS_admin_username" name="GOTMLS_admin_username" size="6" value="'.$current_user->user_login.'"'.$gt.$lt.'input style="float: left;" type="submit" value="Change"'.$gt.$lt.'/div'.$gt.$lt.'div style="padding: 0 30px;"'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.'threat.gif"'.$gt.$lt.'b'.$gt.'Admin Notice'.$lt.'/b'.$gt.$lt.'/p'.$gt.__("Your username is \"admin\", this is the most commonly guessed username by hackers and brute-force scripts. It is highly recommended that you change your username immediately.",'gotmls').$lt.'/div'.$gt.$lt.'/form'.$gt;
		}
	}
	if ($GOTMLS_nonce_found && isset($_POST["GOTMLS_wpfirewall_action"])) {
		if ($_POST["GOTMLS_wpfirewall_action"] == "exclude_terms")
			update_option("WP_firewall_exclude_terms", "");
		elseif ($_POST["GOTMLS_wpfirewall_action"] == "whitelisted_ip" && isset($_SERVER["REMOTE_ADDR"])) {
			$ips = maybe_unserialize(get_option("WP_firewall_whitelisted_ip", "not Array!"));
			if (is_array($ips))
				$ips = array_merge($ips, array($_SERVER["REMOTE_ADDR"]));
			else
				$ips = array($_SERVER["REMOTE_ADDR"]);
			update_option("WP_firewall_whitelisted_ip", serialize($ips));
		}
	}
	if (get_option("WP_firewall_exclude_terms", "Not Found!") == "allow") {
		$end = "$lt/div$gt$lt/form$gt\n{$lt}hr /$gt";
		$img = 'threat.gif"';
		$button = $lt.'input type="submit" onclick="document.getElementById(\'GOTMLS_wpfirewall_action\').value=\'exclude_terms\';" value="'.__("Disable this Rule",'gotmls').'"'.$gt;
		$wpfirewall_action = $lt.'form method="POST" name="GOTMLS_Form_wpfirewall2"'.$gt.$lt.'div style="float: right;"'.$gt.$lt.'input type="hidden" name="GOTMLS_wpfirewall_action" id="GOTMLS_wpfirewall_action" value=""'.$gt.$lt.'input type="hidden" name="'.str_replace('=', '" value="', GOTMLS_set_nonce(__FUNCTION__."1223")).'"'.$gt.$button.$lt.'/div'.$gt.$lt.'div style="padding: 0 30px;"'.$gt.$lt.'p'.$gt.$lt.'img src="'.GOTMLS_images_path.$img.$gt.$lt.'b'.$gt."WP Firewall 2 (Conflicting Firewall Rule)$lt/b$gt$lt/p$gt".__("The Conflicting Firewall Rule (WP_firewall_exclude_terms) activated by the WP Firewall 2 plugin has been shown to interfere with the Definition Updates and WP Core File Scans in my Anti-Malware plugin. I recommend that you disable this rule in the WP Firewall 2 plugin.",'gotmls').$end;
		if (isset($_SERVER["REMOTE_ADDR"])) {
			if (is_array($ips = maybe_unserialize(get_option("WP_firewall_whitelisted_ip", "not Array!"))) && in_array($_SERVER["REMOTE_ADDR"], $ips))
				$wpfirewall_action = str_replace(array($img, $end), array('question.gif"', __(" However, your current IP has been Whitelisted so you could probably keep this rule enabled if you really want to.",'gotmls').$end), $wpfirewall_action);
			else
				$wpfirewall_action = str_replace(array($button, $end), array($button.$lt."br /$gt$lt".'input type="submit" onclick="document.getElementById(\'GOTMLS_wpfirewall_action\').value=\'whitelisted_ip\';" value="'.__("Whitelist your IP",'gotmls').'"'.$gt, __(" However, if you would like to keep this rule enabled you should at least Whitelist your IP.",'gotmls').$end), $wpfirewall_action);
		}
		$sec_opts = $wpfirewall_action.$sec_opts;
	}
	echo GOTMLS_box(__("Firewall Options",'gotmls'), $save_action.$sec_opts.$admin_notice)."\n</div></div></div>";
}

function GOTMLS_update_definitions() {
	global $wp_version, $current_user, $wpdb;
	wp_get_current_user();
	$GOTMLS_definitions_versions = array();
	$user_info = array();
	$saved = false;
	$moreJS = "";
	$finJS = "\n}";
	$form = 'registerKeyForm';
	$innerHTML = "<li style=\\\"color: #f00\\\">Your Installation Key could not be confirmed!</li>";
	$autoUpJS = '<span style="color: #C00;">This new feature is currently only available to registered users who have donated above the default level.</span><br />';
	foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"] as $threat_level=>$definition_names)
		foreach ($definition_names as $definition_name=>$definition_version)
			if (is_array($definition_version) && isset($definition_version[0]) && strlen($definition_version[0]) == 5)
				if (!isset($GOTMLS_definitions_versions[$threat_level]) || $definition_version[0] > $GOTMLS_definitions_versions[$threat_level])
					$GOTMLS_definitions_versions[$threat_level] = $definition_version[0];
	asort($GOTMLS_definitions_versions);
	if (isset($_REQUEST["UPDATE_definitions_array"]) && strlen($_REQUEST["UPDATE_definitions_array"]) && GOTMLS_get_nonce()) {
		if (strlen($_REQUEST["UPDATE_definitions_array"]) > 1) {
			$GOTnew_definitions = maybe_unserialize(GOTMLS_decode($_REQUEST["UPDATE_definitions_array"]));
			if (is_array($GOTnew_definitions)) {
				$form = 'autoUpdateDownload';
				$GLOBALS["GOTMLS"]["tmp"]["onLoad"] .= "updates_complete('Downloaded Definitions');";
			}
		} elseif ($_REQUEST["UPDATE_definitions_array"] == 0) {
			$GLOBALS["GOTMLS"]["tmp"]["definitions_array"] = array();
			$GOTnew_definitions = array();
		} elseif (($DEF = GOTMLS_get_URL('http:'.GOTMLS_update_home.'definitions.php?ver='.GOTMLS_Version.'&wp='.$wp_version.'&'.GOTMLS_set_nonce(__FUNCTION__."870").'&d='.ur1encode(GOTMLS_siteurl))) && is_array($GOTnew_definitions = maybe_unserialize(GOTMLS_decode($DEF))) && count($GOTnew_definitions)) {
			if (isset($GOTnew_definitions["you"]["user_email"]) && strlen($GOTnew_definitions["you"]["user_email"]) == 32) {
				$user_info = $GOTnew_definitions["you"];
				if ($user_info["user_email"] == md5($current_user->user_email))
					$toInfo = $current_user->user_email;
				elseif (!($toInfo = $wpdb->get_var("SELECT `user_nicename` FROM $wpdb->users WHERE MD5(`user_email`) = '".$user_info["user_email"]."'")))
					$toInfo = get_option("siteurl");
				$innerHTML = "<li style=\\\"color: #0C0\\\">Your Installation Key is Registered to:<br /> $toInfo</li>";
				$form = 'autoUpdateForm';
				if (isset($user_info["user_donations"]) && isset($user_info["user_donation_total"]) && isset($user_info["user_donation_freshness"])) {
					$user_donations_src = $user_info["user_donations"];
					if ($user_info["user_donation_total"] > 27.99) {
						$autoUpJS = '<input type="checkbox" id="auto_UPDATE_definitions_check" name="UPDATE_definitions_array" value="1"> <input type="hidden" name="UPDATE_definitions_checkbox" value="UPDATE_definitions_array">';
						$moreJS = 'if (foundUpdates = document.getElementById("check_wp_core_div_NA"))
		foundUpdates.innerHTML = "<a href=\'javascript:document.updateform.submit();\' onclick=\'document.updateform.UPDATE_definitions_array.value=1;\' style=\'color: #f00;\'>Set Definition Updates to Automatically Download to activate this feature.</a>";';
					}
					if ($user_donations_src > 0 && $user_info["user_donation_total"] > 0)
						$li = "<li> You have made $user_donations_src donation".($user_donations_src?'s totalling':' for').' $'.$user_info["user_donation_total"].".</li><!-- ".$user_info["user_donation_freshness"]." -->";
				}
			} else 
				$innerHTML = "<li style=\\\"color: #f00\\\">Your Installation Key is not registered!</li>";
			//unset($GOTnew_definitions["you"]);
			asort($GOTnew_definitions);
			if (serialize($GOTnew_definitions) == serialize($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]))
				unset($GOTnew_definitions);
			else {
				$debug = substr(serialize($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]), 0, 9)." ".md5(serialize($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]))." ".strlen(serialize($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]))." ".substr(serialize($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]), -9)." = ".substr(serialize($GOTnew_definitions), 0, 9)." ".md5(serialize($GOTnew_definitions))." ".strlen(serialize($GOTnew_definitions)." ".substr(serialize($GOTnew_definitions), -9));
				$GLOBALS["GOTMLS"]["tmp"]["definitions_array"] = $GOTnew_definitions;
				$GLOBALS["GOTMLS"]["tmp"]["onLoad"] .= "updates_complete('New Definitions Automatically Installed :-)');";
			}
			$finJS .= "\nif (divNAtext)\n\tloadGOTMLS();\nelse\n\tdivNAtext = setTimeout('loadGOTMLS()', 4000);";
			$finJS .= "\nif (typeof stopCheckingDefinitions !== 'undefined')\n\tclearTimeout(stopCheckingDefinitions);";
		} else
			$innerHTML = "<li style=\\\"color: #f00\\\"><a title='report error' href='#' onclick=\\\"stopCheckingDefinitions = checkupdateserver(alt_addr+'&error=".GOTMLS_encode(serialize(array("get_URL"=>$GLOBALS["GOTMLS"]["get_URL"])))."', 'Definition_Updates');\\\">Automatic Update Connection Failed!</a></li>";
	} else 
		$innerHTML = "<li style=\\\"color: #f00\\\">".GOTMLS_Invalid_Nonce("Nonce Error")."</li>";	
	if (isset($GOTnew_definitions) && is_array($GOTnew_definitions)) {
		$GLOBALS["GOTMLS"]["tmp"]["definitions_array"] = GOTMLS_array_replace($GLOBALS["GOTMLS"]["tmp"]["definitions_array"], $GOTnew_definitions);	
		if (file_exists(GOTMLS_plugin_path.'definitions_update.txt'))
			@unlink(GOTMLS_plugin_path.'definitions_update.txt');
		$saved = GOTMLS_update_option('definitions', $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]);
		$_REQUEST["check"] = array();
		foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"] as $threat_level=>$definition_names) {
			if ($threat_level != "potential")
				$_REQUEST["check"][] = $threat_level;
			foreach ($definition_names as $definition_name=>$definition_version)
				if (is_array($definition_version) && isset($definition_version[0]) && strlen($definition_version[0]) == 5)
					if (!isset($GOTMLS_definitions_versions[$threat_level]) || $definition_version[0] > $GOTMLS_definitions_versions[$threat_level])
						$GOTMLS_definitions_versions[$threat_level] = $definition_version[0];
		}
		$GLOBALS["GOTMLS"]["log"]["settings"]["check"] = $_REQUEST["check"];
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"] = $_REQUEST["check"];
		asort($GOTMLS_definitions_versions);
		$autoUpJS .= '<span style="color: #0C0;">(Newest Definition Updates Installed.)</span>';
	} elseif ($form != 'registerKeyForm') {
		$form = 'autoUpdateDownload';
		$autoUpJS .= '<span style="color: #0C0;">(No newer Definition Updates are available at this time.)</span>';
		$innerHTML .= "<li style=\\\"color: #0C0\\\">No Newer Definition Updates Available.</li>";
	}
	if (isset($_SERVER["SCRIPT_FILENAME"]) && preg_match('/\/admin-ajax\.php/i', $_SERVER["SCRIPT_FILENAME"]) && isset($_REQUEST["action"]) && $_REQUEST["action"] == "GOTMLS_auto_update") {
		if (!$user_donations_src)
			$li = "<li style=\\\"color: #f00;\\\">You have not donated yet!</li>";
		if (strlen($moreJS) == 0)
			$moreJS = 'if (foundUpdates = document.getElementById("check_wp_core_div_NA"))
		foundUpdates.innerHTML = "<a href=\'javascript:document.ppdform.submit();\' onclick=\'document.ppdform.amount.value=32;\' style=\'color: #f00;\'>Donate $29+ now then enable Automatic Definition Updates to Scan for Core Files changes.</a>";';
		$moreJS .= "\n\tif (foundUpdates = document.getElementById('pastDonations'))\n\tfoundUpdates.innerHTML = '$li';";
		@header("Content-type: text/javascript");
		if (is_array($GOTMLS_definitions_versions) && count($GOTMLS_definitions_versions) && (strlen($new_ver = trim(array_pop($GOTMLS_definitions_versions))) == 5) && $saved) {
			$innerHTML .= "<li style=\\\"color: #0C0\\\">New Definition Updates Installed.</li>";
			$finJS .= "\nif (foundUpdates = document.getElementById('GOTMLS_definitions_date')) foundUpdates.innerHTML = '$new_ver';";
		} elseif (is_array($GOTnew_definitions) && count($GOTnew_definitions))
			$finJS .= "\nalert('Definition update $new_ver could not be saved because update_option Failed! $debug');";
		die('//<![CDATA[
var inc_form = "";
if (foundUpdates = document.getElementById("autoUpdateDownload"))
	foundUpdates.src += "?'.$user_donations_src.'";
if (foundUpdates = document.getElementById("registerKeyForm"))
	foundUpdates.style.display = "none";
if (foundUpdates = document.getElementById("'.$form.'"))
	foundUpdates.style.display = "block";
if (foundUpdates = document.getElementById("Definition_Updates"))
	foundUpdates.innerHTML = "<ul class=\\"sidebar-links\\">'.$innerHTML.'</ul>"+inc_form;
function setDivNAtext() {
	var foundUpdates;
	'.$moreJS.$finJS.'
if (foundUpdates = document.getElementById("UPDATE_definitions_div"))
	foundUpdates.innerHTML = \''.$autoUpJS.'\';
//]]>');
	}
	$GLOBALS["GOTMLS"]["tmp"]["Definition"]["Updates"] = '?div=Definition_Updates';
	foreach ($GOTMLS_definitions_versions as $definition_name=>$GLOBALS["GOTMLS"]["tmp"]["Definition"]["Latest"])
		$GLOBALS["GOTMLS"]["tmp"]["Definition"]["Updates"] .= "&def[$definition_name]=".$GLOBALS["GOTMLS"]["tmp"]["Definition"]["Latest"];
	if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["you"]["user_email"]) && strlen($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["you"]["user_email"]) == 32)
		$GLOBALS["GOTMLS"]["tmp"]["Definition"]["Updates"] .= "&def[you]=".$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["you"]["user_email"];
}
add_action('wp_ajax_GOTMLS_auto_update', 'GOTMLS_update_definitions');

function GOTMLS_settings() {
	global $current_user, $wpdb, $wp_version, $GOTMLS_dirs_at_depth, $GOTMLS_dir_at_depth;
	$GOTMLS_scan_groups = array();
	$gt = ">";
	$lt = "<";
	GOTMLS_update_definitions();
	if (($GOTMLS_nonce_found = GOTMLS_get_nonce()) && isset($_REQUEST["check"]) && is_array($_REQUEST["check"]))
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"] = $_REQUEST["check"];
	/*	$threat_names = array_keys($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["known"]);
		foreach ($threat_names as $threat_name) {
			if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["known"][$threat_name]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["known"][$threat_name]) && count($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["known"][$threat_name]) > 1) {
				if ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["known"][$threat_name][0] > $GOTMLS_definitions_version)
					$GOTMLS_definitions_version = $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["known"][$threat_name][0];
				if (!(count($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]) && in_array($threat_name, $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]))) {
					$GLOBALS["GOTMLS"]["tmp"]["threat_levels"][$threat_name] = count($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["known"][$threat_name]);
					if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"]) && $GLOBALS["GOTMLS"]["tmp"]["threat_levels"][$threat_name] > 2)
						$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"] = "known";
				}
			}
		}*/
	if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"])) {
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"] = $GLOBALS["GOTMLS"]["tmp"]["threat_levels"];
		update_option("GOTMLS_settings_array", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]);
	}
	$dirs = GOTMLS_explode_dir(__FILE__);
	for ($SL=0;$SL<intval($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"]);$SL++)
		$GOTMLS_scan_groups[] = $lt.'b'.$gt.implode(GOTMLS_slash(), array_slice($dirs, -1 * (3 + $SL), 1)).$lt.'/b'.$gt;
	if (isset($_POST["exclude_ext"])) {	
		if (strlen(trim(str_replace(",","",$_POST["exclude_ext"]).' ')) > 0)
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"] = preg_split('/[\s]*([,]+[\s]*)+/', trim(str_replace('.', ',', htmlentities($_POST["exclude_ext"]))), -1, PREG_SPLIT_NO_EMPTY);
		else
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"] = array();
	}
	$default_exclude_ext = str_replace(",gotmls", "", implode(",", $GLOBALS["GOTMLS"]["tmp"]["skip_ext"]));
	$GLOBALS["GOTMLS"]["tmp"]["skip_ext"] = $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"];
	if (isset($_POST["UPDATE_definitions_checkbox"])) {
		if (isset($_POST[$_POST["UPDATE_definitions_checkbox"]]) && $_POST[$_POST["UPDATE_definitions_checkbox"]] == 1)
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["auto_UPDATE_definitions"] = 1;
		else
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["auto_UPDATE_definitions"] = "";
	}
	if (isset($_POST["exclude_dir"])) {
		if (strlen(trim(str_replace(",","",$_POST["exclude_dir"]).' ')) > 0)
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"] = preg_split('/[\s]*([,]+[\s]*)+/', trim(htmlentities($_POST["exclude_dir"])), -1, PREG_SPLIT_NO_EMPTY);
		else
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"] = array();
		for ($d=0; $d<count($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"]); $d++)
			if (dirname($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"][$d]) != ".")
				$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"][$d] = str_replace("\\", "", str_replace("/", "", str_replace(dirname($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"][$d]), "", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"][$d])));
	}
	$GLOBALS["GOTMLS"]["tmp"]["skip_dirs"] = array_merge($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"], $GLOBALS["GOTMLS"]["tmp"]["skip_dirs"]);
	if (isset($_POST["scan_what"]) && is_numeric($_POST["scan_what"]) && $_POST["scan_what"] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"] = $_POST["scan_what"];
	if (isset($_POST["check_custom"]) && $_POST["check_custom"] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"] = stripslashes($_POST["check_custom"]);
	if (isset($_POST["scan_depth"]) && is_numeric($_POST["scan_depth"]) && $_POST["scan_depth"] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"] = $_POST["scan_depth"];
/*	if (isset($_POST['check_htaccess']) && is_numeric($_POST['check_htaccess']) && $_POST['check_htaccess'] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_htaccess'])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_htaccess'] = $_POST['check_htaccess'];
	if (isset($_POST['check_timthumb']) && is_numeric($_POST['check_timthumb']) && $_POST['check_timthumb'] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_timthumb'])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_timthumb'] = $_POST['check_timthumb'];
	if (isset($_POST['check_wp_core']) && is_numeric($_POST['check_wp_core']) && $_POST['check_wp_core'] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_wp_core'])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_wp_core'] = $_POST['check_wp_core'];
	if (isset($_POST['check_known']) && is_numeric($_POST['check_known']) && $_POST['check_known'] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_known'])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_known'] = $_POST['check_known'];
	if (isset($_POST['check_potential']) && is_numeric($_POST['check_potential']) && $_POST['check_potential'] != $GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_potential'])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]['check_potential'] = $_POST['check_potential'];*/
	if (isset($_POST['skip_quarantine']) && $_POST['skip_quarantine'])
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]['skip_quarantine'] = $_POST['skip_quarantine'];
	elseif (isset($_POST["exclude_ext"]))
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]['skip_quarantine'] = 0;
	GOTMLS_update_scan_log(array("settings" => $GLOBALS["GOTMLS"]["tmp"]["settings_array"]));
	$scan_whatopts = '';
	$scan_optjs = "\n{$lt}script type=\"text/javascript\"$gt\nfunction showOnly(what) {\n";
	foreach ($GOTMLS_scan_groups as $mg => $GOTMLS_scan_group) {
		$scan_optjs .= "document.getElementById('only$mg').style.display = 'none';\n";
		$scan_whatopts = "\n$lt/div$gt\n$lt/div$gt\n$scan_whatopts";
		$dir = implode(GOTMLS_slash(), array_slice($dirs, 0, -1 * (2 + $mg)));
		$files = GOTMLS_getfiles($dir);
		if (is_array($files))
			foreach ($files as $file)
				if (is_dir(GOTMLS_trailingslashit($dir).$file))
					$scan_whatopts = $lt.'input type="checkbox" name="scan_only[]" value="'.htmlentities($file).'" /'.$gt.htmlentities($file).$lt.'br /'.$gt.$scan_whatopts;
		$scan_whatopts = "\n$lt".'div style="padding: 4px 30px;" id="scan_group_div_'.$mg.'"'.$gt.$lt.'input type="radio" name="scan_what" id="not-only'.$mg.'" value="'.$mg.'"'.($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"]==$mg?' checked':'').' /'.$gt.$lt.'a style="text-decoration: none;" href="#scan_what" onclick="showOnly(\''.$mg.'\');document.getElementById(\'not-only'.$mg.'\').checked=true;"'."$gt$GOTMLS_scan_group$lt/a$gt{$lt}br /$gt\n$lt".'div class="rounded-corners" style="position: absolute; display: none; background-color: #CCF; margin: 0; padding: 10px; z-index: 10;" id="only'.$mg.'"'.$gt.$lt.'div style="padding-bottom: 6px;"'.$gt.GOTMLS_close_button('only'.$mg, 0).$lt.'b'.$gt.str_replace(" ", "&nbsp;", __("Only Scan These Folders:",'gotmls')).$lt.'/b'.$gt.$lt.'/div'.$gt.$scan_whatopts;
	}
	$scan_optjs .= "document.getElementById('only'+what).style.display = 'block';\n}";
	if (isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["auto_UPDATE_definitions"]) && $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["auto_UPDATE_definitions"])
		$scan_optjs .= "\nfunction auto_UPDATE_check() {\n\tif (auto_UPdef_check = document.getElementById('auto_UPDATE_definitions_check'))\n\t\tauto_UPdef_check.checked = true;\n}\nif (window.addEventListener)\n\twindow.addEventListener('load', auto_UPDATE_check)\nelse\n\tdocument.attachEvent('onload', auto_UPDATE_check);\n";
	$scan_optjs .= "$lt/script$gt";
	$GOTMLS_nonce_URL = GOTMLS_set_nonce(__FUNCTION__."853");
	$scan_opts = "\n$lt".'form method="POST" name="GOTMLS_Form"'.$gt.$lt.'input type="hidden" name="'.str_replace('=', '" value="', $GOTMLS_nonce_URL).'"'.$gt.$lt.'input type="hidden" name="scan_type" id="scan_type" value="Complete Scan" /'.$gt.$lt.'div style="float: right;"'.$gt.$lt.'input type="submit" id="complete_scan" value="'.__("Run Complete Scan",'gotmls').'" class="button-primary" onclick="document.getElementById(\'scan_type\').value=\'Complete Scan\';" /'.$gt.$lt.'/div'.$gt.'
	'.$lt.'div style="float: left;"'.$gt.$lt.'p'.$gt.$lt.'b'.$gt.__("What to look for:",'gotmls').$lt.'/b'.$gt.$lt.'/p'.$gt.'
	'.$lt.'div style="padding: 0 30px;"'.$gt;
	$cInput = '"'.$gt.$lt.'input';
	$pCheck = "$cInput checked";
	$kCheck = "";
	foreach ($GLOBALS["GOTMLS"]["tmp"]["threat_levels"] as $threat_level_name=>$threat_level) {
		$scan_opts .= $lt.'div id="check_'.$threat_level.'_div" style="padding: 0; position: relative;';
		if (($threat_level != "wp_core" && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"][$threat_level])) || isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"][$threat_level]["$wp_version"])) {
			if ($threat_level != "potential" && in_array($threat_level,$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"])) {
				$pCheck = " display: none;$cInput";
				$scan_opts .= "$cInput checked";
			} elseif ($threat_level == "potential")
				$scan_opts .= $pCheck;
			else
				$scan_opts .= $cInput;
			if ($threat_level != "potential")
				$kCheck .= ",'$threat_level'";
			$scan_opts .= ' type="checkbox" onchange="pCheck(this);" name="check[]" id="check_'.$threat_level.'_Yes" value="'.$threat_level.'" /'.$gt.' '.$lt.'a style="text-decoration: none;" href="#check_'.$threat_level.'_div_0" onclick="document.getElementById(\'check_'.$threat_level.'_Yes\').checked=true;pCheck(document.getElementById(\'check_'.$threat_level.'_Yes\'));showhide(\'dont_check_'.$threat_level.'\');"'."$gt{$lt}b$gt$threat_level_name$lt/b$gt$lt/a$gt\n";
			if (isset($_GET["SESSION"])) {
				if (isset($_SESSION["GOTMLS_debug"][$threat_level]))
					$lt.'div style="float: right;"'.$gt.print_r($_SESSION["GOTMLS_debug"][$threat_level],1)."$lt/div$gt";
				$scan_opts .= "\n$lt".'div style="padding: 0 20px; position: relative; top: -18px; display: none;" id="dont_check_'.$threat_level.'"'.$gt.$lt.'a class="rounded-corners" style="position: absolute; left: 0; margin: 0; padding: 0 4px; text-decoration: none; color: #C00; background-color: #FCC; border: solid #F00 1px;" href="#check_'.$threat_level.'_div_0" onclick="showhide(\'dont_check_'.$threat_level.'\');"'.$gt.'X'.$lt.'/a'.$gt;
				foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"][$threat_level] as $threat_name => $threat_regex)
					$scan_opts .= $lt."br /$gt\n$lt".'input type="checkbox" name="dont_check[]" value="'.htmlspecialchars($threat_name).'"'.(in_array($threat_name, $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"])?' checked /'.$gt.$lt.'script'.$gt.'showhide("dont_check_'.$threat_level.'", true);'.$lt.'/script'.$gt:' /'.$gt).(isset($_SESSION["GOTMLS_debug"][$threat_name])?$lt.'div style="float: right;"'.$gt.print_r($_SESSION["GOTMLS_debug"][$threat_name],1)."$lt/div$gt":"").$threat_name;
				$scan_opts .= "\n$lt/div$gt";
			}
		} else
			$scan_opts .= $lt.'a title="'.__("Download Definition Updates to Use this feature",'gotmls').'"'.$gt.$lt.'img src="'.GOTMLS_images_path.'blocked.gif" height=16 width=16 alt="X"'.$gt.$lt.'b'.$gt.'&nbsp; '.$threat_level_name.$lt.'/b'.$gt.$lt.'br /'.$gt.$lt.'div style="padding: 14px;" id="check_'.$threat_level.'_div_NA"'.$gt.$lt.'span style="color: #F00"'.$gt.__("Download the new definitions (Right sidebar) to activate this feature.",'gotmls')."$lt/span$gt$lt/div$gt";
		$scan_opts .= "\n$lt/div$gt";
	}
	$scan_opts .= $lt.'/div'.$gt.$lt.'/div'.$gt.'
	'.$lt.'div style="float: left;"'.$gt.$lt.'p'.$gt.$lt.'b'.$gt.__("What to scan:",'gotmls').$lt.'/b'.$gt.$lt.'/p'.$gt.$scan_whatopts.$scan_optjs.$lt.'/div'.$gt.'
	'.$lt.'div style="float: left;" id="scanwhatfolder"'.$gt.$lt.'/div'.$gt.'
	'.$lt.'div style="float: left;"'.$gt.$lt.'p'.$gt.$lt.'b'.$gt.__("Scan Depth:",'gotmls').$lt.'/b'.$gt.$lt.'/p'.$gt.'
	'.$lt.'div style="padding: 0 30px;"'.$gt.$lt.'input type="text" value="'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"].'" name="scan_depth" size="5"'.$gt.$lt.'br /'.$gt.__("how far to drill down",'gotmls').$lt.'br /'.$gt.'('.__("-1 is infinite depth",'gotmls').')'.$lt.'/div'.$gt.$lt.'/div'.$gt.$lt.'br style="clear: left;"'.$gt;
	if (isset($_GET["SESSION"]) && isset($_SESSION["GOTMLS_debug"]['total'])) {$scan_opts .= $lt.'div style="float: right;"'.$gt.print_r($_SESSION["GOTMLS_debug"]['total'],1)."$lt/div$gt"; unset($_SESSION["GOTMLS_debug"]);}
	if (isset($_GET["eli"])) {//still testing this option
		$scan_opts .= "\n$lt".'div style="padding: 10px;"'.$gt.$lt.'p'.$gt.$lt.'b'.$gt.__("Custom RegExp:",'gotmls').$lt.'/b'.$gt.' ('.__("For very advanced users only. Do not use this without talking to Eli first. If used incorrectly you could easily break your site.",'gotmls').')'.$lt.'/p'.$gt.$lt.'input type="text" name="check_custom" style="width: 100%;" value="'.htmlspecialchars($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"]).'" /'."$gt$lt/div$gt\n";
	} 
	$QuickScan = $lt.((is_dir(dirname(__FILE__)."/../../../wp-includes") && is_dir(dirname(__FILE__)."/../../../wp-admin"))?'a href="'.admin_url("admin.php?page=GOTMLS-settings&scan_type=Quick+Scan&$GOTMLS_nonce_URL").'" class="button-primary" style="height: 22px; line-height: 13px; padding: 3px;">WP_Core</a':"!-- No wp-includes or wp-admin --").$gt;
	foreach (array("Plugins", "Themes") as $ScanFolder)
		$QuickScan .= '&nbsp;'.$lt.((is_dir(dirname(__FILE__)."/../../../wp-content/".strtolower($ScanFolder)))?'a href="'.admin_url("admin.php?page=GOTMLS-settings&scan_type=Quick+Scan&scan_only[]=wp-content/".strtolower($ScanFolder)."&$GOTMLS_nonce_URL")."\" class=\"button-primary\" style=\"height: 22px; line-height: 13px; padding: 3px;\"$gt$ScanFolder$lt/a":"!-- No $ScanFolder in wp-content --").$gt;
	$scan_opts .= "\n$lt".'p'.$gt.$lt.'b'.$gt.__("Skip files with the following extensions:",'gotmls')."$lt/b$gt".(($default_exclude_ext!=implode(",", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"]))?" {$lt}a href=\"javascript:void(0);\" onclick=\"document.getElementById('exclude_ext').value = '$default_exclude_ext';\"{$gt}[Restore Defaults]$lt/a$gt":"").$lt.'/p'.$gt.'
	'.$lt.'div style="padding: 0 30px;"'.$gt.$lt.'input type="text" placeholder="'.__("a comma separated list of file extentions to skip",'gotmls').'" name="exclude_ext" id="exclude_ext" value="'.implode(",", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"]).'" style="width: 100%;" /'."$gt$lt/div$gt$lt".'p'.$gt.$lt.'b'.$gt.__("Skip directories with the following names:",'gotmls')."$lt/b$gt$lt/p$gt$lt".'div style="padding: 0 30px;"'.$gt.$lt.'input type="text" placeholder="'.__("a folder name or comma separated list of folder names to skip",'gotmls').'" name="exclude_dir" value="'.implode(",", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"]).'" style="width: 100%;" /'.$gt.$lt.'/div'.$gt.'
	'.$lt.'table style="width: 100%" cellspacing="10"'.$gt.$lt.'tr'.$gt.$lt.'td nowrap valign="top" style="white-space: nowrap; width: 1px;"'.$gt.$lt.'b'.$gt.__("Automatically Update Definitions:",'gotmls').$lt."br$gt$lt/b$gt$lt/td$gt$lt".'td'.$gt.$lt.'div id="UPDATE_definitions_div"'.$gt.$lt.'br'.$gt.$lt.'span style="color: #C00;"'.$gt.__("This feature is only available to registered users who have donated at a certain level.",'gotmls')."$lt/span$gt$lt/div$gt$lt/td$gt$lt".'td align="right" valign="bottom"'.$gt.$lt.'input type="submit" id="save_settings" value="'.__("Save Settings",'gotmls').'" class="button-primary" onclick="document.getElementById(\'scan_type\').value=\'Save\';" /'."$gt$lt/td$gt$lt/tr$gt$lt/table$gt$lt/form$gt";
	$title_tagline = $lt."li$gt Site Title: ".htmlspecialchars($wpdb->get_var("SELECT `option_value` FROM `$wpdb->options` WHERE `option_name` = 'blogname'"));
	$title_tagline .= "$lt/li$gt$lt"."li$gt Tagline: ".htmlspecialchars($wpdb->get_var("SELECT `option_value` FROM `$wpdb->options` WHERE `option_name` = 'blogdescription'"));
	if (preg_match('/h[\@a]ck[3e]d.*by/is', $title_tagline))
		echo $lt.'div class="error"'.$gt.sprintf(__("Your Site Title or Tagline suggests that you may have been hacked ...%sThis could impact the indexing of your site and may even lead to blacklisting. You can change those options on the %sGeneral Settings$lt/a$gt page.",'gotmls'), "$title_tagline$lt/li$gt", $lt.'a href="'.admin_url("options-general.php").'"'.$gt)."$lt/div$gt";
	@ob_start();
	$OB_default_handlers = array("default output handler", "zlib output compression");
	$OB_handlers = @ob_list_handlers();
	if (is_array($OB_handlers) && count($OB_handlers))
		foreach ($OB_handlers as $OB_last_handler)
			if (!in_array($OB_last_handler, $OB_default_handlers))
				echo $lt.'div class="error"'.$gt.sprintf(__("Another Plugin or Theme is using '%s' to handle output buffers. <br />This prevents actively outputing the buffer on-the-fly and could severely degrade the performance of this (and many other) Plugins. <br />Consider disabling caching and compression plugins (at least during the scanning process).",'gotmls'), $OB_last_handler)."$lt/div$gt";
	GOTMLS_display_header();
	$scan_groups = array_merge(array(__("Scanned Files",'gotmls')=>"scanned",__("Selected Folders",'gotmls')=>"dirs",__("Scanned Folders",'gotmls')=>"dir",__("Skipped Folders",'gotmls')=>"skipdirs",__("Skipped Files",'gotmls')=>"skipped",__("Read/Write Errors",'gotmls')=>"errors",__("Quarantined Files",'gotmls')=>"bad"), $GLOBALS["GOTMLS"]["tmp"]["threat_levels"]);
	echo $lt.'script type="text/javascript">
var percent = 0;
function pCheck(chkb) {
	var kCheck = ['.trim($kCheck,",").'];
	chk = true;
	for (var i = 0; i < kCheck.length; i++) {
		var chkbox = document.getElementById("check_"+kCheck[i]+"_Yes");
		if (chkbox && chkb.id == "check_potential_Yes" && chkb.checked == false) {
			chk = false;
			chkbox.checked = true;
		} else if (chkbox && chkbox.checked) {
			chk = false;
		}
	}
	if (chkbox = document.getElementById("check_potential_Yes"))
		chkbox.checked = chk;
	if (chk) {
		document.getElementById("check_potential_div").style.display = "block";
		alert("If you do not select any other threat types, then only potential threats will be found and the automatic fix will not be available!");
	} else
		document.getElementById("check_potential_div").style.display = "none";
}
function changeFavicon(percent) {
	var oldLink = document.getElementById("wait_gif");
	if (oldLink) {
		if (percent >= 100) {
			document.getElementsByTagName("head")[0].removeChild(oldLink);
			var link = document.createElement("link");
			link.id = "wait_gif";
			link.type = "image/gif";
			link.rel = "shortcut icon";
			var threats = '.implode(" + ", array_merge($GLOBALS["GOTMLS"]["tmp"]["threat_levels"], array(__("Potential Threats",'gotmls')=>"errors",__("WP-Login Updates",'gotmls')=>"errors"))).';
			if (threats > 0) {
				if ((errors * 2) == threats)
					linkhref = "blocked";
				else
					linkhref = "threat";
			} else
				linkhref = "checked";
			link.href = "'.GOTMLS_images_path.'"+linkhref+".gif";
			document.getElementsByTagName("head")[0].appendChild(link);
		}
	} else {
		var icons = document.getElementsByTagName("link");
		var link = document.createElement("link");
		link.id = "wait_gif";
		link.type = "image/gif";
		link.rel = "shortcut icon";
		link.href = "'.GOTMLS_images_path.'wait.gif";
	//	document.head.appendChild(link);
		document.getElementsByTagName("head")[0].appendChild(link);
	}
}
function update_status(title, time) {
	sdir = (dir+direrrors);
	if (arguments[2] >= 0 && arguments[2] <= 100)
		percent = arguments[2];
	else
		percent = Math.floor((sdir*100)/dirs);
	scan_state = "6F6";
	if (percent == 100) {
		showhide("pause_button", true);
		showhide("pause_button");
		title = "'.$lt.'b'.$gt.__("Scan Complete!",'gotmls').$lt.'/b'.$gt.'";
	} else
		scan_state = "99F";
	changeFavicon(percent);
	if (sdir) {
		if (arguments[2] >= 0 && arguments[2] <= 100)
			timeRemaining = Math.ceil(((time-startTime)*(100/percent))-(time-startTime));
		else
			timeRemaining = Math.ceil(((time-startTime)*(dirs/sdir))-(time-startTime));
		if (timeRemaining > 59)
			timeRemaining = Math.ceil(timeRemaining/60)+" Minute";
		else
			timeRemaining += " Second";
		if (timeRemaining.substr(0, 2) != "1 ")
			timeRemaining += "s";
	} else
		timeRemaining = "Calculating Time";
	timeElapsed = Math.ceil(time);
	if (timeElapsed > 59)
		timeElapsed = Math.floor(timeElapsed/60)+" Minute";
	else
		timeElapsed += " Second";
	if (timeElapsed.substr(0, 2) != "1 ")
		timeElapsed += "s";
	divHTML = \''.$lt.'div align="center" style="vertical-align: middle; background-color: #ccc; z-index: 3; height: 18px; width: 100%; border: solid #000 1px; position: relative; padding: 10px 0;"'.$gt.$lt.'div style="height: 18px; padding: 10px 0; position: absolute; top: 0px; left: 0px; background-color: #\'+scan_state+\'; width: \'+percent+\'%"'.$gt.$lt.'/div'.$gt.$lt.'div style="height: 32px; position: absolute; top: 3px; left: 10px; z-index: 5; line-height: 16px;" align="left"'.$gt.'\'+sdir+" Folder"+(sdir==1?"":"s")+" Checked'.$lt.'br /'.$gt.'"+timeElapsed+\' Elapsed'.$lt.'/div'.$gt.$lt.'div style="height: 38px; position: absolute; top: 0px; left: 0px; width: 100%; z-index: 5; line-height: 38px; font-size: 30px; text-align: center;"'.$gt.'\'+percent+\'%'.$lt.'/div'.$gt.$lt.'div style="height: 32px; position: absolute; top: 3px; right: 10px; z-index: 5; line-height: 16px;" align="right"'.$gt.'\'+(dirs-sdir)+" Folder"+((dirs-sdir)==1?"":"s")+" Remaining'.$lt.'br /'.$gt.'"+timeRemaining+" Remaining'.$lt.'/div'.$gt.$lt.'/div'.$gt.'";
	document.getElementById("status_bar").innerHTML = divHTML;
	document.getElementById("status_text").innerHTML = title;
	dis="none";
	divHTML = \''.$lt.'ul style="float: right; margin: 0 20px; text-align: right;"'.$gt.'\';
	/*'.$lt.'!--*'.'/';
	$MAX = 0;
	$vars = "var i, intrvl, direrrors=0";
	$fix_button_js = "";
	$found = "";
	$li_js = "return false;";
	if (isset($_REQUEST["scan_type"]) && $_REQUEST["scan_type"] == "Quick Scan") {
		$GLOBALS["GOTMLS"]["log"]["settings"]["check"] = array();
		foreach ($GLOBALS["GOTMLS"]["tmp"]["threat_levels"] as $check)
			if ($check != "potential")
				$GLOBALS["GOTMLS"]["log"]["settings"]["check"][] = $check;
	}
	foreach ($scan_groups as $scan_name => $scan_group) {
		if ($MAX++ == 6) {
			$quarantineCountOnly = GOTMLS_get_quarantine(true);
			$vars .= ", $scan_group=$quarantineCountOnly";
			echo "/*--{$gt}*"."/\n\tif ($scan_group > 0)\n\t\tscan_state = ' potential'; \n\telse\n\t\tscan_state = '';\n\tdivHTML += '</ul><ul style=\"text-align: left;\"><li class=\"GOTMLS_li\"><a href=\"admin.php?page=GOTMLS-View-Quarantine\" class=\"GOTMLS_plugin".("'+scan_state+'\" title=\"".GOTMLS_View_Quarantine_LANGUAGE)."\">'+$scan_group+'&nbsp;'+($scan_group==1?('$scan_name').slice(0,-1):'$scan_name')+'</a></li>';\n/*{$lt}!--*"."/";
			$found = "Found ";
			$fix_button_js = "\n\t\tdis='block';";
		} else {
			$vars .= ", $scan_group=0";
			if ($found && !in_array($scan_group, $GLOBALS["GOTMLS"]["log"]["settings"]["check"]))
				$potential_threat = ' potential" title="'.__("You are not currently scanning for this type of threat!",'gotmls');
			else
				$potential_threat = "";
			echo "/*--{$gt}*"."/\n\tif ($scan_group > 0) {\n\t\tscan_state = ' href=\"#found_$scan_group\" onclick=\"$li_js showhide(\\'found_$scan_group\\', true);\" class=\"GOTMLS_plugin $scan_group\"';$fix_button_js".($MAX>6?"\n\tshowhide('found_$scan_group', true);":"")."\n\t} else\n\t\tscan_state = ' class=\"GOTMLS_plugin$potential_threat\"';\n\tdivHTML += '<li class=\"GOTMLS_li\"".(($found && $scan_group == "potential" && !in_array($scan_group, $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check"]))?' style="display: none;"':"")."><a'+scan_state+'>$found'+$scan_group+'&nbsp;'+($scan_group==1?('$scan_name').slice(0,-1):'$scan_name')+'</a></li>';\n/*{$lt}!--*"."/";
		}
		$li_js = "";
		if ($MAX > 11)
			$fix_button_js = "";
	}
	$ScanSettings = $lt.'div style="float: right;"'.$gt.GOTMLS_Run_Quick_Scan_LANGUAGE.":&nbsp;$QuickScan$lt/div$gt".GOTMLS_Scan_Settings_LANGUAGE;
	echo "/*--{$gt}*".'/
	document.getElementById("status_counts").innerHTML = divHTML+"'.$lt.'/ul'.$gt.'";
	document.getElementById("fix_button").style.display = dis;
}
'.$vars.';
function showOnly(what) {
	document.getElementById("only_what").innerHTML = document.getElementById("only"+what).innerHTML;
}
var startTime = 0;
'.$lt.'/script'.$gt.GOTMLS_box($ScanSettings, $scan_opts);
	$Settings_Saved = "\n{$lt}div onclick=\"this.style.display='none';\" style='position: relative; top: -50px; margin: 0 300px 0 130px;' class='updated'$gt\nSettings Saved!$lt/div$gt\n";//script type='text/javascript'$gt\nalert('Settings Saved!');\n$lt/script$gt\n";
	if (isset($_REQUEST["scan_type"]) && $_REQUEST["scan_type"] == "Save") {
		if ($GOTMLS_nonce_found) {
			update_option('GOTMLS_settings_array', $GLOBALS["GOTMLS"]["tmp"]["settings_array"]);
			echo $Settings_Saved;
		} else
			echo GOTMLS_box(GOTMLS_Invalid_Nonce(""), __("Saving these settings requires a valid Nonce Token. No valid Nonce Token was found at this time, either because the token have expired or because the data was invalid. Please try re-submitting the form above.",'gotmls')."\n{$lt}script type='text/javascript'$gt\nalert('".GOTMLS_Invalid_Nonce("")."');\n$lt/script$gt\n");
		echo GOTMLS_box(__("Scan Logs",'gotmls'), GOTMLS_get_scanlog());
	} elseif (isset($_REQUEST["scan_what"]) && is_numeric($_REQUEST["scan_what"]) && ($_REQUEST["scan_what"] > -1)) {
		if ($GOTMLS_nonce_found) {
			update_option('GOTMLS_settings_array', $GLOBALS["GOTMLS"]["tmp"]["settings_array"]);
			$GLOBALS["GOTMLS"]["log"]["settings"]["check"] = array();
			GOTMLS_update_scan_log(array("settings" => $GLOBALS["GOTMLS"]["tmp"]["settings_array"]));
			echo $Settings_Saved;
			if (!isset($_REQUEST["scan_type"]))
				$_REQUEST["scan_type"] = "Complete Scan";
			elseif ($_REQUEST["scan_type"] == "Quick Scan") {
				$li_js = "\nfunction testComplete() {\n\tif (percent != 100)\n\t\talert('".__("The Quick Scan was unable to finish because of a shortage of memory or a problem accessing a file. Please try using the Complete Scan, it is slower but it will handle these errors better and continue scanning the rest of the files.",'gotmls')."');\n}\nwindow.onload=testComplete;\n$lt/script$gt\n$lt".'script type="text/javascript"'.$gt;
				$GLOBALS["GOTMLS"]["log"]["settings"]["check"] = array();
				foreach ($GLOBALS["GOTMLS"]["tmp"]["threat_levels"] as $check)
					if ($check != "potential")
						$GLOBALS["GOTMLS"]["log"]["settings"]["check"][] = $check;
			}
			echo "\n$lt".'form method="POST" action="'.admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."1030")).(isset($_SERVER["QUERY_STRING"])&&strlen($_SERVER["QUERY_STRING"])?"&".$_SERVER["QUERY_STRING"]:"").'" target="GOTMLS_iFrame" name="GOTMLS_Form_clean"'.$gt.$lt.'input type="hidden" name="action" value="GOTMLS_fix"'.$gt.$lt.'input type="hidden" id="GOTMLS_fixing" name="GOTMLS_fixing" value="1"'.$gt;
			foreach ($_POST as $name => $value) {
				if (substr($name, 0, 10) != 'GOTMLS_fix') {
					if (is_array($value)) {
						foreach ($value as $val)
							echo $lt.'input type="hidden" name="'.$name.'[]" value="'.htmlspecialchars($val).'"'.$gt;
					} else
						echo $lt.'input type="hidden" name="'.$name.'" value="'.htmlspecialchars($value).'"'.$gt;
				}
			}
			echo "\n$lt".'script type="text/javascript"'.$gt.'showhide("inside_'.md5($ScanSettings).'");'.$lt.'/script'.$gt.GOTMLS_box(htmlentities($_REQUEST["scan_type"]).' Status', $lt.'div id="status_text"'.$gt.$lt.'img src="'.GOTMLS_images_path.'wait.gif" height=16 width=16 alt="..."'.$gt.' '.GOTMLS_Loading_LANGUAGE.$lt.'/div'.$gt.$lt.'div id="status_bar"'.$gt.$lt.'/div'.$gt.$lt.'p id="pause_button" style="display: none; position: absolute; left: 0; text-align: center; margin-left: -30px; padding-left: 50%;"'.$gt.$lt.'input type="button" value="Pause" class="button-primary" onclick="pauseresume(this);" id="resume_button" /'.$gt.$lt.'/p'.$gt.$lt.'div id="status_counts"'.$gt.$lt.'/div'.$gt.$lt.'p id="fix_button" style="display: none; text-align: center;"'.$gt.$lt.'input id="repair_button" type="submit" value="'.GOTMLS_Automatically_Fix_LANGUAGE.'" class="button-primary" onclick="loadIframe(\'Examine Results\');" /'.$gt.$lt.'/p'.$gt);
			$scan_groups_UL = "";
			foreach ($scan_groups as $scan_name => $scan_group)
				$scan_groups_UL .= "\n{$lt}ul name=\"found_$scan_group\" id=\"found_$scan_group\" class=\"GOTMLS_plugin $scan_group\" style=\"background-color: #ccc; display: none; padding: 0;\"$gt{$lt}a class=\"rounded-corners\" name=\"link_$scan_group\" style=\"float: right; padding: 0 4px; margin: 5px 5px 0 30px; line-height: 16px; text-decoration: none; color: #C00; background-color: #FCC; border: solid #F00 1px;\" href=\"#found_top\" onclick=\"showhide('found_$scan_group');\"{$gt}X$lt/a$gt{$lt}h3$gt$scan_name$lt/h3$gt\n".($scan_group=='potential'?$lt.'p'.$gt.' &nbsp; * '.__("NOTE: These are probably not malicious scripts (but it's a good place to start looking <u>IF</u> your site is infected and no Known Threats were found).",'gotmls').$lt.'/p'.$gt:($scan_group=='wp_core'?$lt.'p'.$gt.' &nbsp; * '.sprintf(__("NOTE: We have detected changes to the WordPress Core files on your site. This could be an intentional modification or the malicious work of a hacker. We can restore these files to their original state to preserve the integrity of your original WordPress %s installation.",'gotmls'), $wp_version).' (for more info '.$lt.'a target="_blank" href="http://gotmls.net/tag/wp-core-files/"'.$gt.__("read my blog",'gotmls').$lt.'/a'.$gt.').'.$lt.'/p'.$gt:$lt.'br /'.$gt)).$lt.'/ul'.$gt;
			if (!($dir = implode(GOTMLS_slash(), array_slice($dirs, 0, -1 * (2 + $_REQUEST["scan_what"]))))) $dir = "/";
			GOTMLS_update_scan_log(array("scan" => array("dir" => $dir, "start" => time(), "type" => htmlentities($_REQUEST["scan_type"]))));
			echo GOTMLS_box($lt.'div style="float: right;"'.$gt.'&nbsp;('.$GLOBALS["GOTMLS"]["log"]["scan"]["dir"].")&nbsp;$lt/div$gt".__("Scan Details:",'gotmls'), $scan_groups_UL);
			$no_flush_LANGUAGE = __("Not flushing OB Handlers: %s",'gotmls');
			if (isset($_REQUEST["no_ob_end_flush"]))
				echo $lt.'div class="error"'.$gt.sprintf($no_flush_LANGUAGE, print_r(ob_list_handlers(), 1))."$lt/div$gt\n";
			elseif (is_array($OB_handlers) && count($OB_handlers)) {
	//			$GOTMLS_OB_handlers = get_option("GOTMLS_OB_handlers", array());
				foreach (array_reverse($OB_handlers) as $OB_handler) {
					if (isset($GOTMLS_OB_handlers[$OB_handler]) && $GOTMLS_OB_handlers[$OB_handler] == "no_end_flush")
						echo $lt.'div class="error"'.$gt.sprintf($no_flush_LANGUAGE, $OB_handler)."$lt/div$gt\n";
					elseif (in_array($OB_handler, $OB_default_handlers)) {
	//					$GOTMLS_OB_handlers[$OB_handler] = "no_end_flush";
	//					update_option("GOTMLS_OB_handlers", $GOTMLS_OB_handlers);
						@ob_end_flush();
	//					$GOTMLS_OB_handlers[$OB_handler] = "ob_end_flush";
	//					update_option("GOTMLS_OB_handlers", $GOTMLS_OB_handlers);
					}
				}
			}
			@ob_start();
			echo "\n{$lt}script type=\"text/javascript\"$gt$li_js\n/*{$lt}!--*"."/";
			if (is_dir($dir)) {
				$GOTMLS_dirs_at_depth[0] = 1;
				$GOTMLS_dir_at_depth[0] = 0;
				if (isset($_REQUEST['scan_only']) && is_array($_REQUEST['scan_only'])) {
					$GOTMLS_dirs_at_depth[0] += (count($_REQUEST['scan_only']) - 1);
					foreach ($_REQUEST['scan_only'] as $only_dir)
						if (is_dir(GOTMLS_trailingslashit($dir).$only_dir))
							GOTMLS_readdir(GOTMLS_trailingslashit($dir).$only_dir);
				} else
					GOTMLS_readdir($dir);
			} else
				echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link("Not a valid directory!"));
			if ($_REQUEST["scan_type"] == "Quick Scan")
				echo GOTMLS_update_status(__("Completed!",'gotmls'), 100);
			else {
				echo GOTMLS_update_status(__("Starting Scan ...",'gotmls'))."/*--{$gt}*"."/";
				echo "\nvar scriptSRC = '".admin_url('admin-ajax.php?action=GOTMLS_scan&'.GOTMLS_set_nonce(__FUNCTION__."1087").'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"].preg_replace('/\&(GOTMLS_scan|mt|GOTMLS_mt|action)=/', '&last_\1=', isset($_SERVER["QUERY_STRING"])&&strlen($_SERVER["QUERY_STRING"])?"&".$_SERVER["QUERY_STRING"]:"").'&GOTMLS_scan=')."';\nvar scanfilesArKeys = new Array('".implode("','", array_keys($GLOBALS["GOTMLS"]["tmp"]["scanfiles"]))."');\nvar scanfilesArNames = new Array('Scanning ".implode("','Scanning ", $GLOBALS["GOTMLS"]["tmp"]["scanfiles"])."');".'
	var scanfilesI = 0;
	var stopScanning;
	var gotStuckOn = "";
	function scanNextDir(gotStuck) {
	clearTimeout(stopScanning);
	if (gotStuck > -1) {
		if (scanfilesArNames[gotStuck].substr(0, 3) != "Re-") {
			if (scanfilesArNames[gotStuck].substr(0, 9) == "Checking ") {
				scanfilesArNames.push(scanfilesArNames[gotStuck]);
				scanfilesArKeys.push(scanfilesArKeys[gotStuck]+"&GOTMLS_skip_file[]="+encodeURIComponent(scanfilesArNames[gotStuck].substr(9)));
			} else {
				scanfilesArNames.push("Re-"+scanfilesArNames[gotStuck]);
				scanfilesArKeys.push(scanfilesArKeys[gotStuck]+"&GOTMLS_only_file=");
			}
		} else {
			scanfilesArNames.push("Got Stuck "+scanfilesArNames[gotStuck]);
			scanfilesArKeys.push(scanfilesArKeys[gotStuck]+"&GOTMLS_skip_dir="+scanfilesArKeys[gotStuck]);
		}
	}
	if (document.getElementById("resume_button").value != "Pause") {
		stopScanning=setTimeout("scanNextDir(-1)", 1000);
		startTime++;
	}
	else if (scanfilesI < scanfilesArKeys.length) {
		document.getElementById("status_text").innerHTML = scanfilesArNames[scanfilesI];
		var newscript = document.createElement("script");
		newscript.setAttribute("src", scriptSRC+scanfilesArKeys[scanfilesI]);
		divx = document.getElementById("found_scanned");
		if (divx)
			divx.appendChild(newscript);
		stopScanning=setTimeout("scanNextDir("+(scanfilesI++)+")",'.$GLOBALS["GOTMLS"]["tmp"]['execution_time'].'000);
	}
	}
	startTime = ('.ceil(time()-$GLOBALS["GOTMLS"]["log"]["scan"]["start"]).'+3);
	stopScanning=setTimeout("scanNextDir(-1)",3000);
	function pauseresume(butt) {
	if (butt.value == "Resume")
		butt.value = "Pause";
	else
		butt.value = "Resume";
	}
	showhide("pause_button", true);'."\n/*{$lt}!--*"."/";
			}
			if (@ob_get_level()) {
				GOTMLS_flush('script');
				@ob_end_flush();
			}
			echo "/*--{$gt}*"."/\n$lt/script$gt";
		} else
			echo GOTMLS_box(GOTMLS_Invalid_Nonce(""), __("Starting a Complete Scan requires a valid Nonce Token. No valid Nonce Token was found at this time, either because the token have expired or because the data was invalid. Please try re-submitting the form above.",'gotmls')."\n{$lt}script type='text/javascript'$gt\nalert('".GOTMLS_Invalid_Nonce("")."');\n$lt/script$gt\n");
	} else
		echo GOTMLS_box(__("Scan Logs",'gotmls'), GOTMLS_get_scanlog());
	echo "\n$lt/div$gt$lt/div$gt$lt/div$gt";
}

function GOTMLS_login_form($form_id = "loginform") {
	$sess = time();
	$ajaxURL = admin_url("admin-ajax.php?action=GOTMLS_logintime&GOTMLS_sess=");
	echo '<input type="hidden" name="sess_id" value="'.substr($sess, 4).'"><input type="hidden" id="offset_id" value="0" name="sess'.substr($sess, 4).'"><script type="text/javascript">'."\nvar GOTMLS_login_offset = new Date();\nvar GOTMLS_login_script = document.createElement('script');\nGOTMLS_login_script.src = '$ajaxURL'+GOTMLS_login_offset.getTime();\n\ndocument.head.appendChild(GOTMLS_login_script);\n</script>\n";//GOTMLS_login_script.onload = set_offset_id();
}
add_action("login_form", "GOTMLS_login_form");

function GOTMLS_ajax_logintime() {
	@header("Content-type: text/javascript");
	$sess = (false && isset($_GET["GOTMLS_sess"]) && is_numeric($_GET["GOTMLS_sess"])) ? $_GET["sess"] : time();
	die("\n//Permission Error: User not authenticated!\nvar GOTMLS_login_offset = new Date();\nvar GOTMLS_login_offset_start = GOTMLS_login_offset.getTime() - ".$sess."000;\nfunction set_offset_id() {\n\tGOTMLS_login_offset = new Date();\n\tif (form_login = document.getElementById('offset_id'))\n\t\tform_login.value = GOTMLS_login_offset.getTime() - GOTMLS_login_offset_start;\n\tsetTimeout(set_offset_id, 15673);\n}\nset_offset_id();");
}
add_action('wp_ajax_nopriv_GOTMLS_logintime', 'GOTMLS_ajax_logintime');
add_action('wp_ajax_GOTMLS_logintime', 'GOTMLS_ajax_logintime');

function GOTMLS_ajax_lognewkey() {
	@header("Content-type: text/javascript");
	if (GOTMLS_get_nonce()) {
		if (isset($_POST["GOTMLS_installation_key"]) && ($_POST["GOTMLS_installation_key"] == GOTMLS_installation_key)) {
			$keys = maybe_unserialize(get_option('GOTMLS_Installation_Keys', array()));
			if (is_array($keys)) {
				$count = count($keys);
				if (!array_key_exists(GOTMLS_installation_key, $keys))
					$keys = array_merge($keys, array(GOTMLS_installation_key => GOTMLS_siteurl));
			} else
				$keys = array(GOTMLS_installation_key => GOTMLS_siteurl);
			update_option("GOTMLS_Installation_Keys", serialize($keys));
			die("\n//$count~".count($keys));
		} else
			die("\n//0");
	} else
		die(GOTMLS_Invalid_Nonce("\n//Log New Key Error: ")."\n");
}
add_action('wp_ajax_GOTMLS_lognewkey', 'GOTMLS_ajax_lognewkey');
add_action('wp_ajax_nopriv_GOTMLS_lognewkey', 'GOTMLS_ajax_nopriv');

function GOTMLS_set_plugin_action_links($links_array, $plugin_file) {
	if ($plugin_file == substr(str_replace("\\", "/", __FILE__), (-1 * strlen($plugin_file))) && strlen($plugin_file) > 10)
		$links_array = array_merge(array('<a href="'.admin_url('admin.php?page=GOTMLS-settings').'"><span style="font-size: 20px; height: 20px; width: 20px;" class="dashicons dashicons-admin-settings"></span>'.GOTMLS_Scan_Settings_LANGUAGE.'</a>'), $links_array);
	return $links_array;
}
add_filter("plugin_action_links", "GOTMLS_set_plugin_action_links", 1, 2);

function GOTMLS_set_plugin_row_meta($links_array, $plugin_file) {
	if ($plugin_file == substr(str_replace("\\", "/", __FILE__), (-1 * strlen($plugin_file))) && strlen($plugin_file) > 10)
		$links_array = array_merge($links_array, array('<a target="_blank" href="http://gotmls.net/faqs/">FAQ</a>','<a target="_blank" href="http://gotmls.net/support/">Support</a>','<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QZHD8QHZ2E7PE"><span style="font-size: 20px; height: 20px; width: 20px;" class="dashicons dashicons-heart"></span>Donate</a>'));
	return $links_array;
}
add_filter("plugin_row_meta", "GOTMLS_set_plugin_row_meta", 1, 2);

function GOTMLS_in_plugin_update_message($args) {
	$transient_name = 'GOTMLS_upgrade_notice_'.$args["Version"].'_'.$args["new_version"];
	if ((false === ($upgrade_notice = get_transient($transient_name))) && ($ret = GOTMLS_get_URL("https://plugins.svn.wordpress.org/gotmls/trunk/readme.txt"))) {
		$upgrade_notice = '';
		if ($match = preg_split('/==\s*Upgrade Notice\s*==\s+/i', $ret)) {
			if (preg_match('/\n+=\s*'.str_replace(".", "\\.", GOTMLS_Version).'\s*=\s+/is', $match[1]))
				$notice = (array) preg_split('/\n+=\s*'.str_replace(".", "\\.", GOTMLS_Version).'\s*=\s+/is', $match[1]);
			else
				$notice = (array) preg_split('/\n+=/is', $match[1]."\n=");
			$upgrade_notice .= '<div class="GOTMLS_upgrade_notice">'.preg_replace('/=\s*([\.0-9]+)\s*=\s*([^=]+)/i', '<li><b>${1}:</b> ${2}</li>', preg_replace('~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $notice[0])).'</div>';
			set_transient($transient_name, $upgrade_notice, DAY_IN_SECONDS);
		}
	}
	echo $upgrade_notice;
}
add_action("in_plugin_update_message-gotmls/index.php", "GOTMLS_in_plugin_update_message");

function GOTMLS_init() {
	if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"]))
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"] = 2;
	if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"]))
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"] = -1;
	if (isset($_REQUEST["scan_type"]) && $_REQUEST["scan_type"] == "Quick Scan") {
		if (!isset($_REQUEST["scan_what"]))	$_REQUEST["scan_what"] = 2;
		if (!isset($_REQUEST["scan_depth"]))
			$_REQUEST["scan_depth"] = 2;
		if (!isset($_REQUEST["scan_only"]))
			$_REQUEST["scan_only"] = array("","wp-includes","wp-admin");
		if ($_REQUEST["scan_only"] && !is_array($_REQUEST["scan_only"]))
			$_REQUEST["scan_only"] = array($_REQUEST["scan_only"]);
	}//$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"] = stripslashes($_POST["check_custom"]);
	if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"]))
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"] = "";
	if (isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"]) && is_numeric($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"]))
		$scan_level = intval($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"]);
	else
		$scan_level = count(explode('/', trailingslashit(get_option("siteurl")))) - 1;
	if (GOTMLS_get_nonce()) {
		if (isset($_REQUEST["dont_check"]) && is_array($_REQUEST["dont_check"]) && count($_REQUEST["dont_check"]))
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"] = $_REQUEST["dont_check"];
		elseif (isset($_POST["scan_type"]) || !(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"])))
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"] = array();
		if (isset($_POST["scan_level"]) && is_numeric($_POST["scan_level"]))
			$scan_level = intval($_POST["scan_level"]);
		if (isset($scan_level) && is_numeric($scan_level))
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"] = intval($scan_level);
	}
	if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"]))
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"] = count(explode('/', trailingslashit(get_option("siteurl")))) - 1;
}
add_action("admin_init", "GOTMLS_init");

function GOTMLS_ajax_position() {
	if (GOTMLS_get_nonce()) {
		$GLOBALS["GOTMLS_msg"] = __("Default position",'gotmls');
		$properties = array("body" => 'style="margin: 0; padding: 0;"');
		if (isset($_GET["GOTMLS_msg"]) && $_GET["GOTMLS_msg"] == $GLOBALS["GOTMLS_msg"]) {
			$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"] = $GLOBALS["GOTMLS"]["tmp"]["default"]["msg_position"];
			$gl = '><';
			$properties["html"] = $gl.'head'.$gl.'script type="text/javascript">
	if (curDiv = window.parent.document.getElementById("div_file")) {
		curDiv.style.left = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0].'";
		curDiv.style.top = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1].'";
		curDiv.style.height = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2].'";
		curDiv.style.width = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3].'";
	}
	</script'.$gl.'/head';
		} elseif (isset($_GET["GOTMLS_x"]) || isset($_GET["GOTMLS_y"]) || isset($_GET["GOTMLS_h"]) || isset($_GET["GOTMLS_w"])) {
			if (isset($_GET["GOTMLS_x"]))
				$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0] = $_GET["GOTMLS_x"];
			if (isset($_GET["GOTMLS_y"]))
				$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1] = $_GET["GOTMLS_y"];
			if (isset($_GET["GOTMLS_h"]))
				$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2] = $_GET["GOTMLS_h"];
			if (isset($_GET["GOTMLS_w"]))
				$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3] = $_GET["GOTMLS_w"];
			$_GET["GOTMLS_msg"] = __("New position",'gotmls');
		} else
			die("\n//Position Error: No new position to save!\n");
		update_option("GOTMLS_settings_array", $GLOBALS["GOTMLS"]["tmp"]["settings_array"]);
		die(GOTMLS_html_tags(array("html" => array("body" => htmlentities($_GET["GOTMLS_msg"]).' '.__("saved.",'gotmls').(implode($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"]) == implode($GLOBALS["GOTMLS"]["tmp"]["default"]["msg_position"])?"":' <a href="'.admin_url('admin-ajax.php?action=GOTMLS_position&'.GOTMLS_set_nonce(__FUNCTION__."1350").'&GOTMLS_msg='.urlencode($GLOBALS["GOTMLS_msg"])).'">['.$GLOBALS["GOTMLS_msg"].']</a>'))), $properties));
	} else
		die(GOTMLS_Invalid_Nonce("\n//Position Error: ")."\n");
}
add_action('wp_ajax_GOTMLS_position', 'GOTMLS_ajax_position');

function GOTMLS_ajax_empty_trash() {
	global $wpdb;
	$gl = '><';
	if (GOTMLS_get_nonce()) {
		if ($trashed = $wpdb->query("DELETE FROM $wpdb->posts WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` != 'private'")) {
			$wpdb->query("REPAIR TABLE $wpdb->posts");
			$trashmsg = __("Emptied $trashed item from the quarantine trash.",'gotmls');
		} else
			$trashmsg = __("Failed to empty the trash.",'gotmls'); 
	} else
		$trashmsg = GOTMLS_Invalid_Nonce("");
	$properties = array("html" => $gl.'head'.$gl."script type='text/javascript'>\nif (curDiv = window.parent.document.getElementById('empty_trash_link'))\n\tcurDiv.style.display = 'none';\nalert('$trashmsg');\n</script$gl/head", "body" => 'style="margin: 0; padding: 0;"');
	die(GOTMLS_html_tags(array("html" => array("body" => $trashmsg)), $properties));
}
add_action('wp_ajax_GOTMLS_empty_trash', 'GOTMLS_ajax_empty_trash');

function GOTMLS_ajax_whitelist() {
	if (GOTMLS_get_nonce()) {
		if (isset($_POST['GOTMLS_whitelist']) && isset($_POST['GOTMLS_chksum'])) {
			$file = GOTMLS_decode($_POST['GOTMLS_whitelist']);
			$chksum = explode("O", $_POST['GOTMLS_chksum']."O");
			if (strlen($chksum[0]) == 32 && strlen($chksum[1]) == 32 && is_file($file) && md5(@file_get_contents($file)) == $chksum[0]) {
				$filesize = @filesize($file);
				if (true) {
					if (!isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"][$file][0]))
						$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"][$file][0] = "A0002";
					$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"][$file][$chksum[0].'O'.$filesize] = "A0002";
				} else
					unset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"][$file]);
				GOTMLS_update_option("definitions", $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]);
				$body = "Added $file to Whitelist!<br />\n<iframe style='width: 90%; height: 250px; border: none;' src='".GOTMLS_plugin_home."whitelist.html?whitelist=".$_POST['GOTMLS_whitelist']."&hash=$chksum[0]&size=$filesize&key=$chksum[1]'></iframe>";
			} else
				$body = "<li>Invalid Data!</li>";
			die(GOTMLS_html_tags(array("html" => array("body" => $body))));
		} else
			die("\n//Whitelist Error: Invalid checksum!\n");
	} else
		die(GOTMLS_Invalid_Nonce("\n//Whitelist Error: ")."\n");
}
add_action('wp_ajax_GOTMLS_whitelist', 'GOTMLS_ajax_whitelist');

function GOTMLS_ajax_fix() {
	if (GOTMLS_get_nonce()) {
		if (isset($_POST["GOTMLS_fix"]) && !is_array($_POST["GOTMLS_fix"]))
			$_POST["GOTMLS_fix"] = array($_POST["GOTMLS_fix"]);
		if (isset($_REQUEST["GOTMLS_fix"]) && is_array($_REQUEST["GOTMLS_fix"]) && isset($_REQUEST["GOTMLS_fixing"]) && $_REQUEST["GOTMLS_fixing"]) {
			GOTMLS_update_scan_log(array("settings" => $GLOBALS["GOTMLS"]["tmp"]["settings_array"]));
			$callAlert = "clearTimeout(callAlert);\ncallAlert=setTimeout('alert_repaired(1)', 30000);";
			$li_js = "\n<script type=\"text/javascript\">\nvar callAlert;\nfunction alert_repaired(failed) {\nclearTimeout(callAlert);\nif (failed)\nfilesFailed='the rest, try again to change more.';\nwindow.parent.check_for_donation('Changed '+filesFixed+' files, failed to change '+filesFailed);\n}\n$callAlert\nwindow.parent.showhide('GOTMLS_iFrame', true);\nfilesFixed=0;\nfilesFailed=0;\nfunction fixedFile(file) {\n filesFixed++;\nif (li_file = window.parent.document.getElementById('check_'+file))\n\tli_file.checked=false;\nif (li_file = window.parent.document.getElementById('list_'+file))\n\tli_file.className='GOTMLS_plugin';\nif (li_file = window.parent.document.getElementById('GOTMLS_quarantine_'+file)) {\n\tli_file.style.display='none';\n\tli_file.innerHTML='';\n\t}\n}\nfunction DeletedFile(file) {\n filesFixed++;\nif (li_file = window.parent.document.getElementById('check_'+file))\n\tli_file.checked=false;\nif (li_file = window.parent.document.getElementById('GOTMLS_quarantine_'+file)) {\n\tli_file.style.display='none';\n\tli_file.innerHTML='';\n\t}}\nfunction failedFile(file) {\n filesFailed++;\nwindow.parent.document.getElementById('check_'+file).checked=false; \n}\n</script>\n<script type=\"text/javascript\">\n/*<!--*"."/";
			@set_time_limit($GLOBALS["GOTMLS"]["tmp"]['execution_time'] * 2);
			$HTML = explode("split-here-for-content", GOTMLS_html_tags(array("html" => array("body" => "split-here-for-content"))));
			echo $HTML[0];
			GOTMLS_update_scan_log(array("scan" => array("dir" => count($_REQUEST["GOTMLS_fix"])." Files", "start" => time())));
			foreach ($_REQUEST["GOTMLS_fix"] as $clean_file) {
				if (is_numeric($clean_file)) {
					if (($Q_post = GOTMLS_get_quarantine($clean_file)) && isset($Q_post["post_type"]) && strtolower($Q_post["post_type"]) == "gotmls_quarantine" && isset($Q_post["post_status"]) && strtolower($Q_post["post_status"]) == "private") {
						$path = $Q_post["post_title"];
						if ($_REQUEST["GOTMLS_fixing"] > 1) {
							echo "<li>Removing $path ... ";
							$Q_post["post_status"] = "trash";
							if (wp_update_post($Q_post)) {
								echo __("Done!",'gotmls');
								$li_js .= "/*-->*"."/\nDeletedFile('$clean_file');\n/*<!--*"."/";
							} else {
								echo __("Failed to delete!",'gotmls');
								$li_js .= "/*-->*"."/\nfailedFile('$clean_file');\n/*<!--*"."/";
							}
							GOTMLS_update_scan_log(array("scan" => array("finish" => time(), "type" => "Removal from Quarantine")));
						} else {
							echo "<li>Restoring $path ... ";
							$Q_post["post_status"] = "pending";
							if (GOTMLS_file_put_contents($path, GOTMLS_decode($Q_post["post_content"])) && wp_update_post($Q_post)) {
								echo __("Complete!",'gotmls');
								$li_js .= "/*-->*"."/\nfixedFile('$clean_file');\n/*<!--*"."/";
							} else {
								echo __("Restore Failed!",'gotmls');
								$li_js .= "/*-->*"."/\nfailedFile('$clean_file');\n/*<!--*"."/";
							}
							GOTMLS_update_scan_log(array("scan" => array("finish" => time(), "type" => "Restoration from Quarantine")));
						}
						echo "</li>\n$li_js/*-->*"."/\n$callAlert\n</script>\n";
						$li_js = "<script type=\"text/javascript\">\n/*<!--*"."/";
					}//else print_r(array("i:$clean_file"=>$Q_post));
				} else {
					$path = realpath(GOTMLS_decode($clean_file));
					if (is_file($path)) {
						echo "<li>Fixing $path ... ";
						$li_js .= GOTMLS_scanfile($path);
						echo "</li>\n$li_js/*-->*"."/\n$callAlert\n//".$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]."\n</script>\n";
						$li_js = "<script type=\"text/javascript\">\n/*<!--*"."/";
					} else
						echo "<li>".__("File ".htmlentities($path)." not found!",'gotmls')."</li>";
					GOTMLS_update_scan_log(array("scan" => array("finish" => time(), "type" => "Automatic Fix")));
				}
			}
			$nonce = GOTMLS_set_nonce(__FUNCTION__."1593");
			die('<div id="check_site_warning" style="background-color: #F00;">'.sprintf(__("Because some changes were made we need to check to make sure it did not break your site. If this stays Red and the frame below does not load please <a %s>revert the changes</a> made during this automated fix process.",'gotmls'), 'href="'.GOTMLS_images_path.'?page=GOTMLS-View-Quarantine&'.$nonce.'"').' <span style="color: #F00;">'.__("Never mind, it worked!",'gotmls').'</span></div><br /><iframe id="test_frame" name="test_frame" src="'.admin_url('admin.php?page=GOTMLS-settings&check_site=1&'.$nonce).'" style="width: 100%; height: 200px"></iframe>'.$li_js."/*-->*"."/\nalert_repaired(0);\n</script>\n$HTML[1]");
		} else
			die(GOTMLS_html_tags(array("html" => array("body" => "<script type=\"text/javascript\">\nwindow.parent.showhide('GOTMLS_iFrame', true);\nalert('".__("Nothing Selected to be Changed!",'gotmls')."');\n</script>".__("Done!",'gotmls')))));
	} else
		die(GOTMLS_html_tags(array("html" => array("body" => "<script type=\"text/javascript\">\nwindow.parent.showhide('GOTMLS_iFrame', true);\nalert('".GOTMLS_Invalid_Nonce("")."');\n</script>".__("Done!",'gotmls')))));
}
add_action('wp_ajax_GOTMLS_fix', 'GOTMLS_ajax_fix');

function GOTMLS_ajax_scan() {
	if (GOTMLS_get_nonce()) {
		@error_reporting(0);
		if (isset($_GET["GOTMLS_scan"])) {
			@set_time_limit($GLOBALS["GOTMLS"]["tmp"]['execution_time'] - 5);
			if (is_numeric($_GET["GOTMLS_scan"])) {
				if (($Q_post = GOTMLS_get_quarantine($_GET["GOTMLS_scan"])) && isset($Q_post["post_type"]) && $Q_post["post_type"] == "GOTMLS_quarantine" && isset($Q_post["post_status"]) && $Q_post["post_status"] == "private") {
					$clean_file = $Q_post["post_title"];
					$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = GOTMLS_decode($Q_post["post_content"]);
					$fa = "";
					$function = 'GOTMLS_decode';
					if (isset($_GET[$function]) && is_array($_GET[$function])) {
						foreach ($_GET[$function] as $decode) {
							$fa .= " NO-$decode";
						}
					} elseif (isset($Q_post["post_excerpt"]) && strlen($Q_post["post_excerpt"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"] = @maybe_unserialize(GOTMLS_decode($Q_post["post_excerpt"])))) {
						$f = 1;
						//print_r(array("excerpt:"=>$GLOBALS["GOTMLS"]["tmp"]["threats_found"]));
						foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found => $threats_name) {
							list($start, $end, $junk) = explode("-", "$threats_found--", 3);
							if (strlen($end) > 0 && is_numeric($start) && is_numeric($end)) {
								if ($start < $end)
									$fa .= ' <a title="'.htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$start.', '.$end.');">['.$f++.']</a>';
								else
									$fa .= ' <a title="'.htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$end.', '.$start.');">['.$f++.']</a>';
							} else {
								if (is_numeric($threats_found)) {
									$threats_found = $threats_name;
									$threats_name = $f;
								}
								$fpos = 0;
								$flen = 0;
								$potential_threat = str_replace("\r", "", $threats_found);
								while (($fpos = strpos(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"]), ($potential_threat), $flen + $fpos)) !== false) {
									$flen = strlen($potential_threat);
									$fa .= ' <a title="'.htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.($fpos).', '.($fpos + $flen).');">['.$f++.']</a>';
								}
							}
						}
					} //else echo "excerpt:".$Q_post["post_excerpt"];
//					foreach ($decode_list as $decode => $regex)	if (preg_match($regex.substr($GLOBALS["GOTMLS"]["tmp"]["default_ext"], 0, 1),  $GLOBALS["GOTMLS"]["tmp"]["file_contents"]))	$fa .= ' <a href="'.GOTMLS_script_URI.'&'.$function.'[]='.$decode.'">decode['.$decode.']</a>';
					die("\n".'<script type="text/javascript">
function select_text_range(ta_id, start, end) {
	var textBox = document.getElementById(ta_id);
	var scrolledText = "";
	scrolledText = textBox.value.substring(0, end);
	textBox.focus();
	if (textBox.setSelectionRange) {
		scrolledText = textBox.value.substring(end);
		textBox.value = textBox.value.substring(0, end);
		textBox.scrollTop = textBox.scrollHeight;
		textBox.value = textBox.value + scrolledText;
		textBox.setSelectionRange(start, end);
	} else if (textBox.createTextRange) {
		var range = textBox.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end);
		range.select();
	} else
		alert("The highlighting function does not work in your browser");
}
window.parent.showhide("GOTMLS_iFrame", true);
</script><table style="top: 0px; left: 0px; width: 100%; height: 100%; position: absolute;"><tr><td style="width: 100%"><form style="margin: 0;" method="post" action="'.admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."1522")).'" onsubmit="return confirm(\''.__("Are you sure you want to delete this file from the quarantine?",'gotmls').'\');"><input type="hidden" name="GOTMLS_fix[]" value="'.$Q_post["ID"].'"><input type="hidden" name="GOTMLS_fixing" value="2"><input type="hidden" name="action" value="GOTMLS_fix"><input type="submit" value="DELETE from Quarantine" style="background-color: #C00; float: right;"></form><div id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>File Details</b><br />encoding: '.(function_exists("mb_detect_encoding")?mb_detect_encoding($GLOBALS["GOTMLS"]["tmp"]["file_contents"]):"Unknown").'<br />size: '.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).' bytes<br />infected:'.$Q_post["post_modified_gmt"].'<br />quarantined:'.$Q_post["post_date_gmt"].'</div><div style="overflow: auto;"><span onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';">'.__("File Details:",'gotmls').'</span> ('.$fa.' )</div></td></tr><tr><td style="height: 100%"><textarea id="ta_file" style="width: 100%; height: 100%">'.htmlentities(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"])).'</textarea></td></tr></table>');
				} else
					die(GOTMLS_html_tags(array("html" => array("body" => __("This file no longer exists in the quarantine.",'gotmls')."<br />\n<script type=\"text/javascript\">\nwindow.parent.showhide('GOTMLS_iFrame', true);\n</script>"))));
			} else {
				$file = GOTMLS_decode($_GET["GOTMLS_scan"]);
				if (is_dir($file)) {
					@error_reporting(0);
					@header("Content-type: text/javascript");
					if (isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"]))
						$GLOBALS["GOTMLS"]["tmp"]["skip_ext"] = $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"];
					@ob_start();
					echo GOTMLS_scandir($file);
					if (@ob_get_level()) {
						GOTMLS_flush();
						@ob_end_flush();
					}
					die('//END OF JavaScript');
				} else {
					if (!file_exists($file))
						die(GOTMLS_html_tags(array("html" => array("body" => sprintf(__("The file %s does not exist, it must have already been deleted.",'gotmls'), $file)."<script type=\"text/javascript\">\nwindow.parent.showhide('GOTMLS_iFrame', true);\n</script>"))));
					else {
						GOTMLS_scanfile($file);
						$fa = "";
						$function = 'GOTMLS_decode';
						if (isset($_GET[$function]) && is_array($_GET[$function])) {
							foreach ($_GET[$function] as $decode) {
								$fa .= " NO-$decode";
							}
						} elseif (isset($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
							$f = 1;
							foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found=>$threats_name) {
								list($start, $end, $junk) = explode("-", "$threats_found--", 3);
								if ($start > $end)
									$fa .= 'ERROR['.($f++).']: Threat_size{'.$threats_found.'} Content_size{'.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'}';
								else
									$fa .= ' <a title="'.htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$start.', '.$end.');">['.$f++.']</a>';
							}
						} else
							$fa = " No Threats Found";
//						foreach ($decode_list as $decode => $regex)	if (preg_match($regex.substr($GLOBALS["GOTMLS"]["tmp"]["default_ext"], 0, 1),  $GLOBALS["GOTMLS"]["tmp"]["file_contents"]))	$fa .= ' <a href="'.GOTMLS_script_URI.'&'.$function.'[]='.$decode.'">decode['.$decode.']</a>';
						die("\n".'<script type="text/javascript">
function select_text_range(ta_id, start, end) {
	var textBox = document.getElementById(ta_id);
	var scrolledText = "";
	scrolledText = textBox.value.substring(0, end);
	textBox.focus();
	if (textBox.setSelectionRange) {
		scrolledText = textBox.value.substring(end);
		textBox.value = textBox.value.substring(0, end);
		textBox.scrollTop = textBox.scrollHeight;
		textBox.value = textBox.value + scrolledText;
		textBox.setSelectionRange(start, end);
	} else if (textBox.createTextRange) {
		var range = textBox.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end);
		range.select();
	} else
		alert("The highlighting function does not work in your browser");
}
window.parent.showhide("GOTMLS_iFrame", true);
</script><table style="top: 0px; left: 0px; width: 100%; height: 100%; position: absolute;"><tr><td style="width: 100%"><form style="margin: 0;" method="post" action="'.admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."1583")).'" onsubmit="return confirm(\''.__("Are you sure this file is not infected and you want to ignore it in future scans?",'gotmls').'\');"><input type="hidden" name="GOTMLS_whitelist" value="'.GOTMLS_encode($file).'"><input type="hidden" name="action" value="GOTMLS_whitelist"><input type="hidden" name="GOTMLS_chksum" value="'.md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'O'.GOTMLS_installation_key.'"><input type="submit" value="Whitelist this file" style="float: right;"></form><div id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>File Details: '.basename($file).'</b><br />in: '.dirname(realpath($file)).'<br />encoding: '.(function_exists("mb_detect_encoding")?mb_detect_encoding($GLOBALS["GOTMLS"]["tmp"]["file_contents"]):"Unknown").'<br />size: '.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).' ('.filesize(realpath($file)).'bytes)<br />permissions: '.GOTMLS_fileperms(realpath($file)).'<br />Owner/Group: '.fileowner(realpath($file)).'/'.filegroup(realpath($file)).' (you are: '.getmyuid().'/'.getmygid().')<br />modified:'.date(" Y-m-d H:i:s ", filemtime(realpath($file))).'<br />changed:'.date(" Y-m-d H:i:s ", filectime(realpath($file))).'</div><div style="overflow: auto;"><span onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';">'.__("Potential threats in file:",'gotmls').'</span> ('.$fa.' )</div></td></tr><tr><td style="height: 100%"><textarea id="ta_file" style="width: 100%; height: 100%">'.htmlentities(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"])).'</textarea></td></tr></table>');
					}
				}
			}
		} else
			die("\n//Directory Error: Nothing to scan!\n");
	} else {
		if (isset($_GET["GOTMLS_scan"]) && is_dir(GOTMLS_decode($_GET["GOTMLS_scan"])))
			@header("Content-type: text/javascript");
		die(GOTMLS_Invalid_Nonce("\n//Ajax Scan Error: ")."\n");
	}
}
add_action('wp_ajax_GOTMLS_scan', 'GOTMLS_ajax_scan');

function GOTMLS_ajax_nopriv() {
	die("\n//Permission Error: User not authenticated!\n");
}
add_action('wp_ajax_nopriv_GOTMLS_scan', 'GOTMLS_ajax_nopriv');
add_action('wp_ajax_nopriv_GOTMLS_position', 'GOTMLS_ajax_nopriv');
add_action('wp_ajax_nopriv_GOTMLS_fix', 'GOTMLS_ajax_nopriv');
add_action('wp_ajax_nopriv_GOTMLS_whitelist', 'GOTMLS_ajax_nopriv');
add_action('wp_ajax_nopriv_GOTMLS_empty_trash', 'GOTMLS_ajax_nopriv');
add_action('wp_ajax_nopriv_GOTMLS_auto_update', 'GOTMLS_update_definitions');

add_action("plugins_loaded", "GOTMLS_loaded");
add_action("admin_notices", "GOTMLS_admin_notices");
add_action("admin_menu", "GOTMLS_menu");
add_action("network_admin_menu", "GOTMLS_menu");
