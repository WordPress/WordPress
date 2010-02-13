<?php
/**
 * Deprecated admin functions from past WordPress versions. You shouldn't use these
 * functions and look for the alternatives instead. The functions will be removed
 * in a later version.
 *
 * @package WordPress
 * @subpackage Deprecated
 */

/*
 * Deprecated functions come here to die.
 */

/**
 * @since 2.1
 * @deprecated 2.1
 * @deprecated Use wp_tiny_mce().
 * @see wp_tiny_mce()
 */
function tinymce_include() {
	_deprecated_function( __FUNCTION__, '2.1', 'wp_tiny_mce()' );

	wp_tiny_mce();
}

/**
 * Unused Admin function.
 *
 * @since 2.0
 * @deprecated 2.5
 *
 */
function documentation_link() {
	_deprecated_function( __FUNCTION__, '2.5', '' );
	return;
}

/**
 * Calculates the new dimentions for a downsampled image.
 *
 * @since 2.0.0
 * @deprecated 3.0.0
 * @deprecated Use wp_constrain_dimensions()
 *
 * @param int $width Current width of the image
 * @param int $height Current height of the image
 * @param int $wmax Maximum wanted width
 * @param int $hmax Maximum wanted height
 * @return mixed Array(height,width) of shrunk dimensions.
 */
function wp_shrink_dimensions( $width, $height, $wmax = 128, $hmax = 96 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_constrain_dimensions()' );
	return wp_constrain_dimensions( $width, $height, $wmax, $hmax );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 * @deprecated unknown
 * @deprecated Use wp_category_checklist()
 * @see wp_category_checklist()
 *
 * @param unknown_type $default
 * @param unknown_type $parent
 * @param unknown_type $popular_ids
 */
function dropdown_categories( $default = 0, $parent = 0, $popular_ids = array() ) {
	_deprecated_function( __FUNCTION__, '0.0', 'wp_category_checklist()' );
	global $post_ID;
	wp_category_checklist( $post_ID );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 * @deprecated unknown
 * @deprecated Use wp_link_category_checklist()
 * @see wp_link_category_checklist()
 *
 * @param unknown_type $default
 */
function dropdown_link_categories( $default = 0 ) {
	_deprecated_function( __FUNCTION__, '0.0', 'wp_link_category_checklist()' );
	global $link_id;
	wp_link_category_checklist( $link_id );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 * @deprecated 3.0.0
 * @deprecated Use wp_dropdown_categories()
 * @see wp_dropdown_categories()
 *
 * @param unknown_type $currentcat
 * @param unknown_type $currentparent
 * @param unknown_type $parent
 * @param unknown_type $level
 * @param unknown_type $categories
 * @return unknown
 */
function wp_dropdown_cats( $currentcat = 0, $currentparent = 0, $parent = 0, $level = 0, $categories = 0 ) {
	_deprecated_function( __FUNCTION__, '3.0', 'wp_dropdown_categories()' );
	if (!$categories )
		$categories = get_categories( array('hide_empty' => 0) );

	if ( $categories ) {
		foreach ( $categories as $category ) {
			if ( $currentcat != $category->term_id && $parent == $category->parent) {
				$pad = str_repeat( '&#8211; ', $level );
				$category->name = esc_html( $category->name );
				echo "\n\t<option value='$category->term_id'";
				if ( $currentparent == $category->term_id )
					echo " selected='selected'";
				echo ">$pad$category->name</option>";
				wp_dropdown_cats( $currentcat, $currentparent, $category->term_id, $level +1, $categories );
			}
		}
	} else {
		return false;
	}
}

?>