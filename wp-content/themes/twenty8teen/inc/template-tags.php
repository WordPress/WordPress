<?php
/**
 * Custom template tags for this theme
 * @package Twenty8teen
 */

/**
 * Generate a CSS gradient unique to the given identifier.
 */
function twenty8teen_identimage( $identifier, $type = 'linear' ) {
	$valid_types = array(
		'linear' => 'linear-gradient(%1$udeg, %2$s)',
		'repeating-linear' => 'repeating-linear-gradient(%1$udeg,  %2$7.7s, %2$s)',
		'radial' => 'radial-gradient(circle farthest-corner at %4$u%% %5$u%%, %2$s)',
		'repeating-radial' => 'repeating-radial-gradient(circle farthest-corner at %4$u%% %5$u%%, %2$7.7s, %2$s)',
		// currently requires prefixfree Javascript polyfill
		'conic' => 'conic-gradient(from %3$ddeg, %2$s)',
		'repeating-conic' => 'repeating-conic-gradient(from %3$ddeg, %2$7.7s, %2$s)',
	);
	if ( empty( $identifier ) ) {
		return $identifier;
	}
	if ( ! array_key_exists( $type, $valid_types ) ) {
		$type = 'linear';
	}
	$hash = md5( $identifier );  // 32 hex characters.
	$angle = hexdec( substr( $hash, 0, 3 ) ) % 360;
	$add = hexdec( $hash[3] );
	$len = strlen( $hash ) - 6;
	for ( $k = 4; $k < $len; $k+=3 ) {
		$add += hexdec( $hash[$k+2] );
		$parts[] = sprintf( '#%6s %d%%', substr( $hash, $k, 6 ), $add );
	}

	$angle180 = ( $angle + 180 ) % 360;  // get opposite angle.
	$anglecss3 = ( $angle180 <= 90 ? 90 : 450 ) - $angle180;  // convert from standard to CSS3 angles.
	$xpercent = abs( ( cos( deg2rad( $anglecss3 ) ) + 1 ) / 2 ) *100;   // convert polar to Cartesian, normalize.
	$ypercent = abs( ( sin( deg2rad( $anglecss3 ) ) - 1 ) / 2 ) *100;

	return sprintf( $valid_types[$type], $angle, join( ',', $parts ), $angle+90,
		$xpercent, $ypercent );
}

/**
 * Add a gradient to an attributes array according to the passed option.
 */
function twenty8teen_add_gradient( $id, $attrs, $opt ) {
	if ( $id && $opt && 'none' !== $opt ) {
		$attrs = wp_parse_args( $attrs, array( 'style' => '', 'class' => '' ) );
		$default = twenty8teen_default_identimages();
		$default = $default['identimage_alpha'];
		$style = '; background-image: '
			. 'linear-gradient(rgba(255,255,255, var(--identimage_alpha, ' . $default . ')) 99%,rgba(255,255,255,0)),'
			. twenty8teen_identimage( $id, $opt );
		$attrs['style'] = trim( $attrs['style'] . $style, '; ' );
		$attrs['class'] .= ' identimage';
	}
	return $attrs;
}

/**
 * Provide a way to filter the HTML attributes being output
 */
function twenty8teen_attributes( $tag, $attrs = array(), $echo = true ) {
	if ( ! is_array( $attrs ) ) {
		// This can mess up nested quotes, but wp_parse_args will mess up all quotes.
		$attrs = str_replace( array( '="', "='", '" ', "' " ),
			array( '=', '=', '&', '&' ), trim( $attrs, "' \"" ) );
	}
	$attrs = wp_parse_args( $attrs );
	$attrs = apply_filters( 'twenty8teen_attributes', $attrs, $tag );
	$out = '';
	foreach ( $attrs as $attr => $value ) {
		if ( is_array( $value ) ) {
			$value = array_map( 'esc_attr', $value );
			$value = join( ' ', array_unique( $value ) );
			if ( ! empty( $value ) ) {
				$out .= sanitize_key( $attr ) . '="' . $value . '" ';
			}
		}
		else {
			$value = ( 'href' === $attr || 'src' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$out .= sanitize_key( $attr ) . '="' . $value . '" ';
		}
	}
	if ( $echo ) {
		echo $out;
	}
	return $out;
}


/**
 * Make a filename look nice for output in widget select element.
 */
function twenty8teen_nice_filename( $filename ) {
	return esc_html( ucwords( str_replace( array( '-', '_' ), ' ', $filename ) ) );
}

/**
 * Generate an array of theme file names from a folder,
 * key is base file name, value is nice name for output
 */
function twenty8teen_get_files( $subfolder = 'template-parts' ) {
	$files = (array) wp_get_theme()->get_files( 'php', 1, true );
	$files = preg_grep( "@{$subfolder}@", array_keys( $files ) );
	$choices = array();
	foreach ( $files as $file ) {
		$file = wp_basename( $file, '.php' );
		$choices[$file] = twenty8teen_nice_filename( $file );
	}
	return $choices;
}

/**
 * Get the post type and/or post format to propagate the template hierarchy.
 */
function twenty8teen_get_type_or_format() {
	$holder = array();
	$default_booleans = twenty8teen_default_booleans();
	if ( get_theme_mod( 'use_posttype_parts', $default_booleans['use_posttype_parts'] )) {
		$type = get_post_type();
		$types = get_post_types( array( 'public'=> true, '_builtin' => false ) );
		$types[] = 'page';
		$holder[] = in_array( $type, $types ) ? $type : '';
	}
	$holder[] = get_post_format();
	return implode( '-', array_filter( $holder ) );
}

/**
 * Generate an anchor link for an author.
 */
function twenty8teen_author_link( $anchor_template = '%s' ) {
	$a = '<a '
		. twenty8teen_attributes( 'a', array(
			'class' => 'fn n',
			'title' => __( 'Author archive', 'twenty8teen' ),
			'href' =>	get_author_posts_url( get_the_author_meta( 'ID' ) ),
			), false )
		. '>';
	$a = sprintf( $anchor_template, $a . esc_html( get_the_author() ) . '</a>' );
	return $a;
}

/**
 * Output the entry title.
 */
function twenty8teen_entry_title() {
	$this_post_type = get_post_type();
	$no_title = is_singular() && is_front_page() ? array( $this_post_type ) : array();
	if ( post_type_supports( $this_post_type, 'title' ) &&
		! in_array( $this_post_type, apply_filters( 'twenty8teen_posttypes_no_title', $no_title ) ) ) {

		if ( is_singular() ) {
			the_title( '<h1 ' . twenty8teen_attributes( 'h1', 'class="entry-title"',
				false ) . '>', '</h1>' );
		}
		else {
			the_title( '<h2 ' . twenty8teen_attributes( 'h2', 'class="entry-title"', false ) . '>' .
				 '<a ' . twenty8teen_attributes( 'a',
				 sprintf( 'href="%s" rel="bookmark"', get_permalink() ),
					false ) . '>',
				'</a></h2>' );
		}
	}
}

/**
 * Generate the entry date.
 */
function twenty8teen_entry_meta_date() {
	$this_post_type = get_post_type();
	$posted_on = '';
	if ( ! in_array( $this_post_type, apply_filters( 'twenty8teen_posttypes_no_date', array( 'page' ) ) ) ) {

		$attr = array( 'class' => 'entry-date published updated', 'datetime' => '%1$s' );
		$time_string = '<time ' . twenty8teen_attributes( 'time', $attr, false ) . '>%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$attr = array( 'class' => 'entry-date published', 'datetime' => '%1$s' );
			$time_string = '<time ' . twenty8teen_attributes( 'time', $attr, false ) . '>%2$s</time>';
			$attr = array( 'class' => 'updated', 'datetime' => '%3$s' );
			$time_string .= '<time ' . twenty8teen_attributes( 'time', $attr, false ) . '>%4$s</time>';
		}
		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);
		$posted_on = '<span class="posted-on"><a '
			. twenty8teen_attributes( 'a', array( 'href' => get_permalink() ), false )
			. '>' . $time_string . '</a></span> ';
	}
	return $posted_on;
}

/**
 * Generate the entry byline.
 */
function twenty8teen_entry_meta_byline() {
	$this_post_type = get_post_type();
	$byline = '';
	if ( post_type_supports( $this_post_type, 'author' ) &&
		! in_array( $this_post_type, apply_filters( 'twenty8teen_posttypes_no_author', array( 'page' ) ) ) ) {

		$byline = '<span '
			. twenty8teen_attributes( 'span', array( 'class' => 'byline author' ), false )
			.	'>' . twenty8teen_author_link(
				/* translators: %s: post author. */
				esc_html_x( 'by %s', 'post author', 'twenty8teen' ) ) . '</span>';
	}
	return $byline;
}

/**
 * Generate the attachment meta.
 * The 'wp_get_attachment_id3_keys' filter can be used to add more fields.
 */
function twenty8teen_attachment_meta( $args = array() ) {
	$args = wp_parse_args( $args, array (
		'post' => 0,
		'before' => '<p class="attachment-meta">',
		'template' => '%1$s: %2$s',
		'sep' => '<br />',
		'after' => '</p>',
		'echo' => true,
	) );
	$apost = get_post( $args['post'] );
	$out = '';
	$meta = (array) wp_get_attachment_metadata( $apost->ID );
	$labels = (array) wp_get_attachment_id3_keys( $apost );
	$labels = array_intersect_key( $labels, $meta );
	if ( ! empty( $labels ) ) {
		foreach ( $labels as $key => $value ) {
 			$labels[$key] = sprintf( $args['template'], $labels[$key], $meta[$key], $key );
 		}
		$out = $args['before'] . implode( $args['sep'], $labels ) . $args['after'];
 	}
 	if ( $args['echo'] ) { 
 		echo $out; 
 	}
 	return $out;
}

/**
 * Get the classes chosen for this area.
 */
function twenty8teen_area_classes( $area, $add = '', $echo = true ) {
	$defaults = twenty8teen_default_area_classes();
	$area_classes = array_merge( $defaults, get_theme_mod( 'area_classes', $defaults ) );
	$area_class = array_key_exists( $area, $area_classes ) ? $area_classes[$area] : '';
	$area_class = apply_filters( 'twenty8teen_area_classes', $area_class, $area, $add );
	$add = esc_attr( trim( preg_replace('/\s\s+/', ' ', $add . ' ' . $area_class ) ) );
	if ( $echo ) {
		echo $add ? ( 'class="' . $add . '"' ) : '';
	}
	return $add;
}
