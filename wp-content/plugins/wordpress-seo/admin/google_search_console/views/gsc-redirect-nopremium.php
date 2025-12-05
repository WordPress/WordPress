<?php
/**
 * WPSEO plugin file.
 *
 * This is the view for the modal box that appears when premium isn't loaded.
 *
 * @package WPSEO\Admin\Google_Search_Console
 */

_deprecated_file( __FILE__, 'Yoast SEO 9.5' );

echo '<h1 class="wpseo-redirect-url-title">';
printf(
	/* Translators: %s: expands to Yoast SEO Premium */
	esc_html__( 'Creating redirects is a %s feature', 'wordpress-seo' ),
	'Yoast SEO Premium'
);
echo '</h1>';
echo '<p>';
printf(
	/* Translators: %1$s: expands to 'Yoast SEO Premium', %2$s: links to Yoast SEO Premium plugin page. */
	esc_html__( 'To be able to create a redirect and fix this issue, you need %1$s. You can buy the plugin, including one year of support and updates, on %2$s.', 'wordpress-seo' ),
	'Yoast SEO Premium',
	'<a href="' . esc_url( WPSEO_Shortlinker::get( 'https://yoa.st/redirects' ) ) . '" target="_blank">yoast.com</a>'
);
echo '</p>';
echo '<button type="button" class="button wpseo-redirect-close">' . esc_html__( 'Close', 'wordpress-seo' ) . '</button>';
