<?php
/**
 * The format of a post, for the post types supporting `post-formats` when
 * the theme supports them. It is plain data: it has no JavaScript parts.
 *
 * The elements are the formats the theme opted in, plus `standard`, which
 * every theme supporting formats gets, as the themes REST route exposes
 * them. Their labels are the ones WordPress already translates for post
 * formats, and they are sorted by label, as the client field sorted them.
 *
 * @package WordPress
 */

$format_labels = get_post_format_strings();

// A theme declares its formats as the first argument of the support. It may
// also declare the support without arguments, or name a format WordPress
// does not have.
$format_support = get_theme_support( 'post-formats' );
$theme_formats  = is_array( $format_support ) && isset( $format_support[0] ) && is_array( $format_support[0] )
	? $format_support[0]
	: array();

$formats  = array_intersect( array_merge( array( 'standard' ), $theme_formats ), array_keys( $format_labels ) );
$elements = array();
foreach ( array_unique( $formats ) as $format ) {
	$elements[] = array(
		'value' => $format,
		'label' => $format_labels[ $format ],
	);
}
usort(
	$elements,
	static function ( $a, $b ) {
		return strcasecmp( $a['label'], $b['label'] );
	}
);

return array(
	'type'          => 'text',
	'label'         => __( 'Format' ),
	'Edit'          => 'radio',
	'elements'      => $elements,
	'enableSorting' => false,
	'enableHiding'  => false,
	'filterBy'      => false,
);
