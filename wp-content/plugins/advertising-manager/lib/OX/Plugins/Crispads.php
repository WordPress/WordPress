<?php
require_once(OX_LIB . '/Ad.php');	

class OX_Plugin_Crispads extends OX_Ad
{
	var $network_name = 'Crisp Ads';
	var $url = 'http://www.crispads.com';
	
	function OX_Plugin_Crispads($aAd = null)
	{
		$this->OX_Ad($aAd);
	}

	/**
	 * This function is called statically from the ad engine.  Use this function to put any hooks in the ad engine that you want to use.
	 */
	function register_plugin(&$engine)
	{
		$engine->addAction('ad_network', get_class($this));
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
			'identifier' => '',
			'slot' => '',
		);
		return $properties + parent::get_network_property_defaults();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('custom', '728x90', '468x60', '234x60', '150x50', '120x90', '120x60', '83x31', '120x600', '160x600', '240x400', '120x240', '336x280', '300x250', '250x250', '200x200', '180x150', '125x125'));
	}
	
	function get_ad_colors()
	{
		return array('border', 'title', 'bg', 'text');
	}
	
	function import_detect_network($code)
	{
		return (	preg_match('/http:\/\/www.crispads.com\/spinner\//', $code, $matches) !==0);
	}
		
	function import_settings($code)
	{
		if (preg_match("/zoneid=(\w*)/", $code, $matches) !=0) {
			$this->set_property('slot', $matches[1]);
			$code = str_replace("zoneid={$matches[1]}", "zoneid={{slot}}", $code);
		}
		if (preg_match("/n=(\w*)/", $code, $matches)!=0) {
			$this->set_property('identifier', $matches[1]);
			$code = str_replace("n={$matches[1]}", "n={{identifier}}", $code);
			$code = str_replace("id=\"{$matches[1]}\"", "id=\"{{identifier}}\"", $code);
			$code = str_replace("name=\"{$matches[1]}\"", "name=\"{{identifier}}\"", $code);
		}
		
		//Only available on IFRAME ads
		$width = '';
		$height = '';
		if (preg_match('/width="(\w*)"/', $code, $matches) != 0) {
			$width = $matches[1]; 
			$code = str_replace("width=\"{$width}\"", "width=\"{{width}}\"", $code);
		}
		if (preg_match('/height="(\w*)"/', $code, $matches) != 0) {
			$height = $matches[1];
			$code = str_replace("height=\"{$height}\"", "height=\"{{height}}\"", $code);
		}
		if ($width != '') {
			$this->set_property('width', $width);
		}
		if ($height != '') {
			$this->set_property('height', $height);
		}
		if (($width != '') && ($height != '')) {
			$this->set_property('adformat', $width . 'x' . $height); //Only set if both width and height present
		}
		
		$code = str_replace('INSERT_RANDOM_NUMBER_HERE', '{{random}}', $code);
		
		parent::import_settings($code);
	}
}
/*
// JAVASCRIPT
<script type="text/javascript"><!--//<![CDATA[
var m3_u = (location.protocol=='https:'?'https://www.crispads.com/spinner/www/delivery/ajs.php':'http://www.crispads.com/spinner/www/delivery/ajs.php');
var m3_r = Math.floor(Math.random()*99999999999);
if (!document.MAX_used) document.MAX_used = ',';
document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
document.write ("?zoneid=123");
document.write ('&amp;cb=' + m3_r);
if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
document.write ("&amp;loc=" + escape(window.location));
if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
if (document.context) document.write ("&context=" + escape(document.context));
if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
document.write ("\'><\/scr"+"ipt>");
//]]>--></script><noscript><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=a234567&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=123&amp;n=a234567" border="0" alt="" /></a></noscript>

//IFRAME
<iframe id="a234567" name="a234567" src="http://www.crispads.com/spinner/www/delivery/afr.php?n=a234567&amp;zoneid=123" framespacing="0" frameborder="no" scrolling="no" width="468" height="60"><a href="http://www.crispads.com/spinner/www/delivery/ck.php?n=a234567&amp;cb=INSERT_RANDOM_NUMBER_HERE" target="_blank"><img src="http://www.crispads.com/spinner/www/delivery/avw.php?zoneid=123&amp;n=a234567" border="0" alt="" /></a></iframe>
<script type="text/javascript" src="http://www.crispads.com/spinner/www/delivery/ag.php"></script>
*/
?>
