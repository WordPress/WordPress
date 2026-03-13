<?php
/**
 * The sidebar containing the header widget area
 * @package Twenty8teen
 */

if ( is_active_sidebar( 'header-widget-area' ) ) {
	dynamic_sidebar( 'header-widget-area' );
}
else {
	get_template_part( 'template-parts/site-logo' );
	get_template_part( 'template-parts/site-branding' );
	get_template_part( 'template-parts/main-nav' );
}
