<?php
/**
 * Title: Text Alternating
 * Slug: ipsum/text-alternating
 * Categories: text, about
 * Viewport width: 1280
 * Description: Rows alternating image and text — introductions for the blog’s sections.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Text Alternating', 'Name of the Text Alternating element', 'ipsum' ); ?>"},"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","focalPoint":{"x":0.5,"y":0.5},"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-cover-essays.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'Pale branching channels running through dark green moss, seen from above.', 'Alt text for the Essays section image', 'ipsum' ); ?>" style="aspect-ratio:4/3;object-fit:cover;object-position:50% 50%"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Paragraph', 'Name of the Title and Paragraph element', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"border":{"bottom":{"color":"var:preset|color|theme-5","width":"5px"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="border-bottom-color:var(--wp--preset--color--theme-5);border-bottom-width:5px"><?php esc_html_e( 'Essays', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'Longer thoughts, written slowly and published when they’re ready. They start somewhere small—a sentence misheard, a note in a margin—and take their time getting to the point.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Paragraph', 'Name of the Title and Paragraph element', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"border":{"bottom":{"color":"var:preset|color|theme-5","width":"5px"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="border-bottom-color:var(--wp--preset--color--theme-5);border-bottom-width:5px"><?php esc_html_e( 'Pictures', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'Photographs are placeholders too, in their way—each one standing in for the hour it was taken. These stand for the hours between posts.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","focalPoint":{"x":0.5,"y":0.5},"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-cover-pictures.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'Close-up of ruffled white petals against a dark green background.', 'Alt text for the Pictures section image', 'ipsum' ); ?>" style="aspect-ratio:4/3;object-fit:cover;object-position:50% 50%"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","focalPoint":{"x":0.5,"y":0.5},"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-cover-sounds.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'Pale winding lines crossing dark green grass, seen from above.', 'Alt text for the Sounds section image', 'ipsum' ); ?>" style="aspect-ratio:4/3;object-fit:cover;object-position:50% 50%"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Paragraph', 'Name of the Title and Paragraph element', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:heading {"style":{"border":{"bottom":{"color":"var:preset|color|theme-5","width":"5px"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="border-bottom-color:var(--wp--preset--color--theme-5);border-bottom-width:5px"><?php esc_html_e( 'Sounds', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e( 'Writing has a soundtrack, even when it’s just the keyboard. Sometimes a recording makes it into a post; this is where those live.', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
