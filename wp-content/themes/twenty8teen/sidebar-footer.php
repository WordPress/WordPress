<?php
/**
 * The sidebar containing the footer widget area
 * @package Twenty8teen
 */

if ( is_active_sidebar( 'footer-widget-area' ) ) {
	dynamic_sidebar( 'footer-widget-area' );
}
else {
	get_template_part( 'template-parts/site-copyright' );
}
