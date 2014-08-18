<?php
require_once(OX_LIB . '/Ad.php');	

class OX_Plugin_Adify extends OX_Ad
{
	var $network_name = 'Adify';
	var $url = 'http://www.adify.com';
	
	function OX_Plugin_Adify($aAd = null)
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
			'adformat' => '250x250',
			'color-bg' 	=> 'FFFFFF',
			'color-border'=> 'FFFFFF',
			'color-text'	=> '000000',
			'color-title'	=> '0000FF',
			'height' => '250',
			'slot' => '',
			'width' => '250',
		);
		
		return $properties + parent::get_network_property_defaults();
	}
	
	function get_ad_formats()
	{
		return array('all' => array('custom','728x90', '468x60', '120x600', '160x600', '300x250', '160x160'));
	}
	
	function import_detect_network($code)
	{
		return (strpos($code,'sr_adspace_id') !== false);
	}
		
	function import_settings($code)
	{
		// Import parent settings first!
		parent::import_settings($code);
		
		// Slot ID
		if (preg_match('/sr_adspace_id( *)=( *)(\d*);/', $code, $matches) != 0) {
			$this->set_property('slot', $matches[3]);
			$code = str_replace("sr_adspace_id{$matches[1]}={$matches[2]}{$matches[3]}", "sr_adspace_id{$matches[1]}={$matches[2]}{{slot}}", $code);
			$code = str_replace("azId={$matches[3]}", "azId={{slot}}", $code);
			$code = str_replace("ID #{$matches[3]}", "ID #{{slot}}", $code);
		}
		
		// Width / Height
		$width = '';
		$height = '';
		if (preg_match('/sr_adspace_width( *)=( *)(\d*);/', $code, $matches) != 0) {
			$width = $matches[3]; 
			if ($width != '') {
				$this->set_property('width', $width);
			}
			$code = str_replace("sr_adspace_width{$matches[1]}={$matches[2]}{$width}", "sr_adspace_width{$matches[1]}={$matches[2]}{{width}}", $code);
		}
		if (preg_match('/sr_adspace_height( *)=( *)(\d*);/', $code, $matches) != 0) {
			$height = $matches[3];
			if ($height != '') {
				$this->set_property('height', $height);
			}
			$code = str_replace("sr_adspace_height{$matches[1]}={$matches[2]}{$height}", "sr_adspace_height{$matches[1]}={$matches[2]}{{height}}", $code);
		}
		if (($width != '') && ($height != '')) {
			$adformats = $this->get_ad_formats();
			$adformat = in_array("{$width}x{$height}", $adformats['all']) ? "{$width}x{$height}" : 'custom';
			$this->set_property('adformat', $adformat);
		}
	}
}
/*
<!-- Begin Adify tag for "Sidebar" Ad Space (160x160) ID #6471907 -->
<script type="text/javascript">
   sr_adspace_id = 6471907;
   sr_adspace_width = 160;
   sr_adspace_height = 160;
   sr_adspace_type = "graphic";
   sr_ad_new_window = true;
   
</script>
<script type="text/javascript" src="http://ad.afy11.net/srad.js?azId=6471907">
</script>
<!-- End Adify tag for "Sidebar" Ad Space (160x160) ID #6471907 -->
*/
?>
