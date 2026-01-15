<?php
/**
 * Title: Sidebar
 * Slug: twentytwentyfour/hidden-sidebar
 * Inserter: no
 */
?>
<!-- wp:group {"style":{"spacing":{"blockGap":"36px","padding":{"right":"0","left":"0"}}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-right:0;padding-left:0">
	<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group" style="margin-top:0;margin-bottom:0">
		<!-- wp:avatar {"size":80,"style":{"border":{"radius":"16px"}}} /-->

		<!-- wp:group {"style":{"spacing":{"blockGap":"16px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"style":{"typography":{"fontSize":"1.6rem"}}} -->
			<h2 class="wp-block-heading" style="font-size:1.6rem"><?php esc_html_e( 'About the author', 'twentytwentyfour' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:post-author-biography {"fontSize":"small"} /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:separator {"backgroundColor":"contrast","className":"is-style-wide"} -->
	<hr class="wp-block-separator has-text-color has-contrast-color has-alpha-channel-opacity has-contrast-background-color has-background is-style-wide"/>
	<!-- /wp:separator -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"16px"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"style":{"typography":{"fontSize":"1.6rem"}}} -->
		<h2 class="wp-block-heading" style="font-size:1.6rem"><?php esc_html_e( 'Popular Categories', 'twentytwentyfour' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:categories {"showHierarchy":true,"showPostCounts":true,"fontSize":"small"} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:separator {"backgroundColor":"contrast","className":"is-style-wide"} -->
	<hr class="wp-block-separator has-text-color has-contrast-color has-alpha-channel-opacity has-contrast-background-color has-background is-style-wide"/>
	<!-- /wp:separator -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"26px"}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group">
		<!-- wp:group {"style":{"spacing":{"blockGap":"16px"}},"layout":{"type":"flex","orientation":"vertical"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"style":{"typography":{"fontSize":"1.6rem"}}} -->
			<h2 class="wp-block-heading" style="font-size:1.6rem"><?php esc_html_e( 'Useful Links', 'twentytwentyfour' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size"><?php esc_html_e( 'Links I found useful and wanted to share.', 'twentytwentyfour' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"},"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"spacing":{"blockGap":"var:preset|spacing|10"}},"fontSize":"small"} -->
		<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Latest inflation report', 'twentytwentyfour' ); ?>","url":"#","className":"is-style-arrow-link","style":{"typography":{"textDecoration":"underline"}}} /-->
		<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Financial apps for families', 'twentytwentyfour' ); ?>","url":"#","className":"is-style-arrow-link","style":{"typography":{"textDecoration":"underline"}}} /-->
		<!-- /wp:navigation -->
	</div>
	<!-- /wp:group -->

	<!-- wp:separator {"backgroundColor":"contrast","className":"is-style-wide"} -->
	<hr class="wp-block-separator has-text-color has-contrast-color has-alpha-channel-opacity has-contrast-background-color has-background is-style-wide"/>
	<!-- /wp:separator -->

	<!-- wp:group {"style":{"spacing":{"blockGap":"16px"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"style":{"typography":{"fontSize":"1.6rem"}}} -->
		<h2 class="wp-block-heading" style="font-size:1.6rem"><?php esc_html_e( 'Search the website', 'twentytwentyfour' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:search {"label":"<?php echo esc_attr_x( 'Search', 'search form label', 'twentytwentyfour' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr_x( 'Search...', 'search form placeholder', 'twentytwentyfour' ); ?>","width":100,"widthUnit":"%","buttonText":"<?php echo esc_attr_x( 'Search', 'search form label', 'twentytwentyfour' ); ?>"} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"var:preset|spacing|10"} -->
	<div style="height:var(--wp--preset--spacing--10)" aria-hidden="true" class="wp-block-spacer">
	</div>
	<!-- /wp:spacer -->
</div>
<!-- /wp:group -->
