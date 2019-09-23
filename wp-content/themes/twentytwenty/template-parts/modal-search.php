<?php
/**
 * Displays the search icon and modal
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

?>
<div class="search-modal cover-modal header-footer-group" data-modal-target-string=".search-modal" aria-expanded="false">

	<div class="search-modal-inner modal-inner">

		<div class="section-inner">

			<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>

			<form role="search" method="get" class="modal-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="<?php echo esc_attr( $unique_id ); ?>">
					<?php echo _x( 'Search for:', 'Label', 'twentytwenty' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core trusts translations ?>
				</label>
				<input type="search" id="<?php echo esc_attr( $unique_id ); ?>" class="search-field" placeholder="<?php echo esc_attr_x( 'Search for&hellip;', 'Placeholder', 'twentytwenty' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
				<button type="submit" class="search-submit"><?php echo _x( 'Search', 'Submit button', 'twentytwenty' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core trusts translations ?></button>
			</form><!-- .search-form -->

			<button class="toggle search-untoggle fill-children-current-color" data-toggle-target=".search-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field">
				<span class="screen-reader-text"><?php _e( 'Close search', 'twentytwenty' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction -- core trusts translations ?></span>
				<?php twentytwenty_the_theme_svg( 'cross' ); ?>
			</button><!-- .search-toggle -->

		</div><!-- .section-inner -->

	</div><!-- .search-modal-inner -->

</div><!-- .menu-modal -->
