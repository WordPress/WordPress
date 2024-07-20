<?php
/**
 * Title: Centered statement, small
 * Slug: twentytwentyfour/text-centered-statement-small
 * Categories: text, about
 * Keywords: mission, introduction
 * Viewport width: 1200
 * Description: A centered italic text statement with compact padding.
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">

	<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"x-large","level":1} -->
	<h1 class="wp-block-heading has-text-align-center has-x-large-font-size">
		<em>
		<?php
		/* Translators: About link placeholder */
			$about_link = '<a href="#" rel="nofollow">' . esc_html__( 'Money Studies', 'twentytwentyfour' ) . '</a>';
			echo sprintf(
				/* Translators: About text placeholder */
				esc_html__( 'I write about finance, management and economy, my book “%1$s” is out now.', 'twentytwentyfour' ),
				$about_link
			);
			?>
		</em>
	</h1>
	<!-- /wp:heading -->
</div>
<!-- /wp:group -->
