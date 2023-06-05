<?php
/**
 * WooCommerce Email Settings
 *
 * @package WooCommerce\Admin
 * @version 2.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Settings_Emails', false ) ) {
	return new WC_Settings_Emails();
}

/**
 * WC_Settings_Emails.
 */
class WC_Settings_Emails extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'email';
		$this->label = __( 'Emails', 'woocommerce' );

		add_action( 'woocommerce_admin_field_email_notification', array( $this, 'email_notification_setting' ) );
		parent::__construct();
	}

	/**
	 * Get own sections.
	 *
	 * @return array
	 */
	protected function get_own_sections() {
		return array(
			'' => __( 'Email options', 'woocommerce' ),
		);
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		$desc_help_text = sprintf(
		/* translators: %1$s: Link to WP Mail Logging plugin, %2$s: Link to Email FAQ support page. */
			__( 'To ensure your store&rsquo;s notifications arrive in your and your customers&rsquo; inboxes, we recommend connecting your email address to your domain and setting up a dedicated SMTP server. If something doesn&rsquo;t seem to be sending correctly, install the <a href="%1$s">WP Mail Logging Plugin</a> or check the <a href="%2$s">Email FAQ page</a>.', 'woocommerce' ),
			'https://wordpress.org/plugins/wp-mail-logging/',
			'https://docs.woocommerce.com/document/email-faq'
		);
		$settings =
			array(
				array(
					'title' => __( 'Email notifications', 'woocommerce' ),
					/* translators: %s: help description with link to WP Mail logging and support page. */
					'desc'  => sprintf( __( 'Email notifications sent from WooCommerce are listed below. Click on an email to configure it.<br>%s', 'woocommerce' ), $desc_help_text ),
					'type'  => 'title',
					'id'    => 'email_notification_settings',
				),

				array( 'type' => 'email_notification' ),

				array(
					'type' => 'sectionend',
					'id'   => 'email_notification_settings',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'email_recipient_options',
				),

				array(
					'title' => __( 'Email sender options', 'woocommerce' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'email_options',
				),

				array(
					'title'    => __( '"From" name', 'woocommerce' ),
					'desc'     => __( 'How the sender name appears in outgoing WooCommerce emails.', 'woocommerce' ),
					'id'       => 'woocommerce_email_from_name',
					'type'     => 'text',
					'css'      => 'min-width:400px;',
					'default'  => esc_attr( get_bloginfo( 'name', 'display' ) ),
					'autoload' => false,
					'desc_tip' => true,
				),

				array(
					'title'             => __( '"From" address', 'woocommerce' ),
					'desc'              => __( 'How the sender email appears in outgoing WooCommerce emails.', 'woocommerce' ),
					'id'                => 'woocommerce_email_from_address',
					'type'              => 'email',
					'custom_attributes' => array(
						'multiple' => 'multiple',
					),
					'css'               => 'min-width:400px;',
					'default'           => get_option( 'admin_email' ),
					'autoload'          => false,
					'desc_tip'          => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'email_options',
				),

				array(
					'title' => __( 'Email template', 'woocommerce' ),
					'type'  => 'title',
					/* translators: %s: Nonced email preview link */
					'desc'  => sprintf( __( 'This section lets you customize the WooCommerce emails. <a href="%s" target="_blank">Click here to preview your email template</a>.', 'woocommerce' ), wp_nonce_url( admin_url( '?preview_woocommerce_mail=true' ), 'preview-mail' ) ),
					'id'    => 'email_template_options',
				),

				array(
					'title'       => __( 'Header image', 'woocommerce' ),
					'desc'        => __( 'Paste the URL of an image you want to show in the email header. Upload images using the media uploader (Media > Add New).', 'woocommerce' ),
					'id'          => 'woocommerce_email_header_image',
					'type'        => 'text',
					'css'         => 'min-width:400px;',
					'placeholder' => __( 'N/A', 'woocommerce' ),
					'default'     => '',
					'autoload'    => false,
					'desc_tip'    => true,
				),

				array(
					'title'       => __( 'Footer text', 'woocommerce' ),
					/* translators: %s: Available placeholders for use */
					'desc'        => __( 'The text to appear in the footer of all WooCommerce emails.', 'woocommerce' ) . ' ' . sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '{site_title} {site_url}' ),
					'id'          => 'woocommerce_email_footer_text',
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'N/A', 'woocommerce' ),
					'type'        => 'textarea',
					'default'     => '{site_title} &mdash; Built with {WooCommerce}',
					'autoload'    => false,
					'desc_tip'    => true,
				),

				array(
					'title'    => __( 'Base color', 'woocommerce' ),
					/* translators: %s: default color */
					'desc'     => sprintf( __( 'The base color for WooCommerce email templates. Default %s.', 'woocommerce' ), '<code>#7f54b3</code>' ),
					'id'       => 'woocommerce_email_base_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#7f54b3',
					'autoload' => false,
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Background color', 'woocommerce' ),
					/* translators: %s: default color */
					'desc'     => sprintf( __( 'The background color for WooCommerce email templates. Default %s.', 'woocommerce' ), '<code>#f7f7f7</code>' ),
					'id'       => 'woocommerce_email_background_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#f7f7f7',
					'autoload' => false,
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Body background color', 'woocommerce' ),
					/* translators: %s: default color */
					'desc'     => sprintf( __( 'The main body background color. Default %s.', 'woocommerce' ), '<code>#ffffff</code>' ),
					'id'       => 'woocommerce_email_body_background_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#ffffff',
					'autoload' => false,
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Body text color', 'woocommerce' ),
					/* translators: %s: default color */
					'desc'     => sprintf( __( 'The main body text color. Default %s.', 'woocommerce' ), '<code>#3c3c3c</code>' ),
					'id'       => 'woocommerce_email_text_color',
					'type'     => 'color',
					'css'      => 'width:6em;',
					'default'  => '#3c3c3c',
					'autoload' => false,
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'email_template_options',
				),

				array(
					'title' => __( 'Store management insights', 'woocommerce' ),
					'type'  => 'title',
					'id'    => 'email_merchant_notes',
				),

				array(
					'title'         => __( 'Enable email insights', 'woocommerce' ),
					'desc'          => __( 'Receive email notifications with additional guidance to complete the basic store setup and helpful insights', 'woocommerce' ),
					'id'            => 'woocommerce_merchant_email_notifications',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
					'default'       => 'no',
					'autoload'      => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'email_merchant_notes',
				),
			);

		return apply_filters( 'woocommerce_email_settings', $settings );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		// Define emails that can be customised here.
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		if ( $current_section ) {
			foreach ( $email_templates as $email_key => $email ) {
				if ( strtolower( $email_key ) === $current_section ) {
					$this->run_email_admin_options( $email );
					break;
				}
			}
		}

		parent::output();
	}

	/**
	 * Run the 'admin_options' method on a given email.
	 * This method exists to easy unit testing.
	 *
	 * @param object $email The email object to run the method on.
	 */
	protected function run_email_admin_options( $email ) {
		$email->admin_options();
	}

	/**
	 * Save settings.
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {
			$this->save_settings_for_current_section();
			$this->do_update_options_action();
		} else {
			$wc_emails = WC_Emails::instance();

			if ( in_array( $current_section, array_map( 'sanitize_title', array_keys( $wc_emails->get_emails() ) ), true ) ) {
				foreach ( $wc_emails->get_emails() as $email_id => $email ) {
					if ( sanitize_title( $email_id ) === $current_section ) {
						$this->do_update_options_action( $email->id );
					}
				}
			} else {
				$this->save_settings_for_current_section();
				$this->do_update_options_action();
			}
		}
	}

	/**
	 * Output email notification settings.
	 */
	public function email_notification_setting() {
		// Define emails that can be customised here.
		$mailer          = WC()->mailer();
		$email_templates = $mailer->get_emails();

		?>
		<tr valign="top">
		<td class="wc_emails_wrapper" colspan="2">
			<table class="wc_emails widefat" cellspacing="0">
				<thead>
					<tr>
						<?php
						$columns = apply_filters(
							'woocommerce_email_setting_columns',
							array(
								'status'     => '',
								'name'       => __( 'Email', 'woocommerce' ),
								'email_type' => __( 'Content type', 'woocommerce' ),
								'recipient'  => __( 'Recipient(s)', 'woocommerce' ),
								'actions'    => '',
							)
						);
						foreach ( $columns as $key => $column ) {
							echo '<th class="wc-email-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
						}
						?>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $email_templates as $email_key => $email ) {
							echo '<tr>';

							foreach ( $columns as $key => $column ) {

								switch ( $key ) {
									case 'name':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=' . strtolower( $email_key ) ) ) . '">' . esc_html( $email->get_title() ) . '</a>
										' . wc_help_tip( $email->get_description() ) . '
									</td>';
										break;
									case 'recipient':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										' . esc_html( $email->is_customer_email() ? __( 'Customer', 'woocommerce' ) : $email->get_recipient() ) . '
									</td>';
										break;
									case 'status':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">';

										if ( $email->is_manual() ) {
											echo '<span class="status-manual tips" data-tip="' . esc_attr__( 'Manually sent', 'woocommerce' ) . '">' . esc_html__( 'Manual', 'woocommerce' ) . '</span>';
										} elseif ( $email->is_enabled() ) {
											echo '<span class="status-enabled tips" data-tip="' . esc_attr__( 'Enabled', 'woocommerce' ) . '">' . esc_html__( 'Yes', 'woocommerce' ) . '</span>';
										} else {
											echo '<span class="status-disabled tips" data-tip="' . esc_attr__( 'Disabled', 'woocommerce' ) . '">-</span>';
										}

										echo '</td>';
										break;
									case 'email_type':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										' . esc_html( $email->get_content_type() ) . '
									</td>';
										break;
									case 'actions':
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
										<a class="button alignright" href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=email&section=' . strtolower( $email_key ) ) ) . '">' . esc_html__( 'Manage', 'woocommerce' ) . '</a>
									</td>';
										break;
									default:
										do_action( 'woocommerce_email_setting_column_' . $key, $email );
										break;
								}
							}

							echo '</tr>';
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}
}

return new WC_Settings_Emails();
