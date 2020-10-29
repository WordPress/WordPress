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
 */
class Custom_Image_Header {

	/**
	 * Callback for administration header.
	 *
	 * @var callable
	 * @since 2.1.0
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
	 * Holds default headers.
	 *
	 * @var array
	 * @since 3.0.0
	 */
	public $default_headers = array();

	/**
	 * Used to trigger a success message when settings updated and set to true.
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	private $updated;

	/**
	 * Constructor - Register administration header callback.
	 *
	 * @since 2.1.0
	 * @param callable $admin_header_callback
	 * @param callable $admin_image_div_callback Optional custom image div output callback.
	 */
	public function __construct( $admin_header_callback, $admin_image_div_callback = '' ) {
		$this->admin_header_callback    = $admin_header_callback;
		$this->admin_image_div_callback = $admin_image_div_callback;

		add_action( 'admin_menu', array( $this, 'init' ) );

		add_action( 'customize_save_after', array( $this, 'customize_set_last_used' ) );
		add_action( 'wp_ajax_custom-header-crop', array( $this, 'ajax_header_crop' ) );
		add_action( 'wp_ajax_custom-header-add', array( $this, 'ajax_header_add' ) );
		add_action( 'wp_ajax_custom-header-remove', array( $this, 'ajax_header_remove' ) );
	}

	/**
	 * Set up the hooks for the Custom Header admin page.
	 *
	 * @since 2.1.0
	 */
	public function init() {
		$page = add_theme_page( __( 'Header' ), __( 'Header' ), 'edit_theme_options', 'custom-header', array( $this, 'admin_page' ) );
		if ( ! $page ) {
			return;
		}

		add_action( "admin_print_scripts-{$page}", array( $this, 'js_includes' ) );
		add_action( "admin_print_styles-{$page}", array( $this, 'css_includes' ) );
		add_action( "admin_head-{$page}", array( $this, 'help' ) );
		add_action( "admin_head-{$page}", array( $this, 'take_action' ), 50 );
		add_action( "admin_head-{$page}", array( $this, 'js' ), 50 );
		if ( $this->admin_header_callback ) {
			add_action( "admin_head-{$page}", $this->admin_header_callback, 51 );
		}
	}

	/**
	 * Adds contextual help.
	 *
	 * @since 3.0.0
	 */
	public function help() {
		get_current_screen()->add_help_tab(
			array(
				'id'      => 'overview',
				'title'   => __( 'Overview' ),
				'content' =>
					'<p>' . __( 'This screen is used to customize the header section of your theme.' ) . '</p>' .
					'<p>' . __( 'You can choose from the theme&#8217;s default header images, or use one of your own. You can also customize how your Site Title and Tagline are displayed.' ) . '<p>',
			)
		);

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'set-header-image',
				'title'   => __( 'Header Image' ),
				'content' =>
					'<p>' . __( 'You can set a custom image header for your site. Simply upload the image and crop it, and the new header will go live immediately. Alternatively, you can use an image that has already been uploaded to your Media Library by clicking the &#8220;Choose Image&#8221; button.' ) . '</p>' .
					'<p>' . __( 'Some themes come with additional header images bundled. If you see multiple images displayed, select the one you&#8217;d like and click the &#8220;Save Changes&#8221; button.' ) . '</p>' .
					'<p>' . __( 'If your theme has more than one default header image, or you have uploaded more than one custom header image, you have the option of having WordPress display a randomly different image on each page of your site. Click the &#8220;Random&#8221; radio button next to the Uploaded Images or Default Images section to enable this feature.' ) . '</p>' .
					'<p>' . __( 'If you don&#8217;t want a header image to be displayed on your site at all, click the &#8220;Remove Header Image&#8221; button at the bottom of the Header Image section of this page. If you want to re-enable the header image later, you just have to select one of the other image options and click &#8220;Save Changes&#8221;.' ) . '</p>',
			)
		);

		get_current_screen()->add_help_tab(
			array(
				'id'      => 'set-header-text',
				'title'   => __( 'Header Text' ),
				'content' =>
					'<p>' . sprintf(
						/* translators: %s: URL to General Settings screen. */
						__( 'For most themes, the header text is your Site Title and Tagline, as defined in the <a href="%s">General Settings</a> section.' ),
						admin_url( 'options-general.php' )
					) .
					'</p>' .
					'<p>' . __( 'In the Header Text section of this page, you can choose whether to display this text or hide it. You can also choose a color for the text by clicking the Select Color button and either typing in a legitimate HTML hex value, e.g. &#8220;#ff0000&#8221; for red, or by choosing a color using the color picker.' ) . '</p>' .
					'<p>' . __( 'Don&#8217;t forget to click &#8220;Save Changes&#8221; when you&#8217;re done!' ) . '</p>',
			)
		);

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
			'<p>' . __( '<a href="https://codex.wordpress.org/Appearance_Header_Screen">Documentation on Custom Header</a>' ) . '</p>' .
			'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
		);
	}

	/**
	 * Get the current step.
	 *
	 * @since 2.6.0
	 *
	 * @return int Current step
	 */
	public function step() {
		if ( ! isset( $_GET['step'] ) ) {
			return 1;
		}

		$step = (int) $_GET['step'];
		if ( $step < 1 || 3 < $step ||
			( 2 == $step && ! wp_verify_nonce( $_REQUEST['_wpnonce-custom-header-upload'], 'custom-header-upload' ) ) ||
			( 3 == $step && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'custom-header-crop-image' ) )
		) {
			return 1;
		}

		return $step;
	}

	/**
	 * Set up the enqueue for the JavaScript files.
	 *
	 * @since 2.1.0
	 */
	public function js_includes() {
		$step = $this->step();

		if ( ( 1 == $step || 3 == $step ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'custom-header' );
			if ( current_theme_supports( 'custom-header', 'header-text' ) ) {
				wp_enqueue_script( 'wp-color-picker' );
			}
		} elseif ( 2 == $step ) {
			wp_enqueue_script( 'imgareaselect' );
		}
	}

	/**
	 * Set up the enqueue for the CSS files
	 *
	 * @since 2.7.0
	 */
	public function css_includes() {
		$step = $this->step();

		if ( ( 1 == $step || 3 == $step ) && current_theme_supports( 'custom-header', 'header-text' ) ) {
			wp_enqueue_style( 'wp-color-picker' );
		} elseif ( 2 == $step ) {
			wp_enqueue_style( 'imgareaselect' );
		}
	}

	/**
	 * Execute custom header modification.
	 *
	 * @since 2.6.0
	 */
	public function take_action() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		if ( empty( $_POST ) ) {
			return;
		}

		$this->updated = true;

		if ( isset( $_POST['resetheader'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			$this->reset_header_image();
			return;
		}

		if ( isset( $_POST['removeheader'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			$this->remove_header_image();
			return;
		}

		if ( isset( $_POST['text-color'] ) && ! isset( $_POST['display-header-text'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			set_theme_mod( 'header_textcolor', 'blank' );
		} elseif ( isset( $_POST['text-color'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			$_POST['text-color'] = str_replace( '#', '', $_POST['text-color'] );
			$color               = preg_replace( '/[^0-9a-fA-F]/', '', $_POST['text-color'] );
			if ( strlen( $color ) == 6 || strlen( $color ) == 3 ) {
				set_theme_mod( 'header_textcolor', $color );
			} elseif ( ! $color ) {
				set_theme_mod( 'header_textcolor', 'blank' );
			}
		}

		if ( isset( $_POST['default-header'] ) ) {
			check_admin_referer( 'custom-header-options', '_wpnonce-custom-header-options' );
			$this->set_header_image( $_POST['default-header'] );
			return;
		}
	}

	/**
	 * Process the default headers
	 *
	 * @since 3.0.0
	 *
	 * @global array $_wp_default_headers
	 */
	public function process_default_headers() {
		global $_wp_default_headers;

		if ( ! isset( $_wp_default_headers ) ) {
			return;
		}

		if ( ! empty( $this->default_headers ) ) {
			return;
		}

		$this->default_headers    = $_wp_default_headers;
		$template_directory_uri   = get_template_directory_uri();
		$stylesheet_directory_uri = get_stylesheet_directory_uri();
		foreach ( array_keys( $this->default_headers ) as $header ) {
			$this->default_headers[ $header ]['url']           = sprintf( $this->default_headers[ $header ]['url'], $template_directory_uri, $stylesheet_directory_uri );
			$this->default_headers[ $header ]['thumbnail_url'] = sprintf( $this->default_headers[ $header ]['thumbnail_url'], $template_directory_uri, $stylesheet_directory_uri );
		}
	}

	/**
	 * Display UI for selecting one of several default headers.
	 *
	 * Show the random image option if this theme has multiple header images.
	 * Random image option is on by default if no header has been set.
	 *
	 * @since 3.0.0
	 *
	 * @param string $type The header type. One of 'default' (for the Uploaded Images control)
	 *                     or 'uploaded' (for the Uploaded Images control).
	 */
	public function show_header_selector( $type = 'default' ) {
		if ( 'default' === $type ) {
			$headers = $this->default_headers;
		} else {
			$headers = get_uploaded_header_images();
			$type    = 'uploaded';
		}

		if ( 1 < count( $headers ) ) {
			echo '<div class="random-header">';
			echo '<label><input name="default-header" type="radio" value="random-' . $type . '-image"' . checked( is_random_header_image( $type ), true, false ) . ' />';
			_e( '<strong>Random:</strong> Show a different image on each page.' );
			echo '</label>';
			echo '</div>';
		}

		echo '<div class="available-headers">';
		foreach ( $headers as $header_key => $header ) {
			$header_thumbnail = $header['thumbnail_url'];
			$header_url       = $header['url'];
			$header_alt_text  = empty( $header['alt_text'] ) ? '' : $header['alt_text'];
			echo '<div class="default-header">';
			echo '<label><input name="default-header" type="radio" value="' . esc_attr( $header_key ) . '" ' . checked( $header_url, get_theme_mod( 'header_image' ), false ) . ' />';
			$width = '';
			if ( ! empty( $header['attachment_id'] ) ) {
				$width = ' width="230"';
			}
			echo '<img src="' . set_url_scheme( $header_thumbnail ) . '" alt="' . esc_attr( $header_alt_text ) . '"' . $width . ' /></label>';
			echo '</div>';
		}
		echo '<div class="clear"></div></div>';
	}

	/**
	 * Execute JavaScript depending on step.
	 *
	 * @since 2.1.0
	 */
	public function js() {
		$step = $this->step();
		if ( ( 1 == $step || 3 == $step ) && current_theme_supports( 'custom-header', 'header-text' ) ) {
			$this->js_1();
		} elseif ( 2 == $step ) {
			$this->js_2();
		}
	}

	/**
	 * Display JavaScript based on Step 1 and 3.
	 *
	 * @since 2.6.0
	 */
	public function js_1() {
		$default_color = '';
		if ( current_theme_supports( 'custom-header', 'default-text-color' ) ) {
			$default_color = get_theme_support( 'custom-header', 'default-text-color' );
			if ( $default_color && false === strpos( $default_color, '#' ) ) {
				$default_color = '#' . $default_color;
			}
		}
		?>
<script type="text/javascript">
(function($){
	var default_color = '<?php echo esc_js( $default_color ); ?>',
		header_text_fields;

	function pickColor(color) {
		$('#name').css('color', color);
		$('#desc').css('color', color);
		$('#text-color').val(color);
	}

	function toggle_text() {
		var checked = $('#display-header-text').prop('checked'),
			text_color;
		header_text_fields.toggle( checked );
		if ( ! checked )
			return;
		text_color = $('#text-color');
		if ( '' === text_color.val().replace('#', '') ) {
			text_color.val( default_color );
			pickColor( default_color );
		} else {
			pickColor( text_color.val() );
		}
	}

	$(document).ready(function() {
		var text_color = $('#text-color');
		header_text_fields = $('.displaying-header-text');
		text_color.wpColorPicker({
			change: function( event, ui ) {
				pickColor( text_color.wpColorPicker('color') );
			},
			clear: function() {
				pickColor( '' );
			}
		});
		$('#display-header-text').click( toggle_text );
		<?php if ( ! display_header_text() ) : ?>
		toggle_text();
		<?php endif; ?>
	});
})(jQuery);
</script>
		<?php
	}

	/**
	 * Display JavaScript based on Step 2.
	 *
	 * @since 2.6.0
	 */
	public function js_2() {

		?>
<script type="text/javascript">
	function onEndCrop( coords ) {
		jQuery( '#x1' ).val(coords.x);
		jQuery( '#y1' ).val(coords.y);
		jQuery( '#width' ).val(coords.w);
		jQuery( '#height' ).val(coords.h);
	}

	jQuery(document).ready(function() {
		var xinit = <?php echo absint( get_theme_support( 'custom-header', 'width' ) ); ?>;
		var yinit = <?php echo absint( get_theme_support( 'custom-header', 'height' ) ); ?>;
		var ratio = xinit / yinit;
		var ximg = jQuery('img#upload').width();
		var yimg = jQuery('img#upload').height();

		if ( yimg < yinit || ximg < xinit ) {
			if ( ximg / yimg > ratio ) {
				yinit = yimg;
				xinit = yinit * ratio;
			} else {
				xinit = ximg;
				yinit = xinit / ratio;
			}
		}

		jQuery('img#upload').imgAreaSelect({
			handles: true,
			keys: true,
			show: true,
			x1: 0,
			y1: 0,
			x2: xinit,
			y2: yinit,
			<?php
			if ( ! current_theme_supports( 'custom-header', 'flex-height' ) && ! current_theme_supports( 'custom-header', 'flex-width' ) ) {
				?>
			aspectRatio: xinit + ':' + yinit,
				<?php
			}
			if ( ! current_theme_supports( 'custom-header', 'flex-height' ) ) {
				?>
			maxHeight: <?php echo get_theme_support( 'custom-header', 'height' ); ?>,
				<?php
			}
			if ( ! current_theme_supports( 'custom-header', 'flex-width' ) ) {
				?>
			maxWidth: <?php echo get_theme_support( 'custom-header', 'width' ); ?>,
				<?php
			}
			?>
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
</script>
		<?php
	}

	/**
	 * Display first step of custom header image page.
	 *
	 * @since 2.1.0
	 */
	public function step_1() {
		$this->process_default_headers();
		?>

<div class="wrap">
<h1><?php _e( 'Custom Header' ); ?></h1>

		<?php if ( current_user_can( 'customize' ) ) { ?>
<div class="notice notice-info hide-if-no-customize">
	<p>
			<?php
			printf(
				/* translators: %s: URL to header image configuration in Customizer. */
				__( 'You can now manage and live-preview Custom Header in the <a href="%s">Customizer</a>.' ),
				admin_url( 'customize.php?autofocus[control]=header_image' )
			);
			?>
	</p>
</div>
		<?php } ?>

		<?php if ( ! empty( $this->updated ) ) { ?>
<div id="message" class="updated">
	<p>
			<?php
			/* translators: %s: Home URL. */
			printf( __( 'Header updated. <a href="%s">Visit your site</a> to see how it looks.' ), home_url( '/' ) );
			?>
	</p>
</div>
		<?php } ?>

<h2><?php _e( 'Header Image' ); ?></h2>

<table class="form-table" role="presentation">
<tbody>

		<?php if ( get_custom_header() || display_header_text() ) : ?>
<tr>
<th scope="row"><?php _e( 'Preview' ); ?></th>
<td>
			<?php
			if ( $this->admin_image_div_callback ) {
				call_user_func( $this->admin_image_div_callback );
			} else {
				$custom_header = get_custom_header();
				$header_image  = get_header_image();

				if ( $header_image ) {
					$header_image_style = 'background-image:url(' . esc_url( $header_image ) . ');';
				} else {
					$header_image_style = '';
				}

				if ( $custom_header->width ) {
					$header_image_style .= 'max-width:' . $custom_header->width . 'px;';
				}
				if ( $custom_header->height ) {
					$header_image_style .= 'height:' . $custom_header->height . 'px;';
				}
				?>
	<div id="headimg" style="<?php echo $header_image_style; ?>">
				<?php
				if ( display_header_text() ) {
					$style = ' style="color:#' . get_header_textcolor() . ';"';
				} else {
					$style = ' style="display:none;"';
				}
				?>
		<h1><a id="name" class="displaying-header-text" <?php echo $style; ?> onclick="return false;" href="<?php bloginfo( 'url' ); ?>" tabindex="-1"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc" class="displaying-header-text" <?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	</div>
			<?php } ?>
</td>
</tr>
		<?php endif; ?>

		<?php if ( current_user_can( 'upload_files' ) && current_theme_supports( 'custom-header', 'uploads' ) ) : ?>
<tr>
<th scope="row"><?php _e( 'Select Image' ); ?></th>
<td>
	<p><?php _e( 'You can select an image to be shown at the top of your site by uploading from your computer or choosing from your media library. After selecting an image you will be able to crop it.' ); ?><br />
			<?php
			if ( ! current_theme_supports( 'custom-header', 'flex-height' ) && ! current_theme_supports( 'custom-header', 'flex-width' ) ) {
				/* translators: 1: Image width in pixels, 2: Image height in pixels. */
				printf( __( 'Images of exactly <strong>%1$d &times; %2$d pixels</strong> will be used as-is.' ) . '<br />', get_theme_support( 'custom-header', 'width' ), get_theme_support( 'custom-header', 'height' ) );
			} elseif ( current_theme_supports( 'custom-header', 'flex-height' ) ) {
				if ( ! current_theme_supports( 'custom-header', 'flex-width' ) ) {
					printf(
						/* translators: %s: Size in pixels. */
						__( 'Images should be at least %s wide.' ) . ' ',
						sprintf(
							/* translators: %d: Custom header width. */
							'<strong>' . __( '%d pixels' ) . '</strong>',
							get_theme_support( 'custom-header', 'width' )
						)
					);
				}
			} elseif ( current_theme_supports( 'custom-header', 'flex-width' ) ) {
				if ( ! current_theme_supports( 'custom-header', 'flex-height' ) ) {
					printf(
						/* translators: %s: Size in pixels. */
						__( 'Images should be at least %s tall.' ) . ' ',
						sprintf(
							/* translators: %d: Custom header height. */
							'<strong>' . __( '%d pixels' ) . '</strong>',
							get_theme_support( 'custom-header', 'height' )
						)
					);
				}
			}
			if ( current_theme_supports( 'custom-header', 'flex-height' ) || current_theme_supports( 'custom-header', 'flex-width' ) ) {
				if ( current_theme_supports( 'custom-header', 'width' ) ) {
					printf(
						/* translators: %s: Size in pixels. */
						__( 'Suggested width is %s.' ) . ' ',
						sprintf(
							/* translators: %d: Custom header width. */
							'<strong>' . __( '%d pixels' ) . '</strong>',
							get_theme_support( 'custom-header', 'width' )
						)
					);
				}
				if ( current_theme_supports( 'custom-header', 'height' ) ) {
					printf(
						/* translators: %s: Size in pixels. */
						__( 'Suggested height is %s.' ) . ' ',
						sprintf(
							/* translators: %d: Custom header height. */
							'<strong>' . __( '%d pixels' ) . '</strong>',
							get_theme_support( 'custom-header', 'height' )
						)
					);
				}
			}
			?>
	</p>
	<form enctype="multipart/form-data" id="upload-form" class="wp-upload-form" method="post" action="<?php echo esc_url( add_query_arg( 'step', 2 ) ); ?>">
	<p>
		<label for="upload"><?php _e( 'Choose an image from your computer:' ); ?></label><br />
		<input type="file" id="upload" name="import" />
		<input type="hidden" name="action" value="save" />
			<?php wp_nonce_field( 'custom-header-upload', '_wpnonce-custom-header-upload' ); ?>
			<?php submit_button( __( 'Upload' ), '', 'submit', false ); ?>
	</p>
			<?php
				$modal_update_href = esc_url(
					add_query_arg(
						array(
							'page' => 'custom-header',
							'step' => 2,
							'_wpnonce-custom-header-upload' => wp_create_nonce( 'custom-header-upload' ),
						),
						admin_url( 'themes.php' )
					)
				);
			?>
	<p>
		<label for="choose-from-library-link"><?php _e( 'Or choose an image from your media library:' ); ?></label><br />
		<button id="choose-from-library-link" class="button"
			data-update-link="<?php echo esc_attr( $modal_update_href ); ?>"
			data-choose="<?php esc_attr_e( 'Choose a Custom Header' ); ?>"
			data-update="<?php esc_attr_e( 'Set as header' ); ?>"><?php _e( 'Choose Image' ); ?></button>
	</p>
	</form>
</td>
</tr>
		<?php endif; ?>
</tbody>
</table>

<form method="post" action="<?php echo esc_url( add_query_arg( 'step', 1 ) ); ?>">
		<?php submit_button( null, 'screen-reader-text', 'save-header-options', false ); ?>
<table class="form-table" role="presentation">
<tbody>
		<?php if ( get_uploaded_header_images() ) : ?>
<tr>
<th scope="row"><?php _e( 'Uploaded Images' ); ?></th>
<td>
	<p><?php _e( 'You can choose one of your previously uploaded headers, or show a random one.' ); ?></p>
			<?php
			$this->show_header_selector( 'uploaded' );
			?>
</td>
</tr>
			<?php
	endif;
		if ( ! empty( $this->default_headers ) ) :
			?>
<tr>
<th scope="row"><?php _e( 'Default Images' ); ?></th>
<td>
			<?php if ( current_theme_supports( 'custom-header', 'uploads' ) ) : ?>
	<p><?php _e( 'If you don&lsquo;t want to upload your own image, you can use one of these cool headers, or show a random one.' ); ?></p>
	<?php else : ?>
	<p><?php _e( 'You can use one of these cool headers or show a random one on each page.' ); ?></p>
	<?php endif; ?>
			<?php
			$this->show_header_selector( 'default' );
			?>
</td>
</tr>
			<?php
	endif;
		if ( get_header_image() ) :
			?>
<tr>
<th scope="row"><?php _e( 'Remove Image' ); ?></th>
<td>
	<p><?php _e( 'This will remove the header image. You will not be able to restore any customizations.' ); ?></p>
			<?php submit_button( __( 'Remove Header Image' ), '', 'removeheader', false ); ?>
</td>
</tr>
			<?php
	endif;

		$default_image = sprintf( get_theme_support( 'custom-header', 'default-image' ), get_template_directory_uri(), get_stylesheet_directory_uri() );
		if ( $default_image && get_header_image() != $default_image ) :
			?>
<tr>
<th scope="row"><?php _e( 'Reset Image' ); ?></th>
<td>
	<p><?php _e( 'This will restore the original header image. You will not be able to restore any customizations.' ); ?></p>
			<?php submit_button( __( 'Restore Original Header Image' ), '', 'resetheader', false ); ?>
</td>
</tr>
	<?php endif; ?>
</tbody>
</table>

		<?php if ( current_theme_supports( 'custom-header', 'header-text' ) ) : ?>

<h2><?php _e( 'Header Text' ); ?></h2>

<table class="form-table" role="presentation">
<tbody>
<tr>
<th scope="row"><?php _e( 'Header Text' ); ?></th>
<td>
	<p>
	<label><input type="checkbox" name="display-header-text" id="display-header-text"<?php checked( display_header_text() ); ?> /> <?php _e( 'Show header text with your image.' ); ?></label>
	</p>
</td>
</tr>

<tr class="displaying-header-text">
<th scope="row"><?php _e( 'Text Color' ); ?></th>
<td>
	<p>
			<?php
			$default_color = '';
			if ( current_theme_supports( 'custom-header', 'default-text-color' ) ) {
				$default_color = get_theme_support( 'custom-header', 'default-text-color' );
				if ( $default_color && false === strpos( $default_color, '#' ) ) {
					$default_color = '#' . $default_color;
				}
			}

			$default_color_attr = $default_color ? ' data-default-color="' . esc_attr( $default_color ) . '"' : '';

			$header_textcolor = display_header_text() ? get_header_textcolor() : get_theme_support( 'custom-header', 'default-text-color' );
			if ( $header_textcolor && false === strpos( $header_textcolor, '#' ) ) {
				$header_textcolor = '#' . $header_textcolor;
			}

			echo '<input type="text" name="text-color" id="text-color" value="' . esc_attr( $header_textcolor ) . '"' . $default_color_attr . ' />';
			if ( $default_color ) {
				/* translators: %s: Default text color. */
				echo ' <span class="description hide-if-js">' . sprintf( _x( 'Default: %s', 'color' ), esc_html( $default_color ) ) . '</span>';
			}
			?>
	</p>
</td>
</tr>
</tbody>
</table>
			<?php
endif;

		/**
		 * Fires just before the submit button in the custom header options form.
		 *
		 * @since 3.1.0
		 */
		do_action( 'custom_header_options' );

		wp_nonce_field( 'custom-header-options', '_wpnonce-custom-header-options' );
		?>

		<?php submit_button( null, 'primary', 'save-header-options' ); ?>
</form>
</div>

		<?php
	}

	/**
	 * Display second step of custom header image page.
	 *
	 * @since 2.1.0
	 */
	public function step_2() {
		check_admin_referer( 'custom-header-upload', '_wpnonce-custom-header-upload' );
		if ( ! current_theme_supports( 'custom-header', 'uploads' ) ) {
			wp_die(
				'<h1>' . __( 'Something went wrong.' ) . '</h1>' .
				'<p>' . __( 'The current theme does not support uploading a custom header image.' ) . '</p>',
				403
			);
		}

		if ( empty( $_POST ) && isset( $_GET['file'] ) ) {
			$attachment_id = absint( $_GET['file'] );
			$file          = get_attached_file( $attachment_id, true );
			$url           = wp_get_attachment_image_src( $attachment_id, 'full' );
			$url           = $url[0];
		} elseif ( isset( $_POST ) ) {
			$data          = $this->step_2_manage_upload();
			$attachment_id = $data['attachment_id'];
			$file          = $data['file'];
			$url           = $data['url'];
		}

		if ( file_exists( $file ) ) {
			list( $width, $height, $type, $attr ) = @getimagesize( $file );
		} else {
			$data   = wp_get_attachment_metadata( $attachment_id );
			$height = isset( $data['height'] ) ? $data['height'] : 0;
			$width  = isset( $data['width'] ) ? $data['width'] : 0;
			unset( $data );
		}

		$max_width = 0;
		// For flex, limit size of image displayed to 1500px unless theme says otherwise.
		if ( current_theme_supports( 'custom-header', 'flex-width' ) ) {
			$max_width = 1500;
		}

		if ( current_theme_supports( 'custom-header', 'max-width' ) ) {
			$max_width = max( $max_width, get_theme_support( 'custom-header', 'max-width' ) );
		}
		$max_width = max( $max_width, get_theme_support( 'custom-header', 'width' ) );

		// If flexible height isn't supported and the image is the exact right size.
		if ( ! current_theme_supports( 'custom-header', 'flex-height' ) && ! current_theme_supports( 'custom-header', 'flex-width' )
			&& get_theme_support( 'custom-header', 'width' ) == $width && get_theme_support( 'custom-header', 'height' ) == $height ) {
			// Add the metadata.
			if ( file_exists( $file ) ) {
				wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
			}

			$this->set_header_image( compact( 'url', 'attachment_id', 'width', 'height' ) );

			/**
			 * Fires after the header image is set or an error is returned.
			 *
			 * @since 2.1.0
			 *
			 * @param string $file          Path to the file.
			 * @param int    $attachment_id Attachment ID.
			 */
			do_action( 'wp_create_file_in_uploads', $file, $attachment_id ); // For replication.

			return $this->finished();
		} elseif ( $width > $max_width ) {
			$oitar = $width / $max_width;
			$image = wp_crop_image( $attachment_id, 0, 0, $width, $height, $max_width, $height / $oitar, false, str_replace( wp_basename( $file ), 'midsize-' . wp_basename( $file ), $file ) );
			if ( ! $image || is_wp_error( $image ) ) {
				wp_die( __( 'Image could not be processed. Please go back and try again.' ), __( 'Image Processing Error' ) );
			}

			/** This filter is documented in wp-admin/includes/class-custom-image-header.php */
			$image = apply_filters( 'wp_create_file_in_uploads', $image, $attachment_id ); // For replication.

			$url    = str_replace( wp_basename( $url ), wp_basename( $image ), $url );
			$width  = $width / $oitar;
			$height = $height / $oitar;
		} else {
			$oitar = 1;
		}
		?>

<div class="wrap">
<h1><?php _e( 'Crop Header Image' ); ?></h1>

<form method="post" action="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>">
	<p class="hide-if-no-js"><?php _e( 'Choose the part of the image you want to use as your header.' ); ?></p>
	<p class="hide-if-js"><strong><?php _e( 'You need JavaScript to choose a part of the image.' ); ?></strong></p>

	<div id="crop_image" style="position: relative">
		<img src="<?php echo esc_url( $url ); ?>" id="upload" width="<?php echo $width; ?>" height="<?php echo $height; ?>" alt="" />
	</div>

	<input type="hidden" name="x1" id="x1" value="0"/>
	<input type="hidden" name="y1" id="y1" value="0"/>
	<input type="hidden" name="width" id="width" value="<?php echo esc_attr( $width ); ?>"/>
	<input type="hidden" name="height" id="height" value="<?php echo esc_attr( $height ); ?>"/>
	<input type="hidden" name="attachment_id" id="attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>" />
	<input type="hidden" name="oitar" id="oitar" value="<?php echo esc_attr( $oitar ); ?>" />
		<?php if ( empty( $_POST ) && isset( $_GET['file'] ) ) { ?>
	<input type="hidden" name="create-new-attachment" value="true" />
	<?php } ?>
		<?php wp_nonce_field( 'custom-header-crop-image' ); ?>

	<p class="submit">
		<?php submit_button( __( 'Crop and Publish' ), 'primary', 'submit', false ); ?>
		<?php
		if ( isset( $oitar ) && 1 == $oitar && ( current_theme_supports( 'custom-header', 'flex-height' ) || current_theme_supports( 'custom-header', 'flex-width' ) ) ) {
			submit_button( __( 'Skip Cropping, Publish Image as Is' ), '', 'skip-cropping', false );
		}
		?>
	</p>
</form>
</div>
		<?php
	}


	/**
	 * Upload the file to be cropped in the second step.
	 *
	 * @since 3.4.0
	 */
	public function step_2_manage_upload() {
		$overrides = array( 'test_form' => false );

		$uploaded_file = $_FILES['import'];
		$wp_filetype   = wp_check_filetype_and_ext( $uploaded_file['tmp_name'], $uploaded_file['name'] );
		if ( ! wp_match_mime_types( 'image', $wp_filetype['type'] ) ) {
			wp_die( __( 'The uploaded file is not a valid image. Please try again.' ) );
		}

		$file = wp_handle_upload( $uploaded_file, $overrides );

		if ( isset( $file['error'] ) ) {
			wp_die( $file['error'], __( 'Image Upload Error' ) );
		}

		$url      = $file['url'];
		$type     = $file['type'];
		$file     = $file['file'];
		$filename = wp_basename( $file );

		// Construct the object array.
		$object = array(
			'post_title'     => $filename,
			'post_content'   => $url,
			'post_mime_type' => $type,
			'guid'           => $url,
			'context'        => 'custom-header',
		);

		// Save the data.
		$attachment_id = wp_insert_attachment( $object, $file );
		return compact( 'attachment_id', 'file', 'filename', 'url', 'type' );
	}

	/**
	 * Display third step of custom header image page.
	 *
	 * @since 2.1.0
	 * @since 4.4.0 Switched to using wp_get_attachment_url() instead of the guid
	 *              for retrieving the header image URL.
	 */
	public function step_3() {
		check_admin_referer( 'custom-header-crop-image' );

		if ( ! current_theme_supports( 'custom-header', 'uploads' ) ) {
			wp_die(
				'<h1>' . __( 'Something went wrong.' ) . '</h1>' .
				'<p>' . __( 'The current theme does not support uploading a custom header image.' ) . '</p>',
				403
			);
		}

		if ( ! empty( $_POST['skip-cropping'] ) && ! ( current_theme_supports( 'custom-header', 'flex-height' ) || current_theme_supports( 'custom-header', 'flex-width' ) ) ) {
			wp_die(
				'<h1>' . __( 'Something went wrong.' ) . '</h1>' .
				'<p>' . __( 'The current theme does not support a flexible sized header image.' ) . '</p>',
				403
			);
		}

		if ( $_POST['oitar'] > 1 ) {
			$_POST['x1']     = $_POST['x1'] * $_POST['oitar'];
			$_POST['y1']     = $_POST['y1'] * $_POST['oitar'];
			$_POST['width']  = $_POST['width'] * $_POST['oitar'];
			$_POST['height'] = $_POST['height'] * $_POST['oitar'];
		}

		$attachment_id = absint( $_POST['attachment_id'] );
		$original      = get_attached_file( $attachment_id );

		$dimensions = $this->get_header_dimensions(
			array(
				'height' => $_POST['height'],
				'width'  => $_POST['width'],
			)
		);
		$height     = $dimensions['dst_height'];
		$width      = $dimensions['dst_width'];

		if ( empty( $_POST['skip-cropping'] ) ) {
			$cropped = wp_crop_image( $attachment_id, (int) $_POST['x1'], (int) $_POST['y1'], (int) $_POST['width'], (int) $_POST['height'], $width, $height );
		} elseif ( ! empty( $_POST['create-new-attachment'] ) ) {
			$cropped = _copy_image_file( $attachment_id );
		} else {
			$cropped = get_attached_file( $attachment_id );
		}

		if ( ! $cropped || is_wp_error( $cropped ) ) {
			wp_die( __( 'Image could not be processed. Please go back and try again.' ), __( 'Image Processing Error' ) );
		}

		/** This filter is documented in wp-admin/includes/class-custom-image-header.php */
		$cropped = apply_filters( 'wp_create_file_in_uploads', $cropped, $attachment_id ); // For replication.

		$object = $this->create_attachment_object( $cropped, $attachment_id );

		if ( ! empty( $_POST['create-new-attachment'] ) ) {
			unset( $object['ID'] );
		}

		// Update the attachment.
		$attachment_id = $this->insert_attachment( $object, $cropped );

		$url = wp_get_attachment_url( $attachment_id );
		$this->set_header_image( compact( 'url', 'attachment_id', 'width', 'height' ) );

		// Cleanup.
		$medium = str_replace( wp_basename( $original ), 'midsize-' . wp_basename( $original ), $original );
		if ( file_exists( $medium ) ) {
			wp_delete_file( $medium );
		}

		if ( empty( $_POST['create-new-attachment'] ) && empty( $_POST['skip-cropping'] ) ) {
			wp_delete_file( $original );
		}

		return $this->finished();
	}

	/**
	 * Display last step of custom header image page.
	 *
	 * @since 2.1.0
	 */
	public function finished() {
		$this->updated = true;
		$this->step_1();
	}

	/**
	 * Display the page based on the current step.
	 *
	 * @since 2.1.0
	 */
	public function admin_page() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( __( 'Sorry, you are not allowed to customize headers.' ) );
		}
		$step = $this->step();
		if ( 2 == $step ) {
			$this->step_2();
		} elseif ( 3 == $step ) {
			$this->step_3();
		} else {
			$this->step_1();
		}
	}

	/**
	 * Unused since 3.5.0.
	 *
	 * @since 3.4.0
	 *
	 * @param array $form_fields
	 * @return array $form_fields
	 */
	public function attachment_fields_to_edit( $form_fields ) {
		return $form_fields;
	}

	/**
	 * Unused since 3.5.0.
	 *
	 * @since 3.4.0
	 *
	 * @param array $tabs
	 * @return array $tabs
	 */
	public function filter_upload_tabs( $tabs ) {
		return $tabs;
	}

	/**
	 * Choose a header image, selected from existing uploaded and default headers,
	 * or provide an array of uploaded header data (either new, or from media library).
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $choice Which header image to select. Allows for values of 'random-default-image',
	 *  for randomly cycling among the default images; 'random-uploaded-image', for randomly cycling
	 *  among the uploaded images; the key of a default image registered for that theme; and
	 *  the key of an image uploaded for that theme (the attachment ID of the image).
	 *  Or an array of arguments: attachment_id, url, width, height. All are required.
	 */
	final public function set_header_image( $choice ) {
		if ( is_array( $choice ) || is_object( $choice ) ) {
			$choice = (array) $choice;
			if ( ! isset( $choice['attachment_id'] ) || ! isset( $choice['url'] ) ) {
				return;
			}

			$choice['url'] = esc_url_raw( $choice['url'] );

			$header_image_data = (object) array(
				'attachment_id' => $choice['attachment_id'],
				'url'           => $choice['url'],
				'thumbnail_url' => $choice['url'],
				'height'        => $choice['height'],
				'width'         => $choice['width'],
			);

			update_post_meta( $choice['attachment_id'], '_wp_attachment_is_custom_header', get_stylesheet() );
			set_theme_mod( 'header_image', $choice['url'] );
			set_theme_mod( 'header_image_data', $header_image_data );
			return;
		}

		if ( in_array( $choice, array( 'remove-header', 'random-default-image', 'random-uploaded-image' ), true ) ) {
			set_theme_mod( 'header_image', $choice );
			remove_theme_mod( 'header_image_data' );
			return;
		}

		$uploaded = get_uploaded_header_images();
		if ( $uploaded && isset( $uploaded[ $choice ] ) ) {
			$header_image_data = $uploaded[ $choice ];

		} else {
			$this->process_default_headers();
			if ( isset( $this->default_headers[ $choice ] ) ) {
				$header_image_data = $this->default_headers[ $choice ];
			} else {
				return;
			}
		}

		set_theme_mod( 'header_image', esc_url_raw( $header_image_data['url'] ) );
		set_theme_mod( 'header_image_data', $header_image_data );
	}

	/**
	 * Remove a header image.
	 *
	 * @since 3.4.0
	 */
	final public function remove_header_image() {
		$this->set_header_image( 'remove-header' );
	}

	/**
	 * Reset a header image to the default image for the theme.
	 *
	 * This method does not do anything if the theme does not have a default header image.
	 *
	 * @since 3.4.0
	 */
	final public function reset_header_image() {
		$this->process_default_headers();
		$default = get_theme_support( 'custom-header', 'default-image' );

		if ( ! $default ) {
			$this->remove_header_image();
			return;
		}
		$default = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );

		$default_data = array();
		foreach ( $this->default_headers as $header => $details ) {
			if ( $details['url'] == $default ) {
				$default_data = $details;
				break;
			}
		}

		set_theme_mod( 'header_image', $default );
		set_theme_mod( 'header_image_data', (object) $default_data );
	}

	/**
	 * Calculate width and height based on what the currently selected theme supports.
	 *
	 * @since 3.9.0
	 *
	 * @param array $dimensions
	 * @return array dst_height and dst_width of header image.
	 */
	final public function get_header_dimensions( $dimensions ) {
		$max_width       = 0;
		$width           = absint( $dimensions['width'] );
		$height          = absint( $dimensions['height'] );
		$theme_height    = get_theme_support( 'custom-header', 'height' );
		$theme_width     = get_theme_support( 'custom-header', 'width' );
		$has_flex_width  = current_theme_supports( 'custom-header', 'flex-width' );
		$has_flex_height = current_theme_supports( 'custom-header', 'flex-height' );
		$has_max_width   = current_theme_supports( 'custom-header', 'max-width' );
		$dst             = array(
			'dst_height' => null,
			'dst_width'  => null,
		);

		// For flex, limit size of image displayed to 1500px unless theme says otherwise.
		if ( $has_flex_width ) {
			$max_width = 1500;
		}

		if ( $has_max_width ) {
			$max_width = max( $max_width, get_theme_support( 'custom-header', 'max-width' ) );
		}
		$max_width = max( $max_width, $theme_width );

		if ( $has_flex_height && ( ! $has_flex_width || $width > $max_width ) ) {
			$dst['dst_height'] = absint( $height * ( $max_width / $width ) );
		} elseif ( $has_flex_height && $has_flex_width ) {
			$dst['dst_height'] = $height;
		} else {
			$dst['dst_height'] = $theme_height;
		}

		if ( $has_flex_width && ( ! $has_flex_height || $width > $max_width ) ) {
			$dst['dst_width'] = absint( $width * ( $max_width / $width ) );
		} elseif ( $has_flex_width && $has_flex_height ) {
			$dst['dst_width'] = $width;
		} else {
			$dst['dst_width'] = $theme_width;
		}

		return $dst;
	}

	/**
	 * Create an attachment 'object'.
	 *
	 * @since 3.9.0
	 *
	 * @param string $cropped              Cropped image URL.
	 * @param int    $parent_attachment_id Attachment ID of parent image.
	 * @return array Attachment object.
	 */
	final public function create_attachment_object( $cropped, $parent_attachment_id ) {
		$parent     = get_post( $parent_attachment_id );
		$parent_url = wp_get_attachment_url( $parent->ID );
		$url        = str_replace( wp_basename( $parent_url ), wp_basename( $cropped ), $parent_url );

		$size       = @getimagesize( $cropped );
		$image_type = ( $size ) ? $size['mime'] : 'image/jpeg';

		$object = array(
			'ID'             => $parent_attachment_id,
			'post_title'     => wp_basename( $cropped ),
			'post_mime_type' => $image_type,
			'guid'           => $url,
			'context'        => 'custom-header',
			'post_parent'    => $parent_attachment_id,
		);

		return $object;
	}

	/**
	 * Insert an attachment and its metadata.
	 *
	 * @since 3.9.0
	 *
	 * @param array  $object  Attachment object.
	 * @param string $cropped File path to cropped image.
	 * @return int Attachment ID.
	 */
	final public function insert_attachment( $object, $cropped ) {
		$parent_id = isset( $object['post_parent'] ) ? $object['post_parent'] : null;
		unset( $object['post_parent'] );

		$attachment_id = wp_insert_attachment( $object, $cropped );
		$metadata      = wp_generate_attachment_metadata( $attachment_id, $cropped );

		// If this is a crop, save the original attachment ID as metadata.
		if ( $parent_id ) {
			$metadata['attachment_parent'] = $parent_id;
		}

		/**
		 * Filters the header image attachment metadata.
		 *
		 * @since 3.9.0
		 *
		 * @see wp_generate_attachment_metadata()
		 *
		 * @param array $metadata Attachment metadata.
		 */
		$metadata = apply_filters( 'wp_header_image_attachment_metadata', $metadata );

		wp_update_attachment_metadata( $attachment_id, $metadata );

		return $attachment_id;
	}

	/**
	 * Gets attachment uploaded by Media Manager, crops it, then saves it as a
	 * new object. Returns JSON-encoded object details.
	 *
	 * @since 3.9.0
	 */
	public function ajax_header_crop() {
		check_ajax_referer( 'image_editor-' . $_POST['id'], 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error();
		}

		if ( ! current_theme_supports( 'custom-header', 'uploads' ) ) {
			wp_send_json_error();
		}

		$crop_details = $_POST['cropDetails'];

		$dimensions = $this->get_header_dimensions(
			array(
				'height' => $crop_details['height'],
				'width'  => $crop_details['width'],
			)
		);

		$attachment_id = absint( $_POST['id'] );

		$cropped = wp_crop_image(
			$attachment_id,
			(int) $crop_details['x1'],
			(int) $crop_details['y1'],
			(int) $crop_details['width'],
			(int) $crop_details['height'],
			(int) $dimensions['dst_width'],
			(int) $dimensions['dst_height']
		);

		if ( ! $cropped || is_wp_error( $cropped ) ) {
			wp_send_json_error( array( 'message' => __( 'Image could not be processed. Please go back and try again.' ) ) );
		}

		/** This filter is documented in wp-admin/includes/class-custom-image-header.php */
		$cropped = apply_filters( 'wp_create_file_in_uploads', $cropped, $attachment_id ); // For replication.

		$object = $this->create_attachment_object( $cropped, $attachment_id );

		$previous = $this->get_previous_crop( $object );

		if ( $previous ) {
			$object['ID'] = $previous;
		} else {
			unset( $object['ID'] );
		}

		$new_attachment_id = $this->insert_attachment( $object, $cropped );

		$object['attachment_id'] = $new_attachment_id;
		$object['url']           = wp_get_attachment_url( $new_attachment_id );

		$object['width']  = $dimensions['dst_width'];
		$object['height'] = $dimensions['dst_height'];

		wp_send_json_success( $object );
	}

	/**
	 * Given an attachment ID for a header image, updates its "last used"
	 * timestamp to now.
	 *
	 * Triggered when the user tries adds a new header image from the
	 * Media Manager, even if s/he doesn't save that change.
	 *
	 * @since 3.9.0
	 */
	public function ajax_header_add() {
		check_ajax_referer( 'header-add', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error();
		}

		$attachment_id = absint( $_POST['attachment_id'] );
		if ( $attachment_id < 1 ) {
			wp_send_json_error();
		}

		$key = '_wp_attachment_custom_header_last_used_' . get_stylesheet();
		update_post_meta( $attachment_id, $key, time() );
		update_post_meta( $attachment_id, '_wp_attachment_is_custom_header', get_stylesheet() );

		wp_send_json_success();
	}

	/**
	 * Given an attachment ID for a header image, unsets it as a user-uploaded
	 * header image for the current theme.
	 *
	 * Triggered when the user clicks the overlay "X" button next to each image
	 * choice in the Customizer's Header tool.
	 *
	 * @since 3.9.0
	 */
	public function ajax_header_remove() {
		check_ajax_referer( 'header-remove', 'nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error();
		}

		$attachment_id = absint( $_POST['attachment_id'] );
		if ( $attachment_id < 1 ) {
			wp_send_json_error();
		}

		$key = '_wp_attachment_custom_header_last_used_' . get_stylesheet();
		delete_post_meta( $attachment_id, $key );
		delete_post_meta( $attachment_id, '_wp_attachment_is_custom_header', get_stylesheet() );

		wp_send_json_success();
	}

	/**
	 * Updates the last-used postmeta on a header image attachment after saving a new header image via the Customizer.
	 *
	 * @since 3.9.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customize manager.
	 */
	public function customize_set_last_used( $wp_customize ) {

		$header_image_data_setting = $wp_customize->get_setting( 'header_image_data' );
		if ( ! $header_image_data_setting ) {
			return;
		}
		$data = $header_image_data_setting->post_value();

		if ( ! isset( $data['attachment_id'] ) ) {
			return;
		}

		$attachment_id = $data['attachment_id'];
		$key           = '_wp_attachment_custom_header_last_used_' . get_stylesheet();
		update_post_meta( $attachment_id, $key, time() );
	}

	/**
	 * Gets the details of default header images if defined.
	 *
	 * @since 3.9.0
	 *
	 * @return array Default header images.
	 */
	public function get_default_header_images() {
		$this->process_default_headers();

		// Get the default image if there is one.
		$default = get_theme_support( 'custom-header', 'default-image' );

		if ( ! $default ) { // If not, easy peasy.
			return $this->default_headers;
		}

		$default             = sprintf( $default, get_template_directory_uri(), get_stylesheet_directory_uri() );
		$already_has_default = false;

		foreach ( $this->default_headers as $k => $h ) {
			if ( $h['url'] === $default ) {
				$already_has_default = true;
				break;
			}
		}

		if ( $already_has_default ) {
			return $this->default_headers;
		}

		// If the one true image isn't included in the default set, prepend it.
		$header_images            = array();
		$header_images['default'] = array(
			'url'           => $default,
			'thumbnail_url' => $default,
			'description'   => 'Default',
		);

		// The rest of the set comes after.
		return array_merge( $header_images, $this->default_headers );
	}

	/**
	 * Gets the previously uploaded header images.
	 *
	 * @since 3.9.0
	 *
	 * @return array Uploaded header images.
	 */
	public function get_uploaded_header_images() {
		$header_images = get_uploaded_header_images();
		$timestamp_key = '_wp_attachment_custom_header_last_used_' . get_stylesheet();
		$alt_text_key  = '_wp_attachment_image_alt';

		foreach ( $header_images as &$header_image ) {
			$header_meta               = get_post_meta( $header_image['attachment_id'] );
			$header_image['timestamp'] = isset( $header_meta[ $timestamp_key ] ) ? $header_meta[ $timestamp_key ] : '';
			$header_image['alt_text']  = isset( $header_meta[ $alt_text_key ] ) ? $header_meta[ $alt_text_key ] : '';
		}

		return $header_images;
	}

	/**
	 * Get the ID of a previous crop from the same base image.
	 *
	 * @since 4.9.0
	 *
	 * @param array $object A crop attachment object.
	 * @return int|false An attachment ID if one exists. False if none.
	 */
	public function get_previous_crop( $object ) {
		$header_images = $this->get_uploaded_header_images();

		// Bail early if there are no header images.
		if ( empty( $header_images ) ) {
			return false;
		}

		$previous = false;

		foreach ( $header_images as $image ) {
			if ( $image['attachment_parent'] === $object['post_parent'] ) {
				$previous = $image['attachment_id'];
				break;
			}
		}

		return $previous;
	}
}
