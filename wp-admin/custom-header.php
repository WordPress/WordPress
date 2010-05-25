<?php
/**
 * The custom header image script.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * The custom header image class.
 *
 * @since 2.1.0
 * @package WordPress
 * @subpackage Administration
 */
class Custom_Image_Header {

	/**
	 * Callback for administration header.
	 *
	 * @var callback
	 * @since 2.1.0
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
	 * Holds default headers.
	 *
	 * @var array
	 * @since 3.0.0
	 * @access private
	 */
	var $default_headers = array();

	/**
	 * PHP4 Constructor - Register administration header callback.
	 *
	 * @since 2.1.0
	 * @param callback $admin_header_callback
	 * @param callback $admin_image_div_callback Optional custom image div output callback.
	 * @return Custom_Image_Header
	 */
	function Custom_Image_Header($admin_header_callback, $admin_image_div_callback = '') {
		$this->admin_header_callback = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;
	}

	/**
	 * Set up the hooks for the Custom Header admin page.
	 *
	 * @since 2.1.0
	 */
	function init() {
		if ( ! current_user_can('edit_theme_options') )
			return;

		$page = add_theme_page(__('Header'), __('Header'), 'edit_theme_options', 'custom-header', array(&$this, 'admin_page'));

		add_action("admin_print_scripts-$page", array(&$this, 'js_includes'));
		add_action("admin_print_styles-$page", array(&$this, 'css_includes'));
		add_action("admin_head-$page", array(&$this, 'take_action'), 50);
		add_action("admin_head-$page", array(&$this, 'js'), 50);
		add_action("admin_head-$page", $this->admin_header_callback, 51);
	}

	/**
	 * Get the current step.
	 *
	 * @since 2.6.0
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
	 * @since 2.1.0
	 */
	function js_includes() {
		$step = $this->step();

		if ( ( 1 == $step || 3 == $step ) && $this->header_text() )
			wp_enqueue_script('farbtastic');
		elseif ( 2 == $step )
			wp_enqueue_script('imgareaselect');
	}

	/**
	 * Set up the enqueue for the CSS files
	 *
	 * @since 2.7
	 */
	function css_includes() {
		$step = $this->step();

		if ( ( 1 == $step || 3 == $step ) && $this->header_text() )
			wp_enqueue_style('farbtastic');
		elseif ( 2 == $step )
			wp_enqueue_style('imgareaselect');
	}
	
	/**
	 * Check if header text is allowed
	 *
	 * @since 3.0.0
	 */
	function header_text() {
		if ( defined( 'NO_HEADER_TEXT' ) && NO_HEADER_TEXT )
			return false;

		return true;
	}

	/**
	 * Execute custom header modification.
	 *
	 * @since 2.6.0
	 */
	function take_action() {
		if ( ! current_user_can('edit_theme_options') )
			return;

		if ( empty( $_POST ) )
			return;

		$this->updated = true;

		if ( isset( $_POST['resetheader'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			remove_theme_mod( 'header_image' );
			return;
		}

		if ( isset( $_POST['resettext'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			remove_theme_mod('header_textcolor');
			return;
		}

		if ( isset( $_POST['removeheader'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			set_theme_mod( 'header_image', '' );
			return;
		}

		if ( isset( $_POST['text-color'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			$_POST['text-color'] = str_replace( '#', '', $_POST['text-color'] );
			if ( 'blank' == $_POST['text-color'] ) {
				set_theme_mod( 'header_textcolor', 'blank' );
			} else {
				$color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['text-color']);
				if ( strlen($color) == 6 || strlen($color) == 3 )
					set_theme_mod('header_textcolor', $color);
			}
		}

		if ( isset($_POST['default-header']) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			$this->process_default_headers();
			if ( isset($this->default_headers[$_POST['default-header']]) )
				set_theme_mod('header_image', esc_url($this->default_headers[$_POST['default-header']]['url']));
		}
	}

	/**
	 * Process the default headers
	 *
	 * @since 3.0.0
	 */
	function process_default_headers() {
		global $_wp_default_headers;

		if ( !empty($this->headers) )
			return;

		if ( !isset($_wp_default_headers) )
			return;

		$this->default_headers = $_wp_default_headers;
		foreach ( array_keys($this->default_headers) as $header ) {
			$this->default_headers[$header]['url'] =  sprintf( $this->default_headers[$header]['url'], get_template_directory_uri(), get_stylesheet_directory_uri() );
			$this->default_headers[$header]['thumbnail_url'] =  sprintf( $this->default_headers[$header]['thumbnail_url'], get_template_directory_uri(), get_stylesheet_directory_uri() );
		}
	}

	/**
	 * Display UI for selecting one of several default headers.
	 *
	 * @since 3.0.0
	 */
	function show_default_header_selector() {
		echo '<div id="available-headers">';
		foreach ( $this->default_headers as $header_key => $header ) {
			$header_thumbnail = $header['thumbnail_url'];
			$header_url = $header['url'];
			$header_desc = $header['description'];
			echo '<div class="default-header">';
			echo '<label><input name="default-header" type="radio" value="' . esc_attr($header_key) . '" ' . checked($header_url, get_theme_mod( 'header_image' ), false) . ' />';
			echo '<img src="' . $header_thumbnail . '" alt="' . esc_attr($header_desc) .'" title="' . esc_attr($header_desc) .'" /></label>';
			echo '</div>';
		}
		echo '<div class="clear"></div></div>';
	}

	/**
	 * Execute Javascript depending on step.
	 *
	 * @since 2.1.0
	 */
	function js() {
		$step = $this->step();
		if ( ( 1 == $step || 3 == $step ) && $this->header_text() )
			$this->js_1();
		elseif ( 2 == $step )
			$this->js_2();
	}

	/**
	 * Display Javascript based on Step 1 and 3.
	 *
	 * @since 2.6.0
	 */
	function js_1() { ?>
<script type="text/javascript">
/* <![CDATA[ */
	var text_objects = ['#name', '#desc', '#text-color-row'];
	var farbtastic;
	var default_color = '#<?php echo HEADER_TEXTCOLOR; ?>';
	var old_color = null;

	function pickColor(color) {
		jQuery('#name').css('color', color);
		jQuery('#desc').css('color', color);
		jQuery('#text-color').val(color);
		farbtastic.setColor(color);
	}

	function toggle_text(s) {
		if (jQuery(s).attr('id') == 'showtext' && jQuery('#text-color').val() != 'blank')
			return;

		if (jQuery(s).attr('id') == 'hidetext' && jQuery('#text-color').val() == 'blank')
			return;

		if (jQuery('#text-color').val() == 'blank') {
			//Show text
			if (old_color == '#blank')
				old_color = default_color;

			jQuery( text_objects.toString() ).show();
			jQuery('#text-color').val(old_color);
			jQuery('#name').css('color', old_color);
			jQuery('#desc').css('color', old_color);
			pickColor(old_color); 
		} else {
			//Hide text
			jQuery( text_objects.toString() ).hide();
			old_color = jQuery('#text-color').val();
			jQuery('#text-color').val('blank');
		}
	}

	jQuery(document).ready(function() {
		jQuery('#pickcolor').click(function() {
			jQuery('#color-picker').show();
		});

		jQuery('input[name="hidetext"]').click(function() {
			toggle_text(this);
		});

		jQuery('#defaultcolor').click(function() {
			pickColor(default_color);
			jQuery('#text-color').val(default_color)
		});

		jQuery('#text-color').keyup(function() {
			var _hex = jQuery('#text-color').val();
			var hex = _hex;
			if ( hex[0] != '#' )
				hex = '#' + hex;
			hex = hex.replace(/[^#a-fA-F0-9]+/, '');
			if ( hex != _hex )
				jQuery('#text-color').val(hex);
			if ( hex.length == 4 || hex.length == 7 )
				pickColor( hex );
		});

		jQuery(document).mousedown(function(){
			jQuery('#color-picker').each( function() {
				var display = jQuery(this).css('display');
				if (display == 'block')
					jQuery(this).fadeOut(2);
			});
		});

		farbtastic = jQuery.farbtastic('#color-picker', function(color) { pickColor(color); });
		<?php if ( $color = get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) ) { ?>
		pickColor('#<?php echo $color; ?>');
		<?php } ?>

		<?php if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || ! $this->header_text() ) { ?>
		toggle_text();
		<?php } ?>
		});
</script>
<?php
	}

	/**
	 * Display Javascript based on Step 2.
	 *
	 * @since 2.6.0
	 */
	function js_2() { ?>
<script type="text/javascript">
/* <![CDATA[ */
	function onEndCrop( coords ) {
		jQuery( '#x1' ).val(coords.x);
		jQuery( '#y1' ).val(coords.y);
		jQuery( '#width' ).val(coords.w);
		jQuery( '#height' ).val(coords.h);
	}

	jQuery(document).ready(function() {
		var xinit = <?php echo HEADER_IMAGE_WIDTH; ?>;
		var yinit = <?php echo HEADER_IMAGE_HEIGHT; ?>;
		var ratio = xinit / yinit;
		var ximg = jQuery('#upload').width();
		var yimg = jQuery('#upload').height();

		if ( yimg < yinit || ximg < xinit ) {
			if ( ximg / yimg > ratio ) {
				yinit = yimg;
				xinit = yinit * ratio;
			} else {
				xinit = ximg;
				yinit = xinit / ratio;
			}
		}

		jQuery('#upload').imgAreaSelect({
			handles: true,
			keys: true,
			aspectRatio: xinit + ':' + yinit,
			show: true,
			x1: 0,
			y1: 0,
			x2: xinit,
			y2: yinit,
			maxHeight: <?php echo HEADER_IMAGE_HEIGHT; ?>,
			maxWidth: <?php echo HEADER_IMAGE_WIDTH; ?>,
			onInit: function () { 
				jQuery('#width').val(xinit);
				jQuery('#height').val(yinit);
			},
			onSelectChange: function(img, c) {
				jQuery('#x1').val(c.x1);
				jQuery('#y1').val(c.y1);
				jQuery('#width').val(c.width);
				jQuery('#height').val(c.height);
			}
		});
	});
/* ]]> */
</script>
<?php
	}

	/**
	 * Display first step of custom header image page.
	 *
	 * @since 2.1.0
	 */
	function step_1() {
		$this->process_default_headers();
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Custom Header'); ?></h2>

<?php if ( ! empty( $this->updated ) ) { ?>
<div id="message" class="updated">
<p><?php printf( __( 'Header updated. <a href="%s">Visit your site</a> to see how it looks.' ), home_url( '/' ) ); ?></p>
</div>
<?php } ?>

<h3><?php _e( 'Header Image' ) ?></h3>
<table class="form-table">
<tbody>


<tr valign="top">
<th scope="row"><?php _e( 'Preview' ); ?></th>
<td >
	<?php if ( $this->admin_image_div_callback ) {
	  call_user_func( $this->admin_image_div_callback );
	} else {
	?>
	<div id="headimg" style="max-width:<?php echo HEADER_IMAGE_WIDTH; ?>px;height:<?php echo HEADER_IMAGE_HEIGHT; ?>px;background-image:url(<?php esc_url ( header_image() ) ?>);">
		<?php
		if ( 'blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || '' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) || ! $this->header_text() )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php bloginfo('url'); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	</div>
	<?php } ?>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e( 'Upload Image' ); ?></th>
<td>
	<p><?php _e( 'You can upload a custom header image to be shown at the top of your site instead of the default one. On the next screen you will be able to crop the image.' ); ?><br />
	<?php printf( __( 'Images of exactly <strong>%1$d x %2$d pixels</strong> will be used as-is.' ), HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT ); ?></p>
	<form enctype="multipart/form-data" id="upload-form" method="post" action="<?php echo esc_attr( add_query_arg( 'step', 2 ) ) ?>">
	<p>
		<label for="upload"><?php _e( 'Choose an image from your computer:' ); ?></label><br />
		<input type="file" id="upload" name="import" />
		<input type="hidden" name="action" value="save" />
		<?php wp_nonce_field( 'custom-header-upload', '_wpnonce-custom-header-upload' ) ?>
		<input type="submit" class="button" value="<?php esc_attr_e( 'Upload' ); ?>" />
	</p>
	</form>
</td>
</tr>
</tbody>
</table>

<form method="post" action="<?php echo esc_attr( add_query_arg( 'step', 1 ) ) ?>">
<table class="form-table">
<tbody>
	<?php if ( ! empty( $this->default_headers ) ) : ?>
<tr valign="top">
<th scope="row"><?php _e( 'Default Images' ); ?></th>
<td>
	<p><?php _e( 'If you don&lsquo;t want to upload your own image, you can use one of these cool headers.' ) ?></p>
	<?php
		$this->show_default_header_selector();
	?>
</td>
</tr>
	<?php endif;

	if ( get_header_image() ) : ?>
<tr valign="top">
<th scope="row"><?php _e( 'Remove Image' ); ?></th>
<td>
	<p><?php _e( 'This will remove the header image. You will not be able to restore any customizations.' ) ?></p>
	<input type="submit" class="button" name="removeheader" value="<?php esc_attr_e( 'Remove Header Image' ); ?>" />
</td>
</tr>
	<?php endif;

	if ( defined( 'HEADER_IMAGE' ) ) : ?>
<tr valign="top">
<th scope="row"><?php _e( 'Reset Image' ); ?></th>
<td>
	<p><?php _e( 'This will restore the original header image. You will not be able to restore any customizations.' ) ?></p>
	<input type="submit" class="button" name="resetheader" value="<?php esc_attr_e( 'Restore Original Header Image' ); ?>" />
</td>
</tr>
	<?php endif; ?>
</tbody>
</table>

	<?php if ( $this->header_text() ) : ?>
<h3><?php _e( 'Header Text' ) ?></h3>
<table class="form-table">
<tbody>
<tr valign="top" class="hide-if-no-js">
<th scope="row"><?php _e( 'Display Text' ); ?></th>
<td>
	<p>
	<?php $hidetext = get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ); ?>
	<label><input type="radio" value="1" name="hidetext" id="hidetext"<?php checked( ( 'blank' == $hidetext || empty( $hidetext ) )  ? true : false ); ?> /> <?php _e( 'No' ); ?></label>
	<label><input type="radio" value="0" name="hidetext" id="showtext"<?php checked( ( 'blank' == $hidetext || empty( $hidetext ) ) ? false : true ); ?> /> <?php _e( 'Yes' ); ?></label>
	</p>
</td>
</tr>

<tr valign="top" id="text-color-row">
<th scope="row"><?php _e( 'Text Color' ); ?></th>
<td>
	<p>
		<input type="text" name="text-color" id="text-color" value="#<?php echo esc_attr( get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) ); ?>" />
		<span class="description hide-if-js"><?php _e( 'If you want to hide header text, add <strong>#blank</strong> as text color.' );?></span>
		<input type="button" class="button hide-if-no-js" value="<?php esc_attr_e( 'Select a Color' ); ?>" id="pickcolor" />
	</p>
	<div id="color-picker" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
</td>
</tr>

	<?php if ( defined('HEADER_TEXTCOLOR') && get_theme_mod('header_textcolor') ) { ?>
<tr valign="top">
<th scope="row"><?php _e('Reset Text Color'); ?></th>
<td>
	<p><?php _e( 'This will restore the original header text. You will not be able to restore any customizations.' ) ?></p>
	<input type="submit" class="button" name="resettext" value="<?php esc_attr_e( 'Restore Original Header Text' ); ?>" />
</td>
</tr>
	<?php } ?>

</tbody>
</table>
	<?php endif;

wp_nonce_field( 'custom-header-options', '_wpnonce-custom-header-options' ); ?>
<p class="submit"><input type="submit" class="button-primary" name="save-header-options" value="<?php esc_attr_e( 'Save Changes' ); ?>" /></p>
</form>
</div>

<?php }

	/**
	 * Display second step of custom header image page.
	 *
	 * @since 2.1.0
	 */
	function step_2() {
		check_admin_referer('custom-header-upload', '_wpnonce-custom-header-upload');
		$overrides = array('test_form' => false);
		$file = wp_handle_upload($_FILES['import'], $overrides);

		if ( isset($file['error']) )
			wp_die( $file['error'],  __( 'Image Upload Error' ) );

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

		list($width, $height, $type, $attr) = getimagesize( $file );

		if ( $width == HEADER_IMAGE_WIDTH && $height == HEADER_IMAGE_HEIGHT ) {
			// Add the meta-data
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

			set_theme_mod('header_image', esc_url($url));
			do_action('wp_create_file_in_uploads', $file, $id); // For replication
			return $this->finished();
		} elseif ( $width > HEADER_IMAGE_WIDTH ) {
			$oitar = $width / HEADER_IMAGE_WIDTH;
			$image = wp_crop_image($file, 0, 0, $width, $height, HEADER_IMAGE_WIDTH, $height / $oitar, false, str_replace(basename($file), 'midsize-'.basename($file), $file));
			if ( is_wp_error( $image ) )
				wp_die( __( 'Image could not be processed.  Please go back and try again.' ), __( 'Image Processing Error' ) );

			$image = apply_filters('wp_create_file_in_uploads', $image, $id); // For replication

			$url = str_replace(basename($url), basename($image), $url);
			$width = $width / $oitar;
			$height = $height / $oitar;
		} else {
			$oitar = 1;
		}
		?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e( 'Crop Header Image' ); ?></h2>

<form method="post" action="<?php echo esc_attr(add_query_arg('step', 3)); ?>">
	<p class="hide-if-no-js"><?php _e('Choose the part of the image you want to use as your header.'); ?></p>
	<p class="hide-if-js"><strong><?php _e( 'You need Javascript to choose a part of the image.'); ?></strong></p>

	<div id="crop_image" style="position: relative">
		<img src="<?php echo esc_url( $url ); ?>" id="upload" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
	</div>

	<p class="submit">
	<input type="hidden" name="x1" id="x1" value="0"/>
	<input type="hidden" name="y1" id="y1" value="0"/>
	<input type="hidden" name="width" id="width" value="<?php echo esc_attr( $width ); ?>"/>
	<input type="hidden" name="height" id="height" value="<?php echo esc_attr( $height ); ?>"/>
	<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo esc_attr( $id ); ?>" />
	<input type="hidden" name="oitar" id="oitar" value="<?php echo esc_attr( $oitar ); ?>" />
	<?php wp_nonce_field( 'custom-header-crop-image' ) ?>
	<input type="submit" value="<?php esc_attr_e( 'Crop Header' ); ?>" />
	</p>
</form>
</div>
		<?php
	}

	/**
	 * Display third step of custom header image page.
	 *
	 * @since 2.1.0
	 */
	function step_3() {
		check_admin_referer('custom-header-crop-image');
		if ( $_POST['oitar'] > 1 ) {
			$_POST['x1'] = $_POST['x1'] * $_POST['oitar'];
			$_POST['y1'] = $_POST['y1'] * $_POST['oitar'];
			$_POST['width'] = $_POST['width'] * $_POST['oitar'];
			$_POST['height'] = $_POST['height'] * $_POST['oitar'];
		}

		$original = get_attached_file( $_POST['attachment_id'] );

		$cropped = wp_crop_image($_POST['attachment_id'], $_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height'], HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT);
		if ( is_wp_error( $cropped ) )
			wp_die( __( 'Image could not be processed.  Please go back and try again.' ), __( 'Image Processing Error' ) );

		$cropped = apply_filters('wp_create_file_in_uploads', $cropped, $_POST['attachment_id']); // For replication

		$parent = get_post($_POST['attachment_id']);
		$parent_url = $parent->guid;
		$url = str_replace(basename($parent_url), basename($cropped), $parent_url);

		// Construct the object array
		$object = array(
			'ID' => $_POST['attachment_id'],
			'post_title' => basename($cropped),
			'post_content' => $url,
			'post_mime_type' => 'image/jpeg',
			'guid' => $url
		);

		// Update the attachment
		wp_insert_attachment($object, $cropped);
		wp_update_attachment_metadata( $_POST['attachment_id'], wp_generate_attachment_metadata( $_POST['attachment_id'], $cropped ) );

		set_theme_mod('header_image', $url);

		// cleanup
		$medium = str_replace(basename($original), 'midsize-'.basename($original), $original);
		@unlink( apply_filters( 'wp_delete_file', $medium ) );
		@unlink( apply_filters( 'wp_delete_file', $original ) );

		return $this->finished();
	}

	/**
	 * Display last step of custom header image page.
	 *
	 * @since 2.1.0
	 */
	function finished() {
		$this->updated = true;
		$this->step_1();
	}

	/**
	 * Display the page based on the current step.
	 *
	 * @since 2.1.0
	 */
	function admin_page() {
		if ( ! current_user_can('edit_theme_options') )
			wp_die(__('You do not have permission to customize headers.'));
		$step = $this->step();
		if ( 1 == $step )
			$this->step_1();
		elseif ( 2 == $step )
			$this->step_2();
		elseif ( 3 == $step )
			$this->step_3();
	}

}
?>
