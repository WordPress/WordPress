<?php 

/*
*  acf_field_groups
*
*  @description: 
*  @since: 3.6
*  @created: 25/01/13
*/

class acf_field_groups 
{

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
		add_action('admin_menu', array($this,'admin_menu'));
	}
	
	
	/*
	*  admin_menu
	*
	*  @description: 
	*  @created: 2/08/12
	*/
	
	function admin_menu()
	{
		
		// validate page
		if( ! $this->validate_page() )
		{
			return;
		}
		
		
		// actions
		add_action('admin_print_scripts', array($this,'admin_print_scripts'));
		add_action('admin_print_styles', array($this,'admin_print_styles'));
		add_action('admin_footer', array($this,'admin_footer'));
		
		
		// columns
		add_filter( 'manage_edit-acf_columns', array($this,'acf_edit_columns'), 10, 1 );
		add_action( 'manage_acf_posts_custom_column' , array($this,'acf_columns_display'), 10, 2 );
		
	}
	
	
	/*
	*  validate_page
	*
	*  @description: returns true | false. Used to stop a function from continuing
	*  @since 3.2.6
	*  @created: 23/06/12
	*/
	
	function validate_page()
	{
		// global
		global $pagenow;
		
		
		// vars
		$return = false;
		
		
		// validate page
		if( in_array( $pagenow, array('edit.php') ) )
		{
		
			// validate post type
			if( isset($_GET['post_type']) && $_GET['post_type'] == 'acf' )
			{
				$return = true;
			}
			
			
			if( isset($_GET['page']) )
			{
				$return = false;
			}
			
		}
		
		
		// return
		return $return;
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
		wp_enqueue_script(array(
			'jquery',
			'thickbox',
		));
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
			'thickbox',
			'acf-global',
			'acf',
		));
	}
	
	
	/*
	*  acf_edit_columns
	*
	*  @description: 
	*  @created: 2/08/12
	*/
	
	function acf_edit_columns( $columns )
	{
		$columns = array(
			'cb'	 	=> '<input type="checkbox" />',
			'title' 	=> __("Title"),
			'fields' 	=> __("Fields", 'acf')
		);
		
		return $columns;
	}
	
	
	/*
	*  acf_columns_display
	*
	*  @description: 
	*  @created: 2/08/12
	*/
	
	function acf_columns_display( $column, $post_id )
	{
		// vars
		switch ($column)
	    {
	        case "fields":
	            
	            // vars
				$count =0;
				$keys = get_post_custom_keys( $post_id );
				
				if($keys)
				{
					foreach($keys as $key)
					{
						if(strpos($key, 'field_') !== false)
						{
							$count++;
						}
					}
			 	}
			 	
			 	echo $count;

	            break;
	    }
	}
	
	
	/*
	*  admin_footer
	*
	*  @description: 
	*  @since 3.1.8
	*  @created: 23/06/12
	*/
	
	function admin_footer()
	{
		// vars
		$version = apply_filters('acf/get_info', 'version');
		$dir = apply_filters('acf/get_info', 'dir');
		$path = apply_filters('acf/get_info', 'path');
		$show_tab = isset($_GET['info']);
		$tab = isset($_GET['info']) ? $_GET['info'] : 'changelog';
		
		?>
<script type="text/html" id="tmpl-acf-col-right">
<div id="acf-col-right">

	<div class="wp-box">
		<div class="inner">
			<h2><?php _e("Advanced Custom Fields",'acf'); ?> <?php echo $version; ?></h2>

			<h3><?php _e("Changelog",'acf'); ?></h3>
			<p><?php _e("See what's new in",'acf'); ?> <a href="<?php echo admin_url('edit.php?post_type=acf&info=changelog'); ?>"><?php _e("version",'acf'); ?> <?php echo $version; ?></a>
			
			<h3><?php _e("Resources",'acf'); ?></h3>
			<ul>
				<li><a href="http://www.advancedcustomfields.com/resources/#getting-started" target="_blank"><?php _e("Getting Started",'acf'); ?></a></li>
				<li><a href="http://www.advancedcustomfields.com/resources/#field-types" target="_blank"><?php _e("Field Types",'acf'); ?></a></li>
				<li><a href="http://www.advancedcustomfields.com/resources/#functions" target="_blank"><?php _e("Functions",'acf'); ?></a></li>
				<li><a href="http://www.advancedcustomfields.com/resources/#actions" target="_blank"><?php _e("Actions",'acf'); ?></a></li>
				<li><a href="http://www.advancedcustomfields.com/resources/#filters" target="_blank"><?php _e("Filters",'acf'); ?></a></li>
				<li><a href="http://www.advancedcustomfields.com/resources/#how-to" target="_blank"><?php _e("'How to' guides",'acf'); ?></a></li>
				<li><a href="http://www.advancedcustomfields.com/resources/#tutorials" target="_blank"><?php _e("Tutorials",'acf'); ?></a></li>
			</ul>
		</div>
		<div class="footer footer-blue">
			<ul class="hl">
				<li><?php _e("Created by",'acf'); ?> Elliot Condon</li>
			</ul>
		</div>
	</div>
</div>
</script>
<script type="text/html" id="tmpl-acf-about">
<!-- acf-about -->
<div id="acf-about" class="acf-content">
	
	<!-- acf-content-title -->
	<div class="acf-content-title">
		<h1><?php _e("Welcome to Advanced Custom Fields",'acf'); ?> <?php echo $version; ?></h1>
		<h2><?php _e("Thank you for updating to the latest version!",'acf'); ?> <br />ACF <?php echo $version; ?> <?php _e("is more polished and enjoyable than ever before. We hope you like it.",'acf'); ?></h2>
	</div>
	<!-- / acf-content-title -->
	
	<!-- acf-content-body -->
	<div class="acf-content-body">
		<h2 class="nav-tab-wrapper">
			<a class="acf-tab-toggle nav-tab <?php if( $tab == 'whats-new' ){ echo 'nav-tab-active'; } ?>" href="<?php echo admin_url('edit.php?post_type=acf&info=whats-new'); ?>"><?php _e("What’s New",'acf'); ?></a>
			<a class="acf-tab-toggle nav-tab <?php if( $tab == 'changelog' ){ echo 'nav-tab-active'; } ?>" href="<?php echo admin_url('edit.php?post_type=acf&info=changelog'); ?>"><?php _e("Changelog",'acf'); ?></a>
			<?php if( $tab == 'download-add-ons' ): ?>
			<a class="acf-tab-toggle nav-tab nav-tab-active" href="<?php echo admin_url('edit.php?post_type=acf&info=download-add-ons'); ?>"><?php _e("Download Add-ons",'acf'); ?></a>
			<?php endif; ?>
		</h2>

<?php if( $tab == 'whats-new' ): 
		
		$activation_codes = array(
			'repeater' => get_option('acf_repeater_ac', ''),
			'gallery' => get_option('acf_gallery_ac', ''),
			'options_page' => get_option('acf_options_page_ac', ''),
			'flexible_content' => get_option('acf_flexible_content_ac', '')
		);
		
		$active = array(
			'repeater' => class_exists('acf_field_repeater'),
			'gallery' => class_exists('acf_field_gallery'),
			'options_page' => class_exists('acf_options_page_plugin'),
			'flexible_content' => class_exists('acf_field_flexible_content')
		);
		
		$update_required = false;
		$update_complete = true;
		
		foreach( $activation_codes as $k => $v )
		{
			if( $v )
			{
				$update_required = true;
				
				if( !$active[ $k ] )
				{
					$update_complete = false;
				}
			}
		}
		
		
		?>

		<table id="acf-add-ons-table" class="alignright">
			<tr>
				<td><img src="<?php echo $dir; ?>images/add-ons/repeater-field-thumb.jpg" /></td>
				<td><img src="<?php echo $dir; ?>images/add-ons/gallery-field-thumb.jpg" /></td>
			</tr>
			<tr>
				<td><img src="<?php echo $dir; ?>images/add-ons/options-page-thumb.jpg" /></td>
				<td><img src="<?php echo $dir; ?>images/add-ons/flexible-content-field-thumb.jpg" /></td>
			</tr>
		</table>
		
		<div style="margin-right: 300px;">
	
			<h3><?php _e("Add-ons",'acf'); ?></h3>
			
			<h4><?php _e("Activation codes have grown into plugins!",'acf'); ?></h4>
			<p><?php _e("Add-ons are now activated by downloading and installing individual plugins. Although these plugins will not be hosted on the wordpress.org repository, each Add-on will continue to receive updates in the usual way.",'acf'); ?></p>
			
			
			<?php if( $update_required ): ?>
				<?php if( $update_complete ): ?>
				<div class="acf-alert acf-alert-success">
					<p><?php _e("All previous Add-ons have been successfully installed",'acf'); ?></p>
				</div>
				<?php else: ?>
				<div class="acf-alert acf-alert-success">
					<p><?php _e("This website uses premium Add-ons which need to be downloaded",'acf'); ?> <a href="<?php echo admin_url('edit.php?post_type=acf&info=download-add-ons'); ?>" class="acf-button" style="display: inline-block;"><?php _e("Download your activated Add-ons",'acf'); ?></a></p>
				</div>
				<?php endif; ?>
			<?php else: ?>
			<div class="acf-alert acf-alert-success">
				<p><?php _e("This website does not use premium Add-ons and will not be affected by this change.",'acf'); ?></p>
			</div>
			<?php endif; ?>
			
		</div>
		
		<div class="clear"></div>
		
		<hr />
		
		<h3><?php _e("Easier Development",'acf'); ?></h3>
		
		<h4><?php _e("New Field Types",'acf'); ?></h4>
		<ul>
			<li><?php _e("Taxonomy Field",'acf'); ?></li>
			<li><?php _e("User Field",'acf'); ?></li>
			<li><?php _e("Email Field",'acf'); ?></li>
			<li><?php _e("Password Field",'acf'); ?></li>
		</ul>
		<h4><?php _e("Custom Field Types",'acf'); ?></h4>
		<p><?php _e("Creating your own field type has never been easier! Unfortunately, version 3 field types are not compatible with version 4.",'acf'); ?><br />
		<?php _e("Migrating your field types is easy, please",'acf'); ?> <a href="http://www.advancedcustomfields.com/docs/tutorials/creating-a-new-field-type/" target="_blank"><?php _e("follow this tutorial",'acf'); ?></a> <?php _e("to learn more.",'acf'); ?></p>
		
		<h4><?php _e("Actions &amp; Filters",'acf'); ?></h4>
		<p><?php _e("All actions & filters have received a major facelift to make customizing ACF even easier! Please",'acf'); ?> <a href="http://www.advancedcustomfields.com/resources/getting-started/migrating-from-v3-to-v4/" target="_blank"><?php _e("read this guide",'acf'); ?></a> <?php _e("to find the updated naming convention.",'acf'); ?></p>
		
		<h4><?php _e("Preview draft is now working!",'acf'); ?></h4>
		<p><?php _e("This bug has been squashed along with many other little critters!",'acf'); ?> <a class="acf-tab-toggle" href="<?php echo admin_url('edit.php?post_type=acf&info=changelog'); ?>" data-tab="2"><?php _e("See the full changelog",'acf'); ?></a></p>
		
		<hr />
		
		<h3><?php _e("Important",'acf'); ?></h3>
		
		<h4><?php _e("Database Changes",'acf'); ?></h4>
		<p><?php _e("Absolutely <strong>no</strong> changes have been made to the database between versions 3 and 4. This means you can roll back to version 3 without any issues.",'acf'); ?></p>
		
		<h4><?php _e("Potential Issues",'acf'); ?></h4>
		<p><?php _e("Do to the sizable changes surounding Add-ons, field types and action/filters, your website may not operate correctly. It is important that you read the full",'acf'); ?> <a href="http://www.advancedcustomfields.com/resources/getting-started/migrating-from-v3-to-v4/" target="_blank"><?php _e("Migrating from v3 to v4",'acf'); ?></a> <?php _e("guide to view the full list of changes.",'acf'); ?></p>
		
		<div class="acf-alert acf-alert-error">
			<p><strong><?php _e("Really Important!",'acf'); ?></strong> <?php _e("If you updated the ACF plugin without prior knowledge of such changes, please roll back to the latest",'acf'); ?> <a href="http://wordpress.org/extend/plugins/advanced-custom-fields/developers/"><?php _e("version 3",'acf'); ?></a> <?php _e("of this plugin.",'acf'); ?></p>
		</div>
		
		<hr />
		
		<h3><?php _e("Thank You",'acf'); ?></h3>
		<p><?php _e("A <strong>BIG</strong> thank you to everyone who has helped test the version 4 beta and for all the support I have received.",'acf'); ?></p>
		<p><?php _e("Without you all, this release would not have been possible!",'acf'); ?></p>

<?php elseif( $tab == 'changelog' ): ?>
		
		<h3><?php _e("Changelog for",'acf'); ?> <?php echo $version; ?></h3>
		<?php
		
		$items = file_get_contents( $path . 'readme.txt' );
		$items = explode('= ' . $version . ' =', $items);
		
		$items = end( $items );
		$items = current( explode("\n\n", $items) );
		$items = array_filter( array_map('trim', explode("*", $items)) );
		
		?>
		<ul class="acf-changelog">
		<?php foreach( $items as $item ): 
			
			$item = explode('http', $item);
				
		?>
			<li><?php echo $item[0]; ?><?php if( isset($item[1]) ): ?><a href="http<?php echo $item[1]; ?>" target="_blank"><?php _e("Learn more",'acf'); ?></a><?php endif; ?></li>
		<?php endforeach; ?>
		</ul>

<?php elseif( $tab == 'download-add-ons' ): ?>
		
		<h3><?php _e("Overview",'acf'); ?></h3>
		
		<p><?php _e("Previously, all Add-ons were unlocked via an activation code (purchased from the ACF Add-ons store). New to v4, all Add-ons act as separate plugins which need to be individually downloaded, installed and updated.",'acf'); ?></p>
		
		<p><?php _e("This page will assist you in downloading and installing each available Add-on.",'acf'); ?></p>
		
		<h3><?php _e("Available Add-ons",'acf'); ?></h3>
		
		<p><?php _e("The following Add-ons have been detected as activated on this website.",'acf'); ?></p>
		
		<?php 
		
		$ac_repeater = get_option('acf_repeater_ac', '');
		$ac_options_page = get_option('acf_options_page_ac', '');
		$ac_flexible_content = get_option('acf_flexible_content_ac', '');
		$ac_gallery = get_option('acf_gallery_ac', '');
		
		?>
		<table class="widefat" id="acf-download-add-ons-table">
			<thead>
			<tr>
				<th colspan="2"><?php _e("Name",'acf'); ?></th>
				<th><?php _e("Activation Code",'acf'); ?></th>
				<th><?php _e("Download",'acf'); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if( $ac_repeater ): ?>
			<tr>
				<td class="td-image"><img src="<?php echo $dir; ?>images/add-ons/repeater-field-thumb.jpg" style="width:50px" /></td>
				<th class="td-name"><?php _e("Repeater Field",'acf'); ?></th>
				<td class="td-code">XXXX-XXXX-XXXX-<?php echo substr($ac_repeater,-4); ?></td>
				<td class="td-download"><a class="button" href="http://download.advancedcustomfields.com/<?php echo $ac_repeater; ?>/trunk"><?php _e("Download",'acf'); ?></a></td>
			</tr>
			<?php endif; ?>
			<?php if( $ac_gallery ): ?>
			<tr>
				<td><img src="<?php echo $dir; ?>images/add-ons/gallery-field-thumb.jpg" /></td>
				<th><?php _e("Gallery Field",'acf'); ?></th>
				<td>XXXX-XXXX-XXXX-<?php echo substr($ac_gallery,-4); ?></td>
				<td><a class="button" href="http://download.advancedcustomfields.com/<?php echo $ac_gallery; ?>/trunk"><?php _e("Download",'acf'); ?></a></td>
			</tr>	
			<?php endif; ?>
			<?php if( $ac_options_page ): ?>
			<tr>
				<td><img src="<?php echo $dir; ?>images/add-ons/options-page-thumb.jpg" /></td>
				<th><?php _e("Options Page",'acf'); ?></th>
				<td>XXXX-XXXX-XXXX-<?php echo substr($ac_options_page,-4); ?></td>
				<td><a class="button" href="http://download.advancedcustomfields.com/<?php echo $ac_options_page; ?>/trunk"><?php _e("Download",'acf'); ?></a></td>
			</tr>
			<?php endif; ?>
			<?php if($ac_flexible_content): ?>
			<tr>
				<td><img src="<?php echo $dir; ?>images/add-ons/flexible-content-field-thumb.jpg" /></td>
				<th><?php _e("Flexible Content",'acf'); ?></th>
				<td>XXXX-XXXX-XXXX-<?php echo substr($ac_flexible_content,-4); ?></td>
				<td><a class="button" href="http://download.advancedcustomfields.com/<?php echo $ac_flexible_content; ?>/trunk"><?php _e("Download",'acf'); ?></a></td>
			</tr>
			<?php endif; ?>
			</tbody>
		</table>
		
		
		
		<h3><?php _e("Installation",'acf'); ?></h3>
		
		<p><?php _e("For each Add-on available, please perform the following:",'acf'); ?></p>
		<ol>
			<li><?php _e("Download the Add-on plugin (.zip file) to your desktop",'acf'); ?></li>
			<li><?php _e("Navigate to",'acf'); ?> <a target="_blank" href="<?php echo admin_url('plugin-install.php?tab=upload'); ?>"><?php _e("Plugins > Add New > Upload",'acf'); ?></a></li>
			<li><?php _e("Use the uploader to browse, select and install your Add-on (.zip file)",'acf'); ?></li>
			<li><?php _e("Once the plugin has been uploaded and installed, click the 'Activate Plugin' link",'acf'); ?></li>
			<li><?php _e("The Add-on is now installed and activated!",'acf'); ?></li>
		</ol>
		
		
<?php endif; ?>

		
	</div>
	<!-- / acf-content-body -->
	
	
	<!-- acf-content-footer -->
	<div class="acf-content-footer">
		<ul class="hl clearfix">
			<li><a class="acf-button acf-button-big" href="<?php echo admin_url('edit.php?post_type=acf'); ?>"><?php _e("Awesome. Let's get to work",'acf'); ?></a></li>
		</ul>
	</div>
	<!-- / acf-content-footer -->
	
	
	
</div>
<!-- / acf-about -->
</script>
<script type="text/javascript">
(function($){
	
	// wrap
	$('#wpbody .wrap').attr('id', 'acf-field_groups');
	$('#acf-field_groups').wrapInner('<div id="acf-col-left" />');
	$('#acf-field_groups').wrapInner('<div id="acf-cols" />');
	
	
	// add sidebar
	$('#acf-cols').prepend( $('#tmpl-acf-col-right').html() );
	
	
	// take out h2 + icon
	$('#acf-col-left > .icon32').insertBefore('#acf-cols');
	$('#acf-col-left > h2').insertBefore('#acf-cols');
	
	
	<?php if( $show_tab ): ?>
	// add about copy
	$('#wpbody-content').prepend( $('#tmpl-acf-about').html() );
	$('#acf-field_groups').hide();
	$('#screen-meta-links').hide();
	<?php endif; ?>
	
})(jQuery);
</script>
		<?php
	}
			
}

new acf_field_groups();

?>
