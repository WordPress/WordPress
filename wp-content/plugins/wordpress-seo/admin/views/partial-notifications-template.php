<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 *
 * @uses    string $yoast_seo_type
 * @uses    string $yoast_seo_dashicon
 * @uses    string $yoast_seo_i18n_title
 * @uses    string $yoast_seo_i18n_issues
 * @uses    string $yoast_seo_i18n_no_issues
 * @uses    string $yoast_seo_i18n_muted_issues_title
 * @uses    int    $yoast_seo_active_total
 * @uses    int    $yoast_seo_dismissed_total
 * @uses    int    $yoast_seo_total
 * @uses    array  $yoast_seo_active
 * @uses    array  $yoast_seo_dismissed
 */

if ( ! function_exists( '_yoast_display_notifications' ) ) {
	/**
	 * Create the notifications HTML with restore/dismiss button.
	 *
	 * @param array  $notifications_list List of notifications.
	 * @param string $status             Status of the notifications (active/dismissed).
	 *
	 * @return string The output to render.
	 */
	function _yoast_display_notifications( $notifications_list, $status ) {
		$notifications = '';

		foreach ( $notifications_list as $notification ) {

			switch ( $status ) {
				case 'active':
					$button = sprintf(
						'<button type="button" class="button dismiss"><span class="screen-reader-text">%1$s</span><span class="dashicons dashicons-hidden"></span></button>',
						/* translators: Hidden accessibility text. */
						esc_html__( 'Hide this item.', 'wordpress-seo' )
					);
					break;

				case 'dismissed':
					$button = sprintf(
						'<button type="button" class="button restore"><span class="screen-reader-text">%1$s</span><span class="dashicons yoast-svg-icon-eye"></span></button>',
						/* translators: Hidden accessibility text. */
						esc_html__( 'Show this item.', 'wordpress-seo' )
					);
					break;
			}

			$notifications .= sprintf(
				'<div class="yoast-notification-holder" id="%1$s" data-nonce="%2$s" data-json="%3$s">%4$s%5$s</div>',
				esc_attr( $notification->get_id() ),
				esc_attr( $notification->get_nonce() ),
				esc_attr( $notification->get_json() ),
				// This needs to be fixed in https://github.com/Yoast/wordpress-seo-premium/issues/2548.
				$notification,
				// Note: $button is properly escaped above.
				$button
			);
		}

		return $notifications;
	}
}

$wpseo_i18n_summary = $yoast_seo_i18n_issues;
if ( ! $yoast_seo_active ) {
	$yoast_seo_dashicon = 'yes';
	$wpseo_i18n_summary = $yoast_seo_i18n_no_issues;
}

?>
<h3 class="yoast-notifications-header" id="<?php echo esc_attr( 'yoast-' . $yoast_seo_type . '-header' ); ?>">
	<span class="dashicons <?php echo esc_attr( 'dashicons-' . $yoast_seo_dashicon ); ?>"></span>
	<?php echo esc_html( $yoast_seo_i18n_title ); ?> (<?php echo (int) $yoast_seo_active_total; ?>)
</h3>

<div id="<?php echo esc_attr( 'yoast-' . $yoast_seo_type ); ?>">

	<?php if ( $yoast_seo_total ) : ?>
		<p><?php echo esc_html( $wpseo_i18n_summary ); ?></p>

		<div class="container yoast-notifications-active" id="<?php echo esc_attr( 'yoast-' . $yoast_seo_type . '-active' ); ?>">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: _yoast_display_notifications() as declared above is safe.
			echo _yoast_display_notifications( $yoast_seo_active, 'active' );
			?>
		</div>

		<?php
		if ( $yoast_seo_dismissed ) {
			$dismissed_paper = new WPSEO_Paper_Presenter(
				esc_html( $yoast_seo_i18n_muted_issues_title ),
				null,
				[
					'paper_id'                 => esc_attr( $yoast_seo_type . '-dismissed' ),
					'paper_id_prefix'          => 'yoast-',
					'class'                    => 'yoast-notifications-dismissed',
					'content'                  => _yoast_display_notifications( $yoast_seo_dismissed, 'dismissed' ),
					'collapsible'              => true,
					'collapsible_header_class' => 'yoast-notification',
				]
			);
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: get_output() output is properly escaped.
			echo $dismissed_paper->get_output();
		}
		?>

	<?php else : ?>

		<p><?php echo esc_html( $yoast_seo_i18n_no_issues ); ?></p>

	<?php endif; ?>
</div>
