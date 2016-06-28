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
	 * @var callable
	 * @since 3.0.0
	 */
	public $admin_header_callback;

	/**
	 * Callback for header div.
	 *
	 * @var callable
	 * @since 3.0.0
	 */
	public $admin_image_div_callback;

	/**
	 * Used to trigger a success message when settings updated and set to true.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var bool
	 */
	private $updated;

	/**
	 * Constructor - Register administration header callback.
	 *
	 * @since 3.0.0
	 * @param callable $admin_header_callback
	 * @param callable $admin_image_div_callback Optional custom image div output callback.
	 */
	public function __construct($admin_header_callback = '', $admin_image_div_callback = '') {
		$this->admin_header_callback = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;

		add_action( 'admin_menu', array( $this, 'init' ) );

		add_action( 'wp_ajax_custom-background-add', array( $this, 'ajax_background_add' ) );

		// Unused since 3.5.0.
		add_action( 'wp_ajax_set-background-image', array( $this, 'wp_set_background_image' ) );
	}

	/**
	 * Set up the hooks for the Custom Background admin page.
	 *
	 * @since 3.0.0
	 */
	public function init() {
		$page = add_theme_page( __( 'Background' ), __( 'Background' ), 'edit_theme_options', 'custom-background', array( $this, 'admin_page' ) );
		if ( ! $page ) {
			return;
		}

		add_action( "load-$page", array( $this, 'admin_load' ) );
		add_action( "load-$page", array( $this, 'take_action' ), 49 );
		add_action( "load-$page", array( $this, 'handle_upload' ), 49 );

		if ( $this->admin_header_callback ) {
			add_action( "admin_head-$page", $this->admin_header_callback, 51 );
		}
	}

	/**
	 * Set up the enqueue for the CSS & JavaScript files.
	 *
	 * @since 3.0.0
	 */
	public function admin_load() {
		get_current_screen()->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __('Overview'),
			'content' =>
				'<p>' . __( 'You can customize the look of your site without touching any of your theme&#8217;s code by using a custom background. Your background can be an image or a color.' ) . '</p>' .
				'<p>' . __( 'To use a background image, simply upload it or choose an image that has already been uploaded to your Media Library by clicking the &#8220;Choose Image&#8221; button. You can display a single instance of your image, or tile it to fill the screen. You can have your background fixed in place, so your site content moves on top of it, or you can have it scroll with your site.' ) . '</p>' .
				'<p>' . __( 'You can also choose a background color by clicking the Select Color button and either typing in a legitimate HTML hex value, e.g. &#8220;#ff0000&#8221; for red, or by choosing a color using the color picker.' ) . '</p>' .
				'<p>' . __( 'Don&#8217;t forget to click on the Save Changes button when you are finished.' ) . '</p>'
		) );

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://codex.wordpress.org/Appearance_Background_Screen" target="_blank">Documentation on Custom Background</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>'
		);

		wp_enqueue_media();
		wp_enqueue_script('custom-background');
		wp_enqueue_style('wp-color-picker');
	}

	/**
	 * Execute custom background modification.
	 *
	 * @since 3.0.0
	 */
	public function take_action() {
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
			wp_safe_redirect( $_POST['_wp_http_referer'] );
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
	public function admin_page() {
?>
<div class="wrap" id="custom-background">
<h1><?php _e( 'Custom Background' ); ?></h1>

<?php if ( current_user_can( 'customize' ) ) { ?>
<div class="notice notice-info hide-if-no-customize">
	<p>
		<?php
		printf(
			__( 'You can now manage and live-preview Custom Backgrounds in the <a href="%1$s">Customizer</a>.' ),
			admin_url( 'customize.php?autofocus[control]=background_image' )
		);
		?>
	</p>
</div>
<?php } ?>

<?php if ( ! empty( $this->updated ) ) { ?>
<div id="message" class="updated">
<p><?php printf( __( 'Background updated. <a href="%s">Visit your site</a> to see how it looks.' ), home_url( '/' ) ); ?></p>
</div>
<?php } ?>

<h3><?php _e( 'Background Image' ); ?></h3>

<table class="form-table">
<tbody>
<tr>
<th scope="row"><?php _e( 'Preview' ); ?></th>
<td>
	<?php
	if ( $this->admin_image_div_callback ) {
		call_user_func( $this->admin_image_div_callback );
	} else {
		$background_styles = '';
		if ( $bgcolor = get_background_color() )
			$background_styles .= 'background-color: #' . $bgcolor . ';';

		$background_image_thumb = get_background_image();
		if ( $background_image_thumb ) {
			$background_image_thumb = esc_url( set_url_scheme( get_theme_mod( 'background_image_thumb', str_replace( '%', '%%', $background_image_thumb ) ) ) );

			// Background-image URL must be single quote, see below.
			$background_styles .= ' background-image: url(\'' . $background_image_thumb . '\');'
				. ' background-repeat: ' . get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) ) . ';'
				. ' background-position: top ' . get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) );
		}
	?>
	<div id="custom-background-image" style="<?php echo $background_styles; ?>"><?php // must be double quote, see above ?>
		<?php if ( $background_image_thumb ) { ?>
		<img class="custom-background-image" src="<?php echo $background_image_thumb; ?>" style="visibility:hidden;" alt="" /><br />
		<img class="custom-background-image" src="<?php echo $background_image_thumb; ?>" style="visibility:hidden;" alt="" />
		<?php } ?>
	</div>
	<?php } ?>
</td>
</tr>

<?php if ( get_background_image() ) : ?>
<tr>
<th scope="row"><?php _e('Remove Image'); ?></th>
<td>
<form method="post">
<?php wp_nonce_field('custom-background-remove', '_wpnonce-custom-background-remove'); ?>
<?php submit_button( __( 'Remove Background Image' ), 'button', 'remove-background', false ); ?><br/>
<?php _e('This will remove the background image. You will not be able to restore any customizations.') ?>
</form>
</td>
</tr>
<?php endif; ?>

<?php $default_image = get_theme_support( 'custom-background', 'default-image' ); ?>
<?php if ( $default_image && get_background_image() != $default_image ) : ?>
<tr>
<th scope="row"><?php _e('Restore Original Image'); ?></th>
<td>
<form method="post">
<?php wp_nonce_field('custom-background-reset', '_wpnonce-custom-background-reset'); ?>
<?php submit_button( __( 'Restore Original Image' ), 'button', 'reset-background', false ); ?><br/>
<?php _e('This will restore the original background image. You will not be able to restore any customizations.') ?>
</form>
</td>
</tr>
<?php endif; ?>

<?php if ( current_user_can( 'upload_files' ) ): ?>
<tr>
<th scope="row"><?php _e('Select Image'); ?></th>
<td><form enctype="multipart/form-data" id="upload-form" class="wp-upload-form" method="post">
	<p>
		<label for="upload"><?php _e( 'Choose an image from your computer:' ); ?></label><br />
		<input type="file" id="upload" name="import" />
		<input type="hidden" name="action" value="save" />
		<?php wp_nonce_field( 'custom-background-upload', '_wpnonce-custom-background-upload' ); ?>
		<?php submit_button( __( 'Upload' ), 'button', 'submit', false ); ?>
	</p>
	<p>
		<label for="choose-from-library-link"><?php _e( 'Or choose an image from your media library:' ); ?></label><br />
		<button id="choose-from-library-link" class="button"
			data-choose="<?php esc_attr_e( 'Choose a Background Image' ); ?>"
			data-update="<?php esc_attr_e( 'Set as background' ); ?>"><?php _e( 'Choose Image' ); ?></button>
	</p>
	</form>
</td>
</tr>
<?php endif; ?>
</tbody>
</table>

<h3><?php _e('Display Options') ?></h3>
<form method="post">
<table class="form-table">
<tbody>
<?php if ( get_background_image() ) : ?>
<tr>
<th scope="row"><?php _e( 'Position' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Position' ); ?></span></legend>
<label>
<input name="background-position-x" type="radio" value="left"<?php checked( 'left', get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) ) ); ?> />
<?php _e('Left') ?>
</label>
<label>
<input name="background-position-x" type="radio" value="center"<?php checked( 'center', get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) ) ); ?> />
<?php _e('Center') ?>
</label>
<label>
<input name="background-position-x" type="radio" value="right"<?php checked( 'right', get_theme_mod( 'background_position_x', get_theme_support( 'custom-background', 'default-position-x' ) ) ); ?> />
<?php _e('Right') ?>
</label>
</fieldset></td>
</tr>

<tr>
<th scope="row"><?php _e( 'Repeat' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Repeat' ); ?></span></legend>
<label><input type="radio" name="background-repeat" value="no-repeat"<?php checked( 'no-repeat', get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) ) ); ?> /> <?php _e('No Repeat'); ?></label>
	<label><input type="radio" name="background-repeat" value="repeat"<?php checked( 'repeat', get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) ) ); ?> /> <?php _e('Tile'); ?></label>
	<label><input type="radio" name="background-repeat" value="repeat-x"<?php checked( 'repeat-x', get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) ) ); ?> /> <?php _e('Tile Horizontally'); ?></label>
	<label><input type="radio" name="background-repeat" value="repeat-y"<?php checked( 'repeat-y', get_theme_mod( 'background_repeat', get_theme_support( 'custom-background', 'default-repeat' ) ) ); ?> /> <?php _e('Tile Vertically'); ?></label>
</fieldset></td>
</tr>

<tr>
<th scope="row"><?php _ex( 'Attachment', 'Background Attachment' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Attachment' ); ?></span></legend>
<label>
<input name="background-attachment" type="radio" value="scroll" <?php checked( 'scroll', get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) ) ); ?> />
<?php _e( 'Scroll' ); ?>
</label>
<label>
<input name="background-attachment" type="radio" value="fixed" <?php checked( 'fixed', get_theme_mod( 'background_attachment', get_theme_support( 'custom-background', 'default-attachment' ) ) ); ?> />
<?php _e( 'Fixed' ); ?>
</label>
</fieldset></td>
</tr>
<?php endif; // get_background_image() ?>
<tr>
<th scope="row"><?php _e( 'Background Color' ); ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Background Color' ); ?></span></legend>
<?php
$default_color = '';
if ( current_theme_supports( 'custom-background', 'default-color' ) )
	$default_color = ' data-default-color="#' . esc_attr( get_theme_support( 'custom-background', 'default-color' ) ) . '"';
?>
<input type="text" name="background-color" id="background-color" value="#<?php echo esc_attr( get_background_color() ); ?>"<?php echo $default_color ?> />
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
	public function handle_upload() {
		if ( empty($_FILES) )
			return;

		check_admin_referer('custom-background-upload', '_wpnonce-custom-background-upload');
		$overrides = array('test_form' => false);

		$uploaded_file = $_FILES['import'];
		$wp_filetype = wp_check_filetype_and_ext( $uploaded_file['tmp_name'], $uploaded_file['name'] );
		if ( ! wp_match_mime_types( 'image', $wp_filetype['type'] ) )
			wp_die( __( 'The uploaded file is not a valid image. Please try again.' ) );

		$file = wp_handle_upload($uploaded_file, $overrides);

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

		set_theme_mod('background_image', esc_url_raw($url));

		$thumbnail = wp_get_attachment_image_src( $id, 'thumbnail' );
		set_theme_mod('background_image_thumb', esc_url_raw( $thumbnail[0] ) );

		/** This action is documented in wp-admin/custom-header.php */
		do_action( 'wp_create_file_in_uploads', $file, $id ); // For replication
		$this->updated = true;
	}

	/**
	 * AJAX handler for adding custom background context to an attachment.
	 *
	 * Triggered when the user adds a new background image from the
	 * Media Manager.
	 *
	 * @since 4.1.0
	 */
	public function ajax_background_add() {
		check_ajax_referer( 'background-add', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error();
		}

		$attachment_id = absint( $_POST['attachment_id'] );
		if ( $attachment_id < 1 ) {
			wp_send_json_error();
		}

		update_post_meta( $attachment_id, '_wp_attachment_is_custom_background', get_stylesheet() );

		wp_send_json_success();
	}

	/**
	 *
	 * @since 3.4.0
	 * @deprecated 3.5.0
	 *
	 * @param array $form_fields
	 * @return array $form_fields
	 */
	public function attachment_fields_to_edit( $form_fields ) {
		return $form_fields;
	}

	/**
	 *
	 * @since 3.4.0
	 * @deprecated 3.5.0
	 *
	 * @param array $tabs
	 * @return array $tabs
	 */
	public function filter_upload_tabs( $tabs ) {
		return $tabs;
	}

	/**
	 *
	 * @since 3.4.0
	 * @deprecated 3.5.0
	 */
	public function wp_set_background_image() {
		if ( ! current_user_can('edit_theme_options') || ! isset( $_POST['attachment_id'] ) ) exit;
		$attachment_id = absint($_POST['attachment_id']);
		/** This filter is documented in wp-admin/includes/media.php */
		$sizes = array_keys(apply_filters( 'image_size_names_choose', array('thumbnail' => __('Thumbnail'), 'medium' => __('Medium'), 'large' => __('Large'), 'full' => __('Full Size')) ));
		$size = 'thumbnail';
		if ( in_array( $_POST['size'], $sizes ) )
			$size = esc_attr( $_POST['size'] );

		update_post_meta( $attachment_id, '_wp_attachment_is_custom_background', get_option('stylesheet' ) );
		$url = wp_get_attachment_image_src( $attachment_id, $size );
		$thumbnail = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
		set_theme_mod( 'background_image', esc_url_raw( $url[0] ) );
		set_theme_mod( 'background_image_thumb', esc_url_raw( $thumbnail[0] ) );
		exit;
	}
}
