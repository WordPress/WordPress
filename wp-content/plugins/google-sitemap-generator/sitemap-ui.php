<?php
/*

 $Id: sitemap-ui.php 935247 2014-06-19 17:13:03Z arnee $

*/

class GoogleSitemapGeneratorUI {

	/**
	 * The Sitemap Generator Object
	 *
	 * @var GoogleSitemapGenerator
	 */
	private $sg = null;


	public function __construct(GoogleSitemapGenerator $sitemapBuilder) {
		$this->sg = $sitemapBuilder;
	}

	private function HtmlPrintBoxHeader($id, $title) {
		?>
			<div id="<?php echo $id; ?>" class="postbox">
				<h3 class="hndle"><span><?php echo $title ?></span></h3>
				<div class="inside">
		<?php
	}

	private function HtmlPrintBoxFooter() {
			?>
				</div>
			</div>
		<?php
	}

	/**
	 * Echos option fields for an select field containing the valid change frequencies
	 *
	 * @since 4.0
	 * @param $currentVal mixed The value which should be selected
	 */
	public function HtmlGetFreqNames($currentVal) {

		foreach($this->sg->GetFreqNames() AS $k=>$v) {
			echo "<option value=\"" . esc_attr($k) . "\" " . self::HtmlGetSelected($k,$currentVal) .">" . esc_attr($v) . "</option>";
		}
	}

	/**
	 * Echos option fields for an select field containing the valid priorities (0- 1.0)
	 *
	 * @since 4.0
	 * @param $currentVal string The value which should be selected
	 * @return void
	 */
	public static function HtmlGetPriorityValues($currentVal) {
		$currentVal=(float) $currentVal;
		for($i=0.0; $i<=1.0; $i+=0.1) {
			$v = number_format($i,1,".","");
			echo "<option value=\"" . esc_attr($v) . "\" " . self::HtmlGetSelected("$i","$currentVal") .">";
			echo esc_attr(number_format_i18n($i,1));
			echo "</option>";
		}
	}

	/**
	 * Returns the checked attribute if the given values match
	 *
	 * @since 4.0
	 * @param $val string The current value
	 * @param $equals string The value to match
	 * @return string The checked attribute if the given values match, an empty string if not
	 */
	public static function HtmlGetChecked($val, $equals) {
		if($val==$equals) return self::HtmlGetAttribute("checked");
		else return "";
	}

	/**
	 * Returns the selected attribute if the given values match
	 *
	 * @since 4.0
	 * @param $val string The current value
	 * @param $equals string The value to match
	 * @return string The selected attribute if the given values match, an empty string if not
	 */
	public static function HtmlGetSelected($val,$equals) {
		if($val==$equals) return self::HtmlGetAttribute("selected");
		else return "";
	}

	/**
	 * Returns an formatted attribute. If the value is NULL, the name will be used.
	 *
	 * @since 4.0
	 * @param $attr string The attribute name
	 * @param $value string The attribute value
	 * @return string The formatted attribute
	 */
	public static function HtmlGetAttribute($attr,$value=NULL) {
		if($value==NULL) $value=$attr;
		return " " . $attr . "=\"" . esc_attr($value) . "\" ";
	}

	/**
	 * Returns an array with GoogleSitemapGeneratorPage objects which is generated from POST values
	 *
	 * @since 4.0
	 * @see GoogleSitemapGeneratorPage
	 * @return array An array with GoogleSitemapGeneratorPage objects
	 */
	public function HtmlApplyPages() {
		// Array with all page URLs
		$pages_ur=(!isset($_POST["sm_pages_ur"]) || !is_array($_POST["sm_pages_ur"])?array():$_POST["sm_pages_ur"]);

		//Array with all priorities
		$pages_pr=(!isset($_POST["sm_pages_pr"]) || !is_array($_POST["sm_pages_pr"])?array():$_POST["sm_pages_pr"]);

		//Array with all change frequencies
		$pages_cf=(!isset($_POST["sm_pages_cf"]) || !is_array($_POST["sm_pages_cf"])?array():$_POST["sm_pages_cf"]);

		//Array with all lastmods
		$pages_lm=(!isset($_POST["sm_pages_lm"]) || !is_array($_POST["sm_pages_lm"])?array():$_POST["sm_pages_lm"]);

		//Array where the new pages are stored
		$pages=array();
		//Loop through all defined pages and set their properties into an object
		if(isset($_POST["sm_pages_mark"]) && is_array($_POST["sm_pages_mark"])) {
			for($i=0; $i<count($_POST["sm_pages_mark"]); $i++) {
				//Create new object
				$p=new GoogleSitemapGeneratorPage();
				if(substr($pages_ur[$i],0,4)=="www.") $pages_ur[$i]="http://" . $pages_ur[$i];
				$p->SetUrl($pages_ur[$i]);
				$p->SetProprity($pages_pr[$i]);
				$p->SetChangeFreq($pages_cf[$i]);
				//Try to parse last modified, if -1 (note ===) automatic will be used (0)
				$lm=(!empty($pages_lm[$i])?strtotime($pages_lm[$i],time()):-1);
				if($lm===-1) $p->setLastMod(0);
				else $p->setLastMod($lm);
				//Add it to the array
				array_push($pages,$p);
			}
		}

		return $pages;
	}

	/**
	 * Displays the option page
	 *
	 * @since 3.0
	 * @access public
	 * @author Arne Brachhold
	 */
	public function HtmlShowOptionsPage() {
		global $wp_version;

		$snl = false; //SNL

		$this->sg->Initate();

		$message="";

		if(!empty($_REQUEST["sm_rebuild"])) { //Pressed Button: Rebuild Sitemap
			check_admin_referer('sitemap');


			if(isset($_GET["sm_do_debug"]) && $_GET["sm_do_debug"]=="true") {

				//Check again, just for the case that something went wrong before
				if(!current_user_can("administrator") || !is_super_admin()) {
					echo '<p>Please log in as admin</p>';
					return;
				}

				$oldErr = error_reporting(E_ALL);
				$oldIni = ini_set("display_errors",1);

				echo '<div class="wrap">';
				echo '<h2>' .  __('XML Sitemap Generator for WordPress', 'sitemap') .  " " . $this->sg->GetVersion(). '</h2>';
				echo '<p>This is the debug mode of the XML Sitemap Generator. It will show all PHP notices and warnings as well as the internal logs, messages and configuration.</p>';
				echo '<p style="font-weight:bold; color:red; padding:5px; border:1px red solid; text-align:center;">DO NOT POST THIS INFORMATION ON PUBLIC PAGES LIKE SUPPORT FORUMS AS IT MAY CONTAIN PASSWORDS OR SECRET SERVER INFORMATION!</p>';
				echo "<h3>WordPress and PHP Information</h3>";
				echo '<p>WordPress ' . $GLOBALS['wp_version'] . ' with ' . ' DB ' . $GLOBALS['wp_db_version'] . ' on PHP ' . phpversion() . '</p>';
				echo '<p>Plugin version: ' . $this->sg->GetVersion() . ' (' . $this->sg->GetSvnVersion() . ')';
				echo '<h4>Environment</h4>';
				echo "<pre>";
				$sc = $_SERVER;
				unset($sc["HTTP_COOKIE"]);
				print_r($sc);
				echo "</pre>";
				echo "<h4>WordPress Config</h4>";
				echo "<pre>";
				$opts = array();
				if(function_exists('wp_load_alloptions')) {
					$opts = wp_load_alloptions();
				} else {
					/** @var $wpdb wpdb*/
					global $wpdb;
					$os = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options");
					foreach ( (array) $os as $o ) $opts[$o->option_name] = $o->option_value;
				}

				$popts = array();
				foreach($opts as $k=>$v) {
					//Try to filter out passwords etc...
					if(preg_match("/pass|login|pw|secret|user|usr|key|auth|token/si",$k)) continue;
					$popts[$k] = htmlspecialchars($v);
				}
				print_r($popts);
				echo "</pre>";
				echo '<h4>Sitemap Config</h4>';
				echo "<pre>";
				print_r($this->sg->GetOptions());
				echo "</pre>";
				echo '<h3>Sitemap Content and Errors, Warnings, Notices</h3>';
				echo '<div>';

				$sitemaps = $this->sg->SimulateIndex();

				foreach($sitemaps AS $sitemap) {

					/** @var $s GoogleSitemapGeneratorSitemapEntry */
					$s = $sitemap["data"];

					echo "<h4>Sitemap: <a href=\"" . $s->GetUrl() . "\">" . $sitemap["type"] . "/" . ($sitemap["params"]?$sitemap["params"]:"(No parameters)") .  "</a> by " . $sitemap["caller"]["class"] . "</h4>";

					$res = $this->sg->SimulateSitemap($sitemap["type"], $sitemap["params"]);

					echo "<ul style='padding-left:10px;'>";
					foreach($res AS $s) {
						/** @var $d GoogleSitemapGeneratorSitemapEntry */
						$d = $s["data"];
						echo "<li>" . $d->GetUrl() . "</li>";
					}
					echo "</ul>";
				}

				$status = GoogleSitemapGeneratorStatus::Load();
				echo '</div>';
				echo '<h3>MySQL Queries</h3>';
				if(defined('SAVEQUERIES') && SAVEQUERIES) {
					echo '<pre>';
					var_dump($GLOBALS['wpdb']->queries);
					echo '</pre>';

					$total = 0;
					foreach($GLOBALS['wpdb']->queries as $q) {
						$total+=$q[1];
					}
					echo '<h4>Total Query Time</h4>';
					echo '<pre>' . count($GLOBALS['wpdb']->queries) . ' queries in ' . round($total,2) . ' seconds.</pre>';
				} else {
					echo '<p>Please edit wp-db.inc.php in wp-includes and set SAVEQUERIES to true if you want to see the queries.</p>';
				}
				echo "<h3>Build Process Results</h3>";
				echo "<pre>";
				print_r($status);
				echo "</pre>";
				echo '<p>Done. <a href="' . wp_nonce_url($this->sg->GetBackLink() . "&sm_rebuild=true&sm_do_debug=true",'sitemap') . '">Rebuild</a> or <a href="' . $this->sg->GetBackLink() . '">Return</a></p>';
				echo '<p style="font-weight:bold; color:red; padding:5px; border:1px red solid; text-align:center;">DO NOT POST THIS INFORMATION ON PUBLIC PAGES LIKE SUPPORT FORUMS AS IT MAY CONTAIN PASSWORDS OR SECRET SERVER INFORMATION!</p>';
				echo '</div>';
				@error_reporting($oldErr);
				@ini_set("display_errors",$oldIni);
				return;
			} else {

				$redirURL = $this->sg->GetBackLink() . '&sm_fromrb=true';

				//Redirect so the sm_rebuild GET parameter no longer exists.
				@header("location: " . $redirURL);
				//If there was already any other output, the header redirect will fail
				echo '<script type="text/javascript">location.replace("' . $redirURL . '");</script>';
				echo '<noscript><a href="' . $redirURL . '">Click here to continue</a></noscript>';
				exit;
			}
		} else if (!empty($_POST['sm_update'])) { //Pressed Button: Update Config
			check_admin_referer('sitemap');

			if(isset($_POST['sm_b_style']) && $_POST['sm_b_style'] == $this->sg->getDefaultStyle()) {
				$_POST['sm_b_style_default'] = true;
				$_POST['sm_b_style'] = '';
			}

			foreach($this->sg->GetOptions() as $k=>$v) {

				//Skip some options if the user is not super admin...
				if(!is_super_admin() && in_array($k,array("sm_b_time","sm_b_memory","sm_b_style","sm_b_style_default"))) {
					continue;
				}

				//Check vor values and convert them into their types, based on the category they are in
				if(!isset($_POST[$k])) $_POST[$k]=""; // Empty string will get false on 2bool and 0 on 2float

				//Options of the category "Basic Settings" are boolean, except the filename and the autoprio provider
				if(substr($k,0,5)=="sm_b_") {
					if($k=="sm_b_prio_provider" || $k == "sm_b_style" || $k == "sm_b_memory" || $k == "sm_b_baseurl") {
						if($k=="sm_b_filename_manual" && strpos($_POST[$k],"\\")!==false){
							$_POST[$k]=stripslashes($_POST[$k]);
						} else if($k=="sm_b_baseurl") {
							$_POST[$k] = trim($_POST[$k]);
							if(!empty($_POST[$k])) $_POST[$k] = trailingslashit($_POST[$k]);
						}
						$this->sg->SetOption($k,(string) $_POST[$k]);
					} else if($k == "sm_b_time") {
						if($_POST[$k]=='') $_POST[$k] = -1;
						$this->sg->SetOption($k,intval($_POST[$k]));
					} else if($k== "sm_i_install_date") {
						if($this->sg->GetOption('i_install_date')<=0) $this->sg->SetOption($k,time());
					} else if($k=="sm_b_exclude") {
						$IDss = array();
						$IDs = explode(",",$_POST[$k]);
						for($x = 0; $x<count($IDs); $x++) {
							$ID = intval(trim($IDs[$x]));
							if($ID>0) $IDss[] = $ID;
						}
						$this->sg->SetOption($k,$IDss);
					} else if($k == "sm_b_exclude_cats") {
						$exCats = array();
						if(isset($_POST["post_category"])) {
							foreach((array) $_POST["post_category"] AS $vv) if(!empty($vv) && is_numeric($vv)) $exCats[] = intval($vv);
						}
						$this->sg->SetOption($k,$exCats);
					} else {
						$this->sg->SetOption($k,(bool) $_POST[$k]);

					}
				//Options of the category "Includes" are boolean
				} else if(substr($k,0,6)=="sm_in_") {
					if($k=='sm_in_tax') {

						$enabledTaxonomies = array();

						foreach(array_keys((array) $_POST[$k]) AS $taxName) {
							if(empty($taxName) || !taxonomy_exists($taxName)) continue;

							$enabledTaxonomies[] = $taxName;
						}

						$this->sg->SetOption($k,$enabledTaxonomies);

					} else if($k=='sm_in_customtypes') {

						$enabledPostTypes = array();

						foreach(array_keys((array) $_POST[$k]) AS $postTypeName) {
							if(empty($postTypeName) || !post_type_exists($postTypeName)) continue;

							$enabledPostTypes[] = $postTypeName;
						}

						$this->sg->SetOption($k, $enabledPostTypes);

					} else $this->sg->SetOption($k,(bool) $_POST[$k]);
				//Options of the category "Change frequencies" are string
				} else if(substr($k,0,6)=="sm_cf_") {
					$this->sg->SetOption($k,(string) $_POST[$k]);
				//Options of the category "Priorities" are float
				} else if(substr($k,0,6)=="sm_pr_") {
					$this->sg->SetOption($k,(float) $_POST[$k]);
				}
			}

			//Apply page changes from POST
			if(is_super_admin()) $this->sg->SetPages($this->HtmlApplyPages());

			if($this->sg->SaveOptions()) $message.=__('Configuration updated', 'sitemap') . "<br />";
			else $message.=__('Error while saving options', 'sitemap') . "<br />";

			if(is_super_admin()) {
				if($this->sg->SavePages()) $message.=__("Pages saved",'sitemap') . "<br />";
				else $message.=__('Error while saving pages', 'sitemap'). "<br />";
			}

		} else if(!empty($_POST["sm_reset_config"])) { //Pressed Button: Reset Config
			check_admin_referer('sitemap');
			$this->sg->InitOptions();
			$this->sg->SaveOptions();

			$message.=__('The default configuration was restored.','sitemap');
		} else if(!empty($_GET["sm_delete_old"])) { //Delete old sitemap files
			check_admin_referer('sitemap');

			//Check again, just for the case that something went wrong before
			if(!current_user_can("administrator")) {
				echo '<p>Please log in as admin</p>';
				return;
			}
			if(!$this->sg->DeleteOldFiles()) {
				$message = __("The old files could NOT be deleted. Please use an FTP program and delete them by yourself.","sitemap");
			} else {
				$message = __("The old files were successfully deleted.","sitemap");
			}
		} else if(!empty($_GET["sm_ping_all"])) {
			check_admin_referer('sitemap');

			//Check again, just for the case that something went wrong before
			if(!current_user_can("administrator")) {
				echo '<p>Please log in as admin</p>';
				return;
			}

			echo <<<HTML
<html>
	<head>
		<style type="text/css">
		html {
			background: #f1f1f1;
		}

		body {
			color: #444;
			font-family: "Open Sans", sans-serif;
			font-size: 13px;
			line-height: 1.4em;
			min-width: 600px;
		}

		h2 {
			font-size: 23px;
			font-weight: 400;
			padding: 9px 10px 4px 0;
			line-height: 29px;
		}
		</style>
	</head>
	<body>
HTML;
			echo "<h2>" . __('Notify Search Engines about all sitemaps','sitemap') ."</h2>";
			echo "<p>" . __('The plugin is notifying the selected search engines about your main sitemap and all sub-sitemaps. This might take a minute or two.','sitemaps') . "</p>";
			flush();
			$results = $this->sg->SendPingAll();

			echo "<ul>";

			foreach($results AS $result) {

				$sitemapUrl = $result["sitemap"];
				/** @var $status GoogleSitemapGeneratorStatus */
				$status = $result["status"];

				echo "<li><a href=\"" . esc_url($sitemapUrl) . "\">" . $sitemapUrl . "</a><ul>";
				$services = $status->GetUsedPingServices();
				foreach($services AS $serviceId) {
					echo "<li>";
					echo $status->GetServiceName($serviceId) . ": " . ($status->GetPingResult($serviceId)==true?"OK":"ERROR");
					echo "</li>";
				}
				echo "</ul></li>";
			}
			echo "</ul>";
			echo "<p>" . __('All done!','sitemap') . "</p>";
			echo <<<HTML

	</body>
HTML;
			exit;
		} else if(!empty($_GET["sm_ping_main"])) {

			check_admin_referer('sitemap');

			//Check again, just for the case that something went wrong before
			if(!current_user_can("administrator")) {
				echo '<p>Please log in as admin</p>';
				return;
			}

			$this->sg->SendPing();
			$message = __("Ping was executed, please see below for the result.","sitemap");
		}

		//Print out the message to the user, if any
		if($message!="") {
			?>
			<div class="updated"><p><strong><?php
			echo $message;
			?></strong></p></div><?php
		}


		if(!$snl) {

			if(isset($_GET['sm_hidedonate'])) {
				$this->sg->SetOption('i_hide_donated',true);
				$this->sg->SaveOptions();
			}
			if(isset($_GET['sm_donated'])) {
				$this->sg->SetOption('i_donated',true);
				$this->sg->SaveOptions();
			}
			if(isset($_GET['sm_hide_note'])) {
				$this->sg->SetOption('i_hide_note',true);
				$this->sg->SaveOptions();
			}
			if(isset($_GET['sm_hidedonors'])) {
				$this->sg->SetOption('i_hide_donors',true);
				$this->sg->SaveOptions();
			}
			if(isset($_GET['sm_hide_works'])) {
				$this->sg->SetOption('i_hide_works',true);
				$this->sg->SaveOptions();
			}
			if(isset($_GET['sm_disable_supportfeed'])) {
				$this->sg->SetOption('i_supportfeed',$_GET["sm_disable_supportfeed"]=="true"?false:true);
				$this->sg->SaveOptions();
			}


			if(isset($_GET['sm_donated']) || ($this->sg->GetOption('i_donated')===true && $this->sg->GetOption('i_hide_donated')!==true)) {
				?>
				<div class="updated">
					<strong><p><?php _e('Thank you very much for your donation. You help me to continue support and development of this plugin and other free software!','sitemap'); ?> <a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_hidedonate=true"; ?>"><small style="font-weight:normal;"><?php _e('Hide this notice', 'sitemap'); ?></small></a></p></strong>
				</div>
				<?php
			} else if($this->sg->GetOption('i_donated') !== true && $this->sg->GetOption('i_install_date')>0 && $this->sg->GetOption('i_hide_note')!==true && time() > ($this->sg->GetOption('i_install_date') + (60*60*24*30))) {
				?>
				<div class="updated">
					<strong><p><?php echo str_replace("%s",$this->sg->GetRedirectLink("sitemap-donate-note"),__('Thanks for using this plugin! You\'ve installed this plugin over a month ago. If it works and you are satisfied with the results, isn\'t it worth at least a few dollar? <a href="%s">Donations</a> help me to continue support and development of this <i>free</i> software! <a href="%s">Sure, no problem!</a>','sitemap')); ?> <a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_donated=true"; ?>" style="float:right; display:block; border:none; margin-left:10px;"><small style="font-weight:normal; "><?php _e('Sure, but I already did!', 'sitemap'); ?></small></a> <a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_hide_note=true"; ?>" style="float:right; display:block; border:none;"><small style="font-weight:normal; "><?php _e('No thanks, please don\'t bug me anymore!', 'sitemap'); ?></small></a></p></strong>
					<div style="clear:right;"></div>
				</div>
				<?php
			} else if($this->sg->GetOption('i_install_date')>0 && $this->sg->GetOption('i_hide_works')!==true && time() > ($this->sg->GetOption('i_install_date') + (60*60*24*15))) {
				?>
				<div class="updated">
					<strong><p><?php echo str_replace("%s",$this->sg->GetRedirectLink("sitemap-works-note"),__('Thanks for using this plugin! You\'ve installed this plugin some time ago. If it works and your are satisfied, why not <a href="%s">rate it</a> and <a href="%s">recommend it</a> to others? :-)','sitemap')); ?> <a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_hide_works=true"; ?>" style="float:right; display:block; border:none;"><small style="font-weight:normal; "><?php _e('Don\'t show this anymore', 'sitemap'); ?></small></a></p></strong>
					<div style="clear:right;"></div>
				</div>
				<?php
			}
		}

		?>

		<style type="text/css">

		li.sm_hint {
			color:green;
		}

		li.sm_optimize {
			color:orange;
		}

		li.sm_error {
			color:red;
		}

		input.sm_warning:hover {
			background: #ce0000;
			color: #fff;
		}

		a.sm_button {
			padding:4px;
			display:block;
			padding-left:25px;
			background-repeat:no-repeat;
			background-position:5px 50%;
			text-decoration:none;
			border:none;
		}

		a.sm_button:hover {
			border-bottom-width:1px;
		}

		a.sm_donatePayPal {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-paypal.gif);
		}

		a.sm_donateAmazon {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-amazon.gif);
		}

		a.sm_pluginHome {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-arne.gif);
		}

		a.sm_pluginHelp {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-help.png);
		}

		a.sm_pluginList {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-email.gif);
		}

		a.sm_pluginSupport {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-wordpress.gif);
		}

		a.sm_pluginBugs {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-trac.gif);
		}

		a.sm_resGoogle {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-google.gif);
		}

		a.sm_resYahoo {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-yahoo.gif);
		}

		a.sm_resBing {
			background-image:url(<?php echo $this->sg->GetPluginUrl(); ?>img/icon-bing.gif);
		}

		div.sm-update-nag p {
			margin:5px;
		}

		.sm-padded .inside {
			margin: 12px !important;
		}

		.sm-padded .inside ul {
			margin: 6px 0 12px 0;
		}

		.sm-padded .inside input {
			padding: 1px;
			margin: 0;
		}

		.hndle {
			cursor:auto!important;
			-webkit-user-select:auto!important;
			-moz-user-select:auto!important;
			-ms-user-select:auto!important;
			user-select:auto!important;
		}


		<?php if (version_compare($wp_version, "3.4", "<")): //Fix style for WP 3.4 (dirty way for now..) ?>

		.inner-sidebar #side-sortables, .columns-2 .inner-sidebar #side-sortables {
			min-height: 300px;
			width: 280px;
			padding: 0;
		}

		.has-right-sidebar .inner-sidebar {
			display: block;
		}

		.inner-sidebar {
			float: right;
			clear: right;
			display: none;
			width: 281px;
			position: relative;
		}

		.has-right-sidebar #post-body-content {
			margin-right: 300px;
		}

		#post-body-content {
			width: auto !important;
			float: none !important;
		}

		<?php endif; ?>


		</style>


		<div class="wrap" id="sm_div">
			<form method="post" action="<?php echo $this->sg->GetBackLink() ?>">
				<h2><?php _e('XML Sitemap Generator for WordPress', 'sitemap'); echo " " . $this->sg->GetVersion() ?> </h2>
				<?php

				if(get_option('blog_public')!=1) {
					?><div class="error"><p><?php echo str_replace("%s","options-reading.php#blog_public",__('Your blog is currently blocking search engines! Visit the <a href="%s">Reading Settings</a> to change this.','sitemap')); ?></p></div><?php
				}

				?>

					<?php if(!$snl): ?>
						<div id="poststuff" class="metabox-holder has-right-sidebar">
							<div class="inner-sidebar">
								<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
					<?php else: ?>
						<div id="poststuff" class="metabox-holder">
					<?php endif; ?>


					<?php if(!$snl): ?>
							<?php $this->HtmlPrintBoxHeader('sm_pnres',__('About this Plugin:','sitemap'),true); ?>
								<a class="sm_button sm_pluginHome"    href="<?php echo $this->sg->GetRedirectLink('sitemap-home'); ?>"><?php _e('Plugin Homepage','sitemap'); ?></a>
								<a class="sm_button sm_pluginHome"    href="<?php echo $this->sg->GetRedirectLink('sitemap-feedback'); ?>"><?php _e('Suggest a Feature','sitemap'); ?></a>
								<a class="sm_button sm_pluginHelp"    href="<?php echo $this->sg->GetRedirectLink('sitemap-help'); ?>"><?php _e('Help / FAQ','sitemap'); ?></a>
								<a class="sm_button sm_pluginList"    href="<?php echo $this->sg->GetRedirectLink('sitemap-list'); ?>"><?php _e('Notify List','sitemap'); ?></a>
								<a class="sm_button sm_pluginSupport" href="<?php echo $this->sg->GetRedirectLink('sitemap-support'); ?>"><?php _e('Support Forum','sitemap'); ?></a>
								<a class="sm_button sm_pluginBugs"    href="<?php echo $this->sg->GetRedirectLink('sitemap-bugs'); ?>"><?php _e('Report a Bug','sitemap'); ?></a>

								<a class="sm_button sm_donatePayPal"  href="<?php echo $this->sg->GetRedirectLink('sitemap-paypal'); ?>"><?php _e('Donate with PayPal','sitemap'); ?></a>
								<a class="sm_button sm_donateAmazon"  href="<?php echo $this->sg->GetRedirectLink('sitemap-amazon'); ?>"><?php _e('My Amazon Wish List','sitemap'); ?></a>
								<?php if(__('translator_name','sitemap')!='translator_name') {?><a class="sm_button sm_pluginSupport" href="<?php _e('translator_url','sitemap'); ?>"><?php _e('translator_name','sitemap'); ?></a><?php } ?>
							<?php $this->HtmlPrintBoxFooter(true); ?>

							<?php $this->HtmlPrintBoxHeader('sm_smres',__('Sitemap Resources:','sitemap'),true); ?>
								<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-gwt'); ?>"><?php _e('Webmaster Tools','sitemap'); ?></a>
								<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-gwb'); ?>"><?php _e('Webmaster Blog','sitemap'); ?></a>

								<a class="sm_button sm_resYahoo"     href="<?php echo $this->sg->GetRedirectLink('sitemap-ywb'); ?>"><?php _e('Search Blog','sitemap'); ?></a>

								<a class="sm_button sm_resBing"      href="<?php echo $this->sg->GetRedirectLink('sitemap-lwt'); ?>"><?php _e('Webmaster Tools','sitemap'); ?></a>
								<a class="sm_button sm_resBing"      href="<?php echo $this->sg->GetRedirectLink('sitemap-lswcb'); ?>"><?php _e('Webmaster Center Blog','sitemap'); ?></a>
								<br />
								<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-prot'); ?>"><?php _e('Sitemaps Protocol','sitemap'); ?></a>
								<a class="sm_button sm_resGoogle"    href="<?php echo $this->sg->GetRedirectLink('sitemap-ofaq'); ?>"><?php _e('Official Sitemaps FAQ','sitemap'); ?></a>
								<a class="sm_button sm_pluginHome"   href="<?php echo $this->sg->GetRedirectLink('sitemap-afaq'); ?>"><?php _e('My Sitemaps FAQ','sitemap'); ?></a>
							<?php $this->HtmlPrintBoxFooter(true); ?>

							<?php $this->HtmlPrintBoxHeader('dm_donations',__('Recent Donations:','sitemap'),true); ?>
								<?php if($this->sg->GetOption('i_hide_donors')!==true) { ?>
									<iframe border="0" frameborder="0" scrolling="no" allowtransparency="yes" style="width:100%; height:80px;" src="<?php echo $this->sg->GetRedirectLink('sitemap-donorlist'); ?>">
									<?php _e('List of the donors','sitemap'); ?>
									</iframe><br />
									<a href="<?php echo $this->sg->GetBackLink() . "&amp;sm_hidedonors=true"; ?>"><small><?php _e('Hide this list','sitemap'); ?></small></a><br /><br />
								<?php } ?>
								<a style="float:left; margin-right:5px; border:none;" href="javascript:document.getElementById('sm_donate_form').submit();"><img style="vertical-align:middle; border:none; margin-top:2px;" src="<?php echo $this->sg->GetPluginUrl(); ?>img/icon-donate.gif" border="0" alt="PayPal" title="Help me to continue support of this plugin :)" /></a>
								<span><small><?php _e('Thanks for your support!','sitemap'); ?></small></span>
								<div style="clear:left; height:1px;"></div>
							<?php $this->HtmlPrintBoxFooter(true); ?>


						</div>
					</div>
					<?php endif; ?>

					<div class="has-sidebar sm-padded" >

						<div id="post-body-content" class="<?php if(!$snl): ?>has-sidebar-content<?php endif; ?>">

							<div class="meta-box-sortabless">


					<!-- Rebuild Area -->
					<?php

						$status = GoogleSitemapGeneratorStatus::Load();
						$head = __('Search engines haven\'t been notified yet','sitemap');
						if($status != null && $status->GetStartTime() > 0) {
							$st=$status->GetStartTime() + (get_option( 'gmt_offset' ) * 3600);

							$head=str_replace("%date%",date_i18n(get_option('date_format'),$st) . " " . date_i18n(get_option('time_format'),$st),__('Result of the last ping, started on %date%.','sitemap'));
						}

						$this->HtmlPrintBoxHeader('sm_rebuild',$head); ?>


						<div style="border-left: 1px #DFDFDF solid; float:right; padding-left:15px; margin-left:10px; width:35%;">
							<strong><?php _e('Recent Support Topics / News','sitemap'); ?></strong>
							<?php
							if($this->sg->GetOption('i_supportfeed')) {

								echo "<small><a href=\"" . wp_nonce_url($this->sg->GetBackLink() . "&sm_disable_supportfeed=true") . "\">" . __('Disable','sitemap') . "</a></small>";

								$supportFeed = $this->sg->GetSupportFeed();

								if (!is_wp_error($supportFeed) && $supportFeed) {
									$supportItems = $supportFeed->get_items(0, $supportFeed->get_item_quantity(3));

									if(count($supportItems)>0) {
										echo "<ul>";
										foreach($supportItems AS $item) {
											$url = esc_url($item->get_permalink());
											$title = esc_html($item->get_title());
											echo "<li><a rel=\"external\" target=\"_blank\" href=\"{$url}\">{$title}</a></li>";

										}
										echo "</ul>";
									}
								} else {
									echo "<ul><li>" . __('No support topics available or an error occurred while fetching them.','sitemap') . "</li></ul>";
								}
							} else {
								echo "<ul><li>" . __('Support Topics have been disabled. Enable them to see useful information regarding this plugin. No Ads or Spam!','sitemap') . " " . "<a href=\"" . wp_nonce_url($this->sg->GetBackLink() . "&sm_disable_supportfeed=false") . "\">" . __('Enable','sitemap') . "</a>". "</li></ul>";
							}
							?>
						</div>


						<div style="min-height:150px;">
							<ul>
								<?php

								if($this->sg->OldFileExists()) {
									echo "<li class=\"sm_error\">" . str_replace("%s",wp_nonce_url($this->sg->GetBackLink() . "&sm_delete_old=true",'sitemap'),__('There is still a sitemap.xml or sitemap.xml.gz file in your blog directory. Please delete them as no static files are used anymore or <a href="%s">try to delete them automatically</a>.','sitemap')) . "</li>";
								}

								echo "<li>" . str_replace("%s",$this->sg->getXmlUrl(),__('The URL to your sitemap index file is: <a href="%s">%s</a>.','sitemap')) . "</li>";

								if($status == null) {
									echo "<li>" . __('Search engines haven\'t been notified yet. Write a post to let them know about your sitemap.','sitemap') . "</li>";
								}  else {

									$services = $status->GetUsedPingServices();

									foreach($services AS $service) {
										$name = $status->GetServiceName($service);

										if($status->GetPingResult($service)) {
											echo "<li>" . sprintf(__("%s was <b>successfully notified</b> about changes.",'sitemap'),$name). "</li>";
											$dur = $status->GetPingDuration($service);
											if($dur > 4) {
												echo "<li class=\sm_optimize\">" . str_replace(array("%time%","%name%"),array($dur,$name),__("It took %time% seconds to notify %name%, maybe you want to disable this feature to reduce the building time.",'sitemap')) . "</li>";
											}
										} else {
											echo "<li class=\"sm_error\">" . str_replace(array("%s","%name%"),array(wp_nonce_url($this->sg->GetBackLink() . "&sm_ping_service=" . $service . "&noheader=true",'sitemap'),$name),__('There was a problem while notifying %name%. <a href="%s" target="_blank">View result</a>','sitemap')) . "</li>";
										}
									}
								}

								?>
								<?php if($this->sg->GetOption('b_ping') || $this->sg->GetOption('b_pingmsn')): ?>
									<li>
										Notify Search Engines about <a href="<?php echo wp_nonce_url($this->sg->GetBackLink() . "&sm_ping_main=true",'sitemap'); ?>">your sitemap </a> or <a href="#" onclick="window.open('<?php echo wp_nonce_url($this->sg->GetBackLink() . "&sm_ping_all=true&noheader=true",'sitemap'); ?>','','width=650, height=500, resizable=yes'); return false;">your main sitemap and all sub-sitemaps</a> now.
									</li>
								<?php endif; ?>

								<?php if(is_super_admin()) echo "<li>" . str_replace("%d",wp_nonce_url($this->sg->GetBackLink() . "&sm_rebuild=true&sm_do_debug=true",'sitemap'),__('If you encounter any problems with your sitemap you can use the <a href="%d">debug function</a> to get more information.','sitemap')) . "</li>"; ?>
							</ul>
							<ul>
								<li>
									<?php echo sprintf(__('If you like the plugin, please <a target="_blank" href="%s">rate it 5 stars</a> or <a href="%s">donate</a> via PayPal! I\'m supporting this plugin since over 9 years! Thanks a lot! :)','sitemap'),$this->sg->GetRedirectLink('sitemap-works-note'),$this->sg->GetRedirectLink('sitemap-paypal')); ?>
								</li>

							</ul>
						</div>
					<?php $this->HtmlPrintBoxFooter(); ?>

					<?php if($this->sg->IsNginx() && $this->sg->IsUsingPermalinks()): ?>
						<?php $this->HtmlPrintBoxHeader('ngin_x',__('Webserver Configuration', 'sitemap')); ?>
						<?php _e('Since you are using Nginx as your web-server, please configure the following rewrite rules in case you get 404 Not Found errors for your sitemap:','sitemap'); ?>
						<p>
							<code style="display:block; overflow-x:auto; white-space: nowrap;">
								<?php
								$rules = GoogleSitemapGeneratorLoader::GetNginXRules();
								foreach($rules AS $rule) {
									echo $rule . "<br />";
								}
								?>
							</code>
						</p>
						<?php $this->HtmlPrintBoxFooter(); ?>
					<?php endif; ?>


					<!-- Basic Options -->
					<?php $this->HtmlPrintBoxHeader('sm_basic_options',__('Basic Options', 'sitemap')); ?>

					<b><?php _e('Update notification:','sitemap'); ?></b> <a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-ping'); ?>"><?php _e('Learn more','sitemap'); ?></a>
					<ul>
						<li>
							<input type="checkbox" id="sm_b_ping" name="sm_b_ping" <?php echo ($this->sg->GetOption("b_ping")==true?"checked=\"checked\"":"") ?> />
							<label for="sm_b_ping"><?php _e('Notify Google about updates of your Blog', 'sitemap') ?></label><br />
							<small><?php echo str_replace("%s",$this->sg->GetRedirectLink('sitemap-gwt'),__('No registration required, but you can join the <a href="%s">Google Webmaster Tools</a> to check crawling statistics.','sitemap')); ?></small>
						</li>
						<li>
							<input type="checkbox" id="sm_b_pingmsn" name="sm_b_pingmsn" <?php echo ($this->sg->GetOption("b_pingmsn")==true?"checked=\"checked\"":"") ?> />
							<label for="sm_b_pingmsn"><?php _e('Notify Bing (formerly MSN Live Search) about updates of your Blog', 'sitemap') ?></label><br />
							<small><?php echo str_replace("%s",$this->sg->GetRedirectLink('sitemap-lwt'),__('No registration required, but you can join the <a href="%s">Bing Webmaster Tools</a> to check crawling statistics.','sitemap')); ?></small>
						</li>
						<li>
							<label for="sm_b_robots">
							<input type="checkbox" id="sm_b_robots" name="sm_b_robots" <?php echo ($this->sg->GetOption("b_robots")==true?"checked=\"checked\"":"") ?> />
							<?php _e("Add sitemap URL to the virtual robots.txt file.",'sitemap'); ?>
							</label>

							<br />
							<small><?php _e('The virtual robots.txt generated by WordPress is used. A real robots.txt file must NOT exist in the blog directory!','sitemap'); ?></small>
						</li>
					</ul>
					<?php if(is_super_admin()): ?>
						<b><?php _e('Advanced options:','sitemap'); ?></b> <a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-adv'); ?>"><?php _e('Learn more','sitemap'); ?></a>
						<ul>
							<li>
								<label for="sm_b_memory"><?php _e('Try to increase the memory limit to:', 'sitemap') ?> <input type="text" name="sm_b_memory" id="sm_b_memory" style="width:40px;" value="<?php echo esc_attr($this->sg->GetOption("b_memory")); ?>" /></label> (<?php echo htmlspecialchars(__('e.g. "4M", "16M"', 'sitemap')); ?>)
							</li>
							<li>
								<label for="sm_b_time"><?php _e('Try to increase the execution time limit to:', 'sitemap') ?> <input type="text" name="sm_b_time" id="sm_b_time" style="width:40px;" value="<?php echo esc_attr(($this->sg->GetOption("b_time")===-1?'':$this->sg->GetOption("b_time"))); ?>" /></label> (<?php echo htmlspecialchars(__('in seconds, e.g. "60" or "0" for unlimited', 'sitemap')) ?>)
							</li>
							<li>
								<label for="sm_b_autozip">
									<input type="checkbox" id="sm_b_autozip" name="sm_b_autozip" <?php echo ($this->sg->GetOption("b_autozip")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Try to automatically compress the sitemap if the requesting client supports it.', 'sitemap') ?>
								</label><br />
								<small><?php _e('Disable this option if you get garbled content or encoding errors in your sitemap.','sitemap'); ?></small>
							</li>
							<li>
								<?php $useDefStyle = ($this->sg->GetDefaultStyle() && $this->sg->GetOption('b_style_default')===true); ?>
								<label for="sm_b_style"><?php _e('Include a XSLT stylesheet:', 'sitemap') ?> <input <?php echo ($useDefStyle?'disabled="disabled" ':'') ?> type="text" name="sm_b_style" id="sm_b_style"  value="<?php echo esc_attr($this->sg->GetOption("b_style")); ?>" /></label>
								(<?php _e('Full or relative URL to your .xsl file', 'sitemap') ?>) <?php if($this->sg->GetDefaultStyle()): ?><label for="sm_b_style_default"><input <?php echo ($useDefStyle?'checked="checked" ':'') ?> type="checkbox" id="sm_b_style_default" name="sm_b_style_default" onclick="document.getElementById('sm_b_style').disabled = this.checked;" /> <?php _e('Use default', 'sitemap') ?></label> <?php endif; ?>
							</li>
							<li>
								<label for="sm_b_baseurl"><?php _e('Override the base URL of the sitemap:', 'sitemap') ?> <input type="text" name="sm_b_baseurl" id="sm_b_baseurl"  value="<?php echo esc_attr($this->sg->GetOption("b_baseurl")); ?>" /></label><br />
								<small><?php _e('Use this if your blog is in a sub-directory, but you want the sitemap be located in the root. Requires .htaccess modification.','sitemap'); ?> <a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-adv-baseurl'); ?>"><?php _e('Learn more','sitemap'); ?></a></small>
							</li>
							<li>
								<label for="sm_b_html">
									<input type="checkbox" id="sm_b_html" name="sm_b_html" <?php if(!$this->sg->IsXslEnabled()) echo 'disabled="disabled"'; ?>  <?php echo ($this->sg->GetOption("b_html")==true && $this->sg->IsXslEnabled()?"checked=\"checked\"":"") ?> />
									<?php _e('Include sitemap in HTML format', 'sitemap') ?>  <?php if(!$this->sg->IsXslEnabled()) _e('(The required PHP XSL Module is not installed)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_b_stats">
									<input type="checkbox" id="sm_b_stats" name="sm_b_stats" <?php echo ($this->sg->GetOption("b_stats")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Allow anonymous statistics (no personal information)', 'sitemap') ?>
								</label> <label><a href="<?php echo $this->sg->GetRedirectLink('sitemap-help-options-adv-stats'); ?>"><?php _e('Learn more','sitemap'); ?></a></label>
							</li>
						</ul>
					<?php endif; ?>

					<?php $this->HtmlPrintBoxFooter(); ?>

					<?php if(is_super_admin()): ?>
						<?php $this->HtmlPrintBoxHeader('sm_pages',__('Additional pages', 'sitemap')); ?>

						<?php
							_e('Here you can specify files or URLs which should be included in the sitemap, but do not belong to your Blog/WordPress.<br />For example, if your domain is www.foo.com and your blog is located on www.foo.com/blog you might want to include your homepage at www.foo.com','sitemap');
							echo "<ul><li>";
							echo "<strong>" . __('Note','sitemap'). "</strong>: ";
							_e("If your blog is in a subdirectory and you want to add pages which are NOT in the blog directory or beneath, you MUST place your sitemap file in the root directory (Look at the &quot;Location of your sitemap file&quot; section on this page)!",'sitemap');
							echo "</li><li>";
							echo "<strong>" . __('URL to the page','sitemap'). "</strong>: ";
							_e("Enter the URL to the page. Examples: http://www.foo.com/index.html or www.foo.com/home ",'sitemap');
							echo "</li><li>";
							echo "<strong>" . __('Priority','sitemap') . "</strong>: ";
							_e("Choose the priority of the page relative to the other pages. For example, your homepage might have a higher priority than your imprint.",'sitemap');
							echo "</li><li>";
							echo "<strong>" . __('Last Changed','sitemap'). "</strong>: ";
							_e("Enter the date of the last change as YYYY-MM-DD (2005-12-31 for example) (optional).",'sitemap');

							echo "</li></ul>";
						?>
						<script type="text/javascript">
							//<![CDATA[
								<?php
								$freqVals = "'" . implode("','",array_keys($this->sg->GetFreqNames())). "'";
								$freqNames = "'" . implode("','",array_values($this->sg->GetFreqNames())). "'";
								?>

							var changeFreqVals = [<?php echo $freqVals; ?>];
							var changeFreqNames = [ <?php echo $freqNames; ?>];
							var priorities= [0<?php for($i=0.1; $i<1; $i+=0.1) { echo "," .  number_format($i,1,".",""); } ?>];

							var pages = [ <?php
								$pages = $this->sg->GetPages();
								$fd = false;
								foreach($pages AS $page) {
									if($page instanceof GoogleSitemapGeneratorPage) {
										if($fd) echo ",";
										else $fd = true;
										echo '{url:"' . esc_js($page->getUrl()) . '", priority:' . esc_js(number_format($page->getPriority(),1,".","")) . ', changeFreq:"' . esc_js($page->getChangeFreq()) . '", lastChanged:"' . esc_js(($page->getLastMod()>0?date("Y-m-d",$page->getLastMod()):"")) . '"}';
									}

								}
								?> ];
							//]]>
						</script>
						<script type="text/javascript" src="<?php echo $this->sg->GetPluginUrl(); ?>img/sitemap.js"></script>
						<table width="100%" cellpadding="3" cellspacing="3" id="sm_pageTable">
							<tr>
								<th scope="col"><?php _e('URL to the page','sitemap'); ?></th>
								<th scope="col"><?php _e('Priority','sitemap'); ?></th>
								<th scope="col"><?php _e('Change Frequency','sitemap'); ?></th>
								<th scope="col"><?php _e('Last Changed','sitemap'); ?></th>
								<th scope="col"><?php _e('#','sitemap'); ?></th>
							</tr>
							<?php
							if(count($pages)<=0) { ?>
								<tr>
									<td colspan="5" align="center"><?php _e('No pages defined.','sitemap') ?></td>
								</tr><?php
							}
							?>
						</table>
						<a href="javascript:void(0);" onclick="sm_addPage();"><?php _e("Add new page",'sitemap'); ?></a>
						<?php $this->HtmlPrintBoxFooter(); ?>
					<?php endif; ?>


					<!-- AutoPrio Options -->
					<?php $this->HtmlPrintBoxHeader('sm_postprio',__('Post Priority', 'sitemap')); ?>

						<p><?php _e('Please select how the priority of each post should be calculated:', 'sitemap') ?></p>
						<ul>
							<li><p><input type="radio" name="sm_b_prio_provider" id="sm_b_prio_provider__0" value="" <?php echo $this->HtmlGetChecked($this->sg->GetOption("b_prio_provider"),"") ?> /> <label for="sm_b_prio_provider__0"><?php _e('Do not use automatic priority calculation', 'sitemap') ?></label><br /><?php _e('All posts will have the same priority which is defined in &quot;Priorities&quot;', 'sitemap') ?></p></li>
							<?php
							$provs = $this->sg->GetPrioProviders();
							for($i=0; $i<count($provs); $i++) {
								echo "<li><p><input type=\"radio\" id=\"sm_b_prio_provider_$i\" name=\"sm_b_prio_provider\" value=\"" . $provs[$i] . "\" " .  $this->HtmlGetChecked($this->sg->GetOption("b_prio_provider"),$provs[$i]) . " /> <label for=\"sm_b_prio_provider_$i\">" . call_user_func(array($provs[$i], 'getName'))  . "</label><br />" .  call_user_func(array($provs[$i], 'getDescription')) . "</p></li>";
							}
							?>
						</ul>
					<?php $this->HtmlPrintBoxFooter(); ?>

					<!-- Includes -->
					<?php $this->HtmlPrintBoxHeader('sm_includes',__('Sitemap Content', 'sitemap')); ?>
						<b><?php _e('WordPress standard content', 'sitemap') ?>:</b>
						<ul>
							<li>
								<label for="sm_in_home">
									<input type="checkbox" id="sm_in_home" name="sm_in_home"  <?php echo ($this->sg->GetOption("in_home")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include homepage', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_posts">
									<input type="checkbox" id="sm_in_posts" name="sm_in_posts"  <?php echo ($this->sg->GetOption("in_posts")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include posts', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_pages">
									<input type="checkbox" id="sm_in_pages" name="sm_in_pages"  <?php echo ($this->sg->GetOption("in_pages")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include static pages', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_cats">
									<input type="checkbox" id="sm_in_cats" name="sm_in_cats"  <?php echo ($this->sg->GetOption("in_cats")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include categories', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_arch">
									<input type="checkbox" id="sm_in_arch" name="sm_in_arch"  <?php echo ($this->sg->GetOption("in_arch")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include archives', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_in_auth">
									<input type="checkbox" id="sm_in_auth" name="sm_in_auth"  <?php echo ($this->sg->GetOption("in_auth")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include author pages', 'sitemap') ?>
								</label>
							</li>
							<?php if($this->sg->IsTaxonomySupported()): ?>
							<li>
								<label for="sm_in_tags">
									<input type="checkbox" id="sm_in_tags" name="sm_in_tags"  <?php echo ($this->sg->GetOption("in_tags")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include tag pages', 'sitemap') ?>
								</label>
							</li>
							<?php endif; ?>
						</ul>

						<?php

						if($this->sg->IsTaxonomySupported()) {
							$taxonomies = $this->sg->GetCustomTaxonomies();

							$enabledTaxonomies = $this->sg->GetOption('in_tax');

							if(count($taxonomies)>0) {
								?><b><?php _e('Custom taxonomies', 'sitemap') ?>:</b><ul><?php


								foreach ($taxonomies as $taxName) {

									$taxonomy = get_taxonomy($taxName);
									$selected = in_array($taxonomy->name, $enabledTaxonomies);
									?>
									<li>
										<label for="sm_in_tax[<?php echo $taxonomy->name; ?>]">
											<input type="checkbox" id="sm_in_tax[<?php echo $taxonomy->name; ?>]" name="sm_in_tax[<?php echo $taxonomy->name; ?>]" <?php echo $selected?"checked=\"checked\"":""; ?> />
											<?php echo str_replace('%s',$taxonomy->label,__('Include taxonomy pages for %s', 'sitemap')); ?>
										</label>
									</li>
									<?php
								}

								?></ul><?php

							}
						}


						if($this->sg->IsCustomPostTypesSupported()) {
							$custom_post_types = $this->sg->GetCustomPostTypes();

							$enabledPostTypes = $this->sg->GetOption('in_customtypes');

							if(count($custom_post_types)>0) {
								?><b><?php _e('Custom post types', 'sitemap') ?>:</b><ul><?php

								foreach ($custom_post_types as $post_type) {
									$post_type_object = get_post_type_object($post_type);

									if (is_array($enabledPostTypes)) $selected = in_array($post_type_object->name, $enabledPostTypes);

									?>
									<li>
										<label for="sm_in_customtypes[<?php echo $post_type_object->name; ?>]">
											<input type="checkbox" id="sm_in_customtypes[<?php echo $post_type_object->name; ?>]" name="sm_in_customtypes[<?php echo $post_type_object->name; ?>]" <?php echo $selected?"checked=\"checked\"":""; ?> />
											<?php echo str_replace('%s',$post_type_object->label,__('Include custom post type %s', 'sitemap')); ?>
										</label>
									</li>
									<?php
								}

								?></ul><?php
							}
						}

						?>

						<b><?php _e('Further options', 'sitemap') ?>:</b>
						<ul>
							<li>
								<label for="sm_in_lastmod">
									<input type="checkbox" id="sm_in_lastmod" name="sm_in_lastmod"  <?php echo ($this->sg->GetOption("in_lastmod")==true?"checked=\"checked\"":"") ?> />
									<?php _e('Include the last modification time.', 'sitemap') ?>
								</label><br />
								<small><?php _e('This is highly recommended and helps the search engines to know when your content has changed. This option affects <i>all</i> sitemap entries.', 'sitemap') ?></small>
							</li>
						</ul>

					<?php $this->HtmlPrintBoxFooter(); ?>

					<!-- Excluded Items -->
					<?php $this->HtmlPrintBoxHeader('sm_excludes',__('Excluded items', 'sitemap')); ?>

						<b><?php _e('Excluded categories', 'sitemap') ?>:</b>

						<div style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; margin:5px 0px 5px 40px; overflow:auto; padding:0.5em 0.5em;">
							<ul>
								<?php wp_category_checklist(0,0,$this->sg->GetOption("b_exclude_cats"),false); ?>
							</ul>
						</div>

						<b><?php _e("Exclude posts","sitemap"); ?>:</b>
						<div style="margin:5px 0 13px 40px;">
							<label for="sm_b_exclude"><?php _e('Exclude the following posts or pages:', 'sitemap') ?> <small><?php _e('List of IDs, separated by comma', 'sitemap') ?></small><br />
							<input name="sm_b_exclude" id="sm_b_exclude" type="text" style="width:400px;" value="<?php echo esc_attr(implode(",",$this->sg->GetOption("b_exclude"))); ?>" /></label><br />
							<cite><?php _e("Note","sitemap") ?>: <?php _e("Child posts won't be excluded automatically!","sitemap"); ?></cite>
						</div>

					<?php $this->HtmlPrintBoxFooter(); ?>

					<!-- Change frequencies -->
					<?php $this->HtmlPrintBoxHeader('sm_change_frequencies',__('Change frequencies', 'sitemap')); ?>

						<p>
							<b><?php _e('Note', 'sitemap') ?>:</b>
							<?php _e('Please note that the value of this tag is considered a hint and not a command. Even though search engine crawlers consider this information when making decisions, they may crawl pages marked "hourly" less frequently than that, and they may crawl pages marked "yearly" more frequently than that. It is also likely that crawlers will periodically crawl pages marked "never" so that they can handle unexpected changes to those pages.', 'sitemap') ?>
						</p>
						<ul>
							<li>
								<label for="sm_cf_home">
									<select id="sm_cf_home" name="sm_cf_home"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_home")); ?></select>
									<?php _e('Homepage', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_posts">
									<select id="sm_cf_posts" name="sm_cf_posts"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_posts")); ?></select>
									<?php _e('Posts', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_pages">
									<select id="sm_cf_pages" name="sm_cf_pages"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_pages")); ?></select>
									<?php _e('Static pages', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_cats">
									<select id="sm_cf_cats" name="sm_cf_cats"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_cats")); ?></select>
									<?php _e('Categories', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_arch_curr">
									<select id="sm_cf_arch_curr" name="sm_cf_arch_curr"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_arch_curr")); ?></select>
									<?php _e('The current archive of this month (Should be the same like your homepage)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_cf_arch_old">
									<select id="sm_cf_arch_old" name="sm_cf_arch_old"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_arch_old")); ?></select>
									<?php _e('Older archives (Changes only if you edit an old post)', 'sitemap') ?>
								</label>
							</li>
							<?php if($this->sg->IsTaxonomySupported()): ?>
							<li>
								<label for="sm_cf_tags">
									<select id="sm_cf_tags" name="sm_cf_tags"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_tags")); ?></select>
									<?php _e('Tag pages', 'sitemap') ?>
								</label>
							</li>
							<?php endif; ?>
							<li>
								<label for="sm_cf_auth">
									<select id="sm_cf_auth" name="sm_cf_auth"><?php $this->HtmlGetFreqNames($this->sg->GetOption("cf_auth")); ?></select>
									<?php _e('Author pages', 'sitemap') ?>
								</label>
							</li>
						</ul>

					<?php $this->HtmlPrintBoxFooter(); ?>

					<!-- Priorities -->
					<?php $this->HtmlPrintBoxHeader('sm_priorities',__('Priorities', 'sitemap')); ?>
						<ul>
							<li>
								<label for="sm_pr_home">
									<select id="sm_pr_home" name="sm_pr_home"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_home")); ?></select>
									<?php _e('Homepage', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_posts">
									<select id="sm_pr_posts" name="sm_pr_posts"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_posts")); ?></select>
									<?php _e('Posts (If auto calculation is disabled)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_posts_min">
									<select id="sm_pr_posts_min" name="sm_pr_posts_min"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_posts_min")); ?></select>
									<?php _e('Minimum post priority (Even if auto calculation is enabled)', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_pages">
									<select id="sm_pr_pages" name="sm_pr_pages"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_pages")); ?></select>
									<?php _e('Static pages', 'sitemap'); ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_cats">
									<select id="sm_pr_cats" name="sm_pr_cats"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_cats")); ?></select>
									<?php _e('Categories', 'sitemap') ?>
								</label>
							</li>
							<li>
								<label for="sm_pr_arch">
									<select id="sm_pr_arch" name="sm_pr_arch"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_arch")); ?></select>
									<?php _e('Archives', 'sitemap') ?>
								</label>
							</li>
							<?php if($this->sg->IsTaxonomySupported()): ?>
							<li>
								<label for="sm_pr_tags">
									<select id="sm_pr_tags" name="sm_pr_tags"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_tags")); ?></select>
									<?php _e('Tag pages', 'sitemap') ?>
								</label>
							</li>
							<?php endif; ?>
							<li>
								<label for="sm_pr_auth">
									<select id="sm_pr_auth" name="sm_pr_auth"><?php $this->HtmlGetPriorityValues($this->sg->GetOption("pr_auth")); ?></select>
									<?php _e('Author pages', 'sitemap') ?>
								</label>
							</li>
						</ul>

					<?php $this->HtmlPrintBoxFooter(); ?>

					</div>
					<div>
						<p class="submit">
							<?php wp_nonce_field('sitemap') ?>
							<input type="submit" class="button-primary" name="sm_update" value="<?php _e('Update options', 'sitemap'); ?>" />
							<input type="submit" onclick='return confirm("Do you really want to reset your configuration?");' class="sm_warning" name="sm_reset_config" value="<?php _e('Reset options', 'sitemap'); ?>" />
						</p>
					</div>


				</div>
				</div>
				</div>
				<script type="text/javascript">if(typeof(sm_loadPages)=='function') addLoadEvent(sm_loadPages); </script>
			</form>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="sm_donate_form">
				<?php
					$lc = array(
						"en"=>array("cc"=>"USD","lc"=>"US"),
						"en-GB"=>array("cc"=>"GBP","lc"=>"GB"),
						"de"=>array("cc"=>"EUR","lc"=>"DE"),
					);
					$myLc = $lc["en"];
					$wpl = get_bloginfo('language');
					if(!empty($wpl)) {
						if(array_key_exists($wpl,$lc)) $myLc = $lc[$wpl];
						else {
							$wpl = substr($wpl,0,2);
							if(array_key_exists($wpl,$lc)) $myLc = $lc[$wpl];
						}
					}
				?>
				<input type="hidden" name="cmd" value="_donations" />
				<input type="hidden" name="business" value="<?php echo "donate" /* N O S P A M */ . "@" . "arnebra" . "chhold.de"; ?>" />
				<input type="hidden" name="item_name" value="Sitemap Generator for WordPress. Please tell me if if you don't want to be listed on the donator list." />
				<input type="hidden" name="no_shipping" value="1" />
				<input type="hidden" name="return" value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->sg->GetBackLink(); ?>&amp;sm_donated=true" />
				<input type="hidden" name="currency_code" value="<?php echo $myLc["cc"]; ?>" />
				<input type="hidden" name="bn" value="PP-BuyNowBF" />
				<input type="hidden" name="lc" value="<?php echo $myLc["lc"]; ?>" />
				<input type="hidden" name="rm" value="2" />
				<input type="hidden" name="on0" value="Your Website" />
				<input type="hidden" name="os0" value="<?php echo get_bloginfo("url"); ?>"/>
			</form>
		</div>
		<?php
	}
}

