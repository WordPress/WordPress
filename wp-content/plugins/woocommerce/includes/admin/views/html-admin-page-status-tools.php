<?php
/**
 * Admin View: Page - Status Tools
 *
 * @package WooCommerce
 */

use Automattic\WooCommerce\Utilities\ArrayUtil;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( $tools as $action_name => $tool ) {
	?>
	<form id="<?php echo esc_attr( 'form_' . $action_name ); ?>" method="GET" action="<?php echo esc_attr( esc_url( admin_url( 'admin.php?foo=bar' ) ) ); ?>">
		<?php wp_nonce_field( 'debug_action', '_wpnonce', false ); ?>
		<input type="hidden" name="page" value="wc-status"/>
		<input type="hidden" name="tab" value="tools"/>
		<input type="hidden" name="action" value="<?php echo esc_attr( $action_name ); ?>"/>
	</form>
	<?php
}
?>

<table class="wc_status_table wc_status_table--tools widefat" cellspacing="0">
	<tbody class="tools">
		<?php foreach ( $tools as $action_name => $tool ) : ?>
			<tr class="<?php echo sanitize_html_class( $action_name ); ?>">
				<th>
					<strong class="name"><?php echo esc_html( $tool['name'] ); ?></strong>
					<p class="description">
						<?php
						echo wp_kses_post( $tool['desc'] );
						if ( ! is_null( ArrayUtil::get_value_or_default( $tool, 'selector' ) ) ) {
							$selector = $tool['selector'];
							if ( isset( $selector['description'] ) ) {
								echo '</p><p class="description">';
								echo wp_kses_post( $selector['description'] );
							}
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo "&nbsp;&nbsp;<select style='width: 300px;' form='form_$action_name' id='selector_$action_name' data-allow_clear='true' class='{$selector['class']}' name='{$selector['name']}' data-placeholder='{$selector['placeholder']}' data-action='{$selector['search_action']}'></select>";
						}
						?>
					</p>
				</th>
				<td class="run-tool">
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<input <?php echo ArrayUtil::is_truthy( $tool, 'disabled' ) ? 'disabled' : ''; ?> type="submit" form="<?php echo 'form_' . $action_name; ?>" class="button button-large" value="<?php echo esc_attr( $tool['button'] ); ?>" />
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
