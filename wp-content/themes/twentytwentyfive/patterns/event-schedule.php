<?php
/**
 * Title: Event schedule
 * Slug: twentytwentyfive/event-schedule
 * Categories: about, media, featured
 * Description: A section with specified dates and times for an event.
 * Keywords: events, agenda, schedule, lectures
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading {"fontSize":"xx-large"} -->
		<h2 class="wp-block-heading has-xx-large-font-size">Agenda</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>These are some of the upcoming events.</p>
		<!-- /wp:paragraph -->
		<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
		<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
		<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"top":{"color":"var:preset|color|accent-6","width":"1px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
		<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--accent-6);border-top-width:1px;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
			<!-- wp:columns -->
			<div class="wp-block-columns">
				<!-- wp:column {"verticalAlignment":"top","width":"40%"} -->
				<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:40%">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading">Friday, Feb. 1</h3>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"verticalAlignment":"top","width":"60%"} -->
				<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:60%">
					<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
					<div class="wp-block-columns is-not-stacked-on-mobile" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)">
						<!-- wp:column {"width":"33.33%"} -->
						<div class="wp-block-column" style="flex-basis:33.33%">
							<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"layout":{"selfStretch":"fixed","flexSize":"270px"}}} -->
							<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/marshland-birds-square.webp" alt="Birds on a lake." style="aspect-ratio:1;object-fit:cover"/></figure>
							<!-- /wp:image -->
						</div>
						<!-- /wp:column -->
						<!-- wp:column {"width":"66.66%"} -->
						<div class="wp-block-column" style="flex-basis:66.66%">
							<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
							<div class="wp-block-group">
								<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
								<div class="wp-block-group">
									<!-- wp:heading {"level":4} -->
									<h4 class="wp-block-heading"><a href="#">Fauna from North America and its characteristics</a></h4>
									<!-- /wp:heading -->
									<!-- wp:paragraph -->
									<p>9 AM — 11 AM</p>
									<!-- /wp:paragraph -->
								</div>
								<!-- /wp:group -->
								<!-- wp:paragraph {"fontSize":"small"} -->
								<p class="has-small-font-size">Lecture by <a href="#">Prof. Fiona Presley</a></p>
								<!-- /wp:paragraph -->
							</div>
							<!-- /wp:group -->
						</div>
						<!-- /wp:column -->
					</div>
					<!-- /wp:columns -->
					<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
					<div class="wp-block-columns is-not-stacked-on-mobile" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)">
						<!-- wp:column {"width":"33.33%"} -->
						<div class="wp-block-column" style="flex-basis:33.33%">
							<!-- wp:image {"id":2772,"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"layout":{"selfStretch":"fixed","flexSize":"270px"}}} -->
							<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/coral-square.webp" alt="View of the deep ocean." class="wp-image-2772" style="aspect-ratio:1;object-fit:cover"/></figure>
							<!-- /wp:image -->
						</div>
						<!-- /wp:column -->
						<!-- wp:column {"width":"66.66%"} -->
						<div class="wp-block-column" style="flex-basis:66.66%">
							<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
							<div class="wp-block-group">
								<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
								<div class="wp-block-group">
									<!-- wp:heading {"level":4} -->
									<h4 class="wp-block-heading"><a href="#">Things you didn’t know about the deep ocean</a></h4>
									<!-- /wp:heading -->
									<!-- wp:paragraph -->
									<p>9 AM — 11 AM</p>
									<!-- /wp:paragraph -->
								</div>
								<!-- /wp:group -->
								<!-- wp:paragraph {"fontSize":"small"} -->
								<p class="has-small-font-size">Lecture by <a href="#">Prof. Fiona Presley</a></p>
								<!-- /wp:paragraph -->
							</div>
							<!-- /wp:group -->
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
		<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
		<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
		<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"border":{"top":{"color":"var:preset|color|accent-6","width":"1px"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
		<div class="wp-block-group" style="border-top-color:var(--wp--preset--color--accent-6);border-top-width:1px;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
			<!-- wp:columns -->
			<div class="wp-block-columns">
				<!-- wp:column {"verticalAlignment":"top","width":"40%"} -->
				<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:40%">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading">Saturday, Feb. 2</h3>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:column -->
				<!-- wp:column {"verticalAlignment":"top","width":"60%"} -->
				<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:60%">
					<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
					<div class="wp-block-columns is-not-stacked-on-mobile" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)">
						<!-- wp:column {"width":"33.33%"} -->
						<div class="wp-block-column" style="flex-basis:33.33%">
							<!-- wp:image {"id":2773,"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"layout":{"selfStretch":"fixed","flexSize":"270px"}}} -->
							<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/parthenon-square.webp" alt="The acropolis in Athens." class="wp-image-2773" style="aspect-ratio:1;object-fit:cover"/></figure>
							<!-- /wp:image -->
						</div>
						<!-- /wp:column -->
						<!-- wp:column {"width":"66.66%"} -->
						<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
							<div class="wp-block-group">
								<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
								<div class="wp-block-group">
									<!-- wp:heading {"level":4} -->
									<h4 class="wp-block-heading"><a href="#">Ancient buildings and symbols</a></h4>
									<!-- /wp:heading -->
									<!-- wp:paragraph -->
									<p>9 AM — 11 AM</p>
									<!-- /wp:paragraph -->
								</div>
								<!-- /wp:group -->
								<!-- wp:paragraph {"fontSize":"small"} -->
								<p class="has-small-font-size">Lecture by <a href="#">Prof. Fiona Presley</a></p>
								<!-- /wp:paragraph -->
							</div>
							<!-- /wp:group -->
						</div>
						<!-- /wp:column -->
					</div>
					<!-- /wp:columns -->
					<!-- wp:columns {"isStackedOnMobile":false,"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
					<div class="wp-block-columns is-not-stacked-on-mobile" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)">
						<!-- wp:column {"width":"33.33%"} -->
						<div class="wp-block-column" style="flex-basis:33.33%">
							<!-- wp:image {"id":2774,"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","style":{"layout":{"selfStretch":"fixed","flexSize":"270px"}}} -->
							<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/agenda-img-4.webp" alt="Black and white photo of an african woman." class="wp-image-2774" style="aspect-ratio:1;object-fit:cover"/></figure>
							<!-- /wp:image -->
						</div>
						<!-- /wp:column -->
						<!-- wp:column {"width":"66.66%"} -->
						<div class="wp-block-column" style="flex-basis:66.66%">
							<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
							<div class="wp-block-group">
								<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
								<div class="wp-block-group">
									<!-- wp:heading {"level":4} -->
									<h4 class="wp-block-heading"><a href="#">An introduction to African dialects</a></h4>
									<!-- /wp:heading -->
									<!-- wp:paragraph -->
									<p>9 AM — 11 AM</p>
									<!-- /wp:paragraph -->
								</div>
								<!-- /wp:group -->
								<!-- wp:paragraph {"fontSize":"small"} -->
								<p class="has-small-font-size">Lecture by <a href="#">Prof. Fiona Presley</a></p>
								<!-- /wp:paragraph -->
							</div>
							<!-- /wp:group -->
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
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
