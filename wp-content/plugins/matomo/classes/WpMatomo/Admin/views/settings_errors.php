<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */
/**
 * phpcs considers all of our variables as global and want them prefixed with matomo
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
 */
/** @var string[] $settings_errors */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="updated error">
	<?php foreach ( $settings_errors as $setting_error ) : ?>
		<p><?php echo esc_html( $setting_error ); ?></p>
	<?php endforeach; ?>
</div>
