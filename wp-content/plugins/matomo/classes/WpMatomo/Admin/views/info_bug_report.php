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
?>
<h2><?php esc_html_e( 'Do you have a bug to report or a feature request?', 'matomo' ); ?></h2>
<p>
	<?php
	echo sprintf(
		esc_html__( 'Please read the recommendations on writing a good %1$sbug report%2$s or %3$sfeature request%4$s. Then register or login to %5$sour issue tracker%6$s and create a %7$snew issue%8$s.', 'matomo' ),
		'<a target="_blank" rel="noreferrer noopener" href="https://developer.matomo.org/guides/core-team-workflow#submitting-a-bug-report">',
		'</a>',
		'<a target="_blank" rel="noreferrer noopener" href="https://developer.matomo.org/guides/core-team-workflow#submitting-a-feature-request">',
		'</a>',
		'<a target="_blank" rel="noreferrer noopener" href="https://github.com/matomo-org/matomo-for-wordpress/issues">',
		'</a>',
		'<a target="_blank" rel="noreferrer noopener" href="https://github.com/matomo-org/matomo-for-wordpress/issues/new">',
		'</a>'
	);
	?>
</p>
