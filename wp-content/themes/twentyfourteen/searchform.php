<?php
/**
 * The template for displaying search forms in Twenty Fourteen
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'twentyfourteen' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'twentyfourteen' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'twentyfourteen' ); ?>">
	</label>
	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'twentyfourteen' ); ?>">
</form>
