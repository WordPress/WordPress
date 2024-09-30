<?php
/**
 * Title: Grid with categories
 * Slug: twentytwentyfive/grid-with-categories
 * Categories: media, featured
 * Viewport width: 1400
 * Description: A grid section with different categories.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|50"}},"layout":{"type":"grid","minimumColumnWidth":"16rem"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"fontSize":"x-large"} -->
			<h2 class="wp-block-heading has-x-large-font-size">Top Categories</h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
		<div class="wp-block-group">
			<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/category-anthuriums.webp","id":2904,"alt":"Close up of a red anthurium.","dimRatio":0,"customOverlayColor":"#833d3a","isUserOverlayColor":true,"layout":{"type":"constrained"}} -->
			<div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#833d3a"></span><img class="wp-block-cover__image-background wp-image-2904" alt="Close up of a red anthurium." src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/category-anthuriums.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
			<p class="has-text-align-center has-large-font-size"></p><!-- /wp:paragraph --></div></div>
			<!-- /wp:cover -->
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Anthuriums</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
		<div class="wp-block-group">
			<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/category-cactus.webp","id":2905,"dimRatio":0,"customOverlayColor":"#828282","isUserOverlayColor":true,"isDark":false,"layout":{"type":"constrained"}} -->
			<div class="wp-block-cover is-light"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#828282"></span><img class="wp-block-cover__image-background wp-image-2905" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/category-cactus.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
			<p class="has-text-align-center has-large-font-size"></p>
			<!-- /wp:paragraph --></div></div>
			<!-- /wp:cover -->
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Cactus</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
		<div class="wp-block-group">
			<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/category-sunflowers.webp","id":2906,"dimRatio":0,"customOverlayColor":"#d6bc98","isUserOverlayColor":true,"isDark":false,"layout":{"type":"constrained"}} -->
			<div class="wp-block-cover is-light"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim" style="background-color:#d6bc98"></span><img class="wp-block-cover__image-background wp-image-2906" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/category-sunflowers.webp" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
			<p class="has-text-align-center has-large-font-size"></p>
			<!-- /wp:paragraph --></div></div>
			<!-- /wp:cover -->
			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center">Sunflowers</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
