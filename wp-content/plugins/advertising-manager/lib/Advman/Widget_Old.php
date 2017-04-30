<?php
require_once (ADVMAN_LIB . '/Tools.php');

// This class is for widgets in WP 2.7 and before
class Advman_Widget
{
	function init()
	{
		global $wp_version;
		global $advman_engine;
		
		$ads = $advman_engine->getAds();
		
		if (!empty($ads)) {
			$widgets = array();
			foreach ($ads as $id => $ad) {
				if (!empty($ad->name)) {
					$i = substr(md5($ad->name), 0, 10);
					$widgets[$i] = $ad;
				}
			}
			
			foreach ($widgets as $id => $ad)
			{
				$n = __('Ad: ', 'advman') . $ad->name;
				$description = __('An ad from the Advertising Manager plugin');
				$args = array(
					'name' => $n,
					'description' => $description,
					//'width' => $ad->get('width', true),
					//'height' => $ad->get('height', true),
				);
				
				if (function_exists('wp_register_sidebar_widget')) {
					//$id, $name, $output_callback, $options = array()
					wp_register_sidebar_widget("advman-$id", $n, array('Advman_Widget','widget'), $args, $ad->name);
					wp_register_widget_control("advman-$id", $n, array('Advman_Widget','widget_control'), null, null, $ad->name); 
				} elseif (function_exists('register_sidebar_module') ) {
					register_sidebar_module($n, array('Advman_Widget', 'sbm_widget'), "advman-$id", $args );
					register_sidebar_module_control($n, array('Advman_Widget','widget_control'), "advman-$id");
				}
			}
		}
	}
	
	// This is the function that outputs advman widget.
	function widget($args,$n='')
	{
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args); //nb. $name comes out of this, hence the use of $n
		
		global $advman_engine;
		
		//If name not passed in (Sidebar Modules), extract from the widget-id (WordPress Widgets)
		if ($n=='') {
			$l = length(__('Ad: '));
			$n = substr($args['widget_name'],$l);   //Chop off beginning advman- bit
		}
		
		if ($n == 'default-ad') {
			$n = $advman_engine->getSetting('default-ad');
		}
		
		$ad = $advman_engine->selectAd($n);
		
		if (!empty($ad)) {
			$widgets = $advman_engine->getSetting('widgets');
			$id = substr(md5($ad->name), 0, 10);
			$suppress = !empty($widgets[$id]['suppress']);
			$ad_widget = '';
			$ad_widget .= $suppress ? '' : $before_widget;
			if(!empty($widgets[$id]['title'])) {
				$ad_widget .= $suppress ? '' : $before_title;
				$ad_widget .= $widgets[$id]['title'];
				$ad_widget .= $suppress ? '' : $after_title;
			}
			$ad_widget .= $ad->display(); //Output the selected ad
			$ad_widget .= $suppress ? '' : $after_widget;
			echo $ad_widget;
		}
	}

	function widget_control($args, $name)
	{
		global $advman_engine;
		
		$widgets = $advman_engine->getSetting('widgets');
		$id = substr(md5($name),0,10);
		
		// Save data if it is posted from the widget control
		if ( $_POST["advman-$id-submit"] ) {
			$title = strip_tags(stripslashes($_POST["advman-$id-title"]));
			$widgets[$id]['title'] = apply_filters('widget_title', $title);
			$suppress = !empty($_POST["advman-$id-suppress"]);
			$widgets[$id]['suppress'] = $suppress;
		}
		
		// Clean up any data from the widgets (e.g. if ads have been renamed)
		if (!empty($widgets)) {
			$ads = $advman_engine->getAds();
			foreach ($widgets as $i => $w) {
				$found = false;
				foreach ($ads as $ad) {
					$ai = substr(md5($ad->name), 0, 10);
					if ($ai == $i) {
						$found = true;
						break;
					}
				}
				if (!$found) {
					unset($widgets[$i]);
				}
			}
		}
		$advman_engine->setSetting('widgets', $widgets);
		
		// Display the widget options
		$title = isset($widgets[$id]['title']) ? $widgets[$id]['title'] : '';
		$checked = !empty($widgets[$id]['suppress']) ? 'checked="checked"' : '';
?>
<p><label for="advman-<?php echo $id; ?>-title"><?php _e('Title:'); ?> <input class="widefat" id="advman-<?php echo $id; ?>-title" name="advman-<?php echo $id; ?>-title" type="text" value="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>" /></label></p>
<p>
    <label for="advman-<?php echo $id; ?>-suppress"><input class="checkbox" type="checkbox" <?php echo $checked; ?> id="advman-<?php echo $id; ?>-suppress" name="advman-<?php echo $id; ?>-suppress" /> <?php _e('Hide widget formatting'); ?></label>
</p>
<input type="hidden" id="advman-<?php echo $id; ?>-submit" name="advman-<?php echo $id; ?>-submit" value="1" />

<?php
	}
	
	/**
	 * Sidebar module compatibility function
	 */
	function advman_sbm_widget($args)
	{
		global $k2sbm_current_module;
		advman_widget($args,$k2sbm_current_module->options['name']);
	}
  
}
?>