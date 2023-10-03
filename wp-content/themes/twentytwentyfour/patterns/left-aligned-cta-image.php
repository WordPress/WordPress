<?php
/**
 * Title: Left Aligned Call to Action with Image
 * Slug: twentytwentyfour/left-aligned-cta-image
 * Categories: call-to-action
 * Viewport width: 1400
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Enhance your architectural journey with the Études Architect app.', 'sample heading for call to action', 'twentytwentyfour' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"style":{"typography":{"lineHeight":"1.75"}},"className":"is-style-checkmark-list"} -->
<ul class="is-style-checkmark-list" style="line-height:1.75"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'Collaborate with fellow architects.', 'A general list item.', 'twentytwentyfour' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Showcase your projects.', 'A general list item.', 'twentytwentyfour' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Experience the world of architecture.', 'A general list item.', 'twentytwentyfour' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php echo esc_html_x( 'Download app', 'sample content for call to action button', 'twentytwentyfour' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php echo esc_html_x( 'How it works', 'sample content for call to action button', 'twentytwentyfour' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
<figure class="wp-block-image size-full is-style-rounded"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/abstract-geometric-art.webp" alt="<?php esc_attr_e( 'White abstract geometric artwork from Dresden, Germany', 'twentytwentyfour' ); ?>" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
