<?php
/**
 * Upgrade to pro options
 */
function twenty_minutes_upgrade_pro_options( $wp_customize ) {

	$wp_customize->add_section(
		'upgrade_premium',
		array(
			'title'    => esc_html( TWENTY_MINUTES_PRO_NAME ),
			'priority' => 1,
		)
	);

	class Twenty_Minutes_Pro_Button_Customize_Control extends WP_Customize_Control {
		public $type = 'upgrade_premium';

		function render_content() {
			?>
			<div class="pro_info">
				<ul>

				    <li><a class="upgrade-to-pro pro-btn" href="<?php echo esc_url( TWENTY_MINUTES_PREMIUM_PAGE ); ?>" target="_blank"><i class="dashicons dashicons-cart"></i><?php esc_html_e( 'Upgrade Pro', 'twenty-minutes' ); ?> </a></li>

					<li><a class="upgrade-to-pro" href="<?php echo esc_url( TWENTY_MINUTES_PRO_DEMO ); ?>" target="_blank"><i class="dashicons dashicons-awards"></i><?php esc_html_e( 'Premium Demo', 'twenty-minutes' ); ?> </a></li>
			
					<li><a class="upgrade-to-pro" href="<?php echo esc_url( TWENTY_MINUTES_REVIEW ); ?>" target="_blank"><i class="dashicons dashicons-star-filled"></i><?php esc_html_e( 'Rate Us', 'twenty-minutes' ); ?> </a></li>
					
					<li><a class="upgrade-to-pro" href="<?php echo esc_url( TWENTY_MINUTES_SUPPORT ); ?>" target="_blank"><i class="dashicons dashicons-lightbulb"></i><?php esc_html_e( 'Support Forum', 'twenty-minutes' ); ?> </a></li>
					
					<li><a class="upgrade-to-pro" href="<?php echo esc_url( TWENTY_MINUTES_THEME_PAGE ); ?>" target="_blank"><i class="dashicons dashicons-admin-appearance"></i><?php esc_html_e( 'Theme Page', 'twenty-minutes' ); ?> </a></li>

					<li><a class="upgrade-to-pro" href="<?php echo esc_url( TWENTY_MINUTES_THEME_DOCUMENTATION ); ?>" target="_blank"><i class="dashicons dashicons-visibility"></i><?php esc_html_e( 'Theme Documentation', 'twenty-minutes' ); ?> </a></li>

				</ul>
			</div>
			<?php
		}
	}

	$wp_customize->add_setting(
		'pro_info_buttons',
		array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'twenty_minutes_sanitize_text',
		)
	);

	$wp_customize->add_control(
		new Twenty_Minutes_Pro_Button_Customize_Control(
			$wp_customize,
			'pro_info_buttons',
			array(
				'section' => 'upgrade_premium',
			)
		)
	);
}
add_action( 'customize_register', 'twenty_minutes_upgrade_pro_options' );
