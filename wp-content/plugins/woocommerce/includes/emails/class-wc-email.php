<?php
/**
 * Class WC_Email file.
 *
 * @package WooCommerce\Emails
 */

use Pelago\Emogrifier\CssInliner;
use Pelago\Emogrifier\HtmlProcessor\CssToAttributeConverter;
use Pelago\Emogrifier\HtmlProcessor\HtmlPruner;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WC_Email', false ) ) {
	return;
}

/**
 * Email Class
 *
 * WooCommerce Email Class which is extended by specific email template classes to add emails to WooCommerce
 *
 * @class       WC_Email
 * @version     2.5.0
 * @package     WooCommerce\Classes\Emails
 * @extends     WC_Settings_API
 */
class WC_Email extends WC_Settings_API {

	/**
	 * Email method ID.
	 *
	 * @var String
	 */
	public $id;

	/**
	 * Email method title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * 'yes' if the method is enabled.
	 *
	 * @var string yes, no
	 */
	public $enabled;

	/**
	 * Description for the email.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Default heading.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @var string
	 */
	public $heading = '';

	/**
	 * Default subject.
	 *
	 * Supported for backwards compatibility but we recommend overloading the
	 * get_default_x methods instead so localization can be done when needed.
	 *
	 * @var string
	 */
	public $subject = '';

	/**
	 * Plain text template path.
	 *
	 * @var string
	 */
	public $template_plain;

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;

	/**
	 * Template path.
	 *
	 * @var string
	 */
	public $template_base;

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient;

	/**
	 * Object this email is for, for example a customer, product, or email.
	 *
	 * @var object|bool
	 */
	public $object;

	/**
	 * Mime boundary (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary;

	/**
	 * Mime boundary header (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary_header;

	/**
	 * True when email is being sent.
	 *
	 * @var bool
	 */
	public $sending;

	/**
	 * True when the email notification is sent manually only.
	 *
	 * @var bool
	 */
	protected $manual = false;

	/**
	 * True when the email notification is sent to customers.
	 *
	 * @var bool
	 */
	protected $customer_email = false;

	/**
	 *  List of preg* regular expression patterns to search for,
	 *  used in conjunction with $plain_replace.
	 *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	 *
	 *  @var array $plain_search
	 *  @see $plain_replace
	 */
	public $plain_search = array(
		"/\r/",                                                  // Non-legal carriage return.
		'/&(nbsp|#0*160);/i',                                    // Non-breaking space.
		'/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i', // Double quotes.
		'/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',               // Single quotes.
		'/&gt;/i',                                               // Greater-than.
		'/&lt;/i',                                               // Less-than.
		'/&#0*38;/i',                                            // Ampersand.
		'/&amp;/i',                                              // Ampersand.
		'/&(copy|#0*169);/i',                                    // Copyright.
		'/&(trade|#0*8482|#0*153);/i',                           // Trademark.
		'/&(reg|#0*174);/i',                                     // Registered.
		'/&(mdash|#0*151|#0*8212);/i',                           // mdash.
		'/&(ndash|minus|#0*8211|#0*8722);/i',                    // ndash.
		'/&(bull|#0*149|#0*8226);/i',                            // Bullet.
		'/&(pound|#0*163);/i',                                   // Pound sign.
		'/&(euro|#0*8364);/i',                                   // Euro sign.
		'/&(dollar|#0*36);/i',                                   // Dollar sign.
		'/&[^&\s;]+;/i',                                         // Unknown/unhandled entities.
		'/[ ]{2,}/',                                             // Runs of spaces, post-handling.
	);

	/**
	 *  List of pattern replacements corresponding to patterns searched.
	 *
	 *  @var array $plain_replace
	 *  @see $plain_search
	 */
	public $plain_replace = array(
		'',                                             // Non-legal carriage return.
		' ',                                            // Non-breaking space.
		'"',                                            // Double quotes.
		"'",                                            // Single quotes.
		'>',                                            // Greater-than.
		'<',                                            // Less-than.
		'&',                                            // Ampersand.
		'&',                                            // Ampersand.
		'(c)',                                          // Copyright.
		'(tm)',                                         // Trademark.
		'(R)',                                          // Registered.
		'--',                                           // mdash.
		'-',                                            // ndash.
		'*',                                            // Bullet.
		'£',                                            // Pound sign.
		'EUR',                                          // Euro sign. € ?.
		'$',                                            // Dollar sign.
		'',                                             // Unknown/unhandled entities.
		' ',                                             // Runs of spaces, post-handling.
	);

	/**
	 * Strings to find/replace in subjects/headings.
	 *
	 * @var array
	 */
	protected $placeholders = array();

	/**
	 * Strings to find in subjects/headings.
	 *
	 * @deprecated 3.2.0 in favour of placeholders
	 * @var array
	 */
	public $find = array();

	/**
	 * Strings to replace in subjects/headings.
	 *
	 * @deprecated 3.2.0 in favour of placeholders
	 * @var array
	 */
	public $replace = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Find/replace.
		$this->placeholders = array_merge(
			array(
				'{site_title}'   => $this->get_blogname(),
				'{site_address}' => wp_parse_url( home_url(), PHP_URL_HOST ),
				'{site_url}'     => wp_parse_url( home_url(), PHP_URL_HOST ),
			),
			$this->placeholders
		);

		// Init settings.
		$this->init_form_fields();
		$this->init_settings();

		// Default template base if not declared in child constructor.
		if ( is_null( $this->template_base ) ) {
			$this->template_base = WC()->plugin_path() . '/templates/';
		}

		$this->email_type = $this->get_option( 'email_type' );
		$this->enabled    = $this->get_option( 'enabled' );

		add_action( 'phpmailer_init', array( $this, 'handle_multipart' ) );
		add_action( 'woocommerce_update_options_email_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Handle multipart mail.
	 *
	 * @param  PHPMailer $mailer PHPMailer object.
	 * @return PHPMailer
	 */
	public function handle_multipart( $mailer ) {
		if ( ! $this->sending ) {
			return $mailer;
		}

		if ( 'multipart' === $this->get_email_type() ) {
			$mailer->AltBody = wordwrap( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) )
			);
		} else {
			$mailer->AltBody = ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		$this->sending = false;
		return $mailer;
	}

	/**
	 * Format email string.
	 *
	 * @param mixed $string Text to replace placeholders in.
	 * @return string
	 */
	public function format_string( $string ) {
		$find    = array_keys( $this->placeholders );
		$replace = array_values( $this->placeholders );

		// If using legacy find replace, add those to our find/replace arrays first. @todo deprecate in 4.0.0.
		$find    = array_merge( (array) $this->find, $find );
		$replace = array_merge( (array) $this->replace, $replace );

		// Take care of blogname which is no longer defined as a valid placeholder.
		$find[]    = '{blogname}';
		$replace[] = $this->get_blogname();

		// If using the older style filters for find and replace, ensure the array is associative and then pass through filters. @todo deprecate in 4.0.0.
		if ( has_filter( 'woocommerce_email_format_string_replace' ) || has_filter( 'woocommerce_email_format_string_find' ) ) {
			$legacy_find    = $this->find;
			$legacy_replace = $this->replace;

			foreach ( $this->placeholders as $find => $replace ) {
				$legacy_key                    = sanitize_title( str_replace( '_', '-', trim( $find, '{}' ) ) );
				$legacy_find[ $legacy_key ]    = $find;
				$legacy_replace[ $legacy_key ] = $replace;
			}

			$string = str_replace( apply_filters( 'woocommerce_email_format_string_find', $legacy_find, $this ), apply_filters( 'woocommerce_email_format_string_replace', $legacy_replace, $this ), $string );
		}

		/**
		 * Filter for main find/replace.
		 *
		 * @since 3.2.0
		 */
		return apply_filters( 'woocommerce_email_format_string', str_replace( $find, $replace, $string ), $this );
	}

	/**
	 * Set the locale to the store locale for customer emails to make sure emails are in the store language.
	 */
	public function setup_locale() {

		/**
		 * Filter the ability to switch email locale.
		 *
		 * @since 6.8.0
		 *
		 * @param bool $default_value The default returned value.
		 * @param WC_Email $this The WC_Email object.
		 */
		$switch_email_locale = apply_filters( 'woocommerce_allow_switching_email_locale', true, $this );

		if ( $switch_email_locale && $this->is_customer_email() && apply_filters( 'woocommerce_email_setup_locale', true ) ) {
			wc_switch_to_site_locale();
		}
	}

	/**
	 * Restore the locale to the default locale. Use after finished with setup_locale.
	 */
	public function restore_locale() {

		/**
		 * Filter the ability to restore email locale.
		 *
		 * @since 6.8.0
		 *
		 * @param bool $default_value The default returned value.
		 * @param WC_Email $this The WC_Email object.
		 */
		$restore_email_locale = apply_filters( 'woocommerce_allow_restoring_email_locale', true, $this );

		if ( $restore_email_locale && $this->is_customer_email() && apply_filters( 'woocommerce_email_restore_locale', true ) ) {
			wc_restore_locale();
		}
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return $this->subject;
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return $this->heading;
	}

	/**
	 * Default content to show below main email content.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_default_additional_content() {
		return '';
	}

	/**
	 * Return content from the additional_content field.
	 *
	 * Displayed above the footer.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_additional_content() {
		/**
		 * Provides an opportunity to inspect and modify additional content for the email.
		 *
		 * @since 3.7.0
		 *
		 * @param string      $additional_content Additional content to be added to the email.
		 * @param object|bool $object             The object (ie, product or order) this email relates to, if any.
		 * @param WC_Email    $email              WC_Email instance managing the email.
		 */
		return apply_filters( 'woocommerce_email_additional_content_' . $this->id, $this->format_string( $this->get_option( 'additional_content', $this->get_default_additional_content() ) ), $this->object, $this );
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_subject() {
		return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->get_option( 'subject', $this->get_default_subject() ) ), $this->object, $this );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_heading() {
		return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->get_option( 'heading', $this->get_default_heading() ) ), $this->object, $this );
	}

	/**
	 * Get valid recipients.
	 *
	 * @return string
	 */
	public function get_recipient() {
		$recipient  = apply_filters( 'woocommerce_email_recipient_' . $this->id, $this->recipient, $this->object, $this );
		$recipients = array_map( 'trim', explode( ',', $recipient ) );
		$recipients = array_filter( $recipients, 'is_email' );
		return implode( ', ', $recipients );
	}

	/**
	 * Get email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$header = 'Content-Type: ' . $this->get_content_type() . "\r\n";

		if ( in_array( $this->id, array( 'new_order', 'cancelled_order', 'failed_order' ), true ) ) {
			if ( $this->object && $this->object->get_billing_email() && ( $this->object->get_billing_first_name() || $this->object->get_billing_last_name() ) ) {
				$header .= 'Reply-to: ' . $this->object->get_billing_first_name() . ' ' . $this->object->get_billing_last_name() . ' <' . $this->object->get_billing_email() . ">\r\n";
			}
		} elseif ( $this->get_from_address() && $this->get_from_name() ) {
			$header .= 'Reply-to: ' . $this->get_from_name() . ' <' . $this->get_from_address() . ">\r\n";
		}

		return apply_filters( 'woocommerce_email_headers', $header, $this->id, $this->object, $this );
	}

	/**
	 * Get email attachments.
	 *
	 * @return array
	 */
	public function get_attachments() {
		return apply_filters( 'woocommerce_email_attachments', array(), $this->id, $this->object, $this );
	}

	/**
	 * Return email type.
	 *
	 * @return string
	 */
	public function get_email_type() {
		return $this->email_type && class_exists( 'DOMDocument' ) ? $this->email_type : 'plain';
	}

	/**
	 * Get email content type.
	 *
	 * @param string $default_content_type Default wp_mail() content type.
	 * @return string
	 */
	public function get_content_type( $default_content_type = '' ) {
		switch ( $this->get_email_type() ) {
			case 'html':
				$content_type = 'text/html';
				break;
			case 'multipart':
				$content_type = 'multipart/alternative';
				break;
			default:
				$content_type = 'text/plain';
				break;
		}

		return apply_filters( 'woocommerce_email_content_type', $content_type, $this, $default_content_type );
	}

	/**
	 * Return the email's title
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'woocommerce_email_title', $this->title, $this );
	}

	/**
	 * Return the email's description
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'woocommerce_email_description', $this->description, $this );
	}

	/**
	 * Proxy to parent's get_option and attempt to localize the result using gettext.
	 *
	 * @param string $key Option key.
	 * @param mixed  $empty_value Value to use when option is empty.
	 * @return string
	 */
	public function get_option( $key, $empty_value = null ) {
		$value = parent::get_option( $key, $empty_value );
		return apply_filters( 'woocommerce_email_get_option', $value, $this, $value, $key, $empty_value );
	}

	/**
	 * Checks if this email is enabled and will be sent.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return apply_filters( 'woocommerce_email_enabled_' . $this->id, 'yes' === $this->enabled, $this->object, $this );
	}

	/**
	 * Checks if this email is manually sent
	 *
	 * @return bool
	 */
	public function is_manual() {
		return $this->manual;
	}

	/**
	 * Checks if this email is customer focussed.
	 *
	 * @return bool
	 */
	public function is_customer_email() {
		return $this->customer_email;
	}

	/**
	 * Get WordPress blog name.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Get email content.
	 *
	 * @return string
	 */
	public function get_content() {
		$this->sending = true;

		if ( 'plain' === $this->get_email_type() ) {
			$email_content = wordwrap( preg_replace( $this->plain_search, $this->plain_replace, wp_strip_all_tags( $this->get_content_plain() ) ), 70 );
		} else {
			$email_content = $this->get_content_html();
		}

		return $email_content;
	}

	/**
	 * Apply inline styles to dynamic content.
	 *
	 * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
	 *
	 * @version 4.0.0
	 * @param string|null $content Content that will receive inline styles.
	 * @return string
	 */
	public function style_inline( $content ) {
		if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ), true ) ) {
			ob_start();
			wc_get_template( 'emails/email-styles.php' );
			$css = apply_filters( 'woocommerce_email_styles', ob_get_clean(), $this );

			$css_inliner_class = CssInliner::class;

			if ( $this->supports_emogrifier() && class_exists( $css_inliner_class ) ) {
				try {
					$css_inliner = CssInliner::fromHtml( $content )->inlineCss( $css );

					do_action( 'woocommerce_emogrifier', $css_inliner, $this );

					$dom_document = $css_inliner->getDomDocument();

					HtmlPruner::fromDomDocument( $dom_document )->removeElementsWithDisplayNone();
					$content = CssToAttributeConverter::fromDomDocument( $dom_document )
						->convertCssToVisualAttributes()
						->render();
				} catch ( Exception $e ) {
					$logger = wc_get_logger();
					$logger->error( $e->getMessage(), array( 'source' => 'emogrifier' ) );
				}
			} else {
				$content = '<style type="text/css">' . $css . '</style>' . $content;
			}
		}

		return $content;
	}

	/**
	 * Return if emogrifier library is supported.
	 *
	 * @version 4.0.0
	 * @since 3.5.0
	 * @return bool
	 */
	protected function supports_emogrifier() {
		return class_exists( 'DOMDocument' );
	}

	/**
	 * Get the email content in plain text format.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return '';
	}

	/**
	 * Get the email content in HTML format.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return '';
	}

	/**
	 * Get the from name for outgoing emails.
	 *
	 * @param string $from_name Default wp_mail() name associated with the "from" email address.
	 * @return string
	 */
	public function get_from_name( $from_name = '' ) {
		$from_name = apply_filters( 'woocommerce_email_from_name', get_option( 'woocommerce_email_from_name' ), $this, $from_name );
		return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	}

	/**
	 * Get the from address for outgoing emails.
	 *
	 * @param string $from_email Default wp_mail() email address to send from.
	 * @return string
	 */
	public function get_from_address( $from_email = '' ) {
		$from_email = apply_filters( 'woocommerce_email_from_address', get_option( 'woocommerce_email_from_address' ), $this, $from_email );
		return sanitize_email( $from_email );
	}

	/**
	 * Send an email.
	 *
	 * @param string $to Email to.
	 * @param string $subject Email subject.
	 * @param string $message Email message.
	 * @param string $headers Email headers.
	 * @param array  $attachments Email attachments.
	 * @return bool success
	 */
	public function send( $to, $subject, $message, $headers, $attachments ) {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$message              = apply_filters( 'woocommerce_mail_content', $this->style_inline( $message ) );
		$mail_callback        = apply_filters( 'woocommerce_mail_callback', 'wp_mail', $this );
		$mail_callback_params = apply_filters( 'woocommerce_mail_callback_params', array( $to, wp_specialchars_decode( $subject ), $message, $headers, $attachments ), $this );
		$return               = $mail_callback( ...$mail_callback_params );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		// Clear the AltBody (if set) so that it does not leak across to different emails.
		$this->clear_alt_body_field();

		/**
		 * Action hook fired when an email is sent.
		 *
		 * @since 5.6.0
		 * @param bool     $return Whether the email was sent successfully.
		 * @param int      $id     Email ID.
		 * @param WC_Email $this   WC_Email instance.
		 */
		do_action( 'woocommerce_email_sent', $return, $this->id, $this );

		return $return;
	}

	/**
	 * Initialise Settings Form Fields - these are generic email options most will use.
	 */
	public function init_form_fields() {
		/* translators: %s: list of placeholders */
		$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
		$this->form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce' ),
				'default' => 'yes',
			),
			'subject'            => array(
				'title'       => __( 'Subject', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'            => array(
				'title'       => __( 'Email heading', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'additional_content' => array(
				'title'       => __( 'Additional content', 'woocommerce' ),
				'description' => __( 'Text to appear below the main email content.', 'woocommerce' ) . ' ' . $placeholder_text,
				'css'         => 'width:400px; height: 75px;',
				'placeholder' => __( 'N/A', 'woocommerce' ),
				'type'        => 'textarea',
				'default'     => $this->get_default_additional_content(),
				'desc_tip'    => true,
			),
			'email_type'         => array(
				'title'       => __( 'Email type', 'woocommerce' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Email type options.
	 *
	 * @return array
	 */
	public function get_email_type_options() {
		$types = array( 'plain' => __( 'Plain text', 'woocommerce' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = __( 'HTML', 'woocommerce' );
			$types['multipart'] = __( 'Multipart', 'woocommerce' );
		}

		return $types;
	}

	/**
	 * Admin Panel Options Processing.
	 */
	public function process_admin_options() {
		// Save regular options.
		parent::process_admin_options();

		$post_data = $this->get_post_data();

		// Save templates.
		if ( isset( $post_data['template_html_code'] ) ) {
			$this->save_template( $post_data['template_html_code'], $this->template_html );
		}
		if ( isset( $post_data['template_plain_code'] ) ) {
			$this->save_template( $post_data['template_plain_code'], $this->template_plain );
		}
	}

	/**
	 * Get template.
	 *
	 * @param  string $type Template type. Can be either 'template_html' or 'template_plain'.
	 * @return string
	 */
	public function get_template( $type ) {
		$type = basename( $type );

		if ( 'template_html' === $type ) {
			return $this->template_html;
		} elseif ( 'template_plain' === $type ) {
			return $this->template_plain;
		}
		return '';
	}

	/**
	 * Save the email templates.
	 *
	 * @since 2.4.0
	 * @param string $template_code Template code.
	 * @param string $template_path Template path.
	 */
	protected function save_template( $template_code, $template_path ) {
		if ( current_user_can( 'edit_themes' ) && ! empty( $template_code ) && ! empty( $template_path ) ) {
			$saved = false;
			$file  = $this->get_theme_template_file( $template_path );
			$code  = wp_unslash( $template_code );

			if ( is_writeable( $file ) ) { // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writeable
				$f = fopen( $file, 'w+' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

				if ( false !== $f ) {
					fwrite( $f, $code ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
					fclose( $f ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
					$saved = true;
				}
			}

			if ( ! $saved ) {
				$redirect = add_query_arg( 'wc_error', rawurlencode( __( 'Could not write to template file.', 'woocommerce' ) ) );
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}

	/**
	 * Get the template file in the current theme.
	 *
	 * @param  string $template Template name.
	 *
	 * @return string
	 */
	public function get_theme_template_file( $template ) {
		return get_stylesheet_directory() . '/' . apply_filters( 'woocommerce_template_directory', 'woocommerce', $template ) . '/' . $template;
	}

	/**
	 * Move template action.
	 *
	 * @param string $template_type Template type.
	 */
	protected function move_template_action( $template_type ) {
		$template = $this->get_template( $template_type );
		if ( ! empty( $template ) ) {
			$theme_file = $this->get_theme_template_file( $template );

			if ( wp_mkdir_p( dirname( $theme_file ) ) && ! file_exists( $theme_file ) ) {

				// Locate template file.
				$core_file     = $this->template_base . $template;
				$template_file = apply_filters( 'woocommerce_locate_core_template', $core_file, $template, $this->template_base, $this->id );

				// Copy template file.
				copy( $template_file, $theme_file );

				/**
				 * Action hook fired after copying email template file.
				 *
				 * @param string $template_type The copied template type
				 * @param string $email The email object
				 */
				do_action( 'woocommerce_copy_email_template', $template_type, $this );

				?>
				<div class="updated">
					<p><?php echo esc_html__( 'Template file copied to theme.', 'woocommerce' ); ?></p>
				</div>
				<?php
			}
		}
	}

	/**
	 * Delete template action.
	 *
	 * @param string $template_type Template type.
	 */
	protected function delete_template_action( $template_type ) {
		$template = $this->get_template( $template_type );

		if ( $template ) {
			if ( ! empty( $template ) ) {
				$theme_file = $this->get_theme_template_file( $template );

				if ( file_exists( $theme_file ) ) {
					unlink( $theme_file ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_unlink

					/**
					 * Action hook fired after deleting template file.
					 *
					 * @param string $template The deleted template type
					 * @param string $email The email object
					 */
					do_action( 'woocommerce_delete_email_template', $template_type, $this );
					?>
					<div class="updated">
						<p><?php echo esc_html__( 'Template file deleted from theme.', 'woocommerce' ); ?></p>
					</div>
					<?php
				}
			}
		}
	}

	/**
	 * Admin actions.
	 */
	protected function admin_actions() {
		// Handle any actions.
		if (
			( ! empty( $this->template_html ) || ! empty( $this->template_plain ) )
			&& ( ! empty( $_GET['move_template'] ) || ! empty( $_GET['delete_template'] ) )
			&& 'GET' === $_SERVER['REQUEST_METHOD'] // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		) {
			if ( empty( $_GET['_wc_email_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_GET['_wc_email_nonce'] ) ), 'woocommerce_email_template_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'woocommerce' ) );
			}

			if ( ! current_user_can( 'edit_themes' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'woocommerce' ) );
			}

			if ( ! empty( $_GET['move_template'] ) ) {
				$this->move_template_action( wc_clean( wp_unslash( $_GET['move_template'] ) ) );
			}

			if ( ! empty( $_GET['delete_template'] ) ) {
				$this->delete_template_action( wc_clean( wp_unslash( $_GET['delete_template'] ) ) );
			}
		}
	}

	/**
	 * Admin Options.
	 *
	 * Setup the email settings screen.
	 * Override this in your email.
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
		// Do admin actions.
		$this->admin_actions();
		?>
		<h2><?php echo esc_html( $this->get_title() ); ?> <?php wc_back_link( __( 'Return to emails', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=email' ) ); ?></h2>

		<?php echo wpautop( wp_kses_post( $this->get_description() ) ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

		<?php
		/**
		 * Action hook fired before displaying email settings.
		 *
		 * @param string $email The email object
		 */
		do_action( 'woocommerce_email_settings_before', $this );
		?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table>

		<?php
		/**
		 * Action hook fired after displaying email settings.
		 *
		 * @param string $email The email object
		 */
		do_action( 'woocommerce_email_settings_after', $this );
		?>

		<?php

		if ( current_user_can( 'edit_themes' ) && ( ! empty( $this->template_html ) || ! empty( $this->template_plain ) ) ) {
			?>
			<div id="template">
				<?php
				$templates = array(
					'template_html'  => __( 'HTML template', 'woocommerce' ),
					'template_plain' => __( 'Plain text template', 'woocommerce' ),
				);

				foreach ( $templates as $template_type => $title ) :
					$template = $this->get_template( $template_type );

					if ( empty( $template ) ) {
						continue;
					}

					$local_file    = $this->get_theme_template_file( $template );
					$core_file     = $this->template_base . $template;
					$template_file = apply_filters( 'woocommerce_locate_core_template', $core_file, $template, $this->template_base, $this->id );
					$template_dir  = apply_filters( 'woocommerce_template_directory', 'woocommerce', $template );
					?>
					<div class="template <?php echo esc_attr( $template_type ); ?>">
						<h4><?php echo wp_kses_post( $title ); ?></h4>

						<?php if ( file_exists( $local_file ) ) : ?>
							<p>
								<a href="#" class="button toggle_editor"></a>

								<?php if ( is_writable( $local_file ) ) : // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable ?>
									<a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'move_template', 'saved' ), add_query_arg( 'delete_template', $template_type ) ), 'woocommerce_email_template_nonce', '_wc_email_nonce' ) ); ?>" class="delete_template button">
										<?php esc_html_e( 'Delete template file', 'woocommerce' ); ?>
									</a>
								<?php endif; ?>

								<?php
								/* translators: %s: Path to template file */
								printf( esc_html__( 'This template has been overridden by your theme and can be found in: %s.', 'woocommerce' ), '<code>' . esc_html( trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ) . '</code>' );
								?>
							</p>

							<div class="editor" style="display:none">
								<textarea class="code" cols="25" rows="20"
								<?php
								if ( ! is_writable( $local_file ) ) : // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable
									?>
									readonly="readonly" disabled="disabled"
								<?php else : ?>
									data-name="<?php echo esc_attr( $template_type ) . '_code'; ?>"<?php endif; ?>><?php echo esc_html( file_get_contents( $local_file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents ?></textarea>
							</div>
						<?php elseif ( file_exists( $template_file ) ) : ?>
							<p>
								<a href="#" class="button toggle_editor"></a>

								<?php
								$emails_dir    = get_stylesheet_directory() . '/' . $template_dir . '/emails';
								$templates_dir = get_stylesheet_directory() . '/' . $template_dir;
								$theme_dir     = get_stylesheet_directory();

								if ( is_dir( $emails_dir ) ) {
									$target_dir = $emails_dir;
								} elseif ( is_dir( $templates_dir ) ) {
									$target_dir = $templates_dir;
								} else {
									$target_dir = $theme_dir;
								}

								if ( is_writable( $target_dir ) ) : // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable
									?>
									<a href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'delete_template', 'saved' ), add_query_arg( 'move_template', $template_type ) ), 'woocommerce_email_template_nonce', '_wc_email_nonce' ) ); ?>" class="button">
										<?php esc_html_e( 'Copy file to theme', 'woocommerce' ); ?>
									</a>
								<?php endif; ?>

								<?php
								/* translators: 1: Path to template file 2: Path to theme folder */
								printf( esc_html__( 'To override and edit this email template copy %1$s to your theme folder: %2$s.', 'woocommerce' ), '<code>' . esc_html( plugin_basename( $template_file ) ) . '</code>', '<code>' . esc_html( trailingslashit( basename( get_stylesheet_directory() ) ) . $template_dir . '/' . $template ) . '</code>' );
								?>
							</p>

							<div class="editor" style="display:none">
								<textarea class="code" readonly="readonly" disabled="disabled" cols="25" rows="20"><?php echo esc_html( file_get_contents( $template_file ) );  // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents ?></textarea>
							</div>
						<?php else : ?>
							<p><?php esc_html_e( 'File was not found.', 'woocommerce' ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php
			wc_enqueue_js(
				"jQuery( 'select.email_type' ).on( 'change', function() {

					var val = jQuery( this ).val();

					jQuery( '.template_plain, .template_html' ).show();

					if ( val != 'multipart' && val != 'html' ) {
						jQuery('.template_html').hide();
					}

					if ( val != 'multipart' && val != 'plain' ) {
						jQuery('.template_plain').hide();
					}

				}).trigger( 'change' );

				var view = '" . esc_js( __( 'View template', 'woocommerce' ) ) . "';
				var hide = '" . esc_js( __( 'Hide template', 'woocommerce' ) ) . "';

				jQuery( 'a.toggle_editor' ).text( view ).on( 'click', function() {
					var label = hide;

					if ( jQuery( this ).closest(' .template' ).find( '.editor' ).is(':visible') ) {
						var label = view;
					}

					jQuery( this ).text( label ).closest(' .template' ).find( '.editor' ).slideToggle();
					return false;
				} );

				jQuery( 'a.delete_template' ).on( 'click', function() {
					if ( window.confirm('" . esc_js( __( 'Are you sure you want to delete this template file?', 'woocommerce' ) ) . "') ) {
						return true;
					}

					return false;
				});

				jQuery( '.editor textarea' ).on( 'change', function() {
					var name = jQuery( this ).attr( 'data-name' );

					if ( name ) {
						jQuery( this ).attr( 'name', name );
					}
				});"
			);
		}
	}

	/**
	 * Clears the PhpMailer AltBody field, to prevent that content from leaking across emails.
	 */
	private function clear_alt_body_field(): void {
		global $phpmailer;

		if ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer ) {
			$phpmailer->AltBody = ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
	}
}
