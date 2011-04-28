<?php
/**
 * Twenty Eleven Theme Options
 *
 * @package WordPress
 * @subpackage Twenty Eleven
 * @since Twenty Eleven 1.0
 */

/**
 * Properly enqueue styles and scripts for our theme options page.
 *
 * This function is attached to the admin_enqueue_scripts action hook.
 *
 * @since Twenty Eleven 1.0
 *
 * @param string $hook_suffix The action passes the current page to the function. We don't
 * 	do anything if we're not on our theme options page.
 */
function twentyeleven_admin_enqueue_scripts( $hook_suffix ) {
	if ( $hook_suffix != 'appearance_page_theme_options' )
		return;

	wp_enqueue_style(  'twentyeleven-theme-options', get_template_directory_uri() . '/inc/theme-options/theme-options.css', '', '0.1' );
	wp_enqueue_script( 'twentyeleven-theme-options', get_template_directory_uri() . '/inc/theme-options/theme-options.js' );
	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );
}
add_action( 'admin_enqueue_scripts', 'twentyeleven_admin_enqueue_scripts' );

/**
 * Register the form setting for our twentyeleven_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * This call to register_setting() registers a validation callback, twentyeleven_theme_options_validate(),
 * which is used when the option is saved, to ensure that our option values are complete, properly
 * formatted, and safe.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_theme_options_init() {
	register_setting( 'twentyeleven_options', 'twentyeleven_theme_options', 'twentyeleven_theme_options_validate' );
}
add_action( 'admin_init', 'twentyeleven_theme_options_init' );

/**
 * Add our theme options page to the admin menu.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_theme_options_add_page() {
	add_theme_page(
		__( 'Theme Options', 'twentyeleven' ), // Name of page
		__( 'Theme Options', 'twentyeleven' ), // Label in menu
		'edit_theme_options',                  // Capability required
		'theme_options',                       // Menu slug, used to uniquely identify the page
		'theme_options_render_page'            // Function that renders the options page
	);
}
add_action( 'admin_menu', 'twentyeleven_theme_options_add_page' );

/**
 * Returns an array of color schemes registered for Twenty Eleven.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_color_schemes() {
	$color_scheme_options = array(
		'light' => array(
			'value' => 'light',
			'label' => __( 'Light', 'twentyeleven' ),
			'thumbnail' => get_template_directory_uri() . '/inc/theme-options/images/light.png',
		),
		'dark' => array(
			'value' => 'dark',
			'label' => __( 'Dark', 'twentyeleven' ),
			'thumbnail' => get_template_directory_uri() . '/inc/theme-options/images/dark.png',
		),
	);

	return apply_filters( 'twentyeleven_color_schemes', $color_scheme_options );
}

/**
 * Returns an array of layout options registered for Twenty Eleven.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_layouts() {
	$layout_options = array(
		'content-sidebar' => array(
			'value' => 'content-sidebar',
			'label' => __( 'Content on left', 'twentyeleven' ),
			'thumbnail' => get_template_directory_uri() . '/inc/theme-options/images/content-sidebar.png',
		),
		'sidebar-content' => array(
			'value' => 'sidebar-content',
			'label' => __( 'Content on right', 'twentyeleven' ),
			'thumbnail' => get_template_directory_uri() . '/inc/theme-options/images/sidebar-content.png',
		),
		'content' => array(
			'value' => 'content',
			'label' => __( 'One-column, no Sidebar', 'twentyeleven' ),
			'thumbnail' => get_template_directory_uri() . '/inc/theme-options/images/content.png',
		),
	);

	return apply_filters( 'twentyeleven_layouts', $layout_options );
}

/**
 * Returns the default options for Twenty Eleven.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_get_default_theme_options() {
	$default_theme_options = array(
		'color_scheme' => 'light',
		'link_color' => '#1b8be0',
		'theme_layout' => 'content-sidebar',
	);

	return apply_filters( 'twentyeleven_default_theme_options', $default_theme_options );
}

/**
 * Returns the options array for Twenty Eleven.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_get_theme_options() {
	$defaults = twentyeleven_get_default_theme_options();
	$options = get_option( 'twentyeleven_theme_options', $defaults );

	return $options;
}

/**
 * Returns the options array for Twenty Eleven.
 *
 * @since Twenty Eleven 1.0
 */
function theme_options_render_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( '%s Theme Options', 'twentyeleven' ), get_current_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'twentyeleven_options' );
				$options = twentyeleven_get_theme_options();
				$default_options = twentyeleven_get_default_theme_options();
			?>

			<table class="form-table">

				<tr valign="top" class="image-radio-option"><th scope="row"><?php _e( 'Color Scheme', 'twentyeleven' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Color Scheme', 'twentyeleven' ); ?></span></legend>
						<?php
							foreach ( twentyeleven_color_schemes() as $color ) {
								?>
								<div class="layout">
								<label class="description">
									<input type="radio" name="twentyeleven_theme_options[color_scheme]" value="<?php echo esc_attr( $color['value'] ); ?>" <?php checked( $options['color_scheme'], $color['value'] ); ?> />
									<span>
										<img src="<?php echo esc_url( $color['thumbnail'] ); ?>"/>
										<?php echo $color['label']; ?>
									</span>
								</label>
								</div>
								<?php
							}
						?>
						</fieldset>
					</td>
				</tr>

				<tr valign="top"><th scope="row"><?php _e( 'Link Color', 'twentyeleven' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Link Color', 'twentyeleven' ); ?></span></legend>
							<input type="text" name="twentyeleven_theme_options[link_color]" id="link-color" value="<?php echo esc_attr( $options['link_color'] ); ?>" />
							<a class="hide-if-no-js" href="#" id="pickcolor"><?php _e( 'Select a Color', 'twentyeleven' ); ?></a>
							<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
							<br />
							<small class="description"><?php printf( __( 'Default color: %s', 'twentyeleven' ), $default_options['link_color'] ); ?></small>
						</fieldset>
					</td>
				</tr>

				<tr valign="top" class="image-radio-option"><th scope="row"><?php _e( 'Layout', 'twentyeleven' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Color Scheme', 'twentyeleven' ); ?></span></legend>
						<?php
							foreach ( twentyeleven_layouts() as $layout ) {
								?>
								<div class="layout">
								<label class="description">
									<input type="radio" name="twentyeleven_theme_options[theme_layout]" value="<?php echo esc_attr( $layout['value'] ); ?>" <?php checked( $options['theme_layout'], $layout['value'] ); ?> />
									<span>
										<img src="<?php echo esc_url( $layout['thumbnail'] ); ?>"/>
										<?php echo $layout['label']; ?>
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

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see twentyeleven_theme_options_init()
 * @todo set up Reset Options action
 *
 * @since Twenty Ten 1.0
 */
function twentyeleven_theme_options_validate( $input ) {
	$output = twentyeleven_get_default_theme_options();

	// Color scheme must be in our array of color scheme options
	if ( isset( $input['color_scheme'] ) && array_key_exists( $input['color_scheme'], twentyeleven_color_schemes() ) )
		$output['color_scheme'] = $input['color_scheme'];

	// Link color must be 3 or 6 hexadecimal characters
	if ( isset( $input['link_color'] ) && preg_match( '/^#?([a-f0-9]{3}){1,2}$/i', $input['link_color'] ) )
			$output['link_color'] = '#' . strtolower( ltrim( $input['link_color'], '#' ) );

	// Theme layout must be in our array of theme layout options
	if ( isset( $input['theme_layout'] ) && array_key_exists( $input['theme_layout'], twentyeleven_layouts() ) )
		$output['theme_layout'] = $input['theme_layout'];

	return $output;
}

/**
 * Register our color schemes and add them to the queue
 */
function twentyeleven_color_styles() {
	$options = twentyeleven_get_theme_options();
	$color_scheme = $options['color_scheme'];

	if ( 'dark' == $color_scheme )
		wp_enqueue_style( 'dark', get_template_directory_uri() . '/colors/dark.css', null, null );

	do_action( 'twentyeleven_color_schemes', $color_scheme );
}
add_action( 'wp_enqueue_scripts', 'twentyeleven_color_styles' );

/**
 * Add a style block to the theme for the current link color.
 *
 * This function is attached to the wp_head action hook.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_link_color() {
	$options = twentyeleven_get_theme_options();
	$link_color = $options['link_color'];

	$default_options = twentyeleven_get_default_theme_options();
	
	// Don't do anything if the current link color is the default.
	if ( $default_options['link_color'] == $link_color )
		return;
?>
	<style>
		/* Link color */
		a,
		.entry-title a:hover {
			color: <?php echo $link_color; ?>;
		}
	</style>
<?php
}
add_action( 'wp_head', 'twentyeleven_link_color' );

/**
 *  Adds Twenty Ten layout classes to the array of body classes
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_layout_classes( $classes ) {
	$options = twentyeleven_get_theme_options();
	$current_layout = $options['theme_layout'];

	$twentyeleven_classes = array();

	$two_column_layouts = array( 'content-sidebar', 'sidebar-content' );

	if ( in_array( $current_layout, $two_column_layouts ) )
		$twentyeleven_classes[] = 'two-column';
	else
		$twentyeleven_classes[] = 'one-column';

	$twentyeleven_classes[] = $current_layout;

	$twentyeleven_classes = apply_filters( 'twentyeleven_layout_classes', $twentyeleven_classes, $current_layout );

	return array_merge( $classes, $twentyeleven_classes );
}
add_filter( 'body_class', 'twentyeleven_layout_classes' );