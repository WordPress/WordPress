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
	 * @since 3.0.0
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
	 * Holds the page menu hook.
	 *
	 * @var string
	 * @since 3.0.0
	 * @access private
	 */
	var $page = '';

	/**
	 * Constructor - Register administration header callback.
	 *
	 * @since 3.0.0
	 * @param callback $admin_header_callback
	 * @param callback $admin_image_div_callback Optional custom image div output callback.
	 * @return Custom_Background
	 */
	function __construct($admin_header_callback = '', $admin_image_div_callback = '') {
		$this->admin_header_callback = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;
	}

	/**
	 * Set up the hooks for the Custom Background admin page.
	 *
	 * @since 3.0.0
	 */
	function init() {
		if ( ! current_user_can('edit_theme_options') )
			return;

		$this->page = $page = add_theme_page(__('Background'), __('Background'), 'edit_theme_options', 'custom-background', array(&$this, 'admin_page'));

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
		add_contextual_help( $this->page, '<p>' . __( 'You can customize the look of your site without touching any of your theme&#8217;s code by using a custom background. Your background can be an image or a color.' ) . '</p>' .
		'<p>' . __( 'To use a background image, simply upload it, then choose your display options below. You can display a single instance of your image, or tile it to fill the screen. You can have your background fixed in place, so your site content moves on top of it, or you can have it scroll with your site.' ) . '</p>' .
		'<p>' . __( 'You can also choose a background color. If you know the hexadecimal code for the color you want, enter it in the Color field. If not, click on the Select a Color link, and a color picker will allow you to choose the exact shade you want.' ) . '</p>' .
		'<p>' . __( 'Don&#8217;t forget to click on the Save Changes button when you are finished.' ) . '</p>' .
		'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
		'<p>' . __( '<a href="http://codex.wordpress.org/Appearance_Background_Screen" target="_blank">Documentation on Custom Background</a>' ) . '</p>' .
		'<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>' );
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

		if ( isset($_POST['reset-background']) ) {
			check_admin_referer('custom-background-reset', '_wpnonce-custom-background-reset');
			remove_theme_mod('background_image');
			remove_theme_mod('background_image_thumb');
			$this->updated = true;
			return;
		}

		if ( isset($_POST['remove-background']) ) {
			// @TODO: Uploaded files are not removed here.
			check_admin_referer('custom-background-remove', '_wpnonce-custom-background-remove');
			set_theme_mod('background_image', '');
			set_theme_mod('background_image_thumb', '');
			$this->updated = true;
			return;
		}

		if ( isset($_POST['background-repeat']) ) {
			check_admin_referer('custom-background');
			if ( in_array($_POST['background-repeat'], array('repeat', 'no-repeat', 'repeat-x', 'repeat-y')) )
				$repeat = $_POST['background-repeat'];
			else
				$repeat = 'repeat';
			set_theme_mod('background_repeat', $repeat);
		}

		if ( isset($_POST['background-position-x']) ) {
			check_admin_referer('custom-background');
			if ( in_array($_POST['background-position-x'], array('center', 'right', 'left')) )
				$position = $_POST['background-position-x'];
			else
				$position = 'left';
			set_theme_mod('background_position_x', $position);
		}

		if ( isset($_POST['background-attachment']) ) {
			check_admin_referer('custom-background');
			if ( in_array($_POST['background-attachment'], array('fixed', 'scroll')) )
				$attachment = $_POST['background-attachment'];
			else
				$attachment = 'fixed';
			set_theme_mod('background_attachment', $attachment);
		}

		if ( isset($_POST['background-color']) ) {
			check_admin_referer('custom-background');
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
<p><?php printf( __( 'Background updated. <a href="%s">Visit your site</a> to see how it looks.' ), home_url( '/' ) ); ?></p>
</div>
<?php }

	if ( $this->admin_image_div_callback ) {
		call_user_func($this->admin_image_div_callback);
	} else {
?>
<h3><?php _e('Background Image'); ?></h3>
<table class="form-table">
<tbody>
<tr valign="top">
<th scope="row"><?php _e('Preview'); ?></th>
<td>
<?php
$background_styles = '';
if ( $bgcolor = get_background_color() )
	$background_styles .= 'background-color: #' . $bgcolor . ';';

if ( get_background_image() ) {
	// background-image URL must be single quote, see below
	$background_styles .= ' background-image: url(\'' . get_theme_mod('background_image_thumb', '') . '\');'
		. ' background-repeat: ' . get_theme_mod('background_repeat', 'repeat') . ';'
		. ' background-position: top ' . get_theme_mod('background_position_x', 'left');
}
?>
<div id="custom-background-image" style="<?php echo $background_styles; ?>"><?php // must be double quote, see above ?>
<?php if ( get_background_image() ) { ?>
<img class="custom-background-image" src="<?php echo get_theme_mod('background_image_thumb', ''); ?>" style="visibility:hidden;" alt="" /><br />
<img class="custom-background-image" src="<?php echo get_theme_mod('background_image_thumb', ''); ?>" style="visibility:hidden;" alt="" />
<?php } ?>
</div>
<?php } ?>
</td>
</tr>
<?php if ( get_background_image() ) : ?>
<tr valign="top">
<th scope="row"><?php _e('Remove Image'); ?></th>
<td>
<form method="post" action="">
<?php wp_nonce_field('custom-background-remove', '_wpnonce-custom-background-remove'); ?>
<?php submit_button( __( 'Remove Background Image' ), 'button', 'remove-background', false ); ?><br/>
<?php _e('This will remove the background image. You will not be able to restore any customizations.') ?>
</form>
</td>
</tr>
<?php endif; ?>

<?php if ( defined( 'BACKGROUND_IMAGE' ) ) : // Show only if a default background image exists ?>
<tr valign="top">
<th scope="row"><?php _e('Restore Original Image'); ?></th>
<td>
<form method="post" action="">
<?php wp_nonce_field('custom-background-reset', '_wpnonce-custom-background-reset'); ?>
<?php submit_button( __( 'Restore Original Image' ), 'button', 'reset-background', false ); ?><br/>
<?php _e('This will restore the original background image. You will not be able to restore any customizations.') ?>
</form>
</td>
</tr>

<?php endif; ?>
<tr valign="top">
<th scope="row"><?php _e('Upload Image'); ?></th>
<td><form enctype="multipart/form-data" id="upload-form" method="post" action="">
<label for="upload"><?php _e('Choose an image from your computer:'); ?></label><br /><input type="file" id="upload" name="import" />
<input type="hidden" name="action" value="save" />
<?php wp_nonce_field('custom-background-upload', '_wpnonce-custom-background-upload') ?>
<?php submit_button( __( 'Upload' ), 'button', 'submit', false ); ?>
</form>
</td>
</tr>
</tbody>
</table>

<h3><?php _e('Display Options') ?></h3>
<form method="post" action="">
<table class="form-table">
<tbody>
<?php if ( get_background_image() ) : ?>
<tr valign="top">
<th scope="row"><?php _e( 'Position' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Position' ); ?></span></legend>
<label>
<input name="background-position-x" type="radio" value="left"<?php checked('left', get_theme_mod('background_position_x', 'left')); ?> />
<?php _e('Left') ?>
</label>
<label>
<input name="background-position-x" type="radio" value="center"<?php checked('center', get_theme_mod('background_position_x', 'left')); ?> />
<?php _e('Center') ?>
</label>
<label>
<input name="background-position-x" type="radio" value="right"<?php checked('right', get_theme_mod('background_position_x', 'left')); ?> />
<?php _e('Right') ?>
</label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e( 'Repeat' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Repeat' ); ?></span></legend>
<label><input type="radio" name="background-repeat" value="no-repeat"<?php checked('no-repeat', get_theme_mod('background_repeat', 'repeat')); ?> /> <?php _e('No Repeat'); ?></label>
	<label><input type="radio" name="background-repeat" value="repeat"<?php checked('repeat', get_theme_mod('background_repeat', 'repeat')); ?> /> <?php _e('Tile'); ?></label>
	<label><input type="radio" name="background-repeat" value="repeat-x"<?php checked('repeat-x', get_theme_mod('background_repeat', 'repeat')); ?> /> <?php _e('Tile Horizontally'); ?></label>
	<label><input type="radio" name="background-repeat" value="repeat-y"<?php checked('repeat-y', get_theme_mod('background_repeat', 'repeat')); ?> /> <?php _e('Tile Vertically'); ?></label>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e( 'Attachment' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Attachment' ); ?></span></legend>
<label>
<input name="background-attachment" type="radio" value="scroll" <?php checked('scroll', get_theme_mod('background_attachment', 'scroll')); ?> />
<?php _e('Scroll') ?>
</label>
<label>
<input name="background-attachment" type="radio" value="fixed" <?php checked('fixed', get_theme_mod('background_attachment', 'scroll')); ?> />
<?php _e('Fixed') ?>
</label>
</fieldset></td>
</tr>
<?php endif; // get_background_image() ?>
<tr valign="top">
<th scope="row"><?php _e( 'Color' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Color' ); ?></span></legend>
<?php $show_clear = get_background_color() ? '' : ' style="display:none"'; ?>
<input type="text" name="background-color" id="background-color" value="#<?php echo esc_attr(get_background_color()) ?>" />
<a class="hide-if-no-js" href="#" id="pickcolor"><?php _e('Select a Color'); ?></a> <span <?php echo $show_clear; ?>class="hide-if-no-js" id="clearcolor"> (<a href="#"><?php _e( 'Clear' ); ?></a>)</span>
<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
</fieldset></td>
</tr>
</tbody>
</table>

<?php wp_nonce_field('custom-background'); ?>
<?php submit_button( null, 'primary', 'save-background-options' ); ?>
</form>

</div>
<?php
	}

	/**
	 * Handle an Image upload for the background image.
	 *
	 * @since 3.0.0
	 */
	function handle_upload() {

		if ( empty($_FILES) )
			return;

		check_admin_referer('custom-background-upload', '_wpnonce-custom-background-upload');
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
			'guid' => $url,
			'context' => 'custom-background'
		);

		// Save the data
		$id = wp_insert_attachment($object, $file);

		// Add the meta-data
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
		update_post_meta( $id, '_wp_attachment_is_custom_background', get_option('stylesheet' ) );

		set_theme_mod('background_image', esc_url($url));

		$thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' );
		set_theme_mod('background_image_thumb', esc_url( $thumbnail[0] ) );

		do_action('wp_create_file_in_uploads', $file, $id); // For replication
		$this->updated = true;
	}

}
?>
