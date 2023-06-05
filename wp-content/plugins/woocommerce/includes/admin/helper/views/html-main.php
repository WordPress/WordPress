<?php
/**
 * Helper main view
 *
 * @package WooCommerce\Helper
 */

?>
<?php defined( 'ABSPATH' ) || exit(); ?>

<div class="wrap woocommerce wc-subscriptions-wrap wc-helper">
	<?php require WC_Helper::get_view_filename( 'html-section-nav.php' ); ?>
	<h1 class="screen-reader-text"><?php esc_html_e( 'WooCommerce Extensions', 'woocommerce' ); ?></h1>

	<?php require WC_Helper::get_view_filename( 'html-section-notices.php' ); ?>

	<div class="subscriptions-header">
		<h2><?php esc_html_e( 'Subscriptions', 'woocommerce' ); ?></h2>
		<?php require WC_Helper::get_view_filename( 'html-section-account.php' ); ?>
		<p>
			<?php
			printf(
				wp_kses(
					/* translators: Introduction to list of WooCommerce.com extensions the merchant has subscriptions for. */
					__(
						'Below is a list of extensions available on your WooCommerce.com account. To receive extension updates please make sure the extension is installed, and its subscription activated and connected to your WooCommerce.com account. Extensions can be activated from the <a href="%s">Plugins</a> screen.',
						'woocommerce'
					),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				),
				esc_url(
					admin_url( 'plugins.php' )
				)
			);
			?>
		</p>
	</div>

	<ul class="subscription-filter">
		<label><?php esc_html_e( 'Sort by:', 'woocommerce' ); ?> <span class="chevron dashicons dashicons-arrow-up-alt2"></span></label>
		<?php
			$filters        = array_keys( WC_Helper::get_filters() );
			$last_filter    = array_pop( $filters );
			$current_filter = WC_Helper::get_current_filter();
			$counts         = WC_Helper::get_filters_counts();
		?>

		<?php
		foreach ( WC_Helper::get_filters() as $key => $label ) :
			// Don't show empty filters.
			if ( empty( $counts[ $key ] ) ) {
				continue;
			}

			$url        = admin_url( 'admin.php?page=wc-addons&section=helper&filter=' . $key );
			$class_html = $current_filter === $key ? 'class="current"' : '';
			?>
			<li>
				<a <?php echo esc_html( $class_html ); ?> href="<?php echo esc_url( $url ); ?>">
					<?php echo esc_html( $label ); ?>
					<span class="count">(<?php echo absint( $counts[ $key ] ); ?>)</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<table class="wp-list-table widefat fixed striped">
		<?php if ( ! empty( $subscriptions ) ) : ?>
			<?php foreach ( $subscriptions as $subscription ) : ?>
				<tbody>
				<tr class="wp-list-table__row is-ext-header">
					<td class="wp-list-table__ext-details">
						<div class="wp-list-table__ext-title">
							<a href="<?php echo esc_url( WC_Helper::add_utm_params_to_url_for_subscription_link( $subscription['product_url'], 'product-name' ) ); ?>" target="_blank">
								<?php echo esc_html( $subscription['product_name'] ); ?>
							</a>
						</div>

						<div class="wp-list-table__ext-description">
							<?php if ( $subscription['lifetime'] ) : ?>
								<span class="renews">
									<?php esc_html_e( 'Lifetime Subscription', 'woocommerce' ); ?>
								</span>
							<?php elseif ( $subscription['expired'] ) : ?>
								<span class="renews">
									<strong><?php esc_html_e( 'Expired :(', 'woocommerce' ); ?></strong>
									<?php echo esc_html( date_i18n( 'F jS, Y', $subscription['expires'] ) ); ?>
								</span>
							<?php elseif ( $subscription['autorenew'] ) : ?>
								<span class="renews">
									<?php esc_html_e( 'Auto renews on:', 'woocommerce' ); ?>
									<?php echo esc_html( date_i18n( 'F jS, Y', $subscription['expires'] ) ); ?>
								</span>
							<?php elseif ( $subscription['expiring'] ) : ?>
								<span class="renews">
									<strong><?php esc_html_e( 'Expiring soon!', 'woocommerce' ); ?></strong>
									<?php echo esc_html( date_i18n( 'F jS, Y', $subscription['expires'] ) ); ?>
								</span>
							<?php else : ?>
								<span class="renews">
									<?php esc_html_e( 'Expires on:', 'woocommerce' ); ?>
									<?php echo esc_html( date_i18n( 'F jS, Y', $subscription['expires'] ) ); ?>
								</span>
							<?php endif; ?>

							<br/>
							<span class="subscription">
								<?php
								if ( ! $subscription['active'] && $subscription['maxed'] ) {
									/* translators: %1$d: sites active, %2$d max sites active */
									printf( esc_html__( 'Subscription: Not available - %1$d of %2$d already in use', 'woocommerce' ), absint( $subscription['sites_active'] ), absint( $subscription['sites_max'] ) );
								} elseif ( $subscription['sites_max'] > 0 ) {
									/* translators: %1$d: sites active, %2$d max sites active */
									printf( esc_html__( 'Subscription: Using %1$d of %2$d sites available', 'woocommerce' ), absint( $subscription['sites_active'] ), absint( $subscription['sites_max'] ) );
								} else {
									esc_html_e( 'Subscription: Unlimited', 'woocommerce' );
								}

								// Check shared.
								if ( ! empty( $subscription['is_shared'] ) && ! empty( $subscription['owner_email'] ) ) {
									/* translators: Email address of person who shared the subscription. */
									printf( '</br>' . esc_html__( 'Shared by %s', 'woocommerce' ), esc_html( $subscription['owner_email'] ) );
								} elseif ( isset( $subscription['master_user_email'] ) ) {
									/* translators: Email address of person who shared the subscription. */
									printf( '</br>' . esc_html__( 'Shared by %s', 'woocommerce' ), esc_html( $subscription['master_user_email'] ) );
								}
								?>
							</span>
						</div>
					</td>
					<td class="wp-list-table__ext-actions">
						<?php if ( ! $subscription['active'] && $subscription['maxed'] ) : ?>
							<a class="button" href="https://woocommerce.com/my-account/my-subscriptions/" target="_blank"><?php esc_html_e( 'Upgrade', 'woocommerce' ); ?></a>
						<?php elseif ( ! $subscription['local']['installed'] && ! $subscription['expired'] ) : ?>
							<a class="button <?php echo empty( $subscription['download_primary'] ) ? 'button-secondary' : ''; ?>" href="<?php echo esc_url( $subscription['download_url'] ); ?>" target="_blank"><?php esc_html_e( 'Download', 'woocommerce' ); ?></a>
						<?php elseif ( $subscription['active'] ) : ?>
							<span class="form-toggle__wrapper">
								<a href="<?php echo esc_url( $subscription['deactivate_url'] ); ?>" class="form-toggle active is-compact" role="link" aria-checked="true"><?php esc_html_e( 'Active', 'woocommerce' ); ?></a>
								<label class="form-toggle__label" for="activate-extension">
									<span class="form-toggle__label-content">
										<label for="activate-extension"><?php esc_html_e( 'Active', 'woocommerce' ); ?></label>
									</span>
									<span class="form-toggle__switch"></span>
								</label>
							</span>
						<?php elseif ( ! $subscription['expired'] ) : ?>
							<span class="form-toggle__wrapper">
								<a href="<?php echo esc_url( $subscription['activate_url'] ); ?>" class="form-toggle is-compact" role="link" aria-checked="false"><?php esc_html_e( 'Inactive', 'woocommerce' ); ?></a>
								<label class="form-toggle__label" for="activate-extension">
									<span class="form-toggle__label-content">
										<label for="activate-extension"><?php esc_html_e( 'Inactive', 'woocommerce' ); ?></label>
									</span>
									<span class="form-toggle__switch"></span>
								</label>
							</span>
						<?php else : ?>
							<span class="form-toggle__wrapper">
								<span class="form-toggle disabled is-compact"><?php esc_html_e( 'Inactive', 'woocommerce' ); ?></span>
								<label class="form-toggle__label" for="activate-extension">
									<span class="form-toggle__label-content">
										<label for="activate-extension"><?php esc_html_e( 'Inactive', 'woocommerce' ); ?></label>
									</span>
								</label>
							</span>
						<?php endif; ?>
					</td>
				</tr>

				<?php foreach ( $subscription['actions'] as $subscription_action ) : ?>
				<tr class="wp-list-table__row wp-list-table__ext-updates">
					<td class="wp-list-table__ext-status <?php echo sanitize_html_class( $subscription_action['status'] ); ?>">
						<p><span class="dashicons <?php echo sanitize_html_class( $subscription_action['icon'] ); ?>"></span>
							<?php echo wp_kses_post( $subscription_action['message'] ); ?>
						</p>
					</td>
					<td class="wp-list-table__ext-actions">
						<?php if ( ! empty( $subscription_action['button_label'] ) && ! empty( $subscription_action['button_url'] ) ) : ?>
						<a class="button <?php echo empty( $subscription_action['primary'] ) ? 'button-secondary' : ''; ?>" href="<?php echo esc_url( $subscription_action['button_url'] ); ?>"><?php echo esc_html( $subscription_action['button_label'] ); ?></a>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>

				</tbody>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="3"><em><?php esc_html_e( 'Could not find any subscriptions on your WooCommerce.com account', 'woocommerce' ); ?></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>

	<?php if ( ! empty( $no_subscriptions ) ) : ?>
		<h2><?php esc_html_e( 'Installed Extensions without a Subscription', 'woocommerce' ); ?></h2>
		<p>Below is a list of WooCommerce.com products available on your site - but are either out-dated or do not have a valid subscription.</p>

		<table class="wp-list-table widefat fixed striped">
			<?php /* Extensions without a subscription. */ ?>
			<?php foreach ( $no_subscriptions as $filename => $data ) : ?>
				<tbody>
					<tr class="wp-list-table__row is-ext-header">
						<td class="wp-list-table__ext-details color-bar autorenews">
							<div class="wp-list-table__ext-title">
								<a href="<?php echo esc_url( WC_Helper::add_utm_params_to_url_for_subscription_link( $data['_product_url'], 'product-name' ) ); ?>" target="_blank">
									<?php echo esc_html( $data['Name'] ); ?>
								</a>
							</div>
							<div class="wp-list-table__ext-description">
							</div>
						</td>
						<td class="wp-list-table__ext-actions">
							<span class="form-toggle__wrapper">
								<span class="form-toggle disabled is-compact" ><?php esc_html_e( 'Inactive', 'woocommerce' ); ?></span>
								<label class="form-toggle__label" for="activate-extension">
									<span class="form-toggle__label-content">
										<label for="activate-extension"><?php esc_html_e( 'Inactive', 'woocommerce' ); ?></label>
									</span>
								</label>
							</span>
						</td>
					</tr>

					<?php foreach ( $data['_actions'] as $subscription_action ) : ?>
					<tr class="wp-list-table__row wp-list-table__ext-updates">
						<td class="wp-list-table__ext-status <?php echo sanitize_html_class( $subscription_action['status'] ); ?>">
							<p><span class="dashicons <?php echo sanitize_html_class( $subscription_action['icon'] ); ?>"></span>
								<?php
									echo wp_kses(
										$subscription_action['message'],
										array(
											'a'      => array(
												'href'  => array(),
												'title' => array(),
											),
											'br'     => array(),
											'em'     => array(),
											'strong' => array(),
										)
									);
								?>
							</p>
						</td>
						<td class="wp-list-table__ext-actions">
							<a class="button" href="<?php echo esc_url( $subscription_action['button_url'] ); ?>" target="_blank"><?php echo esc_html( $subscription_action['button_label'] ); ?></a>
						</td>
					</tr>
					<?php endforeach; ?>

				</tbody>

			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>
