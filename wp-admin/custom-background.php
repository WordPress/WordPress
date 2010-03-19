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
	 * Set up the hooks for the Custom Background admin page.
	 *
	 * @since unknown
	 */
	function init() {
		if ( ! current_user_can('switch_themes') )
			return;

		$page = add_theme_page(__('Background'), __('Background'), 'switch_themes', 'custom-background', array(&$this, 'admin_page'));

		add_action("admin_print_scripts-$page", array(&$this, 'js_includes'));
		add_action("admin_print_styles-$page", array(&$this, 'css_includes'));
		add_action("admin_head-$page", array(&$this, 'js'), 50);
		add_action("admin_head-$page", array(&$this, 'take_action'), 49);
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
	 * Set up the enqueue for the JavaScript files.
	 *
	 * @since unknown
	 */
	function js_includes() {
		$step = $this->step();

		if ( 1 == $step )
			wp_enqueue_script('farbtastic');
	}

	/**
	 * Set up the enqueue for the CSS files
	 *
	 * @since unknown
	 */
	function css_includes() {
		$step = $this->step();

		if ( 1 == $step )
			wp_enqueue_style('farbtastic');
	}

	/**
	 * Execute custom background modification.
	 *
	 * @since unknown
	 */
	function take_action() {
		if ( ! current_user_can('switch_themes') )
			return;

		if ( empty($_POST) )
			return;

		check_admin_referer('custom-background');

		if ( isset($_POST['reset-background']) )
			remove_theme_mods();
		if ( isset($_POST['background-repeat']) ) {
			if ( in_array($_POST['background-repeat'], array('repeat', 'no-repeat')) )
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
		if ( isset($_POST['remove-background']) )
			set_theme_mod('background_image', '');
		if ( isset( $_POST['background-color'] ) ) {
			$color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['background-color']);
			if ( strlen($color) == 6 || strlen($color) == 3 )
				set_theme_mod('background_color', $color);
			else
				set_theme_mod('background_color', '');
		}
	}

	/**
	 * Execute Javascript depending on step.
	 *
	 * @since unknown
	 */
	function js() {
		$step = $this->step();
		if ( 1 == $step )
			$this->js_1();
	}

	/**
	 * Display Javascript based on Step 1.
	 *
	 * @since unknown
	 */
	function js_1() { ?>
<script type="text/javascript">
	var buttons = ['#pickcolor'],
		farbtastic;

	function pickColor(color) {
		jQuery('#background-color').val(color);
		farbtastic.setColor(color);
		jQuery('#custom-background-image').css('background-color', color);
	}

	jQuery(document).ready(function() {
		jQuery('#pickcolor').click(function() {
			jQuery('#colorPickerDiv').show();
		});
		jQuery('#background-color').keyup(function() {
			var _hex = jQuery('#background-color').val();
			var hex = _hex;
			if ( hex[0] != '#' )
				hex = '#' + hex;
			hex = hex.replace(/[^#a-fA-F0-9]+/, '');
			if ( hex != _hex )
				jQuery('#background-color').val(hex);
			if ( hex.length == 4 || hex.length == 7 )
				pickColor( hex );
		});

		farbtastic = jQuery.farbtastic('#colorPickerDiv', function(color) { pickColor(color); });
		pickColor('#<?php background_color(); ?>');
	});

	jQuery(document).mousedown(function(){
		hide_picker(); // Make the picker disappear if you click outside its div element
	});

	function hide_picker(what) {
		var update = false;
		jQuery('#colorPickerDiv').each(function(){
			var id = jQuery(this).attr('id');
			if (id == what) {
				return;
			}
			var display = jQuery(this).css('display');
			if (display == 'block') {
				jQuery(this).fadeOut(2);
			}
		});
	}

</script>
<?php
	}

	/**
	 * Display first step of custom background image page.
	 *
	 * @since unknown
	 */
	function step_1() {
?>
<div class="wrap" id="custom-background">
<?php screen_icon(); ?>
<h2><?php _e('Custom Background'); ?></h2>
<?php if ( isset($_GET['updated']) && $_GET['updated'] ) { ?>
<div id="message" class="updated">
<p><?php printf(__('Background updated. <a href="%s">Visit your site</a> to see how it looks.'), home_url()); ?></p>
</div>
<?php }

if ( get_background_image() || get_background_color() ) { ?>
<p><?php _e('This is your current background.'); ?></p>
<?php
	if ( $this->admin_image_div_callback ) {
		call_user_func($this->admin_image_div_callback);
	} else {
		if ( $bgcolor = get_background_color() )
			$bgcolor = ' style="background-color: #' . $bgcolor . ';"';
		else
			$bgcolor = '';
?>
<div id="custom-background-image"<?php echo $bgcolor; ?>>
<?php if ( get_background_image() ) { ?>
<img class="custom-background-image" src="<?php background_image(); ?>" />
<?php } ?>
</div>
<?php }
} else { ?>
<p><?php _e('There is currently no background image.'); ?></p> <?php
}

if ( get_background_image() ) : ?>

<h3><?php _e('Change Display Options') ?></h3>
<form method="post" action="<?php echo esc_attr(add_query_arg('step', 1)) ?>">
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
<input name="background-repeat" type="radio" value="no-repeat" <?php checked('no-repeat', get_theme_mod('background_repeat', 'repeat')); ?> />
<?php _e('No repeat') ?>
</label>
<label>
<input name="background-repeat" type="radio" value="repeat" <?php checked('repeat', get_theme_mod('background_repeat', 'repeat')); ?> />
<?php _e('Tile') ?>
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

<?php endif; ?>

<h3><?php _e('Upload New Background Image'); ?></h3>
<form enctype="multipart/form-data" id="uploadForm" method="POST" action="<?php echo esc_attr(add_query_arg('step', 2)) ?>">
<label for="upload"><?php _e('Choose an image from your computer:'); ?></label><br /><input type="file" id="upload" name="import" />
<input type="hidden" name="action" value="save" />
<?php wp_nonce_field('custom-background') ?>
<p class="submit">
<input type="submit" value="<?php esc_attr_e('Upload'); ?>" />
</p>
</form>

<?php if ( get_background_image() ) : ?>
<h3><?php _e('Remove Background Image'); ?></h3>
<p><?php _e('This will remove the background image. You will not be able to retrieve any customizations.') ?></p>
<form method="post" action="<?php echo esc_attr(add_query_arg('step', 1)) ?>">
<?php wp_nonce_field('custom-background'); ?>
<input type="submit" class="button" name="remove-background" value="<?php esc_attr_e('Remove Background'); ?>" />
</form>

<?php endif; ?>
</div>
<?php
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
		if ( ! current_user_can('switch_themes') )
			wp_die(__('You do not have permission to customize the background.'));
		$step = $this->step();
		if ( 1 == $step )
			$this->step_1();
		elseif ( 2 == $step )
			$this->step_2();
	}

}
?>
