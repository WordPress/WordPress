<?php
/**
 * Irregular grid of posts block pattern
 */
return array(
	'title'      => __( 'Irregular grid of posts', 'twentytwentytwo' ),
	'categories' => array( 'query' ),
	'blockTypes' => array( 'core/query' ),
	'content'    => '<!-- wp:group {"align":"wide"} -->
					<div class="wp-block-group alignwide"><!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":0,"postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"1","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:spacer {"height":64} -->
					<div style="height:64px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"2","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:spacer {"height":128} -->
					<div style="height:128px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"3","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"4","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:spacer {"height":96} -->
					<div style="height:96px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"5","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:spacer {"height":160} -->
					<div style="height:160px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"6","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:spacer {"height":32} -->
					<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"7","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:spacer {"height":160} -->
					<div style="height:160px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:query {"query":{"offset":"8","postType":"post","categoryIds":[],"tagIds":[],"order":"desc","orderBy":"date","author":"","search":"","sticky":"","perPage":"1"},"displayLayout":{"type":"list","columns":3},"align":"wide","layout":{"inherit":true}} -->
					<div class="wp-block-query alignwide"><!-- wp:post-template {"align":"wide"} -->
					<!-- wp:spacer {"height":96} -->
					<div style="height:96px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:post-featured-image {"isLink":true,"width":"100%","height":"318px"} /-->

					<!-- wp:post-title {"isLink":true,"fontSize":"x-large"} /-->

					<!-- wp:post-excerpt /-->

					<!-- wp:post-date {"format":"F j, Y","isLink":true,"fontSize":"small"} /-->
					<!-- /wp:post-template --></div>
					<!-- /wp:query --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->',
);
