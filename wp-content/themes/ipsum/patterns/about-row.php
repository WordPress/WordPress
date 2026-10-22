<?php
/**
 * Title: About Row
 * Slug: ipsum/about-row
 * Categories: about
 * Viewport width: 1280
 * Description: An about section on a dark band — the blog’s story beside the author’s square portrait, under a fit-text heading.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'About Row', 'Name of the about row group', 'ipsum' ); ?>"},"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|theme-1"}}},"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"theme-6","textColor":"theme-1","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-theme-1-color has-theme-6-background-color has-text-color has-background has-link-color" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"bottom","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|60","left":"var:preset|spacing|80"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-bottom"><!-- wp:column {"verticalAlignment":"bottom","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:50%"><!-- wp:heading {"fitText":true} -->
<h2 class="wp-block-heading has-fit-text"><?php esc_html_e( 'About this blog', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"medium"} -->
<p class="has-medium-font-size"><?php esc_html_e( 'This blog belongs to Lorem—teacher by day, keeper of a quiet corner of the web by night. It runs on three sections: essays that take their time, pictures standing in for the hours between posts, and the occasional sound worth pressing play on.', 'ipsum' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"medium"} -->
<p class="has-medium-font-size"><?php esc_html_e( 'Nothing here is optimized, scheduled, or sponsored; posts appear when they’re ready and stay up long after the trends move on. The name is borrowed from the oldest placeholder in publishing, and the page tries to live up to it—holding its shape until your own words move in.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","width":"50%","layout":{"type":"default"}} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:50%"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-portrait-editorial.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php esc_attr_e( 'Portrait of the author.', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
