<?php
/**
 * Title: Link in bio with tight margins
 * Slug: twentytwentyfive/page-link-in-bio-with-tight-margins
 * Categories: twentytwentyfive_page, banner
 * Keywords: starter
 * Block Types: core/post-content
 * Post Types: page, wp_template
 * Viewport width: 1400
 * Description: A full-width, full-height link in bio section with an image, a paragraph and social links.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","className":"is-style-section-5","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}},"dimensions":{"minHeight":"100vh"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignfull is-style-section-5" style="min-height:100vh;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"align":"full","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignfull">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/link-in-bio-image.webp","alt":"Black and white photo focusing on a woman and a child from afar.","dimRatio":0,"customOverlayColor":"#818181","isUserOverlayColor":true,"minHeight":100,"minHeightUnit":"vh","isDark":false,"layout":{"type":"default"}} -->
			<div class="wp-block-cover is-light" style="min-height:100vh">
				<span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#818181"></span>
				<img class="wp-block-cover__image-background" alt="<?php esc_attr_e( 'Black and white photo focusing on a woman and a child from afar.', 'twentytwentyfive' ); ?>" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/link-in-bio-image.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container">

				<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
				<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->
			</div></div>
			<!-- /wp:cover -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"dimensions":{"minHeight":"100%"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"space-between","flexWrap":"nowrap","justifyContent":"stretch"}} -->
			<div class="wp-block-group" style="min-height:100%">
				<!-- wp:paragraph {"align":"left","style":{"typography":{"lineHeight":"1.2"}},"fontSize":"x-large"} -->
				<p class="has-text-align-left has-x-large-font-size" style="line-height:1.2"><?php esc_html_e( 'I’m Asahachi Kōno, a Japanese photographer, a member of Los Angeles’s Japanese Camera Pictorialists of California. Before returning to Japan, I worked as a photo retoucher.', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
				<div class="wp-block-group">
					<!-- wp:paragraph -->
					<p><a href="#"><?php esc_html_e( 'Instagram', 'twentytwentyfive' ); ?></a></p>
					<!-- /wp:paragraph -->

					<!-- wp:paragraph -->
					<p><a href="#"><?php echo esc_html_x( 'X', 'Refers to the social media platform formerly known as Twitter.', 'twentytwentyfive' ); ?></a></p>
					<!-- /wp:paragraph -->

					<!-- wp:paragraph -->
					<p><a href="#"><?php esc_html_e( 'TikTok', 'twentytwentyfive' ); ?></a></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
