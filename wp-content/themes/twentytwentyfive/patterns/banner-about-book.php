<?php
/**
 * Title: Banner with book description
 * Slug: twentytwentyfive/banner-about-book
 * Categories: banner
 * Description: Banner with book description and accompanying image for promotion.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"verticalAlignment":null,"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|70"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"verticalAlignment":"center","width":""} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:heading {"className":"wp-block-heading","fontSize":"xx-large"} -->
			<h2 class="wp-block-heading has-xx-large-font-size"><?php esc_html_e( 'About the book', 'twentytwentyfive' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"fontSize":"medium"} -->
			<p class="has-medium-font-size"><?php echo esc_html_x( 'This exquisite compilation showcases a diverse array of photographs that capture the essence of different eras and cultures, reflecting the unique styles and perspectives of each artist. Fleckenstein’s evocative imagery, Strand’s groundbreaking modernist approach, and Kōno’s meticulous documentation of Japanese life come together in a harmonious blend that celebrates the art of photography. Each image in “The Stories Book” is accompanied by insightful commentary, providing historical context and revealing the stories behind the photographs. This collection is not only a visual feast but also a tribute to the power of photography to preserve and narrate the multifaceted experiences of humanity.', 'Pattern placeholder text.', 'twentytwentyfive' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"","layout":{"type":"default"}} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/book-image-landing.webp" alt="<?php esc_attr_e( 'Image of a book', 'twentytwentyfive' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
