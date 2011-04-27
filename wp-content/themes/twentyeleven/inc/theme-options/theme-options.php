<?php
/**
 * Twenty Eleven Theme Options
 *
 * @package WordPress
 * @subpackage Twenty Eleven
 */

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Add theme options page styles and scripts
 */
wp_register_style( 'twentyeleven-theme-options', get_template_directory_uri() . '/inc/theme-options/theme-options.css', '', '0.1' );
wp_register_script( 'twentyeleven-theme-options', get_template_directory_uri() . '/inc/theme-options/theme-options.js' );
if ( isset( $_GET['page'] ) && $_GET['page'] == 'theme_options' ) {
	wp_enqueue_style( 'twentyeleven-theme-options' );
	wp_enqueue_script( 'twentyeleven-theme-options' );
	wp_enqueue_script( 'farbtastic' );
	wp_enqueue_style( 'farbtastic' );
}

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'twentyeleven_options', 'twentyeleven_theme_options', 'twentyeleven_theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page( __( 'Theme Options', 'twentyeleven' ), __( 'Theme Options', 'twentyeleven' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

/**
 * Return array for our color schemes
 */
function twentyeleven_color_schemes() {
	$color_scheme_options = array(
		'light' => array(
			'value' => 'light',
			'label' => __( 'Light', 'twentyeleven' )
		),
		'dark' => array(
			'value' => 'dark',
			'label' => __( 'Dark', 'twentyeleven' )
		),
	);

	return $color_scheme_options;
}

/**
 * Return array for our layout options
 */
function twentyeleven_layouts() {
	$layout_options = array(
		'content-sidebar' => array(
			'value' => 'content-sidebar',
			'label' => __( 'Content on left', 'twentyeleven' ),
		),
		'sidebar-content' => array(
			'value' => 'sidebar-content',
			'label' => __( 'Content on right', 'twentyeleven' )
		),
		'content' => array(
			'value' => 'content',
			'label' => __( 'One-column, no Sidebar', 'twentyeleven' )
		),
	);

	return $layout_options;
}

/**
 *  Return the default Twenty Eleven theme option values
 */
function twentyeleven_get_default_theme_options() {
	return array(
		'color_scheme' => 'light',
		'link_color' => '#1b8be0',
		'theme_layout' => 'content-sidebar',
	);
}

/**
 *  Return the current Twenty Eleven theme options, with default values as fallback
 */
function twentyeleven_get_theme_options() {
	$defaults = twentyeleven_get_default_theme_options();
	$options = get_option( 'twentyeleven_theme_options', $defaults );

	return $options;
}

/**
 * Create the options page
 */
function theme_options_do_page() {
	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;

	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>" . get_current_theme() . __( ' Theme Options', 'twentyeleven' ) . "</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'twentyeleven' ); ?></strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'twentyeleven_options' ); ?>
			<?php $options = twentyeleven_get_theme_options(); ?>

			<table class="form-table">

				<?php
				/**
				 * Color Scheme Options
				 */
				?>
				<tr valign="top" class="image-radio-option"><th scope="row"><?php _e( 'Color Scheme', 'twentyeleven' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Color Scheme', 'twentyeleven' ); ?></span></legend>
						<?php
							if ( ! isset( $checked ) )
								$checked = '';
							foreach ( twentyeleven_color_schemes() as $option ) {
								$radio_setting = $options['color_scheme'];

								if ( '' != $radio_setting ) {
									if ( $options['color_scheme'] == $option['value'] ) {
										$checked = "checked=\"checked\"";
									} else {
										$checked = '';
									}
								}
								?>
								<div class="layout">
								<label class="description">
									<input type="radio" name="twentyeleven_theme_options[color_scheme]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> />
									<span>
										<img src="<?php echo get_template_directory_uri(); ?>/inc/theme-options/images/<?php echo $option['value']; ?>.png"/>
										<?php echo $option['label']; ?>
									</span>
								</label>
								</div>
								<?php
							}
						?>
						</fieldset>
					</td>
				</tr>

				<?php
				/**
				 * Link Color Options
				 */
				?>
				<tr valign="top"><th scope="row"><?php _e( 'Link Color', 'twentyeleven' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Link Color', 'twentyeleven' ); ?></span></legend>
							<input type="text" name="twentyeleven_theme_options[link_color]" id="link-color" value="<?php esc_attr_e( $options['link_color'] ); ?>" />
							<a class="hide-if-no-js" href="#" id="pickcolor"><?php _e( 'Select a Color', 'twentyeleven' ); ?></a>
							<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
							<br />
							<small class="description"><?php printf( __( 'Default color: %s', 'twentyeleven' ), '#1b8be0' ); ?></small>
						</fieldset>
					</td>
				</tr>

				<?php
				/**
				 * Layout Options
				 */
				?>
				<tr valign="top" class="image-radio-option"><th scope="row"><?php _e( 'Layout', 'twentyeleven' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Color Scheme', 'twentyeleven' ); ?></span></legend>
						<?php
							if ( ! isset( $checked ) )
								$checked = '';
							foreach ( twentyeleven_layouts() as $option ) {
								$radio_setting = $options['theme_layout'];

								if ( '' != $radio_setting ) {
									if ( $options['theme_layout'] == $option['value'] ) {
										$checked = "checked=\"checked\"";
									} else {
										$checked = '';
									}
								}
								?>
								<div class="layout">
								<label class="description">
									<input type="radio" name="twentyeleven_theme_options[theme_layout]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> />
									<span>
										<img src="<?php echo get_template_directory_uri(); ?>/inc/theme-options/images/<?php echo $option['value']; ?>.png"/>
										<?php echo $option['label']; ?>
									</span>
								</label>
								</div>
								<?php
							}
						?>
						</fieldset>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Options', 'twentyeleven' ); ?>" />
			</p>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 *
 * todo set up Reset Options action
 */
function twentyeleven_theme_options_validate( $input ) {
	$defaults = twentyeleven_get_default_theme_options();

	// Color scheme must be in our array of color scheme options
	if ( ! isset( $input['color_scheme'] ) || ! array_key_exists( $input['color_scheme'], twentyeleven_color_schemes() ) )
		$input['color_scheme'] = $defaults['color_scheme'];

	// Link color must be 3 or 6 hexadecimal characters
	if ( ! isset( $input[ 'link_color' ] ) ) {
		$input['link_color'] = $defaults['link_color'];
	} else {
		if ( preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $input['link_color'] ) ) {
			$link_color = $input['link_color'];
			// If color value doesn't have a preceding hash, add it
			if ( false === strpos( $link_color, '#' ) )
				$link_color = '#' . $link_color;
		} else {
			$input['link_color'] = $defaults['link_color'];
		}
	}

	// Theme layout must be in our array of theme layout options
	if ( ! isset( $input['theme_layout'] ) || ! array_key_exists( $input['theme_layout'], twentyeleven_layouts() ) )
		$input['theme_layout'] = $defaults['theme_layout'];

	return $input;
}

/**
 *  Returns the current Twenty Eleven color scheme as selected in the theme options
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_current_color_scheme() {
	$options = twentyeleven_get_theme_options();
	return $options['color_scheme'];
}

/**
 * Register our color schemes and add them to the queue
 */
function twentyeleven_color_registrar() {
	$color_scheme = twentyeleven_current_color_scheme();

	if ( 'dark' == $color_scheme ) {
		wp_register_style( 'dark', get_template_directory_uri() . '/colors/dark.css', null, null );
		wp_enqueue_style( 'dark' );
	}
}
if ( ! is_admin() )
	add_action( 'wp_print_styles', 'twentyeleven_color_registrar' );

/**
 *  Returns the current Twenty Eleven layout as selected in the theme options
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_current_layout() {
	$options = twentyeleven_get_theme_options();
	$current_layout = $options['theme_layout'];

	$two_columns = array( 'content-sidebar', 'sidebar-content' );

	if ( in_array( $current_layout, $two_columns ) )
		return 'two-column ' . $current_layout;
	else
		return 'one-column ' . $current_layout;
}

/**
 * Add an internal style block for the link color to wp_head()
 */
function twentyeleven_link_color() {
	$options = twentyeleven_get_theme_options();
	$current_link_color = $options['link_color'];

	// Is the link color just the default color?
	if ( '#1b8be0' == $current_link_color ) :
		return; // we don't need to do anything then
	else :
		?>
			<style>
				/* Link color */
				a,
				.entry-title a:hover {
				    color: <?php echo $current_link_color; ?>;
				}
			</style>
		<?php
	endif;
}
add_action( 'wp_head', 'twentyeleven_link_color' );

/**
 *  Adds twentyeleven_current_layout() to the array of body classes
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_layout_class( $classes ) {
	$classes[] = twentyeleven_current_layout();

	return $classes;
}
add_filter( 'body_class', 'twentyeleven_layout_class' );