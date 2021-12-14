<?php
/**
 * Simple dark about page
 */
return array(
	'title'      => __( 'Simple dark about page', 'twentytwentytwo' ),
	'categories' => array( 'pages' ),
	'content'    => '<!-- wp:cover {"overlayColor":"foreground","minHeight":100,"minHeightUnit":"vh","contentPosition":"center center","align":"full","style":{"spacing":{"padding":{"top":"max(1.25rem, 8vw)","right":"max(1.25rem, 8vw)","bottom":"max(1.25rem, 8vw)","left":"max(1.25rem, 8vw)"}}}} -->
					<div class="wp-block-cover alignfull has-foreground-background-color has-background-dim" style="padding-top:max(1.25rem, 8vw);padding-right:max(1.25rem, 8vw);padding-bottom:max(1.25rem, 8vw);padding-left:max(1.25rem, 8vw);min-height:100vh"><div class="wp-block-cover__inner-container"><!-- wp:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"},"overlayMenu":"always"} -->
					<!-- wp:page-list {"isNavigationChild":true,"showSubmenuIcon":true,"openSubmenusOnClick":false} /-->
					<!-- /wp:navigation -->

					<!-- wp:columns -->
					<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"bottom","width":"45%","style":{"spacing":{"padding":{"top":"12rem"}}}} -->
					<div class="wp-block-column is-vertically-aligned-bottom" style="padding-top:12rem;flex-basis:45%"><!-- wp:site-logo {"width":60} /-->

					<!-- wp:heading {"style":{"typography":{"fontWeight":"300","lineHeight":"1.115","fontSize":"clamp(3rem, 6vw, 4.5rem)"}}} -->
					<h2 style="font-size:clamp(3rem, 6vw, 4.5rem);font-weight:300;line-height:1.115"><em>' . wp_kses_post( __( 'Jesús<br>Rodriguez', 'twentytwentytwo' ) ) . '</em></h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph {"style":{"typography":{"lineHeight":"1.6"}}} -->
					<p style="line-height:1.6">' . esc_html__( 'Oh hello. My name’s Jesús, and you’ve found your way to my website. I’m an avid bird watcher, and I also broadcast my own radio show on Tuesday evenings at 11PM EDT.', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:social-links {"iconColor":"background","iconColorValue":"var(--wp--preset--color--foreground)","iconBackgroundColor":"foreground","iconBackgroundColorValue":"var(--wp--preset--color--background)"} -->
					<ul class="wp-block-social-links has-icon-color has-icon-background-color"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->

					<!-- wp:social-link {"url":"#","service":"twitter"} /-->

					<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /wp:social-links --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"padding":{"top":"0rem","right":"0rem","bottom":"4rem","left":"0rem"}}}} -->
					<div class="wp-block-column is-vertically-aligned-center" style="padding-top:0rem;padding-right:0rem;padding-bottom:4rem;padding-left:0rem"><!-- wp:separator {"color":"background","className":"is-style-wide"} -->
					<hr class="wp-block-separator has-text-color has-background has-background-background-color has-background-color is-style-wide"/>
					<!-- /wp:separator --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns --></div></div>
					<!-- /wp:cover -->',
);
