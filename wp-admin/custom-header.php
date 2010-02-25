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
 * @since unknown
 * @package WordPress
 * @subpackage Administration
 */
class Custom_Image_Header {

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

	var $default_headers = array();

	/**
	 * PHP4 Constructor - Register administration header callback.
	 *
	 * @since unknown
	 * @param callback $admin_header_callback
	 * @param callback $admin_image_div_callback Optional custom image div output callback.
	 * @return Custom_Image_Header
	 */
	function Custom_Image_Header($admin_header_callback, $admin_image_div_callback = '') {
		$this->admin_header_callback = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;
	}

	/**
	 * Setup the hooks for the Custom Header admin page.
	 *
	 * @since unknown
	 */
	function init() {
		if ( ! current_user_can('switch_themes') )
			return;

		$page = add_theme_page(__('Header'), __('Header'), 'switch_themes', 'custom-header', array(&$this, 'admin_page'));

		add_action("admin_print_scripts-$page", array(&$this, 'js_includes'));
		add_action("admin_print_styles-$page", array(&$this, 'css_includes'));
		add_action("admin_head-$page", array(&$this, 'take_action'), 50);
		add_action("admin_head-$page", array(&$this, 'js'), 50);
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
	 * Setup the enqueue for the JavaScript files.
	 *
	 * @since unknown
	 */
	function js_includes() {
		$step = $this->step();

		if ( 1 == $step )
			wp_enqueue_script('farbtastic');
		elseif ( 2 == $step )
			wp_enqueue_script('jcrop');
	}

	/**
	 * Setup the enqueue for the CSS files
	 *
	 * @since 2.7
	 */
	function css_includes() {
		$step = $this->step();

		if ( 1 == $step )
			wp_enqueue_style('farbtastic');
		elseif ( 2 == $step )
			wp_enqueue_style('jcrop');
	}

	/**
	 * Execute custom header modification.
	 *
	 * @since unknown
	 */
	function take_action() {
		if ( ! current_user_can('switch_themes') )
			return;

		if ( isset( $_POST['textcolor'] ) ) {
			check_admin_referer('custom-header');
			if ( 'blank' == $_POST['textcolor'] ) {
				set_theme_mod('header_textcolor', 'blank');
			} else {
				$color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['textcolor']);
				if ( strlen($color) == 6 || strlen($color) == 3 )
					set_theme_mod('header_textcolor', $color);
			}
		}

		if ( isset($_POST['resetheader']) ) {
			check_admin_referer('custom-header');
			remove_theme_mods();
		}

		if ( isset($_POST['default-header']) ) {
			check_admin_referer('custom-header');
			$this->process_default_headers();
			if ( isset($this->default_headers[$_POST['default-header']]) )
				set_theme_mod('header_image', esc_url($this->default_headers[$_POST['default-header']]['url']));
		}
	}

	/**
	 * Process the default headers
	 *
	 *  @since 3.0.0
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
		echo '<table id="available-headers" cellspacing="0" cellpadding="0">';

		$headers = array_keys($this->default_headers);
		$table = array();
		$rows = ceil(count($headers) / 3);
		for ( $row = 1; $row <= $rows; $row++ ) {
			for ( $col = 1; $col <= 3; $col++ ) {
				$table[$row][$col] = array_shift($headers);
			}
		}

		foreach ( $table as $row => $cols ) {
			echo '<tr>';
			foreach ( $cols as $col => $header_key ) {
				if ( !$header_key )
					continue;
				$class = array('available-theme');
				if ( $row == 1 ) $class[] = 'top';
				if ( $col == 1 ) $class[] = 'left';
				if ( $row == $rows ) $class[] = 'bottom';
				if ( $col == 3 ) $class[] = 'right';
				if ( !isset($this->headers[$header_key]))
				echo '<td class="' . join(' ', $class) . '">';
				$header_thumbnail = $this->default_headers[$header_key]['thumbnail_url'];
				$header_url = $this->default_headers[$header_key]['url'];
				$header_desc = $this->default_headers[$header_key]['description'];
				echo '<label><input name="default-header" type="radio" value="' . esc_attr($header_key) . '" ' . checked($header_url, get_header_image(), false) . ' />';
				echo '<img src="' . $header_thumbnail . '" alt="' . esc_attr($header_desc) .'" /></label>';
				echo  '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
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
		elseif ( 2 == $step )
			$this->js_2();
	}

	/**
	 * Display Javascript based on Step 1.
	 *
	 * @since unknown
	 */
	function js_1() { ?>
<script type="text/javascript">
	var buttons = ['#name', '#desc', '#pickcolor', '#defaultcolor'];
	var farbtastic;

	function pickColor(color) {
		jQuery('#name').css('color', color);
		jQuery('#desc').css('color', color);
		jQuery('#textcolor').val(color);
		farbtastic.setColor(color);
	}

	jQuery(document).ready(function() {
		jQuery('#pickcolor').click(function() {
			jQuery('#colorPickerDiv').show();
		});

		jQuery('#hidetext').click(function() {
			toggle_text();
		});

		farbtastic = jQuery.farbtastic('#colorPickerDiv', function(color) { pickColor(color); });
		pickColor('#<?php echo get_theme_mod('header_textcolor', HEADER_TEXTCOLOR); ?>');

		<?php if ( 'blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) ) { ?>
		toggle_text();
		<?php } ?>
	});

	jQuery(document).mousedown(function(){
		// Make the picker disappear, since we're using it in an independant div
		hide_picker();
	});

	function colorDefault() {
		pickColor('#<?php echo HEADER_TEXTCOLOR; ?>');
	}

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

	function toggle_text(force) {
		if (jQuery('#textcolor').val() == 'blank') {
			//Show text
			jQuery( buttons.toString() ).show();
			jQuery('#textcolor').val('<?php echo HEADER_TEXTCOLOR; ?>');
			jQuery('#hidetext').val('<?php _e('Hide Text'); ?>');
		}
		else {
			//Hide text
			jQuery( buttons.toString() ).hide();
			jQuery('#textcolor').val('blank');
			jQuery('#hidetext').val('<?php _e('Show Text'); ?>');
		}
	}



</script>
<?php
	}

	/**
	 * Display Javascript based on Step 2.
	 *
	 * @since unknown
	 */
	function js_2() { ?>
<script type="text/javascript">
	function onEndCrop( coords ) {
		jQuery( '#x1' ).val(coords.x);
		jQuery( '#y1' ).val(coords.y);
		jQuery( '#x2' ).val(coords.x2);
		jQuery( '#y2' ).val(coords.y2);
		jQuery( '#width' ).val(coords.w);
		jQuery( '#height' ).val(coords.h);
	}

	// with a supplied ratio
	jQuery(document).ready(function() {
		var xinit = <?php echo HEADER_IMAGE_WIDTH; ?>;
		var yinit = <?php echo HEADER_IMAGE_HEIGHT; ?>;
		var ratio = xinit / yinit;
		var ximg = jQuery('#upload').width();
		var yimg = jQuery('#upload').height();

		//set up default values
		jQuery( '#x1' ).val(0);
		jQuery( '#y1' ).val(0);
		jQuery( '#x2' ).val(xinit);
		jQuery( '#y2' ).val(yinit);
		jQuery( '#width' ).val(xinit);
		jQuery( '#height' ).val(yinit);

		if ( yimg < yinit || ximg < xinit ) {
			if ( ximg / yimg > ratio ) {
				yinit = yimg;
				xinit = yinit * ratio;
			} else {
				xinit = ximg;
				yinit = xinit / ratio;
			}
		}

		jQuery('#upload').Jcrop({
			aspectRatio: ratio,
			setSelect: [ 0, 0, xinit, yinit ],
			onSelect: onEndCrop
		});
	});
</script>
<?php
	}

	/**
	 * Display first step of custom header image page.
	 *
	 * @since unknown
	 */
	function step_1() {
		$this->process_default_headers();
		if ( isset($_GET['updated']) && $_GET['updated'] ) { ?>
<div id="message" class="updated">
<p><?php printf(__('Header updated. <a href="%s">Visit your site</a> to see how it looks.'), home_url()); ?></p>
</div>
		<?php } ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php _e('Your Header Image'); ?></h2>
<?php
if ( get_theme_mod('header_image') || empty($this->default_headers) ) :
?>
<p><?php _e('This is your header image. You can change the text color or upload and crop a new image.'); ?></p>
<?php

if ( $this->admin_image_div_callback ) {
  call_user_func($this->admin_image_div_callback);
} else {
?>
<div id="headimg" style="background-image: url(<?php esc_url(header_image()) ?>);">
<h1><a onclick="return false;" href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" id="name"><?php bloginfo('name'); ?></a></h1>
<div id="desc"><?php bloginfo('description');?></div>
</div>
<?php } ?>

<?php if ( !defined( 'NO_HEADER_TEXT' ) ) { ?>
<form method="post" action="<?php echo admin_url('themes.php?page=custom-header&amp;updated=true') ?>">
<input type="button" class="button" value="<?php esc_attr_e('Hide Text'); ?>" onclick="hide_text()" id="hidetext" />
<input type="button" class="button" value="<?php esc_attr_e('Select a Text Color'); ?>" id="pickcolor" /><input type="button" class="button" value="<?php esc_attr_e('Use Original Color'); ?>" onclick="colorDefault()" id="defaultcolor" />
<?php wp_nonce_field('custom-header'); ?>
<input type="hidden" name="textcolor" id="textcolor" value="#<?php esc_attr(header_textcolor()) ?>" /><input name="submit" type="submit" class="button" value="<?php esc_attr_e('Save Changes'); ?>" /></form>
<?php } ?>

<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"> </div>
</div>
<?php
else:
	echo '<p>' . __('Choose one of these cool headers, or upload your own image below.') . '</p>';
	echo '<form method="post" action="' . admin_url('themes.php?page=custom-header&amp;updated=true') . '">';
	wp_nonce_field('custom-header');
	$this->show_default_header_selector();
	echo '<input type="submit" class="button" value="' . esc_attr__('Save Changes') . '"  />';
	echo '</form>';
	echo '</div>';
endif;
?>
<div class="wrap">
<h2><?php _e('Upload New Header Image'); ?></h2><p><?php _e('Here you can upload a custom header image to be shown at the top of your blog instead of the default one. On the next screen you will be able to crop the image.'); ?></p>
<p><?php printf(__('Images of exactly <strong>%1$d x %2$d pixels</strong> will be used as-is.'), HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT); ?></p>

<form enctype="multipart/form-data" id="uploadForm" method="POST" action="<?php echo esc_attr(add_query_arg('step', 2)) ?>" style="margin: auto; width: 50%;">
<label for="upload"><?php _e('Choose an image from your computer:'); ?></label><br /><input type="file" id="upload" name="import" />
<input type="hidden" name="action" value="save" />
<?php wp_nonce_field('custom-header') ?>
<p class="submit">
<input type="submit" value="<?php esc_attr_e('Upload'); ?>" />
</p>
</form>

</div>

		<?php if ( get_theme_mod('header_image') || get_theme_mod('header_textcolor') ) : ?>
<div class="wrap">
<h2><?php _e('Reset Header Image and Color'); ?></h2>
<form method="post" action="<?php echo esc_attr(add_query_arg('step', 1)) ?>">
<?php
wp_nonce_field('custom-header');
if ( !empty($this->default_headers) ) {
?>
<p><?php _e('Use one of these cool headers.') ?></p>
<?php
	$this->show_default_header_selector();
?>
	<input type="submit" class="button" name="resetheader" value="<?php esc_attr_e('Save Changes'); ?>" />
<?php
} else {
?>
<p><?php _e('This will restore the original header image and color. You will not be able to retrieve any customizations.') ?></p>
<input type="submit" class="button" name="resetheader" value="<?php esc_attr_e('Restore Original Header'); ?>" />
<?php } ?>
</form>
</div>
		<?php endif;

	}

	/**
	 * Display second step of custom header image page.
	 *
	 * @since unknown
	 */
	function step_2() {
		check_admin_referer('custom-header');
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

<form method="POST" action="<?php echo esc_attr(add_query_arg('step', 3)) ?>">

<p><?php _e('Choose the part of the image you want to use as your header.'); ?></p>
<div id="testWrap" style="position: relative">
<img src="<?php echo $url; ?>" id="upload" width="<?php echo $width; ?>" height="<?php echo $height; ?>" />
</div>

<p class="submit">
<input type="hidden" name="x1" id="x1" />
<input type="hidden" name="y1" id="y1" />
<input type="hidden" name="x2" id="x2" />
<input type="hidden" name="y2" id="y2" />
<input type="hidden" name="width" id="width" />
<input type="hidden" name="height" id="height" />
<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo esc_attr($id); ?>" />
<input type="hidden" name="oitar" id="oitar" value="<?php echo esc_attr($oitar); ?>" />
<?php wp_nonce_field('custom-header') ?>
<input type="submit" value="<?php esc_attr_e('Crop Header'); ?>" />
</p>

</form>
</div>
		<?php
	}

	/**
	 * Display third step of custom header image page.
	 *
	 * @since unknown
	 */
	function step_3() {
		check_admin_referer('custom-header');
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
