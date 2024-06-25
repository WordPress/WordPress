<?php
/**
 * Title: Centered statement
 * Slug: twentytwentyfour/text-centered-statement
 * Categories: text, about, featured
 * Keywords: mission, introduction
 * Viewport width: 1400
 * Description: A centered text statement with a large paddings.
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|60","right":"var:preset|spacing|60"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"base-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-2-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--60)">
	<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:paragraph {"align":"center","style":{"typography":{"lineHeight":"1.2","fontStyle":"normal","fontWeight":"400"}},"fontSize":"x-large","fontFamily":"heading"} -->
		<p class="has-text-align-center has-heading-font-family has-x-large-font-size" style="font-style:normal;font-weight:400;line-height:1.2"><?php echo wp_kses_post( __( '<em>Études</em> is not confined to the past—we are passionate about the cutting edge designs shaping our world today.', 'twentytwentyfour' ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
