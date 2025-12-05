<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

echo sprintf(
	esc_html__(
		'%1$sMatomo Analytics%2$s is the most powerful
    analytics platform for WordPress, designed for your success. It is our mission to help you grow
    your business while giving you %3$sfull control over your data%4$s. All
    data is stored in your WordPress. You own the data, nobody else.',
		'matomo'
	),
	'<a target="_blank" rel="noreferrer noopener" href="https://matomo.org">',
	'</a>',
	'<strong>',
	'</strong>'
);
