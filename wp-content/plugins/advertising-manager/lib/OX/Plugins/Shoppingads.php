<?php
require_once(OX_LIB . '/Ad.php');	

class OX_Plugin_Shoppingads extends OX_Ad
{
	var $network_name = 'Shopping Ads';
	var $url = 'http://www.shoppingads.com';
	
	function OX_Plugin_Shoppingads($aAd = null)
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
			'account-id' => '',
			'adformat' => '250x250',
			'attitude' => 'cool',
			'campaign' => '',
			'color-bg' => 'FFFFFF',
			'color-border' => 'FFFFFF',
			'color-link' => '008000',
			'color-text' => '000000',
			'color-title' => '00A0E2',
			'height' => '250',
			'keywords' => '',
			'new-window' => 'no',
			'width' => '250',
		);
		return $properties + parent::get_network_property_defaults();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('728x90', '468x60', '234x60', '120x600', '160x600', '120x240', '336x280', '300x250', '250x250', '180x150', '125x125'));
	}
	
	function get_ad_colors()
	{
		return array('border', 'title', 'bg', 'text');
	}
	
	function import_detect_network($code)
	{
		return ( strpos($code,'shoppingads_ad_client')!==false );
	}
		
	function import_settings($code)
	{
		if (preg_match("/shoppingads_ad_client(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('account-id', $matches[4]);
			$code = str_replace("shoppingads_ad_client{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_client{$matches[1]}={$matches[2]}{$matches[3]}{{account-id}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_ad_campaign(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('campaign', $matches[4]);
			$code = str_replace("shoppingads_ad_campaign{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_campaign{$matches[1]}={$matches[2]}{$matches[3]}{{campaign}}{$matches[5]}", $code);
		}
		
		//Process dimensions and fake adformat (to auto-select from list when editing) (NO CUSTOM OPTIONS)
		$width = '';
		$height = '';
		if (preg_match("/shoppingads_ad_height(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$height = $matches[4];
			$code = str_replace("shoppingads_ad_height{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_height{$matches[1]}={$matches[2]}{$matches[3]}{{height}}{$matches[5]}", $code);
		}
		if (preg_match("/shoppingads_ad_width(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$width = $matches[4];
			$code = str_replace("shoppingads_ad_width{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_width{$matches[1]}={$matches[2]}{$matches[3]}{{width}}{$matches[5]}", $code);
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
		
		if (preg_match("/shoppingads_ad_kw(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('keywords', $matches[4]);
			$code = str_replace("shoppingads_ad_kw{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_ad_kw{$matches[1]}={$matches[2]}{$matches[3]}{{keywords}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_border(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('color-border', $matches[4]);
			$code = str_replace("shoppingads_color_border{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_border{$matches[1]}={$matches[2]}{$matches[3]}{{color-border}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_bg(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('color-bg', $matches[4]);
			$code = str_replace("shoppingads_color_bg{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_bg{$matches[1]}={$matches[2]}{$matches[3]}{{color-bg}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_heading(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('color-title', $matches[4]);
			$code = str_replace("shoppingads_color_heading{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_heading{$matches[1]}={$matches[2]}{$matches[3]}{{color-title}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_text(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('color-text', $matches[4]);
			$code = str_replace("shoppingads_color_text{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_text{$matches[1]}={$matches[2]}{$matches[3]}{{color-text}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_color_link(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('color-link', $matches[4]);
			$code = str_replace("shoppingads_color_link{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_color_link{$matches[1]}={$matches[2]}{$matches[3]}{{color-link}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_attitude(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('attitude', $matches[4]);
			$code = str_replace("shoppingads_attitude{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_attitude{$matches[1]}={$matches[2]}{$matches[3]}{{attitude}}{$matches[5]}", $code);
		}
		
		if (preg_match("/shoppingads_options(\s*)=(\s*)([\'\"]{1})(\w*)([\'\"]{1});/", $code, $matches) != 0) {
			$this->set_property('new-window', ($matches[4]=='n') ? 'yes' : 'no');
			$code = str_replace("shoppingads_options{$matches[1]}={$matches[2]}{$matches[3]}{$matches[4]}{$matches[5]}", "shoppingads_options{$matches[1]}={$matches[2]}{$matches[3]}{{new-window}}{$matches[5]}", $code);
		}
		
		parent::import_settings($code);
	}
}
/*
<script type="text/javascript"><!--' . "\n";
shoppingads_ad_client = 'myaccount';
shoppingads_ad_campaign = 'campaign';
shoppingads_ad_width = '468';
shoppingads_ad_height = '60';
shoppingads_ad_kw = 'keywords';
shoppingads_color_border = 'ccbbaa';
shoppingads_color_bg = 'aabbcc';
shoppingads_color_heading = '112233';
shoppingads_color_text = '226644';
shoppingads_color_link = '444466';
shoppingads_attitude = 'attitude';
shoppingads_options = "n";
--></script>
<script type="text/javascript" src="http://ads.shoppingads.com/pagead/show_sa_ads.js">
</script>
*/
?>
