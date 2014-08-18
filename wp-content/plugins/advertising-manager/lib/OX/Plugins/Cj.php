<?php
require_once(OX_LIB . '/Ad.php');	

class OX_Plugin_Cj extends OX_Ad
{
	var $network_name = 'Commission Junction';
	var $url = 'http://www.cj.com';
	
	function OX_Plugin_Cj($aAd = null)
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
	
	function display($codeonly = false, $search = array(), $replace = array())
	{
		$xdomains = OX_Plugin_Cj::get_domains();
		$search[] = '{{xdomain}}';
		$replace[] = $xdomains[array_rand($xdomains)];
		
		return parent::display($codeonly, $search, $replace);
	}
	
	/**
	 * The domains that CJ randomly chooses to serve ads.  Add to this list as they become available.
	 */
	function get_domains()
	{
		return array(
			'www.commission-junction.com',
			'www.cj.com',
			'www.qksrv.net',
			'www.kqzyfj.com',
			'www.tkqlhce.com',
			'www.jdoqocy.com',
			'www.dpbolvw.net',
			'www.lduhtrp.net',
			'www.anrdoezrs.net',
		);
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '250x250',
			'alt-text' => '',
			'height' => '250',
			'new-window' => 'no',
			'slot' => '',
			'status' => '',
			'width' => '250',
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
		$match = false;
		$xdomains = OX_Plugin_Cj::get_domains();
		foreach ($xdomains as $d) {
			if (strpos($code, ('"http://' . $d)) !== false) {
				$match = true;
				break;
			}
		}
		
		return $match;
	}
		
	function import_settings($code)
	{
		if (preg_match('/http:\/\/([.\w]*)\/click-(\d*)-(\d*)/', $code, $matches) != 0) { 
			$this->set_property('account-id', $matches[2]);
			$this->set_property('slot', $matches[3]); 
			$code = str_replace("http://{$matches[1]}/click-{$matches[2]}-{$matches[3]}", "http://{{xdomain}}/click-{{account-id}}-{{slot}}", $code);
		}

		$a = $matches[2];
		$s = $matches[3];
		if (preg_match("/http:\/\/([.\w]*)\/image-{$a}-{$s}/", $code, $matches) != 0) { 
			$code = str_replace("http://{$matches[1]}/image-{$a}-{$s}", "http://{{xdomain}}/image-{{account-id}}-{{slot}}", $code);
		}
		
		if (preg_match("/onmouseover=\"window.status='([^']*)';return true;\"/", $code, $matches)) {
			$this->set_property('status', $matches[1]);
			$code = str_replace("onmouseover=\"window.status='{$matches[1]}';return true;\"", "onmouseover=\"window.status='{{status}}';return true;\"", $code);
		}

		if (preg_match("/ alt=\"([^\"]*)\"/", $code, $matches)) {
			$this->set_property('alt-text', $matches[1]);
			$code = str_replace(" alt=\"{$matches[1]}\"", " alt=\"{{alt-text}}\"", $code);
		}
		
		if ($v = strpos($code, " target=\"_blank\"")) {
			$this->set_property('new-window', 'yes');
			$code = str_replace(" target=\"_blank\"", "{{new-window}}", $code);
		}
		
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
		
		parent::import_settings($code);
	}
}
/*
JAVASCRIPT CODE
<script type="text/javascript" language="javascript" src="http://www.kqzyfj.com/placeholder-3707246?sid=test&target=_top&mouseover=Y"></script>

HTML CODE
<a href="http://www.tkqlhce.com/click-3430243-10379078?sid=test" target="_top" onmouseover="window.status='http://www.godaddy.com';return true;" onmouseout="window.status=' ';return true;">
<img src="http://www.tqlkg.com/image-3430243-10379078" width="468" height="60" alt="Go Daddy $7.49 .com domains 468x60" border="0"/></a>
*/
?>
