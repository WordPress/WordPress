<?php
/**
** A base module for [captchac] and [captchar]
**/

/* Shortcode handler */

add_action( 'wpcf7_init', 'wpcf7_add_shortcode_captcha' );

function wpcf7_add_shortcode_captcha() {
	wpcf7_add_shortcode( array( 'captchac', 'captchar' ),
		'wpcf7_captcha_shortcode_handler', true );
}

function wpcf7_captcha_shortcode_handler( $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	if ( 'captchac' == $tag->type && ! class_exists( 'ReallySimpleCaptcha' ) )
		return '<em>' . __( 'To use CAPTCHA, you need <a href="http://wordpress.org/extend/plugins/really-simple-captcha/">Really Simple CAPTCHA</a> plugin installed.', 'contact-form-7' ) . '</em>';

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	if ( 'captchac' == $tag->type ) { // CAPTCHA-Challenge (image)
		$class .= ' wpcf7-captcha-' . $tag->name;

		$atts = array();

		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();

		$op = array( // Default
			'img_size' => array( 72, 24 ),
			'base' => array( 6, 18 ),
			'font_size' => 14,
			'font_char_width' => 15 );

		$op = array_merge( $op, wpcf7_captchac_options( $tag->options ) );

		if ( ! $filename = wpcf7_generate_captcha( $op ) )
			return '';

		if ( ! empty( $op['img_size'] ) ) {
			if ( isset( $op['img_size'][0] ) )
				$atts['width'] = $op['img_size'][0];

			if ( isset( $op['img_size'][1] ) )
				$atts['height'] = $op['img_size'][1];
		}

		$atts['alt'] = 'captcha';
		$atts['src'] = wpcf7_captcha_url( $filename );

		$atts = wpcf7_format_atts( $atts );

		$prefix = substr( $filename, 0, strrpos( $filename, '.' ) );

		$html = sprintf(
			'<input type="hidden" name="_wpcf7_captcha_challenge_%1$s" value="%2$s" /><img %3$s />',
			$tag->name, $prefix, $atts );

		return $html;

	} elseif ( 'captchar' == $tag->type ) { // CAPTCHA-Response (input)
		if ( $validation_error )
			$class .= ' wpcf7-not-valid';

		$atts = array();

		$atts['size'] = $tag->get_size_option( '40' );
		$atts['maxlength'] = $tag->get_maxlength_option();
		$atts['class'] = $tag->get_class_option( $class );
		$atts['id'] = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

		$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

		$value = (string) reset( $tag->values );

		if ( wpcf7_is_posted() )
			$value = '';

		if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
			$atts['placeholder'] = $value;
			$value = '';
		}

		$atts['value'] = $value;
		$atts['type'] = 'text';
		$atts['name'] = $tag->name;

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf(
			'<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span>',
			sanitize_html_class( $tag->name ), $atts, $validation_error );

		return $html;
	}
}


/* Validation filter */

add_filter( 'wpcf7_validate_captchar', 'wpcf7_captcha_validation_filter', 10, 2 );

function wpcf7_captcha_validation_filter( $result, $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	$type = $tag->type;
	$name = $tag->name;

	$captchac = '_wpcf7_captcha_challenge_' . $name;

	$prefix = isset( $_POST[$captchac] ) ? (string) $_POST[$captchac] : '';
	$response = isset( $_POST[$name] ) ? (string) $_POST[$name] : '';

	if ( 0 == strlen( $prefix ) || ! wpcf7_check_captcha( $prefix, $response ) ) {
		$result['valid'] = false;
		$result['reason'][$name] = wpcf7_get_message( 'captcha_not_match' );
	}

	if ( isset( $result['reason'][$name] ) && $id = $tag->get_id_option() ) {
		$result['idref'][$name] = $id;
	}

	if ( 0 != strlen( $prefix ) ) {
		wpcf7_remove_captcha( $prefix );
	}

	return $result;
}


/* Ajax echo filter */

add_filter( 'wpcf7_ajax_onload', 'wpcf7_captcha_ajax_refill' );
add_filter( 'wpcf7_ajax_json_echo', 'wpcf7_captcha_ajax_refill' );

function wpcf7_captcha_ajax_refill( $items ) {
	if ( ! is_array( $items ) )
		return $items;

	$fes = wpcf7_scan_shortcode( array( 'type' => 'captchac' ) );

	if ( empty( $fes ) )
		return $items;

	$refill = array();

	foreach ( $fes as $fe ) {
		$name = $fe['name'];
		$options = $fe['options'];

		if ( empty( $name ) )
			continue;

		$op = wpcf7_captchac_options( $options );
		if ( $filename = wpcf7_generate_captcha( $op ) ) {
			$captcha_url = wpcf7_captcha_url( $filename );
			$refill[$name] = $captcha_url;
		}
	}

	if ( ! empty( $refill ) )
		$items['captcha'] = $refill;

	return $items;
}


/* Messages */

add_filter( 'wpcf7_messages', 'wpcf7_captcha_messages' );

function wpcf7_captcha_messages( $messages ) {
	return array_merge( $messages, array( 'captcha_not_match' => array(
		'description' => __( "The code that sender entered does not match the CAPTCHA", 'contact-form-7' ),
		'default' => __( 'Your entered code is incorrect.', 'contact-form-7' )
	) ) );
}


/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_captcha', 45 );

function wpcf7_add_tag_generator_captcha() {
	if ( ! function_exists( 'wpcf7_add_tag_generator' ) )
		return;

	wpcf7_add_tag_generator( 'captcha', __( 'CAPTCHA', 'contact-form-7' ),
		'wpcf7-tg-pane-captcha', 'wpcf7_tg_pane_captcha' );
}

function wpcf7_tg_pane_captcha( $contact_form ) {
?>
<div id="wpcf7-tg-pane-captcha" class="hidden">
<form action="">
<table>

<?php if ( ! class_exists( 'ReallySimpleCaptcha' ) ) : ?>
<tr><td colspan="2"><strong style="color: #e6255b"><?php echo esc_html( __( "Note: To use CAPTCHA, you need Really Simple CAPTCHA plugin installed.", 'contact-form-7' ) ); ?></strong><br /><a href="http://wordpress.org/extend/plugins/really-simple-captcha/">http://wordpress.org/extend/plugins/really-simple-captcha/</a></td></tr>
<?php endif; ?>

<tr><td><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br /><input type="text" name="name" class="tg-name oneline" /></td><td></td></tr>
</table>

<table class="scope captchac">
<caption><?php echo esc_html( __( "Image settings", 'contact-form-7' ) ); ?></caption>

<tr>
<td><code>id</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="id" class="idvalue oneline option" /></td>

<td><code>class</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="class" class="classvalue oneline option" /></td>
</tr>

<tr>
<td><?php echo esc_html( __( "Foreground color", 'contact-form-7' ) ); ?> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="fg" class="color oneline option" /></td>

<td><?php echo esc_html( __( "Background color", 'contact-form-7' ) ); ?> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="bg" class="color oneline option" /></td>
</tr>

<tr><td colspan="2"><?php echo esc_html( __( "Image size", 'contact-form-7' ) ); ?> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="checkbox" name="size:s" class="exclusive option" />&nbsp;<?php echo esc_html( __( "Small", 'contact-form-7' ) ); ?>&emsp;
<input type="checkbox" name="size:m" class="exclusive option" />&nbsp;<?php echo esc_html( __( "Medium", 'contact-form-7' ) ); ?>&emsp;
<input type="checkbox" name="size:l" class="exclusive option" />&nbsp;<?php echo esc_html( __( "Large", 'contact-form-7' ) ); ?>
</td></tr>
</table>

<table class="scope captchar">
<caption><?php echo esc_html( __( "Input field settings", 'contact-form-7' ) ); ?></caption>

<tr>
<td><code>id</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="id" class="idvalue oneline option" /></td>

<td><code>class</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="text" name="class" class="classvalue oneline option" /></td>
</tr>

<tr>
<td><code>size</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="number" name="size" class="numeric oneline option" min="1" /></td>

<td><code>maxlength</code> (<?php echo esc_html( __( 'optional', 'contact-form-7' ) ); ?>)<br />
<input type="number" name="maxlength" class="numeric oneline option" min="1" /></td>
</tr>
</table>

<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'contact-form-7' ) ); ?>
<br />1) <?php echo esc_html( __( "For image", 'contact-form-7' ) ); ?>
<input type="text" name="captchac" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" />
<br />2) <?php echo esc_html( __( "For input field", 'contact-form-7' ) ); ?>
<input type="text" name="captchar" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()" />
</div>
</form>
</div>
<?php
}


/* Warning message */

add_action( 'wpcf7_admin_notices', 'wpcf7_captcha_display_warning_message' );

function wpcf7_captcha_display_warning_message() {
	if ( ! $contact_form = wpcf7_get_current_contact_form() ) {
		return;
	}

	$has_tags = (bool) $contact_form->form_scan_shortcode(
		array( 'type' => array( 'captchac' ) ) );

	if ( ! $has_tags )
		return;

	if ( ! class_exists( 'ReallySimpleCaptcha' ) )
		return;

	$uploads_dir = wpcf7_captcha_tmp_dir();
	wpcf7_init_captcha();

	if ( ! is_dir( $uploads_dir ) || ! wp_is_writable( $uploads_dir ) ) {
		$message = sprintf( __( 'This contact form contains CAPTCHA fields, but the temporary folder for the files (%s) does not exist or is not writable. You can create the folder or change its permission manually.', 'contact-form-7' ), $uploads_dir );

		echo '<div class="error"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
	}

	if ( ! function_exists( 'imagecreatetruecolor' ) || ! function_exists( 'imagettftext' ) ) {
		$message = __( 'This contact form contains CAPTCHA fields, but the necessary libraries (GD and FreeType) are not available on your server.', 'contact-form-7' );

		echo '<div class="error"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
	}
}


/* CAPTCHA functions */

function wpcf7_init_captcha() {
	static $captcha = null;

	if ( $captcha ) {
		return $captcha;
	}

	if ( class_exists( 'ReallySimpleCaptcha' ) ) {
		$captcha = new ReallySimpleCaptcha();
	} else {
		return false;
	}

	$dir = trailingslashit( wpcf7_captcha_tmp_dir() );

	$captcha->tmp_dir = $dir;

	if ( is_callable( array( $captcha, 'make_tmp_dir' ) ) ) {
		$result = $captcha->make_tmp_dir();

		if ( ! $result ) {
			return false;
		}

		return $captcha;
	}

	if ( wp_mkdir_p( $dir ) ) {
		$htaccess_file = $dir . '.htaccess';

		if ( file_exists( $htaccess_file ) ) {
			return $captcha;
		}

		if ( $handle = @fopen( $htaccess_file, 'w' ) ) {
			fwrite( $handle, 'Order deny,allow' . "\n" );
			fwrite( $handle, 'Deny from all' . "\n" );
			fwrite( $handle, '<Files ~ "^[0-9A-Za-z]+\\.(jpeg|gif|png)$">' . "\n" );
			fwrite( $handle, '    Allow from all' . "\n" );
			fwrite( $handle, '</Files>' . "\n" );
			fclose( $handle );
		}
	} else {
		return false;
	}

	return $captcha;
}

function wpcf7_captcha_tmp_dir() {
	if ( defined( 'WPCF7_CAPTCHA_TMP_DIR' ) )
		return WPCF7_CAPTCHA_TMP_DIR;
	else
		return wpcf7_upload_dir( 'dir' ) . '/wpcf7_captcha';
}

function wpcf7_captcha_tmp_url() {
	if ( defined( 'WPCF7_CAPTCHA_TMP_URL' ) )
		return WPCF7_CAPTCHA_TMP_URL;
	else
		return wpcf7_upload_dir( 'url' ) . '/wpcf7_captcha';
}

function wpcf7_captcha_url( $filename ) {
	$url = trailingslashit( wpcf7_captcha_tmp_url() ) . $filename;

	if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return apply_filters( 'wpcf7_captcha_url', esc_url_raw( $url ) );
}

function wpcf7_generate_captcha( $options = null ) {
	if ( ! $captcha = wpcf7_init_captcha() ) {
		return false;
	}

	if ( ! is_dir( $captcha->tmp_dir ) || ! wp_is_writable( $captcha->tmp_dir ) )
		return false;

	$img_type = imagetypes();
	if ( $img_type & IMG_PNG )
		$captcha->img_type = 'png';
	elseif ( $img_type & IMG_GIF )
		$captcha->img_type = 'gif';
	elseif ( $img_type & IMG_JPG )
		$captcha->img_type = 'jpeg';
	else
		return false;

	if ( is_array( $options ) ) {
		if ( isset( $options['img_size'] ) )
			$captcha->img_size = $options['img_size'];
		if ( isset( $options['base'] ) )
			$captcha->base = $options['base'];
		if ( isset( $options['font_size'] ) )
			$captcha->font_size = $options['font_size'];
		if ( isset( $options['font_char_width'] ) )
			$captcha->font_char_width = $options['font_char_width'];
		if ( isset( $options['fg'] ) )
			$captcha->fg = $options['fg'];
		if ( isset( $options['bg'] ) )
			$captcha->bg = $options['bg'];
	}

	$prefix = mt_rand();
	$captcha_word = $captcha->generate_random_word();
	return $captcha->generate_image( $prefix, $captcha_word );
}

function wpcf7_check_captcha( $prefix, $response ) {
	if ( ! $captcha = wpcf7_init_captcha() ) {
		return false;
	}

	return $captcha->check( $prefix, $response );
}

function wpcf7_remove_captcha( $prefix ) {
	if ( ! $captcha = wpcf7_init_captcha() ) {
		return false;
	}

	if ( preg_match( '/[^0-9]/', $prefix ) ) // Contact Form 7 generates $prefix with mt_rand()
		return false;

	$captcha->remove( $prefix );
}

add_action( 'template_redirect', 'wpcf7_cleanup_captcha_files', 20 );

function wpcf7_cleanup_captcha_files() {
	if ( ! $captcha = wpcf7_init_captcha() ) {
		return false;
	}

	if ( is_callable( array( $captcha, 'cleanup' ) ) )
		return $captcha->cleanup();

	$dir = trailingslashit( wpcf7_captcha_tmp_dir() );

	if ( ! is_dir( $dir ) || ! is_readable( $dir ) || ! wp_is_writable( $dir ) )
		return false;

	if ( $handle = @opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( ! preg_match( '/^[0-9]+\.(php|txt|png|gif|jpeg)$/', $file ) )
				continue;

			$stat = @stat( $dir . $file );
			if ( $stat['mtime'] + 3600 < time() ) // 3600 secs == 1 hour
				@unlink( $dir . $file );
		}
		closedir( $handle );
	}
}

function wpcf7_captchac_options( $options ) {
	if ( ! is_array( $options ) )
		return array();

	$op = array();
	$image_size_array = preg_grep( '%^size:[smlSML]$%', $options );

	if ( $image_size = array_shift( $image_size_array ) ) {
		preg_match( '%^size:([smlSML])$%', $image_size, $is_matches );
		switch ( strtolower( $is_matches[1] ) ) {
			case 's':
				$op['img_size'] = array( 60, 20 );
				$op['base'] = array( 6, 15 );
				$op['font_size'] = 11;
				$op['font_char_width'] = 13;
				break;
			case 'l':
				$op['img_size'] = array( 84, 28 );
				$op['base'] = array( 6, 20 );
				$op['font_size'] = 17;
				$op['font_char_width'] = 19;
				break;
			case 'm':
			default:
				$op['img_size'] = array( 72, 24 );
				$op['base'] = array( 6, 18 );
				$op['font_size'] = 14;
				$op['font_char_width'] = 15;
		}
	}

	$fg_color_array = preg_grep( '%^fg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $options );
	if ( $fg_color = array_shift( $fg_color_array ) ) {
		preg_match( '%^fg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $fg_color, $fc_matches );
		if ( 3 == strlen( $fc_matches[1] ) ) {
			$r = substr( $fc_matches[1], 0, 1 );
			$g = substr( $fc_matches[1], 1, 1 );
			$b = substr( $fc_matches[1], 2, 1 );
			$op['fg'] = array( hexdec( $r . $r ), hexdec( $g . $g ), hexdec( $b . $b ) );
		} elseif ( 6 == strlen( $fc_matches[1] ) ) {
			$r = substr( $fc_matches[1], 0, 2 );
			$g = substr( $fc_matches[1], 2, 2 );
			$b = substr( $fc_matches[1], 4, 2 );
			$op['fg'] = array( hexdec( $r ), hexdec( $g ), hexdec( $b ) );
		}
	}

	$bg_color_array = preg_grep( '%^bg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $options );
	if ( $bg_color = array_shift( $bg_color_array ) ) {
		preg_match( '%^bg:#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$%', $bg_color, $bc_matches );
		if ( 3 == strlen( $bc_matches[1] ) ) {
			$r = substr( $bc_matches[1], 0, 1 );
			$g = substr( $bc_matches[1], 1, 1 );
			$b = substr( $bc_matches[1], 2, 1 );
			$op['bg'] = array( hexdec( $r . $r ), hexdec( $g . $g ), hexdec( $b . $b ) );
		} elseif ( 6 == strlen( $bc_matches[1] ) ) {
			$r = substr( $bc_matches[1], 0, 2 );
			$g = substr( $bc_matches[1], 2, 2 );
			$b = substr( $bc_matches[1], 4, 2 );
			$op['bg'] = array( hexdec( $r ), hexdec( $g ), hexdec( $b ) );
		}
	}

	return $op;
}

?>