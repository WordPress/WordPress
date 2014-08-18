<?php
/**
 * The template for displaying search forms
 *
 * @package Theme Meme
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="form-group">
		<input type="search" class="form-control" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'themememe' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'themememe' ); ?>">
	</div>
	<div class="form-submit">
		<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>
	</div>
</form>