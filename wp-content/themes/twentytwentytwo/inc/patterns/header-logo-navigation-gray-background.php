<?php
/**
 * Logo and navigation header with gray background
 */
return array(
	'title'      => __( 'Logo and navigation header with background', 'twentytwentytwo' ),
	'categories' => array( 'header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground"}}},"spacing":{"padding":{"top":"var(--wp--custom--spacing--small, 1.25rem)","bottom":"var(--wp--custom--spacing--small, 1.25rem)"}}},"backgroundColor":"tertiary","textColor":"foreground","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull has-foreground-color has-tertiary-background-color has-text-color has-background has-link-color" style="padding-top:var(--wp--custom--spacing--small, 1.25rem);padding-bottom:var(--wp--custom--spacing--small, 1.25rem)"><!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"0rem","top":"0px","right":"0px","left":"0px"}}},"layout":{"type":"flex","justifyContent":"space-between"}} -->
					<div class="wp-block-group alignwide" style="padding-top:0px;padding-right:0px;padding-bottom:0rem;padding-left:0px"><!-- wp:site-logo {"width":64} /-->

					<!-- wp:navigation {"layout":{"type":"flex","setCascadingProperties":true,"justifyContent":"right"}} -->
					<!-- wp:page-list /-->
					<!-- /wp:navigation --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->',
);
