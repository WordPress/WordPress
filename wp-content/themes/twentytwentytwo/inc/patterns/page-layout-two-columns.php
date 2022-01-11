<?php
/**
 * Page layout with two columns.
 */
return array(
	'title'      => __( 'Page layout with two columns', 'twentytwentytwo' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var(--wp--custom--spacing--large, 8rem)","bottom":"var(--wp--custom--spacing--large, 8rem)"}}},"layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull" style="padding-top:var(--wp--custom--spacing--large, 8rem);padding-bottom:var(--wp--custom--spacing--large, 8rem);"><!-- wp:heading {"level":1,"align":"wide","style":{"typography":{"fontSize":"clamp(4rem, 15vw, 12.5rem)","lineHeight":"1","fontWeight":"200"}}} -->
					<h1 class="alignwide" style="font-size:clamp(4rem, 15vw, 12.5rem);font-weight:200;line-height:1">' . wp_kses_post( __( '<em>Goldfinch </em><br><em>&amp; Sparrow</em>', 'twentytwentytwo' ) ) . '</h1>
					<!-- /wp:heading -->

					<!-- wp:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:group {"align":"wide","layout":{"inherit":false}} -->
					<div class="wp-block-group alignwide"><!-- wp:columns -->
					<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"center","width":"20%"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:20%"><!-- wp:paragraph -->
					<p>' . esc_html__( 'WELCOME', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center","width":"80%"} -->
					<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:80%"><!-- wp:separator {"className":"is-style-wide"} -->
					<hr class="wp-block-separator is-style-wide"/>
					<!-- /wp:separator --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:paragraph -->
					<p>' . wp_kses_post( __( 'Oh hello. My name’s Angelo, and I operate this blog. I was born in Portland, but I currently live in upstate New York. You may recognize me from publications with names like <a href="#">Eagle Beagle</a> and <a href="#">Mourning Dive</a>. I write for a living.<br><br>I usually use this blog to catalog extensive lists of birds and other things that I find interesting. If you find an error with one of my lists, please keep it to yourself.<br><br>If that’s not your cup of tea, <a href="#">I definitely recommend this tea</a>. It’s my favorite.', 'twentytwentytwo' ) ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:spacer -->
					<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:separator {"className":"is-style-wide"} -->
					<hr class="wp-block-separator is-style-wide"/>
					<!-- /wp:separator --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph -->
					<p>' . esc_html__( 'POSTS', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:columns {"align":"wide"} -->
					<div class="wp-block-columns alignwide"><!-- wp:column -->
					<div class="wp-block-column"></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:latest-posts /--></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div>
					<!-- /wp:group -->',
);
