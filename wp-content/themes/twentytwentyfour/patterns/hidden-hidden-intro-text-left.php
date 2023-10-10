<?php
/**
 * Title: Heading on left
 * Slug: twentytwentyfour/intro-text-left
 * Categories: text
 * Inserter: no
 */
?>

<!-- wp:spacer {"height":"var:preset|spacing|50","style":{"layout":{}}} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide">
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"70%"} -->
<div class="wp-block-column" style="flex-basis:70%">

<!-- wp:heading {"level":1,"style":{"typography":{"lineHeight":"1.2"}},"fontSize":"xx-large"} --><h1 class="wp-block-heading has-xx-large-font-size" style="line-height:1.2"><?php echo wp_kses_post( __( 'I’m <em>Leia Acosta</em>, a passionate photographer who finds inspiration in capturing the fleeting beauty of life.' ) ); ?></h1><!-- /wp:heading -->

</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
