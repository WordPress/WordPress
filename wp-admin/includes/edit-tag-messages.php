<?php
/**
 * Edit Tags Administration: Messages
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

$messages = array();
// 0 = unused. Messages start at index 1.
$messages['_item'] = array(
	0 => '',
	1 => __( 'Item added.' ),
	2 => __( 'Item deleted.' ),
	3 => __( 'Item updated.' ),
	4 => __( 'Item not added.' ),
	5 => __( 'Item not updated.' ),
	6 => __( 'Items deleted.' ),
);

$messages['category'] = array(
	0 => '',
	1 => __( 'Category added.' ),
	2 => __( 'Category deleted.' ),
	3 => __( 'Category updated.' ),
	4 => __( 'Category not added.' ),
	5 => __( 'Category not updated.' ),
	6 => __( 'Categories deleted.' ),
);

$messages['post_tag'] = array(
	0 => '',
	1 => __( 'Tag added.' ),
	2 => __( 'Tag deleted.' ),
	3 => __( 'Tag updated.' ),
	4 => __( 'Tag not added.' ),
	5 => __( 'Tag not updated.' ),
	6 => __( 'Tags deleted.' ),
);

/**
 * Filters the messages displayed when a tag is updated.
 *
 * @since 3.7.0
 *
 * @param array $messages The messages to be displayed.
 */
$messages = apply_filters( 'term_updated_messages', $messages );

$message = false;
if ( isset( $_REQUEST['message'] ) && (int) $_REQUEST['message'] ) {
	$msg = (int) $_REQUEST['message'];
	if ( isset( $messages[ $taxonomy ][ $msg ] ) ) {
		$message = $messages[ $taxonomy ][ $msg ];
	} elseif ( ! isset( $messages[ $taxonomy ] ) && isset( $messages['_item'][ $msg ] ) ) {
		$message = $messages['_item'][ $msg ];
	}
}
