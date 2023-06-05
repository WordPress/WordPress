<?php
/**
 * Represents a single shipping zone
 *
 * @since   2.6.0
 * @version 3.0.0
 * @package WooCommerce\Classes
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/legacy/class-wc-legacy-shipping-zone.php';

/**
 * WC_Shipping_Zone class.
 */
class WC_Shipping_Zone extends WC_Legacy_Shipping_Zone {

	/**
	 * Zone ID
	 *
	 * @var int|null
	 */
	protected $id = null;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'shipping_zone';

	/**
	 * Zone Data.
	 *
	 * @var array
	 */
	protected $data = array(
		'zone_name'      => '',
		'zone_order'     => 0,
		'zone_locations' => array(),
	);

	/**
	 * Constructor for zones.
	 *
	 * @param int|object $zone Zone ID to load from the DB or zone object.
	 */
	public function __construct( $zone = null ) {
		if ( is_numeric( $zone ) && ! empty( $zone ) ) {
			$this->set_id( $zone );
		} elseif ( is_object( $zone ) ) {
			$this->set_id( $zone->zone_id );
		} elseif ( 0 === $zone || '0' === $zone ) {
			$this->set_id( 0 );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = WC_Data_Store::load( 'shipping-zone' );
		if ( false === $this->get_object_read() ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * --------------------------------------------------------------------------
	 * Getters
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Get zone name.
	 *
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_zone_name( $context = 'view' ) {
		return $this->get_prop( 'zone_name', $context );
	}

	/**
	 * Get zone order.
	 *
	 * @param  string $context View or edit context.
	 * @return int
	 */
	public function get_zone_order( $context = 'view' ) {
		return $this->get_prop( 'zone_order', $context );
	}

	/**
	 * Get zone locations.
	 *
	 * @param  string $context View or edit context.
	 * @return array of zone objects
	 */
	public function get_zone_locations( $context = 'view' ) {
		return $this->get_prop( 'zone_locations', $context );
	}

	/**
	 * Return a text string representing what this zone is for.
	 *
	 * @param  int    $max Max locations to return.
	 * @param  string $context View or edit context.
	 * @return string
	 */
	public function get_formatted_location( $max = 10, $context = 'view' ) {
		$location_parts = array();
		$all_continents = WC()->countries->get_continents();
		$all_countries  = WC()->countries->get_countries();
		$all_states     = WC()->countries->get_states();
		$locations      = $this->get_zone_locations( $context );
		$continents     = array_filter( $locations, array( $this, 'location_is_continent' ) );
		$countries      = array_filter( $locations, array( $this, 'location_is_country' ) );
		$states         = array_filter( $locations, array( $this, 'location_is_state' ) );
		$postcodes      = array_filter( $locations, array( $this, 'location_is_postcode' ) );

		foreach ( $continents as $location ) {
			$location_parts[] = $all_continents[ $location->code ]['name'];
		}

		foreach ( $countries as $location ) {
			$location_parts[] = $all_countries[ $location->code ];
		}

		foreach ( $states as $location ) {
			$location_codes   = explode( ':', $location->code );
			$location_parts[] = $all_states[ $location_codes[0] ][ $location_codes[1] ];
		}

		foreach ( $postcodes as $location ) {
			$location_parts[] = $location->code;
		}

		// Fix display of encoded characters.
		$location_parts = array_map( 'html_entity_decode', $location_parts );

		if ( count( $location_parts ) > $max ) {
			$remaining = count( $location_parts ) - $max;
			// @codingStandardsIgnoreStart
			return sprintf( _n( '%s and %d other region', '%s and %d other regions', $remaining, 'woocommerce' ), implode( ', ', array_splice( $location_parts, 0, $max ) ), $remaining );
			// @codingStandardsIgnoreEnd
		} elseif ( ! empty( $location_parts ) ) {
			return implode( ', ', $location_parts );
		} else {
			return __( 'Everywhere', 'woocommerce' );
		}
	}

	/**
	 * Get shipping methods linked to this zone.
	 *
	 * @param bool   $enabled_only Only return enabled methods.
	 * @param string $context Getting shipping methods for what context. Valid values, admin, json.
	 * @return array of objects
	 */
	public function get_shipping_methods( $enabled_only = false, $context = 'admin' ) {
		if ( null === $this->get_id() ) {
			return array();
		}

		$raw_methods     = $this->data_store->get_methods( $this->get_id(), $enabled_only );
		$wc_shipping     = WC_Shipping::instance();
		$allowed_classes = $wc_shipping->get_shipping_method_class_names();
		$methods         = array();

		foreach ( $raw_methods as $raw_method ) {
			if ( in_array( $raw_method->method_id, array_keys( $allowed_classes ), true ) ) {
				$class_name  = $allowed_classes[ $raw_method->method_id ];
				$instance_id = $raw_method->instance_id;

				// The returned array may contain instances of shipping methods, as well
				// as classes. If the "class" is an instance, just use it. If not,
				// create an instance.
				if ( is_object( $class_name ) ) {
					$class_name_of_instance  = get_class( $class_name );
					$methods[ $instance_id ] = new $class_name_of_instance( $instance_id );
				} else {
					// If the class is not an object, it should be a string. It's better
					// to double check, to be sure (a class must be a string, anything)
					// else would be useless.
					if ( is_string( $class_name ) && class_exists( $class_name ) ) {
						$methods[ $instance_id ] = new $class_name( $instance_id );
					}
				}

				// Let's make sure that we have an instance before setting its attributes.
				if ( is_object( $methods[ $instance_id ] ) ) {
					$methods[ $instance_id ]->method_order       = absint( $raw_method->method_order );
					$methods[ $instance_id ]->enabled            = $raw_method->is_enabled ? 'yes' : 'no';
					$methods[ $instance_id ]->has_settings       = $methods[ $instance_id ]->has_settings();
					$methods[ $instance_id ]->settings_html      = $methods[ $instance_id ]->supports( 'instance-settings-modal' ) ? $methods[ $instance_id ]->get_admin_options_html() : false;
					$methods[ $instance_id ]->method_description = wp_kses_post( wpautop( $methods[ $instance_id ]->method_description ) );
				}

				if ( 'json' === $context ) {
					// We don't want the entire object in this context, just the public props.
					$methods[ $instance_id ] = (object) get_object_vars( $methods[ $instance_id ] );
					unset( $methods[ $instance_id ]->instance_form_fields, $methods[ $instance_id ]->form_fields );
				}
			}
		}

		uasort( $methods, 'wc_shipping_zone_method_order_uasort_comparison' );

		return apply_filters( 'woocommerce_shipping_zone_shipping_methods', $methods, $raw_methods, $allowed_classes, $this );
	}

	/**
	 * --------------------------------------------------------------------------
	 * Setters
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Set zone name.
	 *
	 * @param string $set Value to set.
	 */
	public function set_zone_name( $set ) {
		$this->set_prop( 'zone_name', wc_clean( $set ) );
	}

	/**
	 * Set zone order. Value to set.
	 *
	 * @param int $set Value to set.
	 */
	public function set_zone_order( $set ) {
		$this->set_prop( 'zone_order', absint( $set ) );
	}

	/**
	 * Set zone locations.
	 *
	 * @since 3.0.0
	 * @param array $locations Value to set.
	 */
	public function set_zone_locations( $locations ) {
		if ( 0 !== $this->get_id() ) {
			$this->set_prop( 'zone_locations', $locations );
		}
	}

	/**
	 * --------------------------------------------------------------------------
	 * Other
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Save zone data to the database.
	 *
	 * @return int
	 */
	public function save() {
		if ( ! $this->get_zone_name() ) {
			$this->set_zone_name( $this->generate_zone_name() );
		}

		if ( ! $this->data_store ) {
			return $this->get_id();
		}

		/**
		 * Trigger action before saving to the DB. Allows you to adjust object props before save.
		 *
		 * @param WC_Data          $this The object being saved.
		 * @param WC_Data_Store_WP $data_store THe data store persisting the data.
		 */
		do_action( 'woocommerce_before_' . $this->object_type . '_object_save', $this, $this->data_store );

		if ( null !== $this->get_id() ) {
			$this->data_store->update( $this );
		} else {
			$this->data_store->create( $this );
		}

		/**
		 * Trigger action after saving to the DB.
		 *
		 * @param WC_Data          $this The object being saved.
		 * @param WC_Data_Store_WP $data_store THe data store persisting the data.
		 */
		do_action( 'woocommerce_after_' . $this->object_type . '_object_save', $this, $this->data_store );

		return $this->get_id();
	}

	/**
	 * Generate a zone name based on location.
	 *
	 * @return string
	 */
	protected function generate_zone_name() {
		$zone_name = $this->get_formatted_location();

		if ( empty( $zone_name ) ) {
			$zone_name = __( 'Zone', 'woocommerce' );
		}

		return $zone_name;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_continent( $location ) {
		return 'continent' === $location->type;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_country( $location ) {
		return 'country' === $location->type;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_state( $location ) {
		return 'state' === $location->type;
	}

	/**
	 * Location type detection.
	 *
	 * @param  object $location Location to check.
	 * @return boolean
	 */
	private function location_is_postcode( $location ) {
		return 'postcode' === $location->type;
	}

	/**
	 * Is passed location type valid?
	 *
	 * @param  string $type Type to check.
	 * @return boolean
	 */
	public function is_valid_location_type( $type ) {
		return in_array( $type, apply_filters( 'woocommerce_valid_location_types', array( 'postcode', 'state', 'country', 'continent' ) ), true );
	}

	/**
	 * Add location (state or postcode) to a zone.
	 *
	 * @param string $code Location code.
	 * @param string $type state or postcode.
	 */
	public function add_location( $code, $type ) {
		if ( 0 !== $this->get_id() && $this->is_valid_location_type( $type ) ) {
			if ( 'postcode' === $type ) {
				$code = trim( strtoupper( str_replace( chr( 226 ) . chr( 128 ) . chr( 166 ), '...', $code ) ) ); // No normalization - postcodes are matched against both normal and formatted versions to support wildcards.
			}
			$location         = array(
				'code' => wc_clean( $code ),
				'type' => wc_clean( $type ),
			);
			$zone_locations   = $this->get_prop( 'zone_locations', 'edit' );
			$zone_locations[] = (object) $location;
			$this->set_prop( 'zone_locations', $zone_locations );
		}
	}


	/**
	 * Clear all locations for this zone.
	 *
	 * @param array|string $types of location to clear.
	 */
	public function clear_locations( $types = array( 'postcode', 'state', 'country', 'continent' ) ) {
		if ( ! is_array( $types ) ) {
			$types = array( $types );
		}
		$zone_locations = $this->get_prop( 'zone_locations', 'edit' );
		foreach ( $zone_locations as $key => $values ) {
			if ( in_array( $values->type, $types, true ) ) {
				unset( $zone_locations[ $key ] );
			}
		}
		$zone_locations = array_values( $zone_locations ); // reindex.
		$this->set_prop( 'zone_locations', $zone_locations );
	}

	/**
	 * Set locations.
	 *
	 * @param array $locations Array of locations.
	 */
	public function set_locations( $locations = array() ) {
		$this->clear_locations();
		foreach ( $locations as $location ) {
			$this->add_location( $location['code'], $location['type'] );
		}
	}

	/**
	 * Add a shipping method to this zone.
	 *
	 * @param string $type shipping method type.
	 * @return int new instance_id, 0 on failure
	 */
	public function add_shipping_method( $type ) {
		if ( null === $this->get_id() ) {
			$this->save();
		}

		$instance_id     = 0;
		$wc_shipping     = WC_Shipping::instance();
		$allowed_classes = $wc_shipping->get_shipping_method_class_names();
		$count           = $this->data_store->get_method_count( $this->get_id() );

		if ( in_array( $type, array_keys( $allowed_classes ), true ) ) {
			$instance_id = $this->data_store->add_method( $this->get_id(), $type, $count + 1 );
		}

		if ( $instance_id ) {
			do_action( 'woocommerce_shipping_zone_method_added', $instance_id, $type, $this->get_id() );
		}

		WC_Cache_Helper::get_transient_version( 'shipping', true );

		return $instance_id;
	}

	/**
	 * Delete a shipping method from a zone.
	 *
	 * @param int $instance_id Shipping method instance ID.
	 * @return True on success, false on failure
	 */
	public function delete_shipping_method( $instance_id ) {
		if ( null === $this->get_id() ) {
			return false;
		}

		// Get method details.
		$method = $this->data_store->get_method( $instance_id );

		if ( $method ) {
			$this->data_store->delete_method( $instance_id );
			do_action( 'woocommerce_shipping_zone_method_deleted', $instance_id, $method->method_id, $this->get_id() );
		}

		WC_Cache_Helper::get_transient_version( 'shipping', true );

		return true;
	}
}
