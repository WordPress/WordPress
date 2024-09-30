<?php
/**
 * Title: Hero, overlapped book cover with links
 * Slug: twentytwentyfive/hero-overlapped-book-cover-with-links
 * Categories: banner, featured
 * Description: A hero with an overlapped book cover and links.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>

<!-- wp:cover {"isUserOverlayColor":true,"customGradient":"linear-gradient(180deg,rgb(251,250,243) 51%,rgb(255,255,255) 51%)","contentPosition":"center center","align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}},"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"textColor":"contrast","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-contrast-color has-text-color has-link-color" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--50)">
	<span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim has-background-gradient" style="background:linear-gradient(180deg,rgb(251,250,243) 51%,rgb(255,255,255) 51%)"></span>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
		<div class="wp-block-group alignwide">
			<!-- wp:columns {"verticalAlignment":"top","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|80","left":"var:preset|spacing|80"}}}} -->
			<div class="wp-block-columns alignwide are-vertically-aligned-top">
				<!-- wp:column {"verticalAlignment":"top","width":"55%"} -->
				<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:55%">
					<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"left","flexWrap":"nowrap","verticalAlignment":"top"}} -->
					<div class="wp-block-group">
						<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
						<div class="wp-block-group">
							<!-- wp:heading {"fontSize":"xx-large"} -->
							<h2 class="wp-block-heading has-xx-large-font-size">
								<?php echo esc_html_x( 'The Stories Book', 'Hero - Overlapped book cover pattern headline text', 'twentytwentyfive' ); ?>
							</h2>
							<!-- /wp:heading -->
							<!-- wp:paragraph {"className":"is-style-text-subtitle"} -->
							<p class="is-style-text-subtitle">
								<?php echo esc_html_x( 'A fine collection of moments in time featuring photographs from Louis Fleckenstein, Paul Strand and Asahachi Kōno.', 'Hero - Overlapped book cover pattern subline text', 'twentytwentyfive' ); ?>
							</p>
							<!-- /wp:paragraph -->
						</div>
						<!-- /wp:group -->

						<!-- wp:spacer {"style":{"layout":{"selfStretch":"fit","flexSize":null},"spacing":{"margin":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}}} -->
						<div style="margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--70)" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer -->

						<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
						<div class="wp-block-group">
							<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|20","left":"var:preset|spacing|20"}}}} -->
							<div class="wp-block-columns">
								<!-- wp:column {"verticalAlignment":"stretch"} -->
								<div class="wp-block-column is-vertically-aligned-stretch">
									<!-- wp:buttons {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"horizontal","flexWrap":"wrap","justifyContent":"space-between"}} -->
									<div class="wp-block-buttons">
										<!-- wp:button {"width":100,"className":"is-style-fill"} -->
										<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill">
											<a class="wp-block-button__link wp-element-button" href="#">
												<?php echo esc_html_x( 'Amazon', 'Hero - Overlapped book cover pattern button 1', 'twentytwentyfive' ); ?>
											</a>
										</div>
										<!-- /wp:button -->
										<!-- wp:button {"width":100,"className":"is-style-fill"} -->
										<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill">
											<a class="wp-block-button__link wp-element-button" href="#">
												<?php echo esc_html_x( 'Apple Books', 'Hero - Overlapped book cover pattern button 2', 'twentytwentyfive' ); ?>
											</a>
										</div>
										<!-- /wp:button -->
									</div>
									<!-- /wp:buttons -->
								</div>
								<!-- /wp:column -->
								<!-- wp:column {"verticalAlignment":"stretch"} -->
								<div class="wp-block-column is-vertically-aligned-stretch">
									<!-- wp:buttons {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"horizontal","flexWrap":"wrap","justifyContent":"space-between"}} -->
									<div class="wp-block-buttons">
										<!-- wp:button {"width":100,"className":"is-style-fill"} -->
										<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill">
											<a class="wp-block-button__link wp-element-button" href="#">
												<?php echo esc_html_x( 'Audible', 'Hero - Overlapped book cover pattern button 3', 'twentytwentyfive' ); ?>
											</a>
										</div>
										<!-- /wp:button -->
										<!-- wp:button {"width":100,"className":"is-style-fill"} -->
										<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill">
											<a class="wp-block-button__link wp-element-button" href="#">
												<?php echo esc_html_x( 'Barnes & Noble', 'Hero - Overlapped book cover pattern button 4', 'twentytwentyfive' ); ?>
											</a>
										</div>
										<!-- /wp:button -->
									</div>
									<!-- /wp:buttons -->
								</div>
								<!-- /wp:column -->
							</div>
							<!-- /wp:columns -->

							<!-- wp:spacer {"style":{"layout":{"selfStretch":"fit","flexSize":null},"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
							<div style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
							<!-- /wp:spacer -->

							<!-- wp:paragraph {"fontSize":"medium"} -->
							<p class="has-medium-font-size">
								<?php echo wp_kses_post( _x( 'Outside Europe? View <a href="#" rel="nofollow">international editions</a>.', 'Hero - Overlapped book cover pattern bottom text', 'twentytwentyfive' ) ); ?>
							</p>
							<!-- /wp:paragraph -->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"verticalAlignment":"top","width":"45%"} -->
				<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:45%">
					<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image size-full">
						<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/book-image.webp" alt="<?php echo esc_attr__( 'Book Image', 'twentytwentyfive' ); ?>" style="aspect-ratio:3/4;object-fit:cover"/>
					</figure>
					<!-- /wp:image -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
