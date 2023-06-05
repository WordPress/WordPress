<?php
/**
 * Class WC_Shipping_Legacy_Flat_Rate file.
 *
 * @package WooCommerce\Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Flat Rate Shipping Method.
 *
 * This class is here for backwards compatibility for methods existing before zones existed.
 *
 * @deprecated  2.6.0
 * @version     2.4.0
 * @package     WooCommerce\Classes\Shipping
 */
class WC_Shipping_Legacy_Flat_Rate extends WC_Shipping_Method {

	/**
	 * Cost passed to [fee] shortcode.
	 *
	 * @var string
	 */
	protected $fee_cost = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id           = 'legacy_flat_rate';
		$this->method_title = __( 'Flat rate (legacy)', 'woocommerce' );
		/* translators: %s: Admin shipping settings URL */
		$this->method_description = '<strong>' . sprintf( __( 'This method is deprecated in 2.6.0 and will be removed in future versions - we recommend disabling it and instead setting up a new rate within your <a href="%s">Shipping zones</a>.', 'woocommerce' ), admin_url( 'admin.php?page=wc-settings&tab=shipping' ) ) . '</strong>';
		$this->init();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_flat_rate_shipping_add_rate', array( $this, 'calculate_extra_shipping' ), 10, 2 );
	}

	/**
	 * Process and redirect if disabled.
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		if ( 'no' === $this->settings['enabled'] ) {
			wp_redirect( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=options' ) );
			exit;
		}
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @since 2.6.0
	 * @return string
	 */
	public function get_option_key() {
		return $this->plugin_id . 'flat_rate_settings';
	}

	/**
	 * Init function.
	 */
	public function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->title        = $this->get_option( 'title' );
		$this->availability = $this->get_option( 'availability' );
		$this->countries    = $this->get_option( 'countries' );
		$this->tax_status   = $this->get_option( 'tax_status' );
		$this->cost         = $this->get_option( 'cost' );
		$this->type         = $this->get_option( 'type', 'class' );
		$this->options      = $this->get_option( 'options', false ); // @deprecated 2.4.0
	}

	/**
	 * Initialise Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = include __DIR__ . '/includes/settings-flat-rate.php';
	}

	/**
	 * Evaluate a cost from a sum/string.
	 *
	 * @param  string $sum Sum to evaluate.
	 * @param  array  $args Arguments.
	 * @return string
	 */
	protected function evaluate_cost( $sum, $args = array() ) {
		include_once WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php';

		$locale   = localeconv();
		$decimals = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'] );

		$this->fee_cost = $args['cost'];

		// Expand shortcodes.
		add_shortcode( 'fee', array( $this, 'fee' ) );

		$sum = do_shortcode(
			str_replace(
				array(
					'[qty]',
					'[cost]',
				),
				array(
					$args['qty'],
					$args['cost'],
				),
				$sum
			)
		);

		remove_shortcode( 'fee', array( $this, 'fee' ) );

		// Remove whitespace from string.
		$sum = preg_replace( '/\s+/', '', $sum );

		// Remove locale from string.
		$sum = str_replace( $decimals, '.', $sum );

		// Trim invalid start/end characters.
		$sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

		// Do the math.
		return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
	}

	/**
	 * Work out fee (shortcode).
	 *
	 * @param  array $atts Shortcode attributes.
	 * @return string
	 */
	public function fee( $atts ) {
		$atts = shortcode_atts(
			array(
				'percent' => '',
				'min_fee' => '',
			),
			$atts,
			'fee'
		);

		$calculated_fee = 0;

		if ( $atts['percent'] ) {
			$calculated_fee = $this->fee_cost * ( floatval( $atts['percent'] ) / 100 );
		}

		if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
			$calculated_fee = $atts['min_fee'];
		}

		return $calculated_fee;
	}

	/**
	 * Calculate shipping.
	 *
	 * @param array $package (default: array()).
	 */
	public function calculate_shipping( $package = array() ) {
		$rate = array(
			'id'      => $this->id,
			'label'   => $this->title,
			'cost'    => 0,
			'package' => $package,
		);

		// Calculate the costs.
		$has_costs = false; // True when a cost is set. False if all costs are blank strings.
		$cost      = $this->get_option( 'cost' );

		if ( '' !== $cost ) {
			$has_costs    = true;
			$rate['cost'] = $this->evaluate_cost(
				$cost,
				array(
					'qty'  => $this->get_package_item_qty( $package ),
					'cost' => $package['contents_cost'],
				)
			);
		}

		// Add shipping class costs.
		$found_shipping_classes = $this->find_shipping_classes( $package );
		$highest_class_cost     = 0;

		foreach ( $found_shipping_classes as $shipping_class => $products ) {
			// Also handles BW compatibility when slugs were used instead of ids.
			$shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
			$class_cost_string   = $shipping_class_term && $shipping_class_term->term_id ? $this->get_option( 'class_cost_' . $shipping_class_term->term_id, $this->get_option( 'class_cost_' . $shipping_class, '' ) ) : $this->get_option( 'no_class_cost', '' );

			if ( '' === $class_cost_string ) {
				continue;
			}

			$has_costs  = true;
			$class_cost = $this->evaluate_cost(
				$class_cost_string,
				array(
					'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
					'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
				)
			);

			if ( 'class' === $this->type ) {
				$rate['cost'] += $class_cost;
			} else {
				$highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
			}
		}

		if ( 'order' === $this->type && $highest_class_cost ) {
			$rate['cost'] += $highest_class_cost;
		}

		$rate['package'] = $package;

		// Add the rate.
		if ( $has_costs ) {
			$this->add_rate( $rate );
		}

		/**
		 * Developers can add additional flat rates based on this one via this action since @version 2.4.
		 *
		 * Previously there were (overly complex) options to add additional rates however this was not user.
		 * friendly and goes against what Flat Rate Shipping was originally intended for.
		 *
		 * This example shows how you can add an extra rate based on this flat rate via custom function:
		 *
		 *      add_action( 'woocommerce_flat_rate_shipping_add_rate', 'add_another_custom_flat_rate', 10, 2 );
		 *
		 *      function add_another_custom_flat_rate( $method, $rate ) {
		 *          $new_rate          = $rate;
		 *          $new_rate['id']    .= ':' . 'custom_rate_name'; // Append a custom ID.
		 *          $new_rate['label'] = 'Rushed Shipping'; // Rename to 'Rushed Shipping'.
		 *          $new_rate['cost']  += 2; // Add $2 to the cost.
		 *
		 *          // Add it to WC.
		 *          $method->add_rate( $new_rate );
		 *      }.
		 */
		do_action( 'woocommerce_flat_rate_shipping_add_rate', $this, $rate );
	}

	/**
	 * Get items in package.
	 *
	 * @param  array $package Package information.
	 * @return int
	 */
	public function get_package_item_qty( $package ) {
		$total_quantity = 0;
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
				$total_quantity += $values['quantity'];
			}
		}
		return $total_quantity;
	}

	/**
	 * Finds and returns shipping classes and the products with said class.
	 *
	 * @param mixed $package Package information.
	 * @return array
	 */
	public function find_shipping_classes( $package ) {
		$found_shipping_classes = array();

		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['data']->needs_shipping() ) {
				$found_class = $values['data']->get_shipping_class();

				if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
					$found_shipping_classes[ $found_class ] = array();
				}

				$found_shipping_classes[ $found_class ][ $item_id ] = $values;
			}
		}

		return $found_shipping_classes;
	}

	/**
	 * Adds extra calculated flat rates.
	 *
	 * @deprecated 2.4.0
	 *
	 * Additional rates defined like this:
	 *  Option Name | Additional Cost [+- Percents%] | Per Cost Type (order, class, or item).
	 *
	 * @param null  $method Deprecated.
	 * @param array $rate Rate information.
	 */
	public function calculate_extra_shipping( $method, $rate ) {
		if ( $this->options ) {
			$options = array_filter( (array) explode( "\n", $this->options ) );

			foreach ( $options as $option ) {
				$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );
				if ( count( $this_option ) !== 3 ) {
					continue;
				}
				$extra_rate          = $rate;
				$extra_rate['id']    = $this->id . ':' . urldecode( sanitize_title( $this_option[0] ) );
				$extra_rate['label'] = $this_option[0];
				$extra_cost          = $this->get_extra_cost( $this_option[1], $this_option[2], $rate['package'] );
				if ( is_array( $extra_rate['cost'] ) ) {
					$extra_rate['cost']['order'] = $extra_rate['cost']['order'] + $extra_cost;
				} else {
					$extra_rate['cost'] += $extra_cost;
				}

				$this->add_rate( $extra_rate );
			}
		}
	}

	/**
	 * Calculate the percentage adjustment for each shipping rate.
	 *
	 * @deprecated 2.4.0
	 * @param  float  $cost Cost.
	 * @param  float  $percent_adjustment Percent adjustment.
	 * @param  string $percent_operator Percent operator.
	 * @param  float  $base_price Base price.
	 * @return float
	 */
	public function calc_percentage_adjustment( $cost, $percent_adjustment, $percent_operator, $base_price ) {
		if ( '+' === $percent_operator ) {
			$cost += $percent_adjustment * $base_price;
		} else {
			$cost -= $percent_adjustment * $base_price;
		}
		return $cost;
	}

	/**
	 * Get extra cost.
	 *
	 * @deprecated 2.4.0
	 * @param  string $cost_string Cost string.
	 * @param  string $type Type.
	 * @param  array  $package Package information.
	 * @return float
	 */
	public function get_extra_cost( $cost_string, $type, $package ) {
		$cost         = $cost_string;
		$cost_percent = false;
		// @codingStandardsIgnoreStart
		$pattern      =
			'/' .           // Start regex.
			'(\d+\.?\d*)' . // Capture digits, optionally capture a `.` and more digits.
			'\s*' .         // Match whitespace.
			'(\+|-)' .      // Capture the operand.
			'\s*' .         // Match whitespace.
			'(\d+\.?\d*)' . // Capture digits, optionally capture a `.` and more digits.
			'\%/';          // Match the percent sign & end regex.
		// @codingStandardsIgnoreEnd
		if ( preg_match( $pattern, $cost_string, $this_cost_matches ) ) {
			$cost_operator = $this_cost_matches[2];
			$cost_percent  = $this_cost_matches[3] / 100;
			$cost          = $this_cost_matches[1];
		}
		switch ( $type ) {
			case 'class':
				$cost = $cost * count( $this->find_shipping_classes( $package ) );
				break;
			case 'item':
				$cost = $cost * $this->get_package_item_qty( $package );
				break;
		}
		if ( $cost_percent ) {
			switch ( $type ) {
				case 'class':
					$shipping_classes = $this->find_shipping_classes( $package );
					foreach ( $shipping_classes as $shipping_class => $items ) {
						foreach ( $items as $item_id => $values ) {
							$cost = $this->calc_percentage_adjustment( $cost, $cost_percent, $cost_operator, $values['line_total'] );
						}
					}
					break;
				case 'item':
					foreach ( $package['contents'] as $item_id => $values ) {
						if ( $values['data']->needs_shipping() ) {
							$cost = $this->calc_percentage_adjustment( $cost, $cost_percent, $cost_operator, $values['line_total'] );
						}
					}
					break;
				case 'order':
					$cost = $this->calc_percentage_adjustment( $cost, $cost_percent, $cost_operator, $package['contents_cost'] );
					break;
			}
		}
		return $cost;
	}
}
