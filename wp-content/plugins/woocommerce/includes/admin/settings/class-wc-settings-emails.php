<?php
/**
 * WooCommerce Email Settings
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Emails' ) ) :

/**
 * WC_Settings_Emails
 */
class WC_Settings_Emails extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'email';
		$this->label = __( 'Emails', 'woocommerce' );

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {
		$sections = array(
			''         => __( 'Email Options', 'woocommerce' )
		);

		// Define emails that can be customised here
		$mailer 			= WC()->mailer();
		$email_templates 	= $mailer->get_emails();

		foreach ( $email_templates as $email ) {
			$title = empty( $email->title ) ? ucfirst( $email->id ) : ucfirst( $email->title );

			$sections[ strtolower( get_class( $email ) ) ] = esc_html( $title );
		}

		return $sections;
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {
		return apply_filters('woocommerce_email_settings', array(

			array( 'type' => 'sectionend', 'id' => 'email_recipient_options' ),

			array(	'title' => __( 'Email Sender Options', 'woocommerce' ), 'type' => 'title', 'desc' => __( 'The following options affect the sender (email address and name) used in WooCommerce emails.', 'woocommerce' ), 'id' => 'email_options' ),

			array(
				'title' => __( '"From" Name', 'woocommerce' ),
				'desc' 		=> '',
				'id' 		=> 'woocommerce_email_from_name',
				'type' 		=> 'text',
				'css' 		=> 'min-width:300px;',
				'default'	=> esc_attr(get_bloginfo('title')),
				'autoload'      => false
			),

			array(
				'title' => __( '"From" Email Address', 'woocommerce' ),
				'desc' 		=> '',
				'id' 		=> 'woocommerce_email_from_address',
				'type' 		=> 'email',
				'custom_attributes' => array(
					'multiple' 	=> 'multiple'
				),
				'css' 		=> 'min-width:300px;',
				'default'	=> get_option('admin_email'),
				'autoload'      => false
			),

			array( 'type' => 'sectionend', 'id' => 'email_options' ),

			array(	'title' => __( 'Email Template', 'woocommerce' ), 'type' => 'title', 'desc' => sprintf(__( 'This section lets you customise the WooCommerce emails. <a href="%s" target="_blank">Click here to preview your email template</a>. For more advanced control copy <code>woocommerce/templates/emails/</code> to <code>yourtheme/woocommerce/emails/</code>.', 'woocommerce' ), wp_nonce_url(admin_url('?preview_woocommerce_mail=true'), 'preview-mail')), 'id' => 'email_template_options' ),

			array(
				'title' => __( 'Header Image', 'woocommerce' ),
				'desc' 		=> sprintf(__( 'Enter a URL to an image you want to show in the email\'s header. Upload your image using the <a href="%s">media uploader</a>.', 'woocommerce' ), admin_url('media-new.php')),
				'id' 		=> 'woocommerce_email_header_image',
				'type' 		=> 'text',
				'css' 		=> 'min-width:300px;',
				'default'	=> '',
				'autoload'  => false
			),

			array(
				'title' => __( 'Email Footer Text', 'woocommerce' ),
				'desc' 		=> __( 'The text to appear in the footer of WooCommerce emails.', 'woocommerce' ),
				'id' 		=> 'woocommerce_email_footer_text',
				'css' 		=> 'width:100%; height: 75px;',
				'type' 		=> 'textarea',
				'default'	=> get_bloginfo('title') . ' - ' . __( 'Powered by WooCommerce', 'woocommerce' ),
				'autoload'  => false
			),

			array(
				'title' => __( 'Base Colour', 'woocommerce' ),
				'desc' 		=> __( 'The base colour for WooCommerce email templates. Default <code>#557da1</code>.', 'woocommerce' ),
				'id' 		=> 'woocommerce_email_base_color',
				'type' 		=> 'color',
				'css' 		=> 'width:6em;',
				'default'	=> '#557da1',
				'autoload'  => false
			),

			array(
				'title' => __( 'Background Colour', 'woocommerce' ),
				'desc' 		=> __( 'The background colour for WooCommerce email templates. Default <code>#f5f5f5</code>.', 'woocommerce' ),
				'id' 		=> 'woocommerce_email_background_color',
				'type' 		=> 'color',
				'css' 		=> 'width:6em;',
				'default'	=> '#f5f5f5',
				'autoload'  => false
			),

			array(
				'title' => __( 'Email Body Background Colour', 'woocommerce' ),
				'desc' 		=> __( 'The main body background colour. Default <code>#fdfdfd</code>.', 'woocommerce' ),
				'id' 		=> 'woocommerce_email_body_background_color',
				'type' 		=> 'color',
				'css' 		=> 'width:6em;',
				'default'	=> '#fdfdfd',
				'autoload'  => false
			),

			array(
				'title' => __( 'Email Body Text Colour', 'woocommerce' ),
				'desc' 		=> __( 'The main body text colour. Default <code>#505050</code>.', 'woocommerce' ),
				'id' 		=> 'woocommerce_email_text_color',
				'type' 		=> 'color',
				'css' 		=> 'width:6em;',
				'default'	=> '#505050',
				'autoload'  => false
			),

			array( 'type' => 'sectionend', 'id' => 'email_template_options' ),

		)); // End email settings
	}

	/**
	 * Output the settings
	 */
	public function output() {
		global $current_section;

		// Define emails that can be customised here
		$mailer 			= WC()->mailer();
		$email_templates 	= $mailer->get_emails();

		if ( $current_section ) {
 			foreach ( $email_templates as $email ) {
				if ( strtolower( get_class( $email ) ) == $current_section ) {
					$email->admin_options();
					break;
				}
			}
 		} else {
			$settings = $this->get_settings();

			WC_Admin_Settings::output_fields( $settings );
		}
	}

	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		if ( ! $current_section ) {

			$settings = $this->get_settings();
			WC_Admin_Settings::save_fields( $settings );

		} else {

			// Load mailer
			$mailer = WC()->mailer();

			if ( class_exists( $current_section ) ) {
				$current_section_class = new $current_section();
				do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section_class->id );
				WC()->mailer()->init();
			} else {
				do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
			}
		}
	}
}

endif;

return new WC_Settings_Emails();