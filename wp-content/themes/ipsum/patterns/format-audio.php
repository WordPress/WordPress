<?php
/**
 * Title: Format Audio
 * Slug: ipsum/format-audio
 * Categories: posts
 * Viewport width: 1280
 * Description: An audio-format post body — cover art, title and a one-line description over the player.
 *
 * @package Ipsum
 * @since Ipsum 1.0
 */

?>
<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Format Audio', 'Name of the audio format group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Format Audio Wrapper', 'Name of the audio format card group', 'ipsum' ); ?>"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":{"topLeft":"10px","topRight":"10px","bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"theme-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-theme-5-background-color has-background" style="border-top-left-radius:10px;border-top-right-radius:10px;border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/ipsum-monobloc-front.webp?ver=<?php echo esc_attr( wp_get_theme( get_template() )->get( 'Version' ) ); ?>" alt="<?php echo esc_attr_x( 'A blurred plastic chair in blue and cream tones.', 'Alt text for the monobloc chair image', 'ipsum' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Title and Description', 'Name of the title and description group', 'ipsum' ); ?>"},"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"large"} -->
<h2 class="wp-block-heading has-large-font-size"><?php esc_html_e( 'The keyboard, slowed down', 'ipsum' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'The first recording to make it into a post: three minutes of typing, stretched until it sounds like weather', 'ipsum' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:audio -->
<figure class="wp-block-audio"><audio controls></audio></figure>
<!-- /wp:audio --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
