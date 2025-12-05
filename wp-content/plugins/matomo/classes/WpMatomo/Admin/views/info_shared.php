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
<h1>
	<span style="margin-right: 2px;"><?php esc_html_e( 'About', 'matomo' ); ?></span>
	<?php matomo_header_icon( true ); ?>
</h1>

<p>
	<?php
	require_once 'info_matomo_desc.php';
	?>
</p>
<ul class="matomo-list">
	<li><?php esc_html_e( '100% data ownership, no one else can see your data', 'matomo' ); ?></li>
	<li><?php esc_html_e( 'Powerful web analytics for WordPress', 'matomo' ); ?></li>
	<li><?php esc_html_e( 'Superb user privacy protection', 'matomo' ); ?></li>
	<li><?php esc_html_e( 'No data limits or sampling whatsoever', 'matomo' ); ?></li>
	<li><?php esc_html_e( 'Easy installation and configuration', 'matomo' ); ?></li>
</ul>
