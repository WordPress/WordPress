<?php

class Custom_Image_Header {
	var $admin_header_callback;

	function Custom_Image_Header($admin_header_callback) {
		$this->admin_header_callback = $admin_header_callback;
	}

	function init() {
		$page = add_theme_page(__('Custom Image Header'), __('Custom Image Header'), 'edit_themes', 'custom-header', array(&$this, 'admin_page'));

		add_action("admin_print_scripts-$page", array(&$this, 'js_includes'));
		add_action("admin_head-$page", array(&$this, 'take_action'), 50);
		add_action("admin_head-$page", array(&$this, 'js'), 50);
		add_action("admin_head-$page", $this->admin_header_callback, 51);
	}

	function step() {
		$step = (int) @$_GET['step'];
		if ( $step < 1 || 3 < $step )
			$step = 1;
		return $step;
	}

	function js_includes() {
		$step = $this->step();
		if ( 1 == $step )
			wp_enqueue_script('colorpicker');
		elseif ( 2 == $step )	
			wp_enqueue_script('cropper');
	}

	function take_action() {
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
	}

	function js() {
		$step = $this->step();
		if ( 1 == $step )
			$this->js_1();
		elseif ( 2 == $step )
			$this->js_2();
	}

	function js_1() { ?>
<script type="text/javascript">
	var cp = new ColorPicker();

	function pickColor(color) {
		$('name').style.color = color;
		$('desc').style.color = color;
		$('textcolor').value = color;
	}
	function PopupWindow_hidePopup(magicword) {
		if ( magicword != 'prettyplease' )
			return false;
		if (this.divName != null) {
			if (this.use_gebi) {
				document.getElementById(this.divName).style.visibility = "hidden";
			}
			else if (this.use_css) {
				document.all[this.divName].style.visibility = "hidden";
			}
			else if (this.use_layers) {
				document.layers[this.divName].visibility = "hidden";
			}
		}
		else {
			if (this.popupWindow && !this.popupWindow.closed) {
				this.popupWindow.close();
				this.popupWindow = null;
			}
		}
		return false;
	}
	function colorSelect(t,p) {
		if ( cp.p == p && document.getElementById(cp.divName).style.visibility != "hidden" ) {
			cp.hidePopup('prettyplease');
		} else {
			cp.p = p;
			cp.select(t,p);
		}
	}
	function colorDefault() {
		pickColor('#<?php echo HEADER_TEXTCOLOR; ?>');
	}

	function hide_text() {
		$('name').style.display = 'none';
		$('desc').style.display = 'none';
		$('pickcolor').style.display = 'none';
		$('defaultcolor').style.display = 'none';
		$('textcolor').value = 'blank';
		$('hidetext').value = '<?php _e('Show Text'); ?>';
//		$('hidetext').onclick = 'show_text()';
		Event.observe( $('hidetext'), 'click', show_text );
	}

	function show_text() {
		$('name').style.display = 'block';
		$('desc').style.display = 'block';
		$('pickcolor').style.display = 'inline';
		$('defaultcolor').style.display = 'inline';
		$('textcolor').value = '<?php echo HEADER_TEXTCOLOR; ?>';
		$('hidetext').value = '<?php _e('Hide Text'); ?>';
		Event.stopObserving( $('hidetext'), 'click', show_text );
		Event.observe( $('hidetext'), 'click', hide_text );
	}

	<?php if ( 'blank' == get_theme_mod('header_textcolor', HEADER_TEXTCOLOR) ) { ?>
Event.observe( window, 'load', hide_text );
	<?php } ?>

</script>
<?php
	}

	function js_2() { ?>
<script type="text/javascript">
	function onEndCrop( coords, dimensions ) {
		$( 'x1' ).value = coords.x1;
		$( 'y1' ).value = coords.y1;
		$( 'x2' ).value = coords.x2;
		$( 'y2' ).value = coords.y2;
		$( 'width' ).value = dimensions.width;
		$( 'height' ).value = dimensions.height;
	}

	// with a supplied ratio
	Event.observe(
		window,
		'load',
		function() {
			var xinit = <?php echo HEADER_IMAGE_WIDTH; ?>;
			var yinit = <?php echo HEADER_IMAGE_HEIGHT; ?>;
			var ratio = xinit / yinit;
			var ximg = $('upload').width;
			var yimg = $('upload').height;
			if ( yimg < yinit || ximg < xinit ) {
				if ( ximg / yimg > ratio ) {
					yinit = yimg;
					xinit = yinit * ratio;
				} else {
					xinit = ximg;
					yinit = xinit / ratio;
				}
			}
			new Cropper.Img(
				'upload',
				{
					ratioDim: { x: xinit, y: yinit },
					displayOnInit: true,
					onEndCrop: onEndCrop
				}
			)
		}
	);
</script>
<?php
	}

	function step_1() {
		if ( $_GET['updated'] ) { ?>
<div id="message" class="updated fade">
<p><?php _e('Header updated.') ?></p>
</div>
		<?php } ?>

<div class="wrap">
<h2><?php _e('Your Header Image'); ?></h2>
<p><?php _e('This is your header image. You can change the text color or upload and crop a new image.'); ?></p>

<div id="headimg" style="background-image: url(<?php clean_url(header_image()) ?>);">
<h1><a onclick="return false;" href="<?php bloginfo('url'); ?>" title="<?php bloginfo('name'); ?>" id="name"><?php bloginfo('name'); ?></a></h1>
<div id="desc"><?php bloginfo('description');?></div>
</div>
<?php if ( !defined( 'NO_HEADER_TEXT' ) ) { ?>
<form method="post" action="<?php echo get_option('siteurl') ?>/wp-admin/themes.php?page=custom-header&amp;updated=true">
<input type="button" value="<?php _e('Hide Text'); ?>" onclick="hide_text()" id="hidetext" />
<input type="button" value="<?php _e('Select a Text Color'); ?>" onclick="colorSelect($('textcolor'), 'pickcolor')" id="pickcolor" /><input type="button" value="<?php _e('Use Original Color'); ?>" onclick="colorDefault()" id="defaultcolor" />
<?php wp_nonce_field('custom-header') ?>
<input type="hidden" name="textcolor" id="textcolor" value="#<?php attribute_escape(header_textcolor()) ?>" /><input name="submit" type="submit" value="<?php _e('Save Changes'); ?>" /></form>
<?php } ?>

<div id="colorPickerDiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;visibility:hidden;"> </div>
</div>
<div class="wrap">
<h2><?php _e('Upload New Header Image'); ?></h2><p><?php _e('Here you can upload a custom header image to be shown at the top of your blog instead of the default one. On the next screen you will be able to crop the image.'); ?></p>
<p><?php printf(__('Images of exactly <strong>%1$d x %2$d pixels</strong> will be used as-is.'), HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT); ?></p>

<form enctype="multipart/form-data" id="uploadForm" method="POST" action="<?php echo attribute_escape(add_query_arg('step', 2)) ?>" style="margin: auto; width: 50%;">
<label for="upload"><?php _e('Choose an image from your computer:'); ?></label><br /><input type="file" id="upload" name="import" />
<input type="hidden" name="action" value="save" />
<?php wp_nonce_field('custom-header') ?>
<p class="submit">
<input type="submit" value="<?php _e('Upload'); ?>" />
</p>
</form>

</div>

		<?php if ( get_theme_mod('header_image') || get_theme_mod('header_textcolor') ) : ?>
<div class="wrap">
<h2><?php _e('Reset Header Image and Color'); ?></h2>
<p><?php _e('This will restore the original header image and color. You will not be able to retrieve any customizations.') ?></p>
<form method="post" action="<?php echo attribute_escape(add_query_arg('step', 1)) ?>">
<?php wp_nonce_field('custom-header'); ?>
<input type="submit" name="resetheader" value="<?php _e('Restore Original Header'); ?>" />
</form>
</div>
		<?php endif;

	}

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

			set_theme_mod('header_image', clean_url($url));
			do_action('wp_create_file_in_uploads', $file, $id); // For replication
			return $this->finished();
		} elseif ( $width > HEADER_IMAGE_WIDTH ) {
			$oitar = $width / HEADER_IMAGE_WIDTH;
			$image = wp_crop_image($file, 0, 0, $width, $height, HEADER_IMAGE_WIDTH, $height / $oitar, false, str_replace(basename($file), 'midsize-'.basename($file), $file));
			$image = apply_filters('wp_create_file_in_uploads', $image, $id); // For replication

			$url = str_replace(basename($url), basename($image), $url);
			$width = $width / $oitar;
			$height = $height / $oitar;
		} else {
			$oitar = 1;
		}
		?>

<div class="wrap">

<form method="POST" action="<?php echo attribute_escape(add_query_arg('step', 3)) ?>">

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
<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo $id; ?>" />
<input type="hidden" name="oitar" id="oitar" value="<?php echo $oitar; ?>" />
<?php wp_nonce_field('custom-header') ?>
<input type="submit" value="<?php _e('Crop Header'); ?>" />
</p>

</form>
</div>
		<?php
	}

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

	function finished() {
		?>
<div class="wrap">
<h2><?php _e('Header complete!'); ?></h2>

<p><?php _e('Visit your site and you should see the new header now.'); ?></p>

</div>
		<?php
	}

	function admin_page() {
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
