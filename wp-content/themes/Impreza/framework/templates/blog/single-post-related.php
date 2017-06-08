<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs posts related to the current single post
 *
 * @action Before the template: 'us_before_template:templates/blog/single-post-related'
 * @action After the template: 'us_after_template:templates/blog/single-post-related'
 */

if ( ! has_tag() ) {
	return;
}

$tag_ids = wp_get_post_tags( get_the_ID(), array( 'fields' => 'ids' ) );
$query_args = array(
	'tag__in' => $tag_ids,
	'post__not_in' => array( get_the_ID() ),
	'paged' => 1,
	'showposts' => 3,
	'orderby' => 'rand',
	'ignore_sticky_posts' => 1,
	'post_type' => get_post_type(),
);
// Overloading global wp_query to use it in the inner templates
us_open_wp_query_context();
global $wp_query;
$wp_query = new WP_Query( $query_args );

if ( $wp_query->have_posts() ) {
	$template_vars = array(
		'layout_type' => us_get_option( 'post_related_layout', 'compact' ),
		'columns' => 1,
		'metas' => array( 'date' ),
		'content_type' => 'none',
		'show_read_more' => FALSE,
		'pagination' => 'none',
	);
	?><section class="l-section for_related">
		<div class="l-section-h i-cf">
			<h4><?php _e('Related Articles', 'us') ?></h4>
			<?php us_load_template( 'templates/blog/listing', $template_vars ) ?>
		</div>
	</section><?php
}

us_close_wp_query_context();
