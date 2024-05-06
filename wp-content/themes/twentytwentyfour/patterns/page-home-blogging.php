<?php
/**
 * Title: Blogging home
 * Slug: twentytwentyfour/page-home-blogging
 * Categories: twentytwentyfour_page
 * Keywords: page, starter
 * Post Types: page, wp_template
 * Viewport width: 1400
 * Description: A blogging home page with a hero section, a text section, a blog section, and a CTA section.
 */
?>

<!-- wp:pattern {"slug":"twentytwentyfour/text-centered-statement-small"}	/-->

<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"1rem","left":"1rem"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"10%"} -->
		<div class="wp-block-column" style="flex-basis:10%">
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"60%"} -->
		<div class="wp-block-column" style="flex-basis:60%">
			<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->
			<div class="wp-block-query">
				<!-- wp:post-template -->
				<!-- wp:group {"tagName":"article","layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
				<article class="wp-block-group">
					<!-- wp:post-featured-image /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"large"} /-->

					<!-- wp:template-part {"slug":"post-meta"} /-->

				</article>
				<!-- /wp:group -->

				<!-- wp:post-excerpt {"moreText":"","excerptLength":40} /-->

				<!-- wp:spacer -->
				<div style="height:100px" aria-hidden="true" class="wp-block-spacer">
				</div>
				<!-- /wp:spacer -->
				<!-- /wp:post-template -->

				<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
				<!-- wp:query-pagination-previous /-->

				<!-- wp:query-pagination-numbers /-->

				<!-- wp:query-pagination-next /-->
				<!-- /wp:query-pagination -->

				<!-- wp:query-no-results -->
				<!-- wp:pattern {"slug":"twentytwentyfour/hidden-no-results"} /-->
				<!-- /wp:query-no-results -->
			</div>
			<!-- /wp:query -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"10%"} -->
		<div class="wp-block-column" style="flex-basis:10%">
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"30%"} -->
		<div class="wp-block-column" style="flex-basis:30%">
			<!-- wp:template-part {"slug":"sidebar","tagName":"aside"} /-->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"10%"} -->
		<div class="wp-block-column" style="flex-basis:10%">
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"twentytwentyfour/cta-subscribe-centered"}	/-->
