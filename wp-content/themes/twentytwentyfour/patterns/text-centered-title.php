<?php
/**
 * Title: Centered title
 * Slug: twentytwentyfour/text-centered-title
 * Categories: text, about
 * Viewport width: 1200
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained","contentSize":"800px"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:paragraph {"align":"center","style":{"typography":{"lineHeight":"1.2","fontStyle":"normal","fontWeight":"400"}},"fontSize":"x-large","fontFamily":"heading"} -->
<p class="has-text-align-center has-heading-font-family has-x-large-font-size" style="font-style:normal;font-weight:400;line-height:1.2"><em>
<?php
	/* Translators: About link placeholder */
	$about_link = '<a href="#" rel="nofollow">' . esc_html__( 'Money Studies', 'twentytwentyfour' ) . '</a>';
	echo sprintf(
		/* Translators: About text placeholder */
		esc_html__( 'I write about finance, management and economy, my book “%1$s” is out now.', 'twentytwentyfour' ),
		$about_link
	);
	?>

</em></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
