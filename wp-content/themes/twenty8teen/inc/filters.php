<?php
/**
 * Custom filters for this theme
 * @package Twenty8teen
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function twenty8teen_body_classes( $classes ) {
	if ( is_singular() ) {
		// Adds a class of the post slug to singular pages.
		$post = get_post();
		$classes[] = 'name-' . esc_attr( $post->post_name );
	}
	else {
		// Adds a class of hfeed to non-singular pages.
		$classes[] = 'hfeed';
	}

	$defaults = twenty8teen_default_booleans();
	if ( get_theme_mod( 'show_vignette', 	$defaults['show_vignette'] ) ) {
		$classes[] = 'vignette';
	}
	if ( get_theme_mod( 'show_header_imagebehind', 	$defaults['show_header_imagebehind'] ) ) {
		$classes[] = 'header-behind';
	}
	if ( is_active_sidebar( 'side-widget-area' ) ) {
		$classes[] = 'has-sidebar';
		if ( get_theme_mod( 'switch_sidebar', 	$defaults['switch_sidebar'] ) ) {
			$classes[] = 'sidebar-leading';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'twenty8teen_body_classes' );

/**
 * Add entry class for all post types.
 * Remove hentry class from the array of post classes, for Pages.
 * Add the cards class if set.
 */
function twenty8teen_post_classes( $classes, $class, $post_id ) {
	$classes[] = 'entry';
	if ( is_page() || is_attachment() ) {
		if ( ($i = array_search( 'hentry', $classes )) !== false ) {
			unset( $classes[$i] );
		}
	}

	$defaults = twenty8teen_default_booleans();
	if ( get_theme_mod( 'show_as_cards', $defaults['show_as_cards'] ) ) {
		$classes[] = 'cards';
	}

	return $classes;
}
add_filter( 'post_class', 'twenty8teen_post_classes', 20, 3 );


/**
 * Adds custom classes to the array of attributes.
 *
 * @see twenty8teen_attributes
 * @return array
 */
function twenty8teen_add_page_classes( $attrs, $tag ) {
	if ( is_page() && isset( $attrs['class'] ) ) {
		$class = is_array( $attrs['class'] ) ? $attrs['class'] : explode( ' ', $attrs['class'] );
		if ( in_array( 'entry-header', $class ) ) {
			$class[] = 'page-header';
		}
		if ( in_array( 'entry-title', $class ) ) {
			$class[] = 'page-title';
		}
		if ( in_array( 'entry-content', $class ) ) {
			$class[] = 'page-content';
		}
		if ( in_array( 'entry-footer', $class ) ) {
			$class[] = 'page-footer';
		}
		$attrs['class'] = $class;
	}
	return $attrs;
}
add_filter( 'twenty8teen_attributes', 'twenty8teen_add_page_classes', 9, 2 );

/**
 * Adds user classes to the array of area class choices.
 */
function twenty8teen_add_user_classes( $choices ) {
	$user_classes = get_theme_mod( 'user_classes', '' );
	if ( $user_classes ) {
		$user_classes = explode( ' ', $user_classes );
		$user_classes = array_map( 'esc_attr', $user_classes );
		$display = array_map( 'ucfirst', $user_classes );
		$choices = $choices + array_combine( $user_classes, $display );
	}
	return $choices;
}
add_filter( 'twenty8teen_area_class_choices', 'twenty8teen_add_user_classes' );

/**
 * For pagination and navigation, adds classes chosen in widgets. Used in template files.
 * 'navigation_markup_template' filter
 */
function twenty8teen_nav_add_widget_classes( $template ) {
	$add = twenty8teen_widget_get_classes();
	if ( $add ) {
		$template = str_replace( '%1$s', $add . ' %1$s', $template );
	}
	return $template;
}

/**
 * For custom logo, adds classes chosen in widgets. Used in logo template file.
 */
function twenty8teen_logo_add_widget_classes( $attr, $attachment, $size ) {
	$add = twenty8teen_widget_get_classes();
	if ( $add ) {
		$attr['class'] .= ' ' . $add;
	}
	return $attr;
}

/**
 * For attachment pages, output the caption as content.
 */
function twenty8teen_attachment( $p ) {
	$apost = get_post();
	if ( ! wp_attachment_is( 'video', $apost ) && ! wp_attachment_is( 'audio', $apost ) ) {
		// non-video and non-audio - use large size or icon
		$p = '<p class="attachment">';
		$p .= wp_get_attachment_link( $apost->ID, 'large', false, true );
		$p .= '</p>';
	}
	$p .= $apost->post_excerpt ? ( '<p class="wp-caption-text">'. wptexturize( $apost->post_excerpt ) . '</p>' ) : '';
	return $p;
}
add_filter('prepend_attachment', 'twenty8teen_attachment');

/**
 * For attachment navigation, adds adjacent image links. Used in template files.
 * 'navigation_markup_template' filter
 */
function twenty8teen_nav_add_attachment_links( $template ) {
	ob_start();
	previous_image_link();  // this function uses echo
	$prev = ob_get_clean();
	ob_start();
	next_image_link();      // this function uses echo
	$next = ob_get_clean();
	if ( $prev || $next ) {
		$add = '<div class="nav-previous">' . $prev . '</div> <div class="nav-next">'
			. $next . '</div> <br class="clear" />'; 
		$template = str_replace( '%3$s', $add . ' %3$s', $template );
	}
	return $template;
}

/**
 * Generate the style rules for the editor to look like front end.
 */
function twenty8teen_editor_dynamic_rules( $mce_init ) {
	if ( array_key_exists( 'body_class', $mce_init ) ) {
		$css = twenty8teen_dynamic_rules( true ) . ' ';
		$css = str_replace( array( '"', "\n" ), array( '\"',' ' ) , $css );
		if ( isset( $mce_init['content_style'] ) ) {
			$mce_init['content_style'] .= ' ' . $css;
		} else {
			$mce_init['content_style'] = $css;
		}
		$booleans = twenty8teen_default_booleans();
		$cards = get_theme_mod( 'show_as_cards', $booleans['show_as_cards'] ) ? ' cards' : '';
		$classes = twenty8teen_area_classes( 'main', 'site-main', false ) . ' ' .
			twenty8teen_area_classes( 'content', 'content-area' . $cards, false );
		$mce_init['body_class'] .= ' ' . $classes;
	}
	return $mce_init;
}
add_filter('tiny_mce_before_init', 'twenty8teen_editor_dynamic_rules', 20 );

/**
 * Add the Formats selector to the TinyMCE editor.
 */
function twenty8teen_mce_buttons( $buttons ) {
	array_unshift( $buttons, 'styleselect' );
	return $buttons;
}
add_filter( 'mce_buttons_2', 'twenty8teen_mce_buttons' );

/**
 * Generate the style format list for the editor Formats selector, with submenus.
 */
function twenty8teen_mce_style_formats( $init_array ) {
	$classes = array( 'cards' => __( 'Card', 'twenty8teen' ), 
		'news-columns' => __( 'News Columns', 'twenty8teen' ),
		'alignleft' => __( 'Align Left', 'twenty8teen' ),
		'aligncenter' => __( 'Align Center', 'twenty8teen' ),
		'alignright' => __( 'Align Right', 'twenty8teen' ) ) +
		twenty8teen_widget_class_choices( '' );
	$style_formats = array_key_exists( 'style_formats', $init_array ) ?
		json_decode( $init_array['style_formats'] ) : array();
	$text = $align = $width = $border = $other = array();
	foreach ( $classes as $class => $title ) {
		if ( preg_match( "/width/i", $class ) ) {
			$width[] = array( 'title' => $title, 'classes' => $class,
				'selector' => 'p,div,section,aside,ol,ul,dl,table,figure,img,blockquote,pre,h1,h2,h3,h4,h5,h6,fieldset,hr' );
		}
		else if ( preg_match( "/border/i", $class ) ) {
			$border[] = array( 'title' => $title, 'classes' => $class, 'selector' => '*' );
		}
		else if ( preg_match( "/align|clear/i", $class ) ) {
			$align[] = array( 'title' => $title, 'classes' => $class, 'selector' => '*' );
		}
		else if ( preg_match( "/font|letter|cap|case/i", $class ) ) {
			$text[] = array( 'title' => $title, 'classes' => $class,
				'inline' => 'span',	'selector' => '*' );
		}
		else {
			$other[] = array( 'title' => $title, 'classes' => $class, 'selector' => '*' );
		}
	}
	if ( $text ) {
		$style_formats[] = array( 'title' => __( 'Text', 'twenty8teen' ), 'items' => $text );
	}
	if ( $align ) {
		$style_formats[] = array( 'title' => __( 'Align', 'twenty8teen' ), 'items' => $align );
	}
	if ( $width ) {
		$style_formats[] = array( 'title' => __( 'Width', 'twenty8teen' ), 'items' => $width );
	}
	if ( $border ) {
		$style_formats[] = array( 'title' => __( 'Border', 'twenty8teen' ), 'items' => $border );
	}
	if ( $other ) {
		$style_formats[] = array( 'title' => __( 'Other', 'twenty8teen' ), 'items' => $other );
	}
	$init_array['style_formats'] = wp_json_encode( $style_formats );
	return $init_array;
}
add_filter( 'tiny_mce_before_init', 'twenty8teen_mce_style_formats' );

/**
 * Put a version number on the editor stylesheets.
 */
function twenty8teen_editor_stylesheets( $stylesheets ) {
	$version = $theme = wp_get_theme();
	$version = $theme->get( 'Version' );
	foreach ($stylesheets as $key => $value) {
		if ( strpos( $value, 'editor-style' ) ) {
			$stylesheets[$key] .= '?ver=' . $version;
		}
	}
	return $stylesheets;
}
add_filter( 'editor_stylesheets', 'twenty8teen_editor_stylesheets' );

/**
 * Change the post excerpt length to user's choice.
 */
function twenty8teen_excerpt_length( $length ) {
	if ( ! is_admin() ) {
		$default = twenty8teen_default_sizes();
		$length = absint( get_theme_mod( 'excerpt_length', $default['excerpt_length'] ) );
	}
	return $length;
}
add_filter( 'excerpt_length', 'twenty8teen_excerpt_length' );

/**
 * Change the post thumbnail size to user's choice.
 */
function twenty8teen_post_thumbnail_size( $size ) {
	if ( $size === 'post-thumbnail' ) {  // only affect default parameter
		$default = twenty8teen_default_sizes();
		$size = is_singular() ?
			( is_attachment() ?
				'none' :
				get_theme_mod( 'featured_size_single', $default['featured_size_single'] )
			)	:
			get_theme_mod( 'featured_size_archives', $default['featured_size_archives'] );
	}
	return $size;
}
add_filter( 'post_thumbnail_size', 'twenty8teen_post_thumbnail_size' );

/**
 * Change the post thumbnail html to show identimage if no thumbnail.
 */
function twenty8teen_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	if ( $size === 'none' ) {
		$html = '';
	}
	else {
		if ( empty( $html ) ) {
			$this_post_type = get_post_type( $post_id );

			if ( post_type_supports( $this_post_type, 'thumbnail' ) &&
				! in_array( $this_post_type, apply_filters( 
					'twenty8teen_posttypes_no_default_thumbnail', array( 'attachment' ) ) ) ) {

				$default = twenty8teen_default_identimages();
				$opt = get_theme_mod( 'show_featured_identimage', $default['show_featured_identimage'] );
				if ( 'none' !== $opt ) {
					$imgattr = twenty8teen_add_gradient( get_permalink( $post_id ), array(
						'style' => '',
						'class' => 'wp-post-image wrapped-media-size-' . ( is_array($size) ? 'thumbnail' : $size ),
						'src' => get_template_directory_uri() . '/images/clear.png',
						'alt' => '',
					), $opt );
					$attr = wp_parse_args( $attr, array( 'class'  => '' ) );
					$imgattr = array_merge( $attr, $imgattr );
					$attr['class'] .= ' wp-post-image-identimage-wrap';
					$html = '<div ' . twenty8teen_attributes( 'div', $attr, false ) . '>';
					$html .= '<img ' . twenty8teen_attributes( 'img', $imgattr, false ) . ' /></div>';
				}
			}
		}
	}
	return $html;
}
add_filter( 'post_thumbnail_html', 'twenty8teen_post_thumbnail_html', 10, 5 );

/**
 * Add a fragment identifier (to the content) to paginated links.
 */
function twenty8teen_link_pages_link( $link, $i ) {
	if ( $i > 1 && preg_match( '/href="([^"]*)"/', $link, $matches ) ) {
		if ( false === strpos( $matches[1], '#' ) ) {
			$link = str_replace( $matches[1], $matches[1] . '#content', $link );
		}
	}
	return $link;
}
add_filter( 'wp_link_pages_link', 'twenty8teen_link_pages_link', 10, 2 );

/**
 * Add preconnect for Google Fonts.
 */
function twenty8teen_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'twenty8teen-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'twenty8teen_resource_hints', 10, 2 );

/**
 * Start archive pages in table view according to option.
 */
function twenty8teen_archive_in_tableview( $area_class, $area ) {
	if ( 'main' == $area && ! is_singular() ) {
		$default = twenty8teen_default_booleans();
		if ( get_theme_mod( 'start_in_tableview', $default['start_in_tableview'] ) ) {
			$area_class .= ' table-view';
		}
		else {
			$classes = preg_split( '#\s+#', $area_class );
			if ( ($i = array_search( 'table-view', $classes )) !== false ) {
				unset( $classes[$i] );
				$area_class = join( ' ', $classes );
			}
		}
	}
	return $area_class;
}
add_filter( 'twenty8teen_area_classes', 'twenty8teen_archive_in_tableview', 10, 2 );

/**
 * Remove the sidebar according to option.
 */
function twenty8teen_is_active_sidebar( $is_active_sidebar, $index ) {
	$default = twenty8teen_default_booleans();
	if ( 'side-widget-area' == $index &&
		! get_theme_mod( 'show_sidebar', $default['show_sidebar'] ) ) {
		$is_active_sidebar = false;
	}
	return $is_active_sidebar;
}
add_filter( 'is_active_sidebar', 'twenty8teen_is_active_sidebar', 10, 2 );

/**
 * Add a taxonomy term list to the parameter.
 * Used in taxonomy-root page template.
 */
function twenty8teen_append_taxonomy( $content, $taxonomy = 'category' ) {
	global $post, $wp;
	if ( func_num_args() == 1 ) {
		$taxonomy = ( $post && $post->post_name ) ? $post->post_name : wp_basename( $wp->request );
	}
	$out = wp_list_categories( array(
		'taxonomy'   => $taxonomy,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'show_count' => 1,
		'title_li'   => '',
		'number'     => 400,  // Limit output.
		'echo'       => 0,
	) );
	if ( $out ) {
		$content .= '<ul class="' . esc_attr( $taxonomy ) . ' taxonomy-list">'
			. $out
			. '</ul><!-- .taxonomy-list -->';
	}
	return $content;
}

/**
 * Apply a preset value to a theme_mod.
 * Added in twenty8teen_add_conditional_preset_filters().
 */
function twenty8teen_preset_theme_mod( $value ) {
	$which = str_replace( 'theme_mod_', '', current_filter() );
	$preset_values = get_theme_support( 'twenty8teen_conditional_presets' );
	$value = isset( $preset_values[0][$which] ) ?
		( is_array( $preset_values[0][$which] ) ?
		 	array_merge( $value, $preset_values[0][$which] )
			: $preset_values[0][$which] )
		: $value;
	return $value;
}

/**
 * Supply the page conditional preset to apply according to option.
 */
function twenty8teen_conditional_presets_for_pages( $presets ) {
	$chosen = get_theme_mod( 'page_conditional_presets', array() );
	if ( count( $chosen ) ) {
		$state = get_body_class();
		foreach ($chosen as $class => $preset) {
			if ( $class && in_array( $class, $state ) ) {
				if ( is_array( $preset ) ) {
					$presets = array_merge( $presets, $preset );
				}
				else {
					$presets[] = $preset;
				}
			}
		}
	}
	return $presets;
}
add_filter( 'twenty8teen_conditional_presets', 'twenty8teen_conditional_presets_for_pages' );

/**
 * Supply default value for sidebar widgets.
 */
function twenty8teen_default_widgets( $default, $option, $passed_default ) {
	$default = array(
		'header-widget-area' => array( 'twenty8teen-template-part-2',
			'twenty8teen-template-part-3' ),
		'content-widget-area' => array( 'twenty8teen-loop-part-2',
			'twenty8teen-template-part-4' ),
		'side-widget-area' => array(),
		'footer-widget-area' => array( 'twenty8teen-template-part-5' ),
	);
	if ( 'theme_mods_twenty8teen' === $option ) {
		$default = array(
			'sidebars_widgets' => array( 'time' => time(), 'data' => $default ),
		);
	}
	return $default;
}
// Will only affect a new site installation.
add_filter( 'default_option_sidebars_widgets', 'twenty8teen_default_widgets', 1, 3 );
// Will only affect first-time theme installation.
add_filter( 'default_option_theme_mods_twenty8teen', 'twenty8teen_default_widgets', 1, 3 );

/**
 * Supply default value for template-parts widgets.
 */
function twenty8teen_default_widget_template_parts( $default, $option, $passed_default ) {
	return array(
		2 => array( 'title' => 'Site Branding', 'part' => 'site-branding' ),
		3 => array( 'title' => 'Main Nav', 'part' => 'main-nav' ),
		4 => array( 'title' => 'Posts Pagination', 'part' => 'posts-pagination' ),
		5 => array( 'title' => 'Site Copyright', 'part' => 'site-copyright' ),
		'_multiwidget' => 1,
	);
}
add_filter( 'default_option_widget_twenty8teen-template-part',
	'twenty8teen_default_widget_template_parts', 1, 3 );

/**
 * Supply default value for loop-parts widgets.
 */
function twenty8teen_default_widget_loop_parts( $default, $option, $passed_default ) {
	return array(
		2 => array(
			'title' => 'Entry, Post Navigation, Comments',
			'part' => array(
				'entry',
				'post-navigation',
				'comments',
			),
			'align' => array( '', '', '' ),
			'class' => array(),
		),
		'_multiwidget' => 1,
	);
}
add_filter( 'default_option_widget_twenty8teen-loop-part',
	'twenty8teen_default_widget_loop_parts', 1, 3 );

/**
 * Add the extra fields for theme classes to all non-theme widgets.
 */
function twenty8teen_other_widget_formm( $widget, $return, $instance ) {
	if ( strpos( $widget->id_base, 'twenty8teen' ) !== false ) {
		return 1;  // Return null if fields are added.
	}
	$instance = wp_parse_args( (array) $instance,
		array( 'twenty8teenalign' => '', 'twenty8teenclass' => array() ) );
	$align = empty( $instance['twenty8teenalign'] ) ? '' : esc_attr( $instance['twenty8teenalign'] );
	$class = (array) $instance['twenty8teenclass'];
	$class = count( $class ) ? array_map( 'esc_attr', $class ) : array();
	$id_align = esc_attr( $widget->get_field_id( 'twenty8teenalign' ) );
	$id_class = esc_attr( $widget->get_field_id( 'twenty8teenclass' ) );
	?>
	<p>
		<label for="<?php echo $id_align; ?>">
		<?php esc_html_e( 'Alignment:', 'twenty8teen' ); ?></label>
		<select id="<?php echo $id_align; ?>"
			name="<?php echo esc_attr( $widget->get_field_name( 'twenty8teenalign' ) ); ?>">
			<option value="" <?php selected( $align, '' ); ?>>--</option>
			<option value="left" <?php selected( $align, 'left' ); ?>> <?php esc_html_e( 'Left', 'twenty8teen' ); ?> </option>
			<option value="center" <?php selected( $align, 'center' ); ?>> <?php esc_html_e( 'Center', 'twenty8teen' ); ?> </option>
			<option value="right" <?php selected( $align, 'right' ); ?>> <?php esc_html_e( 'Right', 'twenty8teen' ); ?> </option>
		</select>
	</p>
	<p>
		<label for="<?php echo $id_class; ?>">
		<?php esc_html_e( 'Styles:', 'twenty8teen' ); ?></label>
		<select id="<?php echo $id_class; ?>" multiple="multiple"
			name="<?php echo esc_attr( $widget->get_field_name( 'twenty8teenclass' ) ); ?>[]">
			<option value="" <?php echo ( count( $class ) ? '' : 'selected' ); ?>>--</option>
			<?php
			$choices = twenty8teen_widget_class_choices( 'template-parts' );
			foreach ( $choices as $aclass => $info ) {
				$selected = selected( in_array( $aclass, $class ) );
				echo "\n\t<option value='" . $aclass . "' $selected>$info</option>";
			}
			?></select>
	</p>
	<?php
	return $return;
}
add_action( 'in_widget_form', 'twenty8teen_other_widget_formm', 10, 3 );

/**
 * Process the extra fields added to all non-theme widgets, when updated.
 */
function twenty8teen_other_widget_update( $instance, $new_instance, $old_instance, $widget ) {
	if ( strpos( $widget->id_base, 'twenty8teen' ) === false ) {
		$new_instance = wp_parse_args( (array) $new_instance,
			array( 'twenty8teenalign' => '', 'twenty8teenclass' => array() ) );
		$instance = wp_parse_args( (array) $instance,
			array( 'twenty8teenalign' => '', 'twenty8teenclass' => array() ) );
		$instance['twenty8teenalign'] = sanitize_html_class( $new_instance['twenty8teenalign'] );
		$instance['twenty8teenclass'] = array_map( 'sanitize_html_class', (array) $new_instance['twenty8teenclass'] );
		// Keep database clean.
		if (empty( $instance['twenty8teenalign'] )) { unset( $instance['twenty8teenalign'] ); }
		if (empty( $instance['twenty8teenclass'] )) { unset( $instance['twenty8teenclass'] ); }
	}
	return $instance;
}
add_filter( 'widget_update_callback', 'twenty8teen_other_widget_update', 10, 4 );

/**
 * A way to add classes to all widgets with the 'widget' class.
 */
function twenty8teen_widget_params( $params ) {
	$classes = twenty8teen_area_classes( 'widgets', '', false );
	if ( $classes ) {
		if ( preg_match( '/class=([\'"])(.+?)\1/i', $params[0]['before_widget'], $match ) ) {
			$before = explode( ' ', $match[2] );
			if ( in_array( 'widget', $before ) ) {
				$params[0]['before_widget'] = str_ireplace ( $match[2],
					$match[2] . ' ' . $classes, $params[0]['before_widget'] );
			}
		}
	}
	return $params;
}
add_filter( 'dynamic_sidebar_params', 'twenty8teen_widget_params' );

/**
 * A way to add theme classes to all non-theme widgets.
 */
function twenty8teen_other_widget_params( $params ) {
	// Only affect the front end.
	if ( ! is_admin() && isset( $params[0] ) ) {
		global $wp_registered_widgets;

		$widget_id = $params[0]['widget_id'];
		// Check that it is not a theme widget.
		if ( strpos( $widget_id, 'twenty8teen' ) === false &&
			array_key_exists( 1, $params ) ) {
			$widget_obj = $wp_registered_widgets[ $widget_id ]['callback'][0];
			$number = $params[1]['number'];
			$widget_obj->_set( $number );
			$instances = $widget_obj->get_settings(); // Retrieve all for this type.
			if ( array_key_exists( $number, $instances ) ) {
				$instance = wp_parse_args( (array) $instances[$number],
					array( 'twenty8teenalign' => '', 'twenty8teenclass' => array() ) );
				$align = esc_attr( $instance['twenty8teenalign'] );
				$class = array_map( 'sanitize_html_class', array_filter( (array) $instance['twenty8teenclass'] ) );
				$class[] = $align ? ( 'align' . $align ) : '';
				$class = array_unique( array_filter( $class ) );
				$class = join( ' ', array_map( 'esc_attr', $class ) );
				if ( preg_match( '/class=([\'"])(.+?)\1/i', $params[0]['before_widget'], $match ) ) {
					$params[0]['before_widget'] = str_ireplace ( $match[2],
						$match[2] . ' ' . $class, $params[0]['before_widget'] );
				}
			}
    }
	}
	return $params;
}
add_filter( 'dynamic_sidebar_params', 'twenty8teen_other_widget_params', 15, 1 );

/**
 * For fallback menu, make the parameters match custom menus.
 */
function twenty8teen_page_menu_args( $args ) {
	list( $before, $after ) = explode( '%3$s', $args['items_wrap'] );
	$walker = new Twenty8teen_Walker_Page;
	$wanted = array(
		'sort_column'  => 'menu_order, post_date',
		'menu_id'      => $args['container_id'],
		'menu_class'   => $args['container_class'],
		'before'       => sprintf( $before, 
			esc_attr( $args['menu_id'] ? $args['menu_id'] : 'id'. rand() ),
			esc_attr( $args['menu_class'] ) ),
		'after'        => $after,
		'show_home'    => false,
		'walker'       => $walker,
	);
	unset( $args['fallback_cb'] );
	return wp_parse_args( $wanted, $args );
}

/**
 * For custom menu, adding an input and label for submenus.
 */
function twenty8teen_nav_menu_start_el( $item_output, $item, $depth, $args ) {
	$classes = empty( $item->classes ) ? array() : (array) $item->classes;
	if ( $classes && in_array( 'menu-item-has-children', $classes ) ||
		in_array( 'page_item_has_children', $classes) ) {
		$an_id = $item->ID . microtime(true);
		$item_output .= '<input type="checkbox" id="sub' . $an_id
			. '" tabindex="-1"><label for="sub' . $an_id . '"></label>';
	}
	return $item_output;
}

/**
 * For page menu, adding an input and label for submenus.
 */
function twenty8teen_page_menu_start_el( $item_output, $page, $depth, $args ) {
	if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
		$an_id = $page->ID . microtime(true);
		$item_output .= '<input type="checkbox" id="sub' . $an_id
			. '" tabindex="-1"><label for="sub' . $an_id . '"></label>';
	}
	return $item_output;
}
add_filter( 'walker_page_menu_start_el', 'twenty8teen_page_menu_start_el', 9, 4);

/**
 * In order to filter the page menu, create a new version with a filter.
 */
class Twenty8teen_Walker_Page extends Walker_Page {
	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$page = $data_object;
		$current_page = $current_object_id;
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}
		if ( $depth ) {
			$indent = str_repeat( $t, $depth );
		} else {
			$indent = '';
		}

		$css_class = array( 'page_item', 'page-item-' . $page->ID );

		if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
			$css_class[] = 'page_item_has_children';
		}

		if ( ! empty( $current_page ) ) {
			$_current_page = get_post( $current_page );
			if ( $_current_page && in_array( $page->ID, $_current_page->ancestors ) ) {
				$css_class[] = 'current_page_ancestor';
			}
			if ( $page->ID == $current_page ) {
				$css_class[] = 'current_page_item';
			} elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
				$css_class[] = 'current_page_parent';
			}
		} elseif ( $page->ID == get_option( 'page_for_posts' ) ) {
			$css_class[] = 'current_page_parent';
		}

		/**
		 * Filters the list of CSS classes to include with each page item in the list.
		 *
		 * @since 2.8.0
		 *
		 * @see wp_list_pages()
		 *
		 * @param array   $css_class    An array of CSS classes to be applied
		 *                              to each list item.
		 * @param WP_Post $page         Page data object.
		 * @param int     $depth        Depth of page, used for padding.
		 * @param array   $args         An array of arguments.
		 * @param int     $current_page ID of the current page.
		 */
		$css_classes = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );
		$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

		if ( '' === $page->post_title ) {
			/* translators: %d: ID of a post */
			$page->post_title = sprintf( __( '#%d (no title)', 'twenty8teen' ), $page->ID );
		}

		$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
		$args['link_after'] = empty( $args['link_after'] ) ? '' : $args['link_after'];

		$atts = array();
		$atts['href'] = get_permalink( $page->ID );
		$atts['aria-current'] = ( $page->ID == $current_page ) ? 'page' : '';
		$atts = apply_filters( 'page_menu_link_attributes', $atts, $page, $depth, $args, $current_page );
		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$output .= $indent . '<li' . $css_classes . '>';

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $page->post_title, $page->ID );

		$item_output = '<a' . $attributes . '>';
		$item_output .= $args['link_before'] . $title . $args['link_after'];
		$item_output .= '</a>';

		$output .= apply_filters( 'walker_page_menu_start_el', $item_output, $page, $depth, $args );

		if ( ! empty( $args['show_date'] ) ) {
			if ( 'modified' === $args['show_date'] ) {
				$time = $page->post_modified;
			} else {
				$time = $page->post_date;
			}

			$date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
			$output .= ' ' . mysql2date( $date_format, $time );
		}
	}

}

/**
 * For admin post editor sidebar, explain featured images.
 */
function twenty8teen_admin_post_thumbnail_html($text) {
	return $text
		. '<p><small>'
		. esc_html__( 'Featured Image will display wherever the widget is placed.', 'twenty8teen' )
		. '</small></p>';
}
add_filter('admin_post_thumbnail_html','twenty8teen_admin_post_thumbnail_html');

/**
 * Mark the style sheets to be processed by prefixfree.
 */
function twenty8teen_prefixfree_style( $tag, $handle ) {
	$target = array( 'twenty8teen-style', get_stylesheet() . '-style', 'twenty8teen-editor' );
	if ( in_array( $handle, $target ) ) {
		$tag = str_replace( 'rel=', 'data-prefix="1" rel=', $tag );
	}
	return $tag;
}
add_filter( 'style_loader_tag', 'twenty8teen_prefixfree_style', 10, 2);

/**
 * Tell prefixfree to only process marked style sheets.
 */
function twenty8teen_prefixfree_script( $tag, $handle ) {
	if ( 'prefixfree' === $handle ) {
		$tag = str_replace( 'src=', 'data-prefix="1" src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'twenty8teen_prefixfree_script', 10, 2);
