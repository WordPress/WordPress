<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WpMatomo\Access;
use WpMatomo\Admin\AccessSettings;
use WpMatomo\Capabilities;
use WpMatomo\Roles;

/** @var Access $access */
/** @var Roles $roles */
/** @var Capabilities $capabilites */
?>

<p><?php esc_html_e( 'Manage which roles can view and manage your reporting data.', 'matomo' ); ?></p>

<form method="post">
	<?php wp_nonce_field( AccessSettings::NONCE_NAME ); ?>

	<table class="matomo-form widefat">
		<thead>
		<tr>
			<th width="30%"><?php esc_html_e( 'WordPress Role', 'matomo' ); ?></th>
			<th><?php esc_html_e( 'Matomo Role', 'matomo' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $roles->get_available_roles_for_configuration() as $matomo_role_id => $matomo_role_name ) {
			echo '<tr><td>';
			echo esc_html( $matomo_role_name ) . '</td>';
			echo "<td><select name='" . esc_attr( AccessSettings::FORM_NAME ) . '[' . esc_attr( $matomo_role_id ) . "]'>";
			$matomo_value = $access->get_permission_for_role( $matomo_role_id );
			foreach ( Access::$matomo_permissions as $matomo_permission => $matomo_display_name ) {
				// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
				echo "<option value='" . esc_attr( $matomo_permission ) . "' " . ( $matomo_value === $matomo_permission ? 'selected' : '' ) . '>' . esc_html__( $matomo_display_name, 'matomo' ) . '</option>';
			}
			echo '</td></tr>';
		}
		?>
		<tr>
			<td colspan="2"><input name="Submit" type="submit" class="button-primary"
								   value="<?php echo esc_attr__( 'Save Changes', 'matomo' ); ?>"/></td>
		</tr>
		</tbody>
	</table>
</form>

<p>
	<?php
	if ( ! is_multisite() ) {
		esc_html_e( 'A user with role administrator automatically has the super user role.', 'matomo' );
	}
	?>
	<?php esc_html_e( 'Learn about the differences between these Matomo roles:', 'matomo' ); ?>
	<a href="https://matomo.org/faq/general/faq_70/" target="_blank"
	   rel="noopener"><?php esc_html_e( 'View', 'matomo' ); ?></a>,
	<a href="https://matomo.org/faq/general/faq_26910/" target="_blank"
	   rel="noopener"><?php esc_html_e( 'Write', 'matomo' ); ?></a>,
	<a href="https://matomo.org/faq/general/faq_69/" target="_blank"
	   rel="noopener"><?php esc_html_e( 'Admin', 'matomo' ); ?></a>,
	<a href="https://matomo.org/faq/general/faq_35/" target="_blank"
	   rel="noopener"><?php esc_html_e( 'Super User', 'matomo' ); ?></a><br/>
	<?php esc_html_e( 'Want to redirect to the home page when not logged in?', 'matomo' ); ?> <a
			href="https://matomo.org/faq/wordpress/how-do-i-hide-my-wordpress-login-url-when-someone-accesses-a-matomo-report-directly/"
			target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Learn more', 'matomo' ); ?></a>
</p>

<h2><?php esc_html_e( 'Roles', 'matomo' ); ?></h2>
<p>
	<?php
	esc_html_e(
		'Want to give individual users access to Matomo? Create a user in your WordPress with one of these roles:',
		'matomo'
	)
	?>
</p>
<ul class="matomo-list">
	<?php foreach ( $roles->get_matomo_roles() as $matomo_role_config ) { ?>
		<li><?php echo esc_html( $matomo_role_config['name'] ); ?></li>
	<?php } ?>
</ul>

<h2><?php esc_html_e( 'Capabilities', 'matomo' ); ?></h2>
<p>
	<?php
	esc_html_e(
		'You can also install a WordPress plugin which lets you manage capabilities for each individual users. These are
    the supported capabilities:',
		'matomo'
	)
	?>
</p>
<ul class="matomo-list">
	<?php
	foreach ( $capabilites->get_all_capabilities_sorted_by_highest_permission() as $matomo_cap_name ) {
		?>
		<li><?php echo esc_html( $matomo_cap_name ); ?></li>
		<?php
	}
	?>
</ul>
