<?php
/**
 * Template part for displaying the comments. Only output for singular pages.
 * @package Twenty8teen
 */

 // If comments are open or we have at least one comment, load up the comment template.
 if ( comments_open() || get_comments_number() ) :
	 comments_template();
 endif;
