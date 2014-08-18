<?php
require_once(OX_LIB . '/Ad.php');	

class OX_Plugin_Adpinion extends OX_Ad
{
	var $network_name = 'Adpinion';
	var $url = 'http://www.adpinion.com';
	
	function OX_Plugin_Adpinion($aAd = null)
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
		if($this->get('width', true) > $this->get('height', true)) {
			$xwidth=18;
			$xheight=17;
		} else {
			$xwidth=0;
			$xheight=35;
		}
	
		$search[] = '{{xwidth}}';
		$search[] = '{{xheight}}';
		$replace[] = $this->get('width', true) + $xwidth;
		$replace[] = $this->get('height', true) + $xheight;
		
		return parent::display($codeonly, $search, $replace);
	}
	
	function get_network_property_defaults()
	{
		$properties = array(
			'account-id' => '',
			'adformat' => '728x90',
			'height'=> '90',
			'width' => '728',
		);
		
		return $properties + parent::get_network_property_defaults();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('728x90', '468x60', '120x600', '160x600', '300x250'));
	}
	
	function import_detect_network($code)
	{
		return ( preg_match('/src="http:\/\/www.adpinion.com\/app\//', $code, $matches) !==0);
	}
		
	function import_settings($code)
	{
		$width = '';
		$height = '';
		if (preg_match("/website=(\w*)/", $code, $matches) != 0) {
			$this->set_property('account-id', $matches[1]);
			$code = str_replace("website={$matches[1]}", "website={{account-id}}'", $code);
		}
		if (preg_match("/width=(\w*)/", $code, $matches) != 0) {
			$width = $matches[1];
			$code = str_replace("width={$matches[1]}", "width={{width}}'", $code);
		}
		if (preg_match("/height=(\w*)/", $code, $matches) != 0) {
			$height = $matches[1];
			$code = str_replace("height={$matches[1]}", "height={{height}}'", $code);
		}
		if (preg_match("/style=\"width:(\d*)px;height:(\d*)px/", $code, $matches) != 0) {
			$code = str_replace("style=\"width:{$matches[1]}px;height:{$matches[2]}px", "style=\"width:{{xwidth}}px;height:{{xheight}}px", $code);
		}
		
		if ($width != '') {
			$this->set_property('width', $width);
		}
		if ($height != '') {
			$this->set_property('height', $height);
		}
		if (($width != '') && ($height != '')) {
			$this->set_property('adformat', $width . 'x' . $height);
		}
		
		parent::import_settings($code);
	}
}
/*
<iframe src="http://www.adpinion.com/app/adpinion_frame?website=133599&amp;width=468&amp;height=60" id="adframe" style="width:486px;height:60px;" scrolling="no" frameborder="0">
*/
?>
