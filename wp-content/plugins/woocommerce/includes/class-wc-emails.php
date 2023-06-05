<?php
/**
 * Transactional Emails Controller
 *
 * WooCommerce Emails Class which handles the sending on transactional emails and email templates. This class loads in available emails.
 *
 * @package WooCommerce\Classes\Emails
 * @version 2.3.0
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * Emails class.
 */
class WC_Emails {

	/**
	 * Array of email notification classes
	 *
	 * @var WC_Email[]
	 */
	public $emails = array();

	/**
	 * The single instance of the class
	 *
	 * @var WC_Emails
	 */
	protected static $_instance = null;

	/**
	 * Background emailer class.
	 *
	 * @var WC_Background_Emailer
	 */
	protected static $background_emailer = null;

	/**
	 * Main WC_Emails Instance.
	 *
	 * Ensures only one instance of WC_Emails is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @return WC_Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ), '2.1' );
	}

	/**
	 * Hook in all transactional emails.
	 */
	public static function init_transactional_emails() {
		$email_actions = apply_filters(
			'woocommerce_email_actions',
			array(
				'woocommerce_low_stock',
				'woocommerce_no_stock',
				'woocommerce_product_on_backorder',
				'woocommerce_order_status_pending_to_processing',
				'woocommerce_order_status_pending_to_completed',
				'woocommerce_order_status_processing_to_cancelled',
				'woocommerce_order_status_pending_to_failed',
				'woocommerce_order_status_pending_to_on-hold',
				'woocommerce_order_status_failed_to_processing',
				'woocommerce_order_status_failed_to_completed',
				'woocommerce_order_status_failed_to_on-hold',
				'woocommerce_order_status_cancelled_to_processing',
				'woocommerce_order_status_cancelled_to_completed',
				'woocommerce_order_status_cancelled_to_on-hold',
				'woocommerce_order_status_on-hold_to_processing',
				'woocommerce_order_status_on-hold_to_cancelled',
				'woocommerce_order_status_on-hold_to_failed',
				'woocommerce_order_status_completed',
				'woocommerce_order_fully_refunded',
				'woocommerce_order_partially_refunded',
				'woocommerce_new_customer_note',
				'woocommerce_created_customer',
			)
		);

		if ( apply_filters( 'woocommerce_defer_transactional_emails', false ) ) {
			self::$background_emailer = new WC_Background_Emailer();

			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'queue_transactional_email' ), 10, 10 );
			}
		} else {
			foreach ( $email_actions as $action ) {
				add_action( $action, array( __CLASS__, 'send_transactional_email' ), 10, 10 );
			}
		}
	}

	/**
	 * Queues transactional email so it's not sent in current request if enabled,
	 * otherwise falls back to send now.
	 *
	 * @param mixed ...$args Optional arguments.
	 */
	public static function queue_transactional_email( ...$args ) {
		if ( is_a( self::$background_emailer, 'WC_Background_Emailer' ) ) {
			self::$background_emailer->push_to_queue(
				array(
					'filter' => current_filter(),
					'args'   => func_get_args(),
				)
			);
		} else {
			self::send_transactional_email( ...$args );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 *
	 * @internal
	 *
	 * @param string $filter Filter name.
	 * @param array  $args Email args (default: []).
	 */
	public static function send_queued_transactional_email( $filter = '', $args = array() ) {
		if ( apply_filters( 'woocommerce_allow_send_queued_transactional_email', true, $filter, $args ) ) {
			self::instance(); // Init self so emails exist.

			// Ensure gateways are loaded in case they need to insert data into the emails.
			WC()->payment_gateways();
			WC()->shipping();

			do_action_ref_array( $filter . '_notification', $args );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 *
	 * @internal
	 *
	 * @param array $args Email args (default: []).
	 */
	public static function send_transactional_email( $args = array() ) {
		try {
			$args = func_get_args();
			self::instance(); // Init self so emails exist.
			do_action_ref_array( current_filter() . '_notification', $args );
		} catch ( Exception $e ) {
			$error  = 'Transactional email triggered fatal error for callback ' . current_filter();
			$logger = wc_get_logger();
			$logger->critical(
				$error . PHP_EOL,
				array(
					'source' => 'transactional-emails',
				)
			);
			if ( Constants::is_true( 'WP_DEBUG' ) ) {
				trigger_error( $error, E_USER_WARNING ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped, WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			}
		}
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 */
	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks.
		add_action( 'woocommerce_email_header', array( $this, 'email_header' ) );
		add_action( 'woocommerce_email_footer', array( $this, 'email_footer' ) );
		add_action( 'woocommerce_email_order_details', array( $this, 'order_downloads' ), 10, 4 );
		add_action( 'woocommerce_email_order_details', array( $this, 'order_details' ), 10, 4 );
		add_action( 'woocommerce_email_order_meta', array( $this, 'order_meta' ), 10, 3 );
		add_action( 'woocommerce_email_customer_details', array( $this, 'customer_details' ), 10, 3 );
		add_action( 'woocommerce_email_customer_details', array( $this, 'email_addresses' ), 20, 3 );

		// Hooks for sending emails during store events.
		add_action( 'woocommerce_low_stock_notification', array( $this, 'low_stock' ) );
		add_action( 'woocommerce_no_stock_notification', array( $this, 'no_stock' ) );
		add_action( 'woocommerce_product_on_backorder_notification', array( $this, 'backorder' ) );
		add_action( 'woocommerce_created_customer_notification', array( $this, 'customer_new_account' ), 10, 3 );

		// Hook for replacing {site_title} in email-footer.
		add_filter( 'woocommerce_email_footer_text', array( $this, 'replace_placeholders' ) );

		// Let 3rd parties unhook the above via this hook.
		do_action( 'woocommerce_email', $this );
	}

	/**
	 * Init email classes.
	 */
	public function init() {
		// Include email classes.
		include_once dirname( __FILE__ ) . '/emails/class-wc-email.php';

		$this->emails['WC_Email_New_Order']                 = include __DIR__ . '/emails/class-wc-email-new-order.php';
		$this->emails['WC_Email_Cancelled_Order']           = include __DIR__ . '/emails/class-wc-email-cancelled-order.php';
		$this->emails['WC_Email_Failed_Order']              = include __DIR__ . '/emails/class-wc-email-failed-order.php';
		$this->emails['WC_Email_Customer_On_Hold_Order']    = include __DIR__ . '/emails/class-wc-email-customer-on-hold-order.php';
		$this->emails['WC_Email_Customer_Processing_Order'] = include __DIR__ . '/emails/class-wc-email-customer-processing-order.php';
		$this->emails['WC_Email_Customer_Completed_Order']  = include __DIR__ . '/emails/class-wc-email-customer-completed-order.php';
		$this->emails['WC_Email_Customer_Refunded_Order']   = include __DIR__ . '/emails/class-wc-email-customer-refunded-order.php';
		$this->emails['WC_Email_Customer_Invoice']          = include __DIR__ . '/emails/class-wc-email-customer-invoice.php';
		$this->emails['WC_Email_Customer_Note']             = include __DIR__ . '/emails/class-wc-email-customer-note.php';
		$this->emails['WC_Email_Customer_Reset_Password']   = include __DIR__ . '/emails/class-wc-email-customer-reset-password.php';
		$this->emails['WC_Email_Customer_New_Account']      = include __DIR__ . '/emails/class-wc-email-customer-new-account.php';

		$this->emails = apply_filters( 'woocommerce_email_classes', $this->emails );
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 *
	 * @return WC_Email[]
	 */
	public function get_emails() {
		return $this->emails;
	}

	/**
	 * Get from name for email.
	 *
	 * @return string
	 */
	public function get_from_name() {
		return wp_specialchars_decode( get_option( 'woocommerce_email_from_name' ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 *
	 * @return string
	 */
	public function get_from_address() {
		return sanitize_email( get_option( 'woocommerce_email_from_address' ) );
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $email_heading Heading for the email.
	 */
	public function email_header( $email_heading ) {
		wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		wc_get_template( 'emails/email-footer.php' );
	}

	/**
	 * Replace placeholder text in strings.
	 *
	 * @since  3.7.0
	 * @param  string $string Email footer text.
	 * @return string         Email footer text with any replacements done.
	 */
	public function replace_placeholders( $string ) {
		$domain = wp_parse_url( home_url(), PHP_URL_HOST );

		return str_replace(
			array(
				'{site_title}',
				'{site_address}',
				'{site_url}',
				'{woocommerce}',
				'{WooCommerce}',
			),
			array(
				$this->get_blogname(),
				$domain,
				$domain,
				'<a href="https://woocommerce.com">WooCommerce</a>',
				'<a href="https://woocommerce.com">WooCommerce</a>',
			),
			$string
		);
	}

	/**
	 * Filter callback to replace {site_title} in email footer
	 *
	 * @since  3.3.0
	 * @deprecated 3.7.0
	 * @param  string $string Email footer text.
	 * @return string         Email footer text with any replacements done.
	 */
	public function email_footer_replace_site_title( $string ) {
		wc_deprecated_function( 'WC_Emails::email_footer_replace_site_title', '3.7.0', 'WC_Emails::replace_placeholders' );
		return $this->replace_placeholders( $string );
	}

	/**
	 * Wraps a message in the woocommerce mail template.
	 *
	 * @param string $email_heading Heading text.
	 * @param string $message       Email message.
	 * @param bool   $plain_text    Set true to send as plain text. Default to false.
	 *
	 * @return string
	 */
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer.
		ob_start();

		do_action( 'woocommerce_email_header', $email_heading, null );

		echo wpautop( wptexturize( $message ) ); // WPCS: XSS ok.

		do_action( 'woocommerce_email_footer', null );

		// Get contents.
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Send the email.
	 *
	 * @param mixed  $to          Receiver.
	 * @param mixed  $subject     Email subject.
	 * @param mixed  $message     Message.
	 * @param string $headers     Email headers (default: "Content-Type: text/html\r\n").
	 * @param string $attachments Attachments (default: "").
	 * @return bool
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
		// Send.
		$email = new WC_Email();
		return $email->send( $to, $subject, $message, $headers, $attachments );
	}

	/**
	 * Prepare and send the customer invoice email on demand.
	 *
	 * @param int|WC_Order $order Order instance or ID.
	 */
	public function customer_invoice( $order ) {
		$email = $this->emails['WC_Email_Customer_Invoice'];

		if ( ! is_object( $order ) ) {
			$order = wc_get_order( absint( $order ) );
		}

		$email->trigger( $order->get_id(), $order );
	}

	/**
	 * Customer new account welcome email.
	 *
	 * @param int   $customer_id        Customer ID.
	 * @param array $new_customer_data  New customer data.
	 * @param bool  $password_generated If password is generated.
	 */
	public function customer_new_account( $customer_id, $new_customer_data = array(), $password_generated = false ) {
		if ( ! $customer_id ) {
			return;
		}

		$user_pass = ! empty( $new_customer_data['user_pass'] ) ? $new_customer_data['user_pass'] : '';

		$email = $this->emails['WC_Email_Customer_New_Account'];
		$email->trigger( $customer_id, $user_pass, $password_generated );
	}

	/**
	 * Show the order details table
	 *
	 * @param WC_Order $order         Order instance.
	 * @param bool     $sent_to_admin If should sent to admin.
	 * @param bool     $plain_text    If is plain text email.
	 * @param string   $email         Email address.
	 */
	public function order_details( $order, $sent_to_admin = false, $plain_text = false, $email = '' ) {
		if ( $plain_text ) {
			wc_get_template(
				'emails/plain/email-order-details.php',
				array(
					'order'         => $order,
					'sent_to_admin' => $sent_to_admin,
					'plain_text'    => $plain_text,
					'email'         => $email,
				)
			);
		} else {
			wc_get_template(
				'emails/email-order-details.php',
				array(
					'order'         => $order,
					'sent_to_admin' => $sent_to_admin,
					'plain_text'    => $plain_text,
					'email'         => $email,
				)
			);
		}
	}

	/**
	 * Show order downloads in a table.
	 *
	 * @since 3.2.0
	 * @param WC_Order $order         Order instance.
	 * @param bool     $sent_to_admin If should sent to admin.
	 * @param bool     $plain_text    If is plain text email.
	 * @param string   $email         Email address.
	 */
	public function order_downloads( $order, $sent_to_admin = false, $plain_text = false, $email = '' ) {
		$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted() && ! $sent_to_admin && ! is_a( $email, 'WC_Email_Customer_Refunded_Order' );

		if ( ! $show_downloads ) {
			return;
		}

		$downloads = $order->get_downloadable_items();
		$columns   = apply_filters(
			'woocommerce_email_downloads_columns',
			array(
				'download-product' => __( 'Product', 'woocommerce' ),
				'download-expires' => __( 'Expires', 'woocommerce' ),
				'download-file'    => __( 'Download', 'woocommerce' ),
			)
		);

		if ( $plain_text ) {
			wc_get_template(
				'emails/plain/email-downloads.php',
				array(
					'order'         => $order,
					'sent_to_admin' => $sent_to_admin,
					'plain_text'    => $plain_text,
					'email'         => $email,
					'downloads'     => $downloads,
					'columns'       => $columns,
				)
			);
		} else {
			wc_get_template(
				'emails/email-downloads.php',
				array(
					'order'         => $order,
					'sent_to_admin' => $sent_to_admin,
					'plain_text'    => $plain_text,
					'email'         => $email,
					'downloads'     => $downloads,
					'columns'       => $columns,
				)
			);
		}
	}

	/**
	 * Add order meta to email templates.
	 *
	 * @param WC_Order $order         Order instance.
	 * @param bool     $sent_to_admin If should sent to admin.
	 * @param bool     $plain_text    If is plain text email.
	 */
	public function order_meta( $order, $sent_to_admin = false, $plain_text = false ) {
		$fields = apply_filters( 'woocommerce_email_order_meta_fields', array(), $sent_to_admin, $order );

		/**
		 * Deprecated woocommerce_email_order_meta_keys filter.
		 *
		 * @since 2.3.0
		 */
		$_fields = apply_filters( 'woocommerce_email_order_meta_keys', array(), $sent_to_admin );

		if ( $_fields ) {
			foreach ( $_fields as $key => $field ) {
				if ( is_numeric( $key ) ) {
					$key = $field;
				}

				$fields[ $key ] = array(
					'label' => wptexturize( $key ),
					'value' => wptexturize( $order->get_meta( $field ) ),
				);
			}
		}

		if ( $fields ) {

			if ( $plain_text ) {

				foreach ( $fields as $field ) {
					if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
						echo $field['label'] . ': ' . $field['value'] . "\n"; // WPCS: XSS ok.
					}
				}
			} else {

				foreach ( $fields as $field ) {
					if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
						echo '<p><strong>' . $field['label'] . ':</strong> ' . $field['value'] . '</p>'; // WPCS: XSS ok.
					}
				}
			}
		}
	}

	/**
	 * Is customer detail field valid?
	 *
	 * @param  array $field Field data to check if is valid.
	 * @return boolean
	 */
	public function customer_detail_field_is_valid( $field ) {
		return isset( $field['label'] ) && ! empty( $field['value'] );
	}

	/**
	 * Allows developers to add additional customer details to templates.
	 *
	 * In versions prior to 3.2 this was used for notes, phone and email but this data has moved.
	 *
	 * @param WC_Order $order         Order instance.
	 * @param bool     $sent_to_admin If should sent to admin.
	 * @param bool     $plain_text    If is plain text email.
	 */
	public function customer_details( $order, $sent_to_admin = false, $plain_text = false ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		$fields = array_filter( apply_filters( 'woocommerce_email_customer_details_fields', array(), $sent_to_admin, $order ), array( $this, 'customer_detail_field_is_valid' ) );

		if ( ! empty( $fields ) ) {
			if ( $plain_text ) {
				wc_get_template( 'emails/plain/email-customer-details.php', array( 'fields' => $fields ) );
			} else {
				wc_get_template( 'emails/email-customer-details.php', array( 'fields' => $fields ) );
			}
		}
	}

	/**
	 * Get the email addresses.
	 *
	 * @param WC_Order $order         Order instance.
	 * @param bool     $sent_to_admin If should sent to admin.
	 * @param bool     $plain_text    If is plain text email.
	 */
	public function email_addresses( $order, $sent_to_admin = false, $plain_text = false ) {
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}
		if ( $plain_text ) {
			wc_get_template(
				'emails/plain/email-addresses.php',
				array(
					'order'         => $order,
					'sent_to_admin' => $sent_to_admin,
				)
			);
		} else {
			wc_get_template(
				'emails/email-addresses.php',
				array(
					'order'         => $order,
					'sent_to_admin' => $sent_to_admin,
				)
			);
		}
	}

	/**
	 * Get blog name formatted for emails.
	 *
	 * @return string
	 */
	private function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * Low stock notification email.
	 *
	 * @param WC_Product $product Product instance.
	 */
	public function low_stock( $product ) {
		if ( 'no' === get_option( 'woocommerce_notify_low_stock', 'yes' ) ) {
			return;
		}

		/**
		 * Determine if the current product should trigger a low stock notification
		 *
		 * @param int $product_id - The low stock product id
		 *
		 * @since 4.7.0
		 */
		if ( false === apply_filters( 'woocommerce_should_send_low_stock_notification', true, $product->get_id() ) ) {
			return;
		}

		$subject = sprintf( '[%s] %s', $this->get_blogname(), __( 'Product low in stock', 'woocommerce' ) );
		$message = sprintf(
			/* translators: 1: product name 2: items in stock */
			__( '%1$s is low in stock. There are %2$d left.', 'woocommerce' ),
			html_entity_decode( wp_strip_all_tags( $product->get_formatted_name() ), ENT_QUOTES, get_bloginfo( 'charset' ) ),
			html_entity_decode( wp_strip_all_tags( $product->get_stock_quantity() ) )
		);

		wp_mail(
			apply_filters( 'woocommerce_email_recipient_low_stock', get_option( 'woocommerce_stock_email_recipient' ), $product, null ),
			apply_filters( 'woocommerce_email_subject_low_stock', $subject, $product, null ),
			apply_filters( 'woocommerce_email_content_low_stock', $message, $product ),
			apply_filters( 'woocommerce_email_headers', '', 'low_stock', $product, null ),
			apply_filters( 'woocommerce_email_attachments', array(), 'low_stock', $product, null )
		);
	}

	/**
	 * No stock notification email.
	 *
	 * @param WC_Product $product Product instance.
	 */
	public function no_stock( $product ) {
		if ( 'no' === get_option( 'woocommerce_notify_no_stock', 'yes' ) ) {
			return;
		}

		/**
		 * Determine if the current product should trigger a no stock notification
		 *
		 * @param int $product_id - The out of stock product id
		 *
		 * @since 4.6.0
		 */
		if ( false === apply_filters( 'woocommerce_should_send_no_stock_notification', true, $product->get_id() ) ) {
			return;
		}

		$subject = sprintf( '[%s] %s', $this->get_blogname(), __( 'Product out of stock', 'woocommerce' ) );
		/* translators: %s: product name */
		$message = sprintf( __( '%s is out of stock.', 'woocommerce' ), html_entity_decode( wp_strip_all_tags( $product->get_formatted_name() ), ENT_QUOTES, get_bloginfo( 'charset' ) ) );

		wp_mail(
			apply_filters( 'woocommerce_email_recipient_no_stock', get_option( 'woocommerce_stock_email_recipient' ), $product, null ),
			apply_filters( 'woocommerce_email_subject_no_stock', $subject, $product, null ),
			apply_filters( 'woocommerce_email_content_no_stock', $message, $product ),
			apply_filters( 'woocommerce_email_headers', '', 'no_stock', $product, null ),
			apply_filters( 'woocommerce_email_attachments', array(), 'no_stock', $product, null )
		);
	}

	/**
	 * Backorder notification email.
	 *
	 * @param array $args Arguments.
	 */
	public function backorder( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'product'  => '',
				'quantity' => '',
				'order_id' => '',
			)
		);

		$order = wc_get_order( $args['order_id'] );
		if (
			! $args['product'] ||
			! is_object( $args['product'] ) ||
			! $args['quantity'] ||
			! $order
		) {
			return;
		}

		$subject = sprintf( '[%s] %s', $this->get_blogname(), __( 'Product backorder', 'woocommerce' ) );
		/* translators: 1: product quantity 2: product name 3: order number */
		$message = sprintf( __( '%1$s units of %2$s have been backordered in order #%3$s.', 'woocommerce' ), $args['quantity'], html_entity_decode( wp_strip_all_tags( $args['product']->get_formatted_name() ), ENT_QUOTES, get_bloginfo( 'charset' ) ), $order->get_order_number() );

		wp_mail(
			apply_filters( 'woocommerce_email_recipient_backorder', get_option( 'woocommerce_stock_email_recipient' ), $args, null ),
			apply_filters( 'woocommerce_email_subject_backorder', $subject, $args, null ),
			apply_filters( 'woocommerce_email_content_backorder', $message, $args ),
			apply_filters( 'woocommerce_email_headers', '', 'backorder', $args, null ),
			apply_filters( 'woocommerce_email_attachments', array(), 'backorder', $args, null )
		);
	}

	/**
	 * Adds Schema.org markup for order in JSON-LD format.
	 *
	 * @deprecated 3.0.0
	 * @see WC_Structured_Data::generate_order_data()
	 *
	 * @since 2.6.0
	 * @param WC_Order $order         Order instance.
	 * @param bool     $sent_to_admin If should sent to admin.
	 * @param bool     $plain_text    If is plain text email.
	 */
	public function order_schema_markup( $order, $sent_to_admin = false, $plain_text = false ) {
		wc_deprecated_function( 'WC_Emails::order_schema_markup', '3.0', 'WC_Structured_Data::generate_order_data' );

		WC()->structured_data->generate_order_data( $order, $sent_to_admin, $plain_text );
		WC()->structured_data->output_structured_data();
	}
}
