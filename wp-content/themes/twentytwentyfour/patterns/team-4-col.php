<?php
/**
 * Title: Team members, 4 columns
 * Slug: twentytwentyfour/team-4-col
 * Categories: team, about
 * Viewport width: 1400
 * Description: A team section, with a heading, a paragraph, and 4 columns for team members.
 */
?>

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'Team members', 'Name of team pattern', 'twentytwentyfour' ); ?>"},"align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","fontSize":"xx-large"} -->
		<h2 class="wp-block-heading has-text-align-center has-xx-large-font-size"><?php echo esc_html_x( 'Meet our team', 'Sample heading for the team pattern', 'twentytwentyfour' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center"><?php echo esc_html_x( 'Our comprehensive suite of professionals caters to a diverse team, ranging from seasoned architects to renowned engineers.', 'Sample descriptive text of the team pattern', 'twentytwentyfour' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
	<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer">
	</div>
	<!-- /wp:spacer -->

	<!-- wp:columns {"align":"wide","style":{"spacing":{"padding":{"right":"0","left":"0"},"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|30"}}}} -->
	<div class="wp-block-columns alignwide" style="padding-right:0;padding-left:0">
		<!-- wp:column {"layout":{"type":"default"}} -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
			<figure class="wp-block-image size-full is-style-rounded">
				<img alt="" style="aspect-ratio:1;object-fit:cover" />
			</figure>
			<!-- /wp:image -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|0"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"center"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size">
					<strong><?php echo esc_html_x( 'Francesca Piovani', 'Sample name of a team member', 'twentytwentyfour' ); ?></strong>
				</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size"><?php echo esc_html_x( 'Founder, CEO & Architect', 'Sample role of a team member', 'twentytwentyfour' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"layout":{"type":"default"}} -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
			<figure class="wp-block-image size-full is-style-rounded">
				<img alt="" style="aspect-ratio:1;object-fit:cover" />
			</figure>
			<!-- /wp:image -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size">
					<strong><?php echo esc_html_x( 'Rhye Moore', 'Sample name of a team member', 'twentytwentyfour' ); ?></strong>
				</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size"><?php echo esc_html_x( 'Engineering Manager', 'Sample role of a team member', 'twentytwentyfour' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"layout":{"type":"default"}} -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
			<figure class="wp-block-image size-full is-style-rounded">
				<img alt="" style="aspect-ratio:1;object-fit:cover" />
			</figure>
			<!-- /wp:image -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size">
					<strong><?php echo esc_html_x( 'Helga Steiner', 'Sample name of a team member', 'twentytwentyfour' ); ?></strong>
				</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size"><?php echo esc_html_x( 'Architect', 'Sample role of a team member', 'twentytwentyfour' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"layout":{"type":"default"}} -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
			<figure class="wp-block-image size-full is-style-rounded">
				<img alt="" style="aspect-ratio:1;object-fit:cover" />
			</figure>
			<!-- /wp:image -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|0"}},"layout":{"type":"flex","orientation":"vertical","flexWrap":"nowrap","justifyContent":"center"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size">
					<strong><?php echo esc_html_x( 'Ivan Lawrence', 'Sample name of a team member', 'twentytwentyfour' ); ?></strong>
				</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
				<p class="has-text-align-center has-small-font-size"><?php echo esc_html_x( 'Project Manager', 'Sample role of a team member', 'twentytwentyfour' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
