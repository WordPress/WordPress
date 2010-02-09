<?php
/**
 * The custom background image script.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * The custom background image class.
 *
 * @since unknown
 * @package WordPress
 * @subpackage Administration
 */
class Custom_Background {

	/**
	 * Callback for administration header.
	 *
	 * @var callback
	 * @since unknown
	 * @access private
	 */
	var $admin_header_callback;

	/**
	 * Callback for header div.
	 *
	 * @var callback
	 * @since unknown
	 * @access private
	 */
	var $admin_image_div_callback;

	/**
	 * PHP4 Constructor - Register administration header callback.
	 *
	 * @since unknown
	 * @param callback $admin_header_callback
	 * @param callback $admin_image_div_callback Optional custom image div output callback.
	 * @return Custom_Background
	 */
	function Custom_Background($admin_header_callback = '', $admin_image_div_callback = '') {
		$this->admin_header_callback = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;
	}

	/**
	 * Setup the hooks for the Custom Background admin page.
	 *
	 * @since unknown
	 */
	function init() {
		$page = add_theme_page(__('Custom Background'), __('Custom Background'), 'switch_themes', 'custom-background', array(&$this, 'admin_page'));

		add_action("admin_head-$page", array(&$this, 'take_action'), 50);
		if ( $this->admin_header_callback )
			add_action("admin_head-$page", $this->admin_header_callback, 51);
	}

	/**
	 * Get the current step.
	 *
	 * @since unknown
	 *
	 * @return int Current step
	 */
	function step() {
		if ( ! isset( $_GET['step'] ) )
			return 1;

		$step = (int) $_GET['step'];
		if ( $step < 1 || 3 < $step )
			$step = 1;

		return $step;
	}

	/**
	 * Execute custom background modification.
	 *
	 * @since unknown
	 */
	function take_action() {
		if ( isset($_POST['reset-background']) ) {
			check_admin_referer('custom-background');
			remove_theme_mods();
		}
		if ( isset($_POST['repeat-background']) ) {
			check_admin_referer('custom-background');
			$repeat = $_POST['repeat-background'] ? true: false;
			set_theme_mod('background_repeat', $repeat);
		} elseif ( isset($_POST['save-background-options']) ) {
			set_theme_mod('background_repeat', false);
		}
		if ( isset($_POST['remove-background']) ) {
			check_admin_referer('custom-background');
			set_theme_mod('background_image', '');
		}
	}

	/**
	 * Display first step of custom background image page.
	 *
	 * @since unknown
	 */
	function step_1() {
		if ( isset($_GET['updated']) && $_GET['updated'] ) { ?>
<div id="message" class="updated">
<p><?php printf(__('Background updated. <a href="%s">Visit your site</a> to see how it looks.'), home_url()); ?></p>
</div>
		<?php } ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Custom Background'); ?></h2>
<?php if ( get_background_image() ) { ?>
<p><?php _e('This is your current background image.'); ?></p>
<?php
} else { ?>
<p><?php _e('There is currently no background image.'); ?></p> <?php
}

if ( $this->admin_image_div_callback ) {
  call_user_func($this->admin_image_div_callback);
} else {
?>
<div id="background-image">
<img src="<?php background_image(); ?>" />
</div>
<?php } ?>
</div>
<div class="wrap">
<h2><?php _e('Upload New Background Image'); ?></h2><p><?php _e('Here you can upload a new background image.'); ?></p>

<form enctype="multipart/form-data" id="uploadForm" method="POST" action="<?php echo esc_attr(add_query_arg('step', 2)) ?>" style="margin: auto; width: 50%;">
<label for="upload"><?php _e('Choose an image from your computer:'); ?></label><br /><input type="file" id="upload" name="import" />
<input type="hidden" name="action" value="save" />
<?php wp_nonce_field('custom-background') ?>
<p class="submit">
<input type="submit" value="<?php esc_attr_e('Upload'); ?>" />
</p>
</form>

</div>

<div class="wrap">

<h2><?php _e('Change Display Options') ?></h2>
<form method="post" action="<?php echo esc_attr(add_query_arg('step', 1)) ?>">
<label for="repeat-background">
<p><input name="repeat-background" type="checkbox" id="repeat-background" value="1" <?php checked(true, get_theme_mod('background_repeat')); ?> />
<?php _e('Tile the background.') ?></label></p>
<?php wp_nonce_field('custom-background'); ?>
<input type="submit" class="button" name="save-background-options" value="<?php esc_attr_e('Save Changes'); ?>" />
</form>
</div>

<?php if ( get_theme_mod('background_image') ) : ?>
<div class="wrap">
<h2><?php _e('Reset Background Image'); ?></h2>
<p><?php _e('This will restore the original background image. You will not be able to retrieve any customizations.') ?></p>
<form method="post" action="<?php echo esc_attr(add_query_arg('step', 1)) ?>">
<?php wp_nonce_field('custom-background'); ?>
<input type="submit" class="button" name="reset-background" value="<?php esc_attr_e('Restore Original Background'); ?>" />
</form>
</div>
<?php endif;

if ( get_background_image() ) :
?>
<div class="wrap">
<h2><?php _e('Remove Background Image'); ?></h2>
<p><?php _e('This will remove background image. You will not be able to retrieve any customizations.') ?></p>
<form method="post" action="<?php echo esc_attr(add_query_arg('step', 1)) ?>">
<?php wp_nonce_field('custom-background'); ?>
<input type="submit" class="button" name="remove-background" value="<?php esc_attr_e('Remove Background'); ?>" />
</form>
</div>
<?php endif;

	}

	/**
	 * Display second step of custom background image page.
	 *
	 * @since unknown
	 */
	function step_2() {
		check_admin_referer('custom-background');
		$overrides = array('test_form' => false);
		$file = wp_handle_upload($_FILES['import'], $overrides);

		if ( isset($file['error']) )
			die( $file['error'] );

		$url = $file['url'];
		$type = $file['type'];
		$file = $file['file'];
		$filename = basename($file);

		// Construct the object array
		$object = array(
		'post_title' => $filename,
		'post_content' => $url,
		'post_mime_type' => $type,
		'guid' => $url);

		// Save the data
		$id = wp_insert_attachment($object, $file);

		// Add the meta-data
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

		set_theme_mod('background_image', esc_url($url));
		do_action('wp_create_file_in_uploads', $file, $id); // For replication
		return $this->finished();
	}

	/**
	 * Display last step of custom header image page.
	 *
	 * @since unknown
	 */
	function finished() {
		$_GET['updated'] = 1;
	  $this->step_1();
	}

	/**
	 * Display the page based on the current step.
	 *
	 * @since unknown
	 */
	function admin_page() {
		$step = $this->step();
		if ( 1 == $step )
			$this->step_1();
		elseif ( 2 == $step )
			$this->step_2();
	}

}
?>
