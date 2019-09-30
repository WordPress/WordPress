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

			<?php
			get_search_form(
				array(
					'label' => _x( 'Search for:', 'Label', 'twentytwenty' ),
				)
			);
			?>

			<button class="toggle search-untoggle fill-children-current-color" data-toggle-target=".search-modal" data-toggle-screen-lock="true" data-toggle-body-class="showing-search-modal" data-set-focus=".search-modal .search-field">
				<span class="screen-reader-text"><?php _e( 'Close search', 'twentytwenty' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction -- core trusts translations ?></span>
				<?php twentytwenty_the_theme_svg( 'cross' ); ?>
			</button><!-- .search-toggle -->

		</div><!-- .section-inner -->

	</div><!-- .search-modal-inner -->

</div><!-- .menu-modal -->
