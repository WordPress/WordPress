<?php
/**
 * Sets up custom filters and actions for the theme.  This does things like sets up sidebars, menus, scripts, 
 * and lots of other awesome stuff that WordPress themes do.
 *
 * @package    Stargazer
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2013 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/themes/stargazer
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register custom image sizes. */
add_action( 'init', 'stargazer_register_image_sizes', 5 );

/* Register custom menus. */
add_action( 'init', 'stargazer_register_menus', 5 );

/* Register sidebars. */
add_action( 'widgets_init', 'stargazer_register_sidebars', 5 );

/* Add custom scripts. */
add_action( 'wp_enqueue_scripts', 'stargazer_enqueue_scripts' );

/* Register custom styles. */
add_action( 'wp_enqueue_scripts',    'stargazer_register_styles', 0 );
add_action( 'admin_enqueue_scripts', 'stargazer_admin_register_styles', 0 );

/* Excerpt-related filters. */
add_filter( 'excerpt_length', 'stargazer_excerpt_length' );

/* Modifies the theme layout. */
add_filter( 'theme_mod_theme_layout', 'stargazer_mod_theme_layout', 15 );

/* Adds custom attributes to the subsidiary sidebar. */
add_filter( 'hybrid_attr_sidebar', 'stargazer_sidebar_subsidiary_class', 10, 2 );

/* Appends comments link to status posts. */
add_filter( 'the_content', 'stargazer_status_content', 9 ); // run before wpautop()

/* Modifies the framework's infinity symbol. */
add_filter( 'hybrid_aside_infinity', 'stargazer_aside_infinity' );

/* Adds custom settings for the visual editor. */
add_filter( 'tiny_mce_before_init', 'stargazer_tiny_mce_before_init' );
add_filter( 'mce_css',              'stargazer_mce_css'              );

/* Filters the calendar output. */
add_filter( 'get_calendar', 'stargazer_get_calendar' );

/* Filters the [audio] shortcode. */
add_filter( 'wp_audio_shortcode', 'stargazer_audio_shortcode', 10, 4 );

/* Filters the [video] shortcode. */
add_filter( 'wp_video_shortcode', 'stargazer_video_shortcode', 10, 3 );

/* Filter the [video] shortcode attributes. */
add_filter( 'shortcode_atts_video', 'stargazer_video_atts' );

/**
 * Registers custom image sizes for the theme.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_register_image_sizes() {

	/* Sets the 'post-thumbnail' size. */
	set_post_thumbnail_size( 175, 131, true );

	/* Adds the 'stargazer-full' image size. */
	add_image_size( 'stargazer-full', 1025, 500, false );
}

/**
 * Registers nav menu locations.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_register_menus() {
	register_nav_menu( 'primary',   _x( 'Primary',   'nav menu location', 'stargazer' ) );
	register_nav_menu( 'secondary', _x( 'Secondary', 'nav menu location', 'stargazer' ) );
	register_nav_menu( 'social',    _x( 'Social',    'nav menu location', 'stargazer' ) );
}

/**
 * Registers sidebars.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_register_sidebars() {

	hybrid_register_sidebar(
		array(
			'id'          => 'primary',
			'name'        => _x( 'Primary', 'sidebar', 'stargazer' ),
			'description' => __( 'The main sidebar. It is displayed on either the left or right side of the page based on the chosen layout.', 'stargazer' )
		)
	);

	hybrid_register_sidebar(
		array(
			'id'          => 'subsidiary',
			'name'        => _x( 'Subsidiary', 'sidebar', 'stargazer' ),
			'description' => __( 'A sidebar located in the footer of the site. Optimized for one, two, or three widgets (and multiples thereof).', 'stargazer' )
		)
	);
}

/**
 * Enqueues scripts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_enqueue_scripts() {

	$suffix = hybrid_get_min_suffix();

	wp_register_script( 'stargazer', trailingslashit( get_template_directory_uri() ) . "js/stargazer{$suffix}.js", array( 'jquery' ), null, true );

	wp_localize_script(
		'stargazer',
		'stargazer_i18n',
		array(
			'search_toggle' => __( 'Expand Search Form', 'stargazer' )
		)
	);

	wp_enqueue_script( 'stargazer' );
}

/**
 * Registers custom stylesheets for the front end.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_register_styles() {
	wp_deregister_style( 'mediaelement' );
	wp_deregister_style( 'wp-mediaelement' );

	wp_register_style( 'stargazer-fonts',        '//fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic|Open+Sans:300,400,600,700' );
	wp_register_style( 'stargazer-mediaelement', trailingslashit( get_template_directory_uri() ) . 'css/mediaelement/mediaelement.min.css' );
}

/**
 * Registers stylesheets for use in the admin.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function stargazer_admin_register_styles() {
	wp_register_style( 'stargazer-fonts', '//fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic|Open+Sans:300,400,600,700' );
	wp_register_style( 'stargazer-admin-custom-header', trailingslashit( get_template_directory_uri() ) . 'css/admin-custom-header.css' );
}

/**
 * Callback function for adding editor styles.  Use along with the add_editor_style() function.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function stargazer_get_editor_styles() {

	/* Set up an array for the styles. */
	$editor_styles = array();

	/* Add the theme's editor styles. */
	$editor_styles[] = trailingslashit( get_template_directory_uri() ) . 'css/editor-style.css';

	/* If a child theme, add its editor styles. Note: WP checks whether the file exists before using it. */
	if ( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . 'css/editor-style.css' ) )
		$editor_styles[] = trailingslashit( get_stylesheet_directory_uri() ) . 'css/editor-style.css';

	/* Add the locale stylesheet. */
	$editor_styles[] = get_locale_stylesheet_uri();

	/* Uses Ajax to display custom theme styles added via the Theme Mods API. */
	$editor_styles[] = add_query_arg( 'action', 'stargazer_editor_styles', admin_url( 'admin-ajax.php' ) );

	/* Return the styles. */
	return $editor_styles;
}

/**
 * Adds the <body> class to the visual editor.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $settings
 * @return array
 */
function stargazer_tiny_mce_before_init( $settings ) {

	$settings['body_class'] = join( ' ', get_body_class() );

	return $settings;
}

/**
 * Removes the media player styles from the visual editor since we're loading our own.
 *
 * @since  1.1.0
 * @access public
 * @param  string  $mce_css
 * @return string
 */
function stargazer_mce_css( $mce_css ) {
	$version = 'ver=' . $GLOBALS['wp_version'];

	$mce_css = str_replace( includes_url( "js/mediaelement/mediaelementplayer.min.css?$version" ) . ',', '', $mce_css );
	$mce_css = str_replace( includes_url( "js/mediaelement/wp-mediaelement.css?$version" ) . ',',        '', $mce_css );

	return $mce_css;
}

/**
 * Modifies the theme layout on attachment pages.  If a specific layout is not selected and the global layout 
 * isn't set to '1c-narrow', this filter will change the layout to '1c'.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $layout
 * @return string
 */
function stargazer_mod_theme_layout( $layout ) {

	if ( is_attachment() && wp_attachment_is_image() ) {
		$post_layout = get_post_layout( get_queried_object_id() );

		if ( 'default' === $post_layout && '1c-narrow' !== $layout )
			$layout = '1c';
	}

	return $layout;
}

/**
 * Adds the comments link to status posts' content.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $content
 * @return string
 */
function stargazer_status_content( $content ) {

	if ( !is_singular() && has_post_format( 'status' ) && in_the_loop() && ( have_comments() || comments_open() ) )
		$content .= ' <a class="comments-link" href="' . get_permalink() . '">' . number_format_i18n( get_comments_number() ) . '</a>';

	return $content;
}

/**
 * Filter's Hybrid Core's infinity symbol for aside posts.  This changes the symbol to a comments link if 
 * the post's comments are open or if the post has comments.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $html
 * @return string
 */
function stargazer_aside_infinity( $html ) {

	if ( have_comments() || comments_open() )
		$html = ' <a class="comments-link" href="' . get_permalink() . '">' . number_format_i18n( get_comments_number() ) . '</a>';

	return $html;
}

/**
 * Adds a custom excerpt length.
 *
 * @since  1.0.0
 * @access public
 * @param  int     $length
 * @return int
 */
function stargazer_excerpt_length( $length ) {
	return 30;
}

/**
 * Adds a custom class to the 'subsidiary' sidebar.  This is used to determine the number of columns used to 
 * display the sidebar's widgets.  This optimizes for 1, 2, and 3 columns or multiples of those values.
 *
 * Note that we're using the global $sidebars_widgets variable here. This is because core has marked 
 * wp_get_sidebars_widgets() as a private function. Therefore, this leaves us with $sidebars_widgets for 
 * figuring out the widget count.
 * @link http://codex.wordpress.org/Function_Reference/wp_get_sidebars_widgets
 *
 * @since  1.0.0
 * @access public
 * @param  array  $attr
 * @param  string $context
 * @return array
 */
function stargazer_sidebar_subsidiary_class( $attr, $context ) {

	if ( 'subsidiary' === $context ) {
		global $sidebars_widgets;

		if ( is_array( $sidebars_widgets ) && !empty( $sidebars_widgets[ $context ] ) ) {

			$count = count( $sidebars_widgets[ $context ] );

			if ( 1 === $count )
				$attr['class'] .= ' sidebar-col-1';

			elseif ( !( $count % 3 ) || $count % 2 )
				$attr['class'] .= ' sidebar-col-3';

			elseif ( !( $count % 2 ) )
				$attr['class'] .= ' sidebar-col-2';
		}
	}

	return $attr;
}

/**
 * Turns the IDs into classes for the calendar.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $calendar
 * @return string
 */
function stargazer_get_calendar( $calendar ) {
	return preg_replace( '/id=([\'"].*?[\'"])/i', 'class=$1', $calendar );
}

/**
 * Adds a featured image (if one exists) next to the audio player.  Also adds a section below the player to 
 * display the audio file information (toggled by custom JS).
 *
 * @since  1.0.0
 * @access public
 * @param  string  $html
 * @param  array   $atts
 * @param  object  $audio
 * @param  object  $post_id
 * @return string
 */
function stargazer_audio_shortcode( $html, $atts, $audio, $post_id ) {

	/* Don't show in the admin. */
	if ( is_admin() )
		return $html;

	/* If we have an actual attachment to work with, use the ID. */
	if ( is_object( $audio ) ) {
		$attachment_id = $audio->ID;
	}

	/* Else, get the ID via the file URL. */
	else {
		$extensions = join( '|', wp_get_audio_extensions() );

		preg_match(
			'/(src|' . $extensions . ')=[\'"](.+?)[\'"]/i', 
			preg_replace( '/(\?_=[0-9])/i', '', $html ),
			$matches
		);

		if ( !empty( $matches ) )
			$attachment_id = hybrid_get_attachment_id_from_url( $matches[2] );
	}

	/* If an attachment ID was found. */
	if ( !empty( $attachment_id ) ) {

		/* Get the attachment's featured image. */
		$image = get_the_image( 
			array( 
				'post_id'      => $attachment_id,  
				'image_class'  => 'audio-image',
				'link_to_post' => is_attachment() ? false : true, 
				'echo'         => false 
			) 
		);

		/* If there's no attachment featured image, see if there's one for the post. */
		if ( empty( $image ) && !empty( $post_id ) )
			$image = get_the_image( array( 'image_class' => 'audio-image', 'link_to_post' => false, 'echo' => false ) );

		/* Add a wrapper for the audio element and image. */
		if ( !empty( $image ) ) {
			$image = preg_replace( array( '/width=[\'"].+?[\'"]/i', '/height=[\'"].+?[\'"]/i' ), '', $image );
			$html = '<div class="audio-shortcode-wrap">' . $image . $html . '</div>';
		}

		/* If not viewing an attachment page, add the media info section. */
		if ( !is_attachment() ) {
			$html .= '<div class="media-shortcode-extend">';
			$html .= '<div class="media-info audio-info">';
			$html .= hybrid_media_meta( array( 'post_id' => $attachment_id, 'echo' => false ) );
			$html .= '</div>';
			$html .= '<button class="media-info-toggle">' . __( 'Audio Info', 'stargazer' ) . '</button>';
			$html .= '</div>';
		}
	}

	return $html;
}

/**
 * Adds a section below the player to  display the video file information (toggled by custom JS).
 *
 * @since  1.0.0
 * @access public
 * @param  string  $html
 * @param  array   $atts
 * @param  object  $audio
 * @return string
 */
function stargazer_video_shortcode( $html, $atts, $video ) {

	/* Don't show on single attachment pages or in the admin. */
	if ( is_attachment() || is_admin() )
		return $html;

	/* If we have an actual attachment to work with, use the ID. */
	if ( is_object( $video ) ) {
		$attachment_id = $video->ID;
	}

	/* Else, get the ID via the file URL. */
	else {
		$extensions = join( '|', wp_get_video_extensions() );

		preg_match(
			'/(src|' . $extensions . ')=[\'"](.+?)[\'"]/i', 
			preg_replace( '/(\?_=[0-9])/i', '', $html ),
			$matches
		);

		if ( !empty( $matches ) )
			$attachment_id = hybrid_get_attachment_id_from_url( $matches[2] );
	}

	/* If an attachment ID was found, add the media info section. */
	if ( !empty( $attachment_id ) ) {

		$html .= '<div class="media-shortcode-extend">';
		$html .= '<div class="media-info video-info">';
		$html .= hybrid_media_meta( array( 'post_id' => $attachment_id, 'echo' => false ) );
		$html .= '</div>';
		$html .= '<button class="media-info-toggle">' . __( 'Video Info', 'stargazer' ) . '</button>';
		$html .= '</div>';
	}

	return $html;
}

/**
 * Featured image for self-hosted videos.  Checks the vidoe attachment for sub-attachment images.  If 
 * none exist, checks the current post (if in The Loop) for its featured image.  If an image is found, 
 * it's used as the "poster" attribute in the [video] shortcode.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $out
 * @return array
 */
function stargazer_video_atts( $out ) {

	/* Don't show in the admin. */
	if ( is_admin() )
		return $out;

	/* Only run if the user didn't set a 'poster' image. */
	if ( empty( $out['poster'] ) ) {

		/* Check the 'src' attribute for an attachment file. */
		if ( !empty( $out['src'] ) )
			$attachment_id = hybrid_get_attachment_id_from_url( $out['src'] );

		/* If we couldn't get an attachment from the 'src' attribute, check other supported file extensions. */
		if ( empty( $attachment_id ) ) {

			$default_types = wp_get_video_extensions();

			foreach ( $default_types as $type ) {

				if ( !empty( $out[ $type ] ) ) {
					$attachment_id = hybrid_get_attachment_id_from_url( $out[ $type ] );

					if ( !empty( $attachment_id ) )
						break;
				}
			}
		}

		/* If there's an attachment ID at this point. */
		if ( !empty( $attachment_id ) ) {

			/* Get the attachment's featured image. */
			$image = get_the_image( 
				array( 
					'post_id'      => $attachment_id, 
					'size'         => 'full',
					'format'       => 'array',
					'echo'         => false
				) 
			);
		}

		/* If no image has been found and we're in the post loop, see if the current post has a featured image. */
		if ( empty( $image ) && get_post() )
			$image = get_the_image( array( 'size' => 'full', 'format' => 'array', 'echo' => false ) );

		/* Set the 'poster' attribute if we have an image at this point. */
		if ( !empty( $image ) )
			$out['poster'] = $image['src'];
	}

	return $out;
}
