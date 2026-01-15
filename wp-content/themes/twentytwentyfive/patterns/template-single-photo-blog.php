<?php
/**
 * Title: Photo blog single post
 * Slug: twentytwentyfive/template-single-photo-blog
 * Template Types: posts, single
 * Viewport width: 1400
 * Inserter: no
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:template-part {"slug":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--60)">
	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
		<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
		<div class="wp-block-columns alignwide">
			<!-- wp:column {"width":"60%"} -->
			<div class="wp-block-column" style="flex-basis:60%">
				<!-- wp:post-title {"level":1} /-->
				</div>
			<!-- /wp:column -->
			<!-- wp:column {"width":"40%"} -->
			<div class="wp-block-column" style="flex-basis:40%">
				<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"top"}} -->
				<div class="wp-block-group">
					<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical"}} -->
					<div class="wp-block-group">
						<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"fontSize":"small","layout":{"type":"constrained"}} -->
						<div class="wp-block-group has-small-font-size">
							<!-- wp:paragraph --><p><?php echo esc_html_x( 'Published on', 'Prefix before the post date block.', 'twentytwentyfive' ); ?></p><!-- /wp:paragraph -->
							<!-- wp:post-date {"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}},"textColor":"contrast"} /-->
						</div>
						<!-- /wp:group -->
						<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"fontSize":"small","layout":{"type":"constrained"}} -->
						<div class="wp-block-group has-small-font-size">
							<!-- wp:paragraph --><p><?php echo esc_html_x( 'Posted by', 'Prefix before the author name. The post author name is displayed in a separate block on the next line.', 'twentytwentyfive' ); ?></p><!-- /wp:paragraph -->
							<!-- wp:post-author-name {"isLink":true} /-->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
					<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","orientation":"vertical"}} -->
					<div class="wp-block-group">
						<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
						<div class="wp-block-group">
							<!-- wp:paragraph {"fontSize":"small"} -->
							<p class="has-small-font-size"><?php echo esc_html_x( 'Categories:', 'Prefix before one or more categories. The categories are displayed in a separate block on the next line.', 'twentytwentyfive' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:post-terms {"term":"category","style":{"typography":{"fontStyle":"normal","fontWeight":"300"}}} /-->
						</div>
						<!-- /wp:group -->
						<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
						<div class="wp-block-group">
							<!-- wp:paragraph {"fontSize":"small"} -->
							<p class="has-small-font-size"><?php echo esc_html_x( 'Tagged:', 'Prefix before one or more tags. The tags are displayed in a separate block on the next line.', 'twentytwentyfive' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:post-terms {"term":"post_tag","style":{"typography":{"fontStyle":"normal","fontWeight":"300"}}} /-->
						</div>
					<!-- /wp:group -->
					</div>
				<!-- /wp:group -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
		<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50","bottom":"0"}}},"layout":{"type":"default"}} -->
		<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50);margin-bottom:0">
			<!-- wp:group {"ariaLabel":"<?php esc_attr_e( 'Post navigation', 'twentytwentyfive' ); ?>","tagName":"nav","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
			<nav aria-label="<?php esc_attr_e( 'Post navigation', 'twentytwentyfive' ); ?>" class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
				<!-- wp:post-navigation-link {"type":"previous","label":"<?php esc_html_e( 'Previous Photo', 'twentytwentyfive' ); ?>","fontSize":"small"} /-->
				<!-- wp:post-navigation-link {"label":"<?php esc_html_e( 'Next Photo', 'twentytwentyfive' ); ?>","fontSize":"small"} /-->
			</nav>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
		<!-- wp:post-featured-image {"aspectRatio":"auto","align":"wide"} /-->
		</div>
	<!-- /wp:group -->

	<!-- wp:post-content {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} /-->

	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:pattern {"slug":"twentytwentyfive/comments"} /-->
	</div>
	<!-- /wp:group -->
</main>
<!-- /wp:group -->
<!-- wp:template-part {"slug":"footer"} /-->
