<?php
/**
 * Title: Project details
 * Slug: twentytwentyfour/text-project-details
 * Categories: text, portfolio
 * Viewport width: 1400
 * Description: A text only section for project details.
 */
?>

<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"0","bottom":"0"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"40%","layout":{"type":"constrained","contentSize":"260px","justifyContent":"left"}} -->
		<div class="wp-block-column" style="flex-basis:40%">
			<!-- wp:paragraph -->
			<p><?php echo esc_html_x( 'The revitalized art gallery is set to redefine cultural landscape.', 'Title text for the feature area', 'twentytwentyfour' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"60%","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
		<div class="wp-block-column" style="flex-basis:60%">

			<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.2"}},"fontSize":"x-large","fontFamily":"heading"} -->
			<p class="has-heading-font-family has-x-large-font-size" style="line-height:1.2"><?php echo esc_html_x( 'With meticulous attention to detail and a commitment to excellence, we create spaces that inspire, elevate, and enrich the lives of those who inhabit them.', 'Descriptive title for the feature area', 'twentytwentyfour' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
			<div class="wp-block-columns">
				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:paragraph {"style":{"layout":{"selfStretch":"fill","flexSize":null}}} -->
					<p><?php echo esc_html_x( 'The revitalized Art Gallery is set to redefine the cultural landscape of Toronto, serving as a nexus of artistic expression, community engagement, and architectural marvel. The expansion and renovation project pay homage to the Art Gallery\'s rich history while embracing the future, ensuring that the gallery remains a beacon of inspiration.', 'Descriptive text for the feature area', 'twentytwentyfour' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:paragraph -->
					<p><?php echo esc_html_x( 'The revitalized Art Gallery is set to redefine the cultural landscape of Toronto, serving as a nexus of artistic expression, community engagement, and architectural marvel. The expansion and renovation project pay homage to the Art Gallery\'s rich history while embracing the future, ensuring that the gallery remains a beacon of inspiration.', 'Descriptive text for the feature area', 'twentytwentyfour' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
