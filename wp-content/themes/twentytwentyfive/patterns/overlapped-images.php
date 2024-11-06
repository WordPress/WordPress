<?php
/**
 * Title: Overlapping images and paragraph on right
 * Slug: twentytwentyfive/overlapped-images
 * Categories: about, featured
 * Description: A section with overlapping images, and a description.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","className":"is-style-section-1","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-style-section-1" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|80","left":"var:preset|spacing|80"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"45%","style":{"spacing":{"padding":{"right":"var:preset|spacing|50"}}}} -->
		<div class="wp-block-column" style="padding-right:var(--wp--preset--spacing--50);flex-basis:45%">
			<!-- wp:image {"sizeSlug":"full"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/red-hibiscus-closeup.webp" alt="<?php esc_attr_e( 'Photography close up of a red flower.', 'twentytwentyfive' ); ?>"/></figure>
			<!-- /wp:image -->
			<!-- wp:group {"style":{"spacing":{"margin":{"top":"-12vw"}}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group" style="margin-top:-12vw">
				<!-- wp:image {"width":"202px","sizeSlug":"full","align":"right","className":"is-resized","style":{"spacing":{"margin":{"right":"-5vw","left":"-5vw"}}}} -->
				<figure class="wp-block-image alignright size-full is-resized" style="margin-right:-5vw;margin-left:-5vw"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/grid-flower-2.webp" alt="<?php esc_attr_e( 'Black and white photography close up of a flower.', 'twentytwentyfive' ); ?>" style="width:202px"/></figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"center","width":"50%","style":{"spacing":{"padding":{"left":"0","right":"0"}}}} -->
		<div class="wp-block-column is-vertically-aligned-center" style="padding-right:0;padding-left:0;flex-basis:50%">
			<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group">
				<!-- wp:heading {"className":"is-style-text-annotation"} -->
				<h2 class="wp-block-heading is-style-text-annotation"><?php esc_html_e( 'About Us', 'twentytwentyfive' ); ?></h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"className":"is-style-text-subtitle"} -->
			<p class="is-style-text-subtitle">
			<?php
				printf(
					/* translators: %s is the brand name, e.g., 'Fleurs'. */
					esc_html__( '%s is a flower delivery and subscription business. Based in the EU, our mission is not only to deliver stunning flower arrangements across but also foster knowledge and enthusiasm on the beautiful gift of nature: flowers.', 'twentytwentyfive' ),
					'<strong>' . esc_html_x( 'Fleurs', 'Example brand name.', 'twentytwentyfive' ) . '</strong>'
				);
				?>
			</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
