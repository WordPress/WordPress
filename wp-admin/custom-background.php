<?php
/**
 * The custom background script.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * The custom background class.
 *
 * @since 3.0.0
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
	 * @since 3.0.0
	 * @access private
	 */
	var $admin_image_div_callback;

	/**
	 * PHP4 Constructor - Register administration header callback.
	 *
	 * @since 3.0.0
	 * @param callback $admin_header_callback
	 * @param callback $admin_image_div_callback Optional custom image div output callback.
	 * @return Custom_Background
	 */
	function Custom_Background($admin_header_callback = '', $admin_image_div_callback = '') {
		$this->admin_header_callback = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;
	}

	/**
	 * Set up the hooks for the Custom Background admin page.
	 *
	 * @since 3.0.0
	 */
	function init() {
		if ( ! current_user_can('switch_themes') )
			return;

		$page = add_theme_page(__('Background'), __('Background'), 'switch_themes', 'custom-background', array(&$this, 'admin_page'));

		add_action("load-$page", array(&$this, 'admin_load'));
		add_action("load-$page", array(&$this, 'take_action'), 49);
		add_action("load-$page", array(&$this, 'handle_upload'), 49);

		if ( $this->admin_header_callback )
			add_action("admin_head-$page", $this->admin_header_callback, 51);
	}

	/**
	 * Set up the enqueue for the CSS & JavaScript files.
	 *
	 * @since 3.0.0
	 */
	function admin_load() {
		wp_enqueue_script('custom-background');
		wp_enqueue_style('farbtastic');
	}

	/**
	 * Execute custom background modification.
	 *
	 * @since 3.0.0
	 */
	function take_action() {

		if ( empty($_POST) )
			return;

		check_admin_referer('custom-background');

		// @TODO: No UI entry point for this:
		if ( isset($_POST['reset-background']) ) {
			remove_theme_mods();
			return;
		}
		if ( isset($_POST['remove-background']) ) {
			// @TODO: Uploaded files are not removed here.
			set_theme_mod('background_image', '');
		}

		if ( isset($_POST['background-repeat']) ) {
			if ( in_array($_POST['background-repeat'], array('repeat', 'no-repeat', 'repeat-x', 'repeat-y')) )
				$repeat = $_POST['background-repeat'];
			else
				$repeat = 'repeat';
			set_theme_mod('background_repeat', $repeat);
		}
		if ( isset($_POST['background-position']) ) {
			if ( in_array($_POST['background-position'], array('center', 'right', 'left')) )
				$position = $_POST['background-position'];
			else
				$position = 'left';
			set_theme_mod('background_position', $position);
		}
		if ( isset($_POST['background-attachment']) ) {
			if ( in_array($_POST['background-attachment'], array('fixed', 'scroll')) )
				$attachment = $_POST['background-attachment'];
			else
				$attachment = 'fixed';
			set_theme_mod('background_attachment', $attachment);
		}
		if ( isset($_POST['background-color']) ) {
			$color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['background-color']);
			if ( strlen($color) == 6 || strlen($color) == 3 )
				set_theme_mod('background_color', $color);
			else
				set_theme_mod('background_color', '');
		}

		$this->updated = true;
	}

	/**
	 * Display the custom background page.
	 *
	 * @since 3.0.0
	 */
	function admin_page() {
?>
<div class="wrap" id="custom-background">
<?php screen_icon(); ?>
<h2><?php _e('Custom Background'); ?></h2>
<?php if ( !empty($this->updated) ) { ?>
<div id="message" class="updated">
<p><?php printf(__('Background updated. <a href="%s">Visit your site</a> to see how it looks.'), home_url()); ?></p>
</div>
<?php } ?>
<p><?php _e('This is your current background.'); ?></p>
<?php
	if ( $this->admin_image_div_callback ) {
		call_user_func($this->admin_image_div_callback);
	} else {
?>

<style type="text/css"> 
#custom-background-image {
	background-color: #<?php echo get_background_color()?>;
	<?php if ( get_background_image() ) { ?>
	background: url(<?php echo get_theme_mod('background_image_thumb', ''); ?>);
	background-repeat: <?php echo get_theme_mod('background_repeat', 'no-repeat'); ?>;
	background-position: top <?php echo get_theme_mod('background_position', 'left'); ?>;
	background-attachment: <?php echo get_theme_mod('background_position', 'fixed'); ?>;
	<?php } ?>
}
</style> 
<div id="custom-background-image">
<?php if ( get_background_image() ) { ?>
<img class="custom-background-image" src="<?php echo get_theme_mod('background_image_thumb', ''); ?>" style="visibility:hidden;" /><br />
<img class="custom-background-image" src="<?php echo get_theme_mod('background_image_thumb', ''); ?>" style="visibility:hidden;" />
<?php } ?>
<br class="clear" />
</div>
<?php } ?>
<h3><?php _e('Change Display Options') ?></h3>
<form method="post" action="">
<table>
<thead>
<tr>
<th scope="col"><?php _e( 'Position' ); ?></th>
<th scope="col"><?php _e( 'Repeat' ); ?></th>
<th scope="col"><?php _e( 'Attachment' ); ?></th>
<th scope="col"><?php _e( 'Color' ); ?></th>
</tr>

<tbody>
<tr>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Position' ); ?></span></legend>
<label>
<input name="background-position" type="radio" value="left" <?php checked('left', get_theme_mod('background_position', 'left')); ?> />
<?php _e('Left') ?>
</label>
<label>
<input name="background-position" type="radio" value="center" <?php checked('center', get_theme_mod('background_position', 'left')); ?> />
<?php _e('Center') ?>
</label>
<label>
<input name="background-position" type="radio" value="right" <?php checked('right', get_theme_mod('background_position', 'left')); ?> />
<?php _e('Right') ?>
</label>
</fieldset></td>

<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Repeat' ); ?></span></legend>
<label>
<select name="background-repeat">
	<option value="no-repeat" <?php selected('no-repeat', get_theme_mod('background_repeat', 'repeat')); ?> ><?php _e('No repeat'); ?></option>
	<option value="repeat" <?php selected('repeat', get_theme_mod('background_repeat', 'repeat')); ?>><?php _e('Tile'); ?></option>
	<option value="repeat-x" <?php selected('repeat-x', get_theme_mod('background_repeat', 'repeat')); ?>><?php _e('Tile Horizontally'); ?></option>
	<option value="repeat-y" <?php selected('repeat-y', get_theme_mod('background_repeat', 'repeat')); ?>><?php _e('Tile Vertically'); ?></option>
</select>
</label>
</fieldset></td>

<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Attachment' ); ?></span></legend>
<label>
<input name="background-attachment" type="radio" value="scroll" <?php checked('scroll', get_theme_mod('background_attachment', 'fixed')); ?> />
<?php _e('Scroll') ?>
</label>
<label>
<input name="background-attachment" type="radio" value="fixed" <?php checked('fixed', get_theme_mod('background_attachment', 'fixed')); ?> />
<?php _e('Fixed') ?>
</label>
</fieldset></td>

<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Color' ); ?></span></legend>
<input type="text" name="background-color" id="background-color" value="#<?php echo esc_attr(get_background_color()) ?>" />
<input type="button" class="button" value="<?php esc_attr_e('Select a Color'); ?>" id="pickcolor" />

<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
</fieldset></td>
</tr>
</tbody>
</table>

<?php wp_nonce_field('custom-background'); ?>
<p class="submit"><input type="submit" class="button" name="save-background-options" value="<?php esc_attr_e('Save Changes'); ?>" /></p>
</form>

<h3><?php _e('Upload New Background Image'); ?></h3>
<form enctype="multipart/form-data" id="uploadForm" method="POST" action="">
<label for="upload"><?php _e('Choose an image from your computer:'); ?></label><br /><input type="file" id="upload" name="import" />
<input type="hidden" name="action" value="save" />
<?php wp_nonce_field('custom-background') ?>
<p class="submit">
<input type="submit" value="<?php esc_attr_e('Upload'); ?>" />
</p>
</form>

<?php if ( get_background_image() ) : ?>
<h3><?php _e('Remove Background Image'); ?></h3>
<p><?php _e('This will remove the background image. You will not be able to restore any customizations.') ?></p>
<form method="post" action="">
<?php wp_nonce_field('custom-background'); ?>
<input type="submit" class="button" name="remove-background" value="<?php esc_attr_e('Remove Background'); ?>" />
</form>
<?php endif; ?>

</div>
<?php
	}

	/**
	 * Handle a Image upload for the background image.
	 *
	 * @since 3.0.0
	 */
	function handle_upload() {

		if ( empty($_FILES) )
			return;

		check_admin_referer('custom-background');
		$overrides = array('test_form' => false);
		$file = wp_handle_upload($_FILES['import'], $overrides);

		if ( isset($file['error']) )
			wp_die( $file['error'] );

		$url = $file['url'];
		$type = $file['type'];
		$file = $file['file'];
		$filename = basename($file);

		// Construct the object array
		$object = array(
			'post_title' => $filename,
			'post_content' => $url,
			'post_mime_type' => $type,
			'guid' => $url
		);

		// Save the data
		$id = wp_insert_attachment($object, $file);

		// Add the meta-data
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

		set_theme_mod('background_image', esc_url($url));

		$thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' );
		set_theme_mod('background_image_thumb', esc_url( $thumbnail[0] ) );

		do_action('wp_create_file_in_uploads', $file, $id); // For replication
		$this->updated = true;
	}

}
?>
