<?php

return array(
	'name' => 'simple-two-columns-posts',
	'title' => 'Simple Two Columns Posts',
	'content' => '<!-- wp:spacer {"height":"6vh"} -->
<div style="height:6vh" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":1,"fontSize":"medium"} -->
<h1 class="has-medium-font-size">See our inside news &amp; editorials</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Designed by bagberry, with premium, durable and environmentally friendly materials.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"accent","textColor":"white","style":{"border":{"radius":"60px"}},"fontSize":"medium"} -->
<div class="wp-block-button has-custom-font-size has-medium-font-size"><a class="wp-block-button__link has-white-color has-accent-background-color has-text-color has-background wp-element-button" style="border-radius:60px">Explore our blog</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:spacer {"height":"10px"} -->
<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:query {"queryId":107,"query":{"perPage":"2","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":2}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:post-featured-image /-->

<!-- wp:post-date /-->

<!-- wp:post-title {"level":5} /-->

<!-- wp:read-more {"content":"Continue reading\u003ca role=\u0022document\u0022 aria-multiline=\u0022true\u0022 aria-label=\u0022Block: Read More\u0022 tabindex=\u00220\u0022 class=\u0022block-editor-rich-text__editable block-editor-block-list__block wp-block is-selected wp-block-read-more rich-text\u0022 id=\u0022block-a558ff98-17ab-4f67-9d8d-150bc3a00465\u0022 data-block=\u0022a558ff98-17ab-4f67-9d8d-150bc3a00465\u0022 data-title=\u0022Read More\u0022 style=\u0022background-color: initial; color: rgb(255, 255, 255); font-family: var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dfont-family\u002d\u002dprimary-font); font-weight: var(\u002d\u002dwp\u002d\u002dcustom\u002d\u002dtypography\u002d\u002dfont-weight\u002d\u002dbody); letter-spacing: var(\u002d\u002dwp\u002d\u002dcustom\u002d\u002dtypography\u002d\u002dletter-spacing\u002d\u002dbody); min-width: 1px; display: inline !important;\u0022 data-type=\u0022core/read-more\u0022\u003e\u003c/a\u003e","style":{"typography":{"textDecoration":"underline"}}} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"6vh"} -->
<div style="height:6vh" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->',
'categories'    => array( 'featured', 'theme-patterns' ),
'viewportWidth' => 1680
);
