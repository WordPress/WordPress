<?php
/**
 * Shipping zone admin
 *
 * @package WooCommerce\Admin\Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<h2>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ); ?>"><?php esc_html_e( 'Shipping zones', 'woocommerce' ); ?></a> &gt;
	<span class="wc-shipping-zone-name"><?php echo esc_html( $zone->get_zone_name() ? $zone->get_zone_name() : __( 'Zone', 'woocommerce' ) ); ?></span>
</h2>

<?php do_action( 'woocommerce_shipping_zone_before_methods_table', $zone ); ?>

<table class="form-table wc-shipping-zone-settings">
	<tbody>
		<?php if ( 0 !== $zone->get_id() ) : ?>
			<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="zone_name">
						<?php esc_html_e( 'Zone name', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'This is the name of the zone for your reference.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<input type="text" data-attribute="zone_name" name="zone_name" id="zone_name" value="<?php echo esc_attr( $zone->get_zone_name( 'edit' ) ); ?>" placeholder="<?php esc_attr_e( 'Zone name', 'woocommerce' ); ?>">
				</td>
			</tr>
			<tr valign="top" class="">
				<th scope="row" class="titledesc">
					<label for="zone_locations">
						<?php esc_html_e( 'Zone regions', 'woocommerce' ); ?>
						<?php echo wc_help_tip( __( 'These are regions inside this zone. Customers will be matched against these regions.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
					</label>
				</th>
				<td class="forminp">
					<select multiple="multiple" data-attribute="zone_locations" id="zone_locations" name="zone_locations" data-placeholder="<?php esc_attr_e( 'Select regions within this zone', 'woocommerce' ); ?>" class="wc-shipping-zone-region-select chosen_select">
						<?php
						foreach ( $shipping_continents as $continent_code => $continent ) {
							echo '<option value="continent:' . esc_attr( $continent_code ) . '"' . wc_selected( "continent:$continent_code", $locations ) . '>' . esc_html( $continent['name'] ) . '</option>';

							$countries = array_intersect( array_keys( $allowed_countries ), $continent['countries'] );

							foreach ( $countries as $country_code ) {
								echo '<option value="country:' . esc_attr( $country_code ) . '"' . wc_selected( "country:$country_code", $locations ) . '>' . esc_html( '&nbsp;&nbsp; ' . $allowed_countries[ $country_code ] ) . '</option>';

								$states = WC()->countries->get_states( $country_code );

								if ( $states ) {
									foreach ( $states as $state_code => $state_name ) {
										echo '<option value="state:' . esc_attr( $country_code . ':' . $state_code ) . '"' . wc_selected( "state:$country_code:$state_code", $locations ) . '>' . esc_html( '&nbsp;&nbsp;&nbsp;&nbsp; ' . $state_name . ', ' . $allowed_countries[ $country_code ] ) . '</option>';
									}
								}
							}
						}
						?>
					</select>
					<?php if ( empty( $postcodes ) ) : ?>
						<a class="wc-shipping-zone-postcodes-toggle" href="#"><?php esc_html_e( 'Limit to specific ZIP/postcodes', 'woocommerce' ); ?></a>
					<?php endif; ?>
					<div class="wc-shipping-zone-postcodes">
						<textarea name="zone_postcodes" data-attribute="zone_postcodes" id="zone_postcodes" placeholder="<?php esc_attr_e( 'List 1 postcode per line', 'woocommerce' ); ?>" class="input-text large-text" cols="25" rows="5"><?php echo esc_textarea( implode( "\n", $postcodes ) ); ?></textarea>
						<?php /* translators: WooCommerce link to setting up shipping zones */ ?>
						<span class="description"><?php printf( __( 'Postcodes containing wildcards (e.g. CB23*) or fully numeric ranges (e.g. <code>90210...99000</code>) are also supported. Please see the shipping zones <a href="%s" target="_blank">documentation</a> for more information.', 'woocommerce' ), 'https://docs.woocommerce.com/document/setting-up-shipping-zones/#section-3' ); ?></span><?php // @codingStandardsIgnoreLine. ?>
					</div>
				</td>
			<?php endif; ?>
		</tr>
		<tr valign="top" class="">
			<th scope="row" class="titledesc">
				<label>
					<?php esc_html_e( 'Shipping methods', 'woocommerce' ); ?>
					<?php echo wc_help_tip( __( 'The following shipping methods apply to customers with shipping addresses within this zone.', 'woocommerce' ) ); // @codingStandardsIgnoreLine ?>
				</label>
			</th>
			<td class="">
				<table class="wc-shipping-zone-methods widefat">
					<thead>
						<tr>
							<th class="wc-shipping-zone-method-sort"></th>
							<th class="wc-shipping-zone-method-title"><?php esc_html_e( 'Title', 'woocommerce' ); ?></th>
							<th class="wc-shipping-zone-method-enabled"><?php esc_html_e( 'Enabled', 'woocommerce' ); ?></th>
							<th class="wc-shipping-zone-method-description"><?php esc_html_e( 'Description', 'woocommerce' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="4">
								<button type="submit" class="button wc-shipping-zone-add-method" value="<?php esc_attr_e( 'Add shipping method', 'woocommerce' ); ?>"><?php esc_html_e( 'Add shipping method', 'woocommerce' ); ?></button>
							</td>
						</tr>
					</tfoot>
					<tbody class="wc-shipping-zone-method-rows"></tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php do_action( 'woocommerce_shipping_zone_after_methods_table', $zone ); ?>

<p class="submit">
	<button type="submit" name="submit" id="submit" class="button button-primary button-large wc-shipping-zone-method-save" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" disabled><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
</p>

<script type="text/html" id="tmpl-wc-shipping-zone-method-row-blank">
	<tr>
		<td class="wc-shipping-zone-method-blank-state" colspan="4">
			<p><?php esc_html_e( 'You can add multiple shipping methods within this zone. Only customers within the zone will see them.', 'woocommerce' ); ?></p>
		</td>
	</tr>
</script>

<script type="text/html" id="tmpl-wc-shipping-zone-method-row">
	<tr data-id="{{ data.instance_id }}" data-enabled="{{ data.enabled }}">
		<td width="1%" class="wc-shipping-zone-method-sort"></td>
		<td class="wc-shipping-zone-method-title">
			<a class="wc-shipping-zone-method-settings" href="admin.php?page=wc-settings&amp;tab=shipping&amp;instance_id={{ data.instance_id }}">{{{ data.title }}}</a>
			<div class="row-actions">
				<a class="wc-shipping-zone-method-settings" href="admin.php?page=wc-settings&amp;tab=shipping&amp;instance_id={{ data.instance_id }}"><?php esc_html_e( 'Edit', 'woocommerce' ); ?></a> | <a href="#" class="wc-shipping-zone-method-delete"><?php esc_html_e( 'Delete', 'woocommerce' ); ?></a>
			</div>
		</td>
		<td width="1%" class="wc-shipping-zone-method-enabled"><a href="#">{{{ data.enabled_icon }}}</a></td>
		<td class="wc-shipping-zone-method-description">
			<strong class="wc-shipping-zone-method-type">{{ data.method_title }}</strong>
			{{{ data.method_description }}}
		</td>
	</tr>
</script>

<script type="text/template" id="tmpl-wc-modal-shipping-method-settings">
	<div class="wc-backbone-modal wc-backbone-modal-shipping-method-settings">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1>
						<?php
						printf(
							/* translators: %s: shipping method title */
							esc_html__( '%s Settings', 'woocommerce' ),
							'{{{ data.method.method_title }}}'
						);
						?>
					</h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
					</button>
				</header>
				<article class="wc-modal-shipping-method-settings">
					<form action="" method="post">
						{{{ data.method.settings_html }}}
						<input type="hidden" name="instance_id" value="{{{ data.instance_id }}}" />
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>

<script type="text/template" id="tmpl-wc-modal-add-shipping-method">
	<div class="wc-backbone-modal">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php esc_html_e( 'Add shipping method', 'woocommerce' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'woocommerce' ); ?></span>
					</button>
				</header>
				<article>
					<form action="" method="post">
						<div class="wc-shipping-zone-method-selector">
							<p><?php esc_html_e( 'Choose the shipping method you wish to add. Only shipping methods which support zones are listed.', 'woocommerce' ); ?></p>

							<select name="add_method_id">
								<?php
								foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
									if ( ! $method->supports( 'shipping-zones' ) ) {
										continue;
									}
									echo '<option data-description="' . esc_attr( wp_kses_post( wpautop( $method->get_method_description() ) ) ) . '" value="' . esc_attr( $method->id ) . '">' . esc_html( $method->get_method_title() ) . '</li>';
								}
								?>
							</select>
						</div>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php esc_html_e( 'Add shipping method', 'woocommerce' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
