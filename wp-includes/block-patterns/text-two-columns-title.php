<?php
/**
 * Two columns text and title.
 *
 * @package WordPress
 */

return array(
	'title'       => _x( 'Two columns text and title', 'Block pattern title' ),
	'categories'  => array( 'columns', 'text' ),
	'content'     => '<!-- wp:group -->
	<div class="wp-block-group"><div class="wp-block-group__inner-container"><!-- wp:heading {"style":{"typography":{"fontSize":38,"lineHeight":"1.4"}}} -->
	<h2 style="font-size:38px;line-height:1.4"><strong>' . esc_html__( 'The voyage had begun, and had begun happily with a soft blue sky, and a calm sea.' ) . '</strong></h2>
	<!-- /wp:heading -->
	
	<!-- wp:columns -->
	<div class="wp-block-columns"><!-- wp:column -->
	<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":18}}} -->
	<p style="font-size:18px">' . esc_html__( 'They followed her on to the deck. All the smoke and the houses had disappeared, and the ship was out in a wide space of sea very fresh and clear though pale in the early light. They had left London sitting on its mud. A very thin line of shadow tapered on the horizon, scarcely thick enough to stand the burden of Paris, which nevertheless rested upon it. They were free of roads, free of mankind, and the same exhilaration at their freedom ran through them all.' ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column -->
	
	<!-- wp:column -->
	<div class="wp-block-column"><!-- wp:paragraph {"style":{"typography":{"fontSize":18}}} -->
	<p style="font-size:18px">' . esc_html__( "The ship was making her way steadily through small waves which slapped her and then fizzled like effervescing water, leaving a little border of bubbles and foam on either side. The colourless October sky above was thinly clouded as if by the trail of wood-fire smoke, and the air was wonderfully salt and brisk. Indeed it was too cold to stand still. Mrs. Ambrose drew her arm within her husband's, and as they moved off it could be seen from the way in which her sloping cheek turned up to his that she had something private to communicate." ) . '</p>
	<!-- /wp:paragraph --></div>
	<!-- /wp:column --></div>
	<!-- /wp:columns --></div></div>
	<!-- /wp:group -->',
	'description' => _x( 'Two columns text and title', 'Block pattern description' ),
);
