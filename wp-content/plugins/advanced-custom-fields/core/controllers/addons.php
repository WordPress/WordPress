<?php 

/*
*  acf_addons
*
*  @description: controller for add-ons sub menu page
*  @since: 3.6
*  @created: 25/01/13
*/

class acf_addons
{
	
	var $action;
	
	
	/*
	*  __construct
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function __construct()
	{
		// actions
		add_action('admin_menu', array($this,'admin_menu'), 11, 0);
	}
	
	
	/*
	*  admin_menu
	*
	*  @description: 
	*  @created: 2/08/12
	*/
	
	function admin_menu()
	{
		// add page
		$page = add_submenu_page('edit.php?post_type=acf', __('Add-ons','acf'), __('Add-ons','acf'), 'manage_options', 'acf-addons', array($this,'html'));
		
		
		// actions
		add_action('load-' . $page, array($this,'load'));
		add_action('admin_print_scripts-' . $page, array($this, 'admin_print_scripts'));
		add_action('admin_print_styles-' . $page, array($this, 'admin_print_styles'));
		add_action('admin_head-' . $page, array($this,'admin_head'));
	}
	
	
	/*
	*  load
	*
	*  @description: 
	*  @since 3.5.2
	*  @created: 16/11/12
	*  @thanks: Kevin Biloski and Charlie Eriksen via Secunia SVCRP
	*/
	
	function load()
	{
		
	}
	
	
	/*
	*  admin_print_scripts
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function admin_print_scripts()
	{
		
	}
	
	
	/*
	*  admin_print_styles
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function admin_print_styles()
	{
		wp_enqueue_style(array(
			'wp-pointer',
			'acf-global',
			'acf',
		));
	}
	
	
	/*
	*  admin_head
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function admin_head()
	{
				
	}
	
	
	/*
	*  html
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function html()
	{
		// vars
		$dir = apply_filters('acf/get_info', 'dir');
		
		
		$premium = array();
		$premium[] = array(
			'title' => __("Repeater Field",'acf'),
			'description' => __("Create infinite rows of repeatable data with this versatile interface!",'acf'),
			'thumbnail' => $dir . 'images/add-ons/repeater-field-thumb.jpg',
			'active' => class_exists('acf_field_repeater'),
			'url' => 'http://www.advancedcustomfields.com/add-ons/repeater-field/'
		);
		$premium[] = array(
			'title' => __("Gallery Field",'acf'),
			'description' => __("Create image galleries in a simple and intuitive interface!",'acf'),
			'thumbnail' => $dir . 'images/add-ons/gallery-field-thumb.jpg',
			'active' => class_exists('acf_field_gallery'),
			'url' => 'http://www.advancedcustomfields.com/add-ons/gallery-field/'
		);
		$premium[] = array(
			'title' => __("Options Page",'acf'),
			'description' => __("Create global data to use throughout your website!",'acf'),
			'thumbnail' => $dir . 'images/add-ons/options-page-thumb.jpg',
			'active' => class_exists('acf_options_page_plugin'),
			'url' => 'http://www.advancedcustomfields.com/add-ons/options-page/'
		);
		$premium[] = array(
			'title' => __("Flexible Content Field",'acf'),
			'description' => __("Create unique designs with a flexible content layout manager!",'acf'),
			'thumbnail' => $dir . 'images/add-ons/flexible-content-field-thumb.jpg',
			'active' => class_exists('acf_field_flexible_content'),
			'url' => 'http://www.advancedcustomfields.com/add-ons/flexible-content-field/'
		);
		
		
		$free = array();
		$free[] = array(
			'title' => __("Gravity Forms Field",'acf'),
			'description' => __("Creates a select field populated with Gravity Forms!",'acf'),
			'thumbnail' => $dir . 'images/add-ons/gravity-forms-field-thumb.jpg',
			'active' => class_exists('gravity_forms_field'),
			'url' => 'https://github.com/stormuk/Gravity-Forms-ACF-Field/'
		);
		$free[] = array(
			'title' => __("Date & Time Picker",'acf'),
			'description' => __("jQuery date & time picker",'acf'),
			'thumbnail' => $dir . 'images/add-ons/date-time-field-thumb.jpg',
			'active' => class_exists('acf_field_date_time_picker'),
			'url' => 'http://wordpress.org/extend/plugins/acf-field-date-time-picker/'
		);
		$free[] = array(
			'title' => __("Location Field",'acf'),
			'description' => __("Find addresses and coordinates of a desired location",'acf'),
			'thumbnail' => $dir . 'images/add-ons/google-maps-field-thumb.jpg',
			'active' => class_exists('acf_field_location'),
			'url' => 'https://github.com/elliotcondon/acf-location-field/'
		);
		$free[] = array(
			'title' => __("Contact Form 7 Field",'acf'),
			'description' => __("Assign one or more contact form 7 forms to a post",'acf'),
			'thumbnail' => $dir . 'images/add-ons/cf7-field-thumb.jpg',
			'active' => class_exists('acf_field_cf7'),
			'url' => 'https://github.com/taylormsj/acf-cf7-field/'
		);
		
		?>
<div class="wrap" style="max-width:970px;">

	<div class="icon32" id="icon-acf"><br></div>
	<h2 style="margin: 4px 0 15px;"><?php _e("Advanced Custom Fields Add-Ons",'acf'); ?></h2>
	
	<div class="acf-alert">
	<p style=""><?php _e("The following Add-ons are available to increase the functionality of the Advanced Custom Fields plugin.",'acf'); ?><br />
	<?php _e("Each Add-on can be installed as a separate plugin (receives updates) or included in your theme (does not receive updates).",'acf'); ?></p>
	</div>
	<?php /*
	<div class="acf-alert">
		<p><strong><?php _e("Just updated to version 4?",'acf'); ?></strong> <?php _e("Activation codes have changed to plugins! Download your purchased add-ons",'acf'); ?> <a href="http://www.advancedcustomfields.com/add-ons-download/" target="_blank"><?php _e("here",'acf'); ?></a></p>
	</div>
	*/ ?>
	
	<div id="add-ons" class="clearfix">
		
		<div class="add-on-group clearfix">
		<?php foreach( $premium as $addon ): ?>
		<div class="add-on wp-box <?php if( $addon['active'] ): ?>add-on-active<?php endif; ?>">
			<a target="_blank" href="<?php echo $addon['url']; ?>">
				<img src="<?php echo $addon['thumbnail']; ?>" />
			</a>
			<div class="inner">
				<h3><a target="_blank" href="<?php echo $addon['url']; ?>"><?php echo $addon['title']; ?></a></h3>
				<p><?php echo $addon['description']; ?></p>
			</div>
			<div class="footer">
				<?php if( $addon['active'] ): ?>
					<a class="button button-disabled"><span class="acf-sprite-tick"></span><?php _e("Installed",'acf'); ?></a>
				<?php else: ?>
					<a target="_blank" href="<?php echo $addon['url']; ?>" class="button"><?php _e("Purchase & Install",'acf'); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
		</div>
		
		<div class="add-on-group clearfix">
		<?php foreach( $free as $addon ): ?>
		<div class="add-on wp-box <?php if( $addon['active'] ): ?>add-on-active<?php endif; ?>">
			<a target="_blank" href="<?php echo $addon['url']; ?>">
				<img src="<?php echo $addon['thumbnail']; ?>" />
			</a>
			<div class="inner">
				<h3><a target="_blank" href="<?php echo $addon['url']; ?>"><?php echo $addon['title']; ?></a></h3>
				<p><?php echo $addon['description']; ?></p>
			</div>
			<div class="footer">
				<?php if( $addon['active'] ): ?>
					<a class="button button-disabled"><span class="acf-sprite-tick"></span><?php _e("Installed",'acf'); ?></a>
				<?php else: ?>
					<a target="_blank" href="<?php echo $addon['url']; ?>" class="button"><?php _e("Download",'acf'); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>	
		</div>
		
				
	</div>
	
</div>
<script type="text/javascript">
(function($) {
	
	$(window).load(function(){
		
		$('#add-ons .add-on-group').each(function(){
		
			var $el = $(this),
				h = 0;
			
			
			$el.find('.add-on').each(function(){
				
				h = Math.max( $(this).height(), h );
				
			});
			
			$el.find('.add-on').height( h );
			
		});
		
	});
	
})(jQuery);	
</script>
		<?php
		
		return;
		
	}		
}

new acf_addons();

?>